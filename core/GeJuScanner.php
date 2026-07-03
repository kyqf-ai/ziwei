<?php
/**
 * 紫微斗数格局扫描引擎 - 强类型严谨核查版 (已完美兼容 PHP 7.4+ 并修复星曜距离Bug)
 */
class GeJuScanner
{
    private array $palaces;       // 12宫位数据
    private int $mingGongIdx;     // 命宫索引
    private int $shenGongIdx;     // 身宫索引
    private string $yearGan;      // 年干
    private string $yearZhi;      // 年支

    private array $shaStars = ['擎羊','陀罗','火星','铃星'];
    private array $taohuaStars = ['红鸾','天喜','咸池','天姚'];

    public function __construct(array $palaces, int $mingGongIdx, int $shenGongIdx, string $yearGan, string $yearZhi)
    {
        $this->palaces = $palaces;
        $this->mingGongIdx = $mingGongIdx;
        $this->shenGongIdx = $shenGongIdx;
        $this->yearGan = $yearGan;
        $this->yearZhi = $yearZhi;
    }

    // ==================== 格局等级常量 ====================

    /** 成格：条件完整，无破坏因素 */
    const GRADE_CHENG  = 'cheng';
    /** 降格：条件满足，但有轻微干扰，效力打折 */
    const GRADE_JIANG  = 'jiang';
    /** 破格：条件满足，但有一票否决因素，格局近乎无效 */
    const GRADE_PO     = 'po';

    /**
     * 扫描所有格局，并附加成格/降格/破格评级
     */
    public function scan(): array
    {
        $matched = [];
        foreach (ZiWeiData::$GE_JU as $geju) {
            if (!isset($geju['rule'])) continue;
            $result = $this->checkRule($geju);
            if (is_array($result)) {
                [$grade, $gradeReasons] = $this->evaluateGrade($geju, $result);
                $matched[] = array_merge($geju, [
                    'detail'       => $result,
                    'rule'         => null,
                    'grade'        => $grade,
                    'grade_label'  => $this->gradeLabel($grade),
                    'grade_reason' => $gradeReasons,
                    'grade_note'   => $this->buildGradeNote($geju, $result, $grade),
                ]);
            }
        }
        return $matched;
    }

    private function gradeLabel(string $grade): string
    {
        switch ($grade) {
            case self::GRADE_CHENG: return '成格';
            case self::GRADE_JIANG: return '降格';
            case self::GRADE_PO:    return '破格';
            default:                return '未知';
        }
    }

    private function buildGradeNote(array $geju, array $detail, string $grade): ?string
    {
        if ($grade === self::GRADE_PO) return null;
        $id = $geju['id'] ?? '';
        $notes = [];

        if ($id === 'TIAN_YI_GONG_MING') {
            $inMing = $detail['in_ming'] ?? [];
            if (!empty($inMing)) {
                $notes[] = implode('、', $inMing) . '直坐命宫，贵人近身，贵气最为直接有力';
            } else {
                $notes[] = '魁钺三方拱照，贵人在远方会合';
            }
        }
        if ($id === 'JUN_CHEN_QINGHUI') {
            $matched = count($detail['matched_groups'] ?? []);
            if ($matched >= 3) $notes[] = "三组辅星俱全（{$matched}组），君臣格局至完整";
        }
        if ($id === 'SAN_QI_JIAHUI') {
            foreach ($this->palaces[$this->mingGongIdx]['main_stars'] ?? [] as $s) {
                if (in_array($s['sihua'] ?? '', ['禄','权','科'], true)) {
                    $notes[] = ($s['name'] ?? '') . '化' . $s['sihua'] . '坐命，三奇从命宫发力';
                    break;
                }
            }
        }
        if ($id === 'SHUANG_LU_JIAOLIU' && ($detail['type'] ?? '') === '同宫') {
            $notes[] = '禄存与化禄同宫，双禄叠加，财气最浓';
        }

        return !empty($notes) ? implode('；', $notes) : null;
    }

    // ==================== 核心评级引擎 ====================

    private function evaluateGrade(array $geju, array $detail): array
    {
        $jiXiong = $geju['jiXiong'] ?? '';
        $rule    = $geju['rule']    ?? [];

        if (in_array($jiXiong, ['凶', '大凶'], true)) {
            return $this->evaluateBadGeju($geju, $detail);
        }
        if ($jiXiong === '中性') {
            return [self::GRADE_CHENG, []];
        }

        $poReasons   = []; 
        $jiangReasons = []; 

        if (!empty($detail['is_broken'])) {
            $poReasons[] = (string)($detail['broken_reason'] ?? '存在破格因素');
        }

        // 核心修复1：目标宫位必须基于格局实际发生的宫位，绝不能死绑在命宫
        $targetIdx = $this->mingGongIdx;
        if (!empty($detail['palace'])) {
            $idx = $this->findPalaceByName($detail['palace']);
            if ($idx >= 0) $targetIdx = $idx;
        }

        // 核心修复2：移除无差别的全局"羊陀夹命"判断。
        $shaInPalace = $this->getShaInPalace($targetIdx);
        if (!empty($shaInPalace)) {
            $jiangReasons[] = implode('、', $shaInPalace) . "同宫干扰";
        }

        $brightnessIssues = $this->checkBrightnessIssues($rule, $detail);
        $jiangReasons = array_merge($jiangReasons, $brightnessIssues);

        $supportIssues = $this->checkSupportIssues($geju, $detail);
        $jiangReasons = array_merge($jiangReasons, $supportIssues);

        if (empty($detail['is_broken']) && $this->isLuMaPartiallyHurt($detail)) {
            $jiangReasons[] = '禄马有轻度受制';
        }

        if (!empty($poReasons)) {
            return [self::GRADE_PO, array_unique($poReasons)];
        }
        if (!empty($jiangReasons)) {
            return [self::GRADE_JIANG, array_unique($jiangReasons)];
        }

        return [self::GRADE_CHENG, []];
    }

    private function evaluateBadGeju(array $geju, array $detail): array
    {
        $targetIdx = $this->mingGongIdx;
        if (!empty($detail['palace'])) {
            $idx = $this->findPalaceByName($detail['palace']);
            if ($idx >= 0) $targetIdx = $idx;
        }

        $jiangReasons = [];
        $hasBenefic = false;
        foreach ($this->getSanfangIndices($targetIdx) as $idx) {
            if ($this->palaceHasLu($idx) || $this->palaceHasStar($idx, '天魁') || $this->palaceHasStar($idx, '天钺')) {
                $hasBenefic = true; break;
            }
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (in_array($s['sihua'] ?? '', ['科', '权'], true)) {
                    $hasBenefic = true; break 2;
                }
            }
        }

        if ($hasBenefic) {
            $jiangReasons[] = '三方见吉星/吉化，凶象有所化解';
            return [self::GRADE_JIANG, $jiangReasons];
        }

        return [self::GRADE_CHENG, []];
    }

    private function checkBrightnessIssues(array $rule, array $detail): array
    {
        $issues   = [];
        $starName = $rule['star'] ?? null;

        if ($starName === null) {
            $stars = $rule['stars'] ?? [];
            foreach ($stars as $s) {
                $idx = $this->findStarPalace($s);
                if ($idx < 0) continue;
                $bright = $this->getStarBrightness($idx, $s);
                if (in_array($bright, ['平', '不', '陷'], true)) {
                    $label = ($this->palaces[$idx]['name'] ?? '') ?: "宫{$idx}";
                    $issues[] = "{$s}在{$label}亮度{$bright}";
                }
            }
            return $issues;
        }

        $bright = $this->getStarBrightness($this->mingGongIdx, (string)$starName);
        if (in_array($bright, ['平'], true)) {
            $issues[] = "{$starName}亮度「平」，格局力量偏弱";
        }
        return $issues;
    }

    private function checkSupportIssues(array $geju, array $detail): array
    {
        $issues  = [];
        $rule    = $geju['rule'] ?? [];
        $minGrps = isset($rule['min_groups']) ? (int)$rule['min_groups'] : null;

        if ($minGrps !== null) {
            $matched = count($detail['matched_groups'] ?? []);
            if ($matched < $minGrps) {
                $issues[] = "辅星组数不足（需{$minGrps}组，实得{$matched}组）";
            } elseif ($matched === $minGrps && $minGrps >= 2) {
                $issues[] = "辅星仅满足最低组数，锦上添花不足";
            }
        }

        if (($geju['id'] ?? '') === 'SAN_QI_JIAHUI') {
            $found = $detail['found'] ?? [];
            if (count($found) < 3) {
                $issues[] = '三奇不完整（缺少禄/权/科之一）';
            }
        }
        return $issues;
    }

    private function isLuMaPartiallyHurt(array $detail): bool
    {
        if (isset($detail['lu_star'], $detail['ma_palace'])) {
            $idx = $this->findPalaceByName((string)($detail['lu_palace'] ?? ''));
            if ($idx >= 0) {
                return !empty($this->getShaInPalace($idx));
            }
        }
        return false;
    }

    private function findPalaceByName(string $name): int
    {
        foreach ($this->palaces as $idx => $p) {
            if (($p['name'] ?? '') === $name) return (int)$idx;
        }
        return -1;
    }

    // ==================== 兼容性重构：规则分发 ====================
    private function checkRule(array $geju)
    {
        $type = $geju['rule']['type'] ?? '';
        switch ($type) {
            case 'stars_same_palace': return $this->checkStarsSamePalace($geju['rule']);
            case 'star_in_ming_zhi': return $this->checkStarInMingZhi($geju['rule']);
            case 'mingzhu_chuhan': return $this->checkMingzhuChuhan();
            case 'stars_in_sanfang_ming': return $this->checkStarsInSanfangMing($geju['rule']);
            case 'star_jiajiu_ming': return $this->checkStarJiajiuMing($geju['rule']);
            case 'star_in_ming_with_sanfang_stars': return $this->checkStarInMingWithSanfangStars($geju['rule']);
            case 'sha_po_lang': return $this->checkShaPoLang();
            case 'shuang_lu': return $this->checkShuangLu();
            case 'san_qi': return $this->checkSanQi();
            case 'yang_tuo_jia_ji': return $this->checkYangTuoJiaJi();
            case 'xing_qiu_jia_yin': return $this->checkXingQiuJiaYin();
            case 'xing_ji_jia_yin': return $this->checkXingJiJiaYin();
            case 'ju_feng_sha': return $this->checkJuFengSha();
            case 'ri_yue_bingming': return $this->checkRiYueBingMing();
            case 'ri_yue_fan_bei': return $this->checkRiYueFanBei();
            case 'lu_chong_po': return $this->checkLuChongPo();
            case 'ju_ji_hua_you': return $this->checkJuJiHuaYou();
            case 'ke_xing_xun_feng': return $this->checkKeXingXunFeng();
            case 'wenxing_gongming': return $this->checkWenxingGongMing();
            case 'lu_ma_jiaochi': return $this->checkLuMaJiaochi();
            case 'jie_kong_jia_ji': return $this->checkJieKongJiaJi();
            case 'shen_ming_tonggong': return $this->checkShenMingTonggong();
            case 'sihua_in_ming_sanfang': return $this->checkSihuaInMingSanfang($geju['rule']);
            case 'ming_wu_zheng_yao': return $this->checkMingWuZhengYao();
            case 'lu_cun_zuo_ming': return $this->checkLuCunZuoMing();
            case 'sha_star_xian_ming': return $this->checkShaStarXianMing();
            case 'ma_luo_kong_wang': return $this->checkMaLuoKongWang();
            case 'lu_shang_mai_shi': return $this->checkLuShangMaiShi();
            case 'ming_lu_an_lu': return $this->checkMingLuAnLu();
            case 'ke_ming_hui_lu': return $this->checkKeMingHuiLu();
            case 'dan_feng_chao_yang': return $this->checkDanFengChaoYang();
            case 'liang_zhong_hua_gai': return $this->checkLiangZhongHuaGai();
            default: return false;
        }
    }

    // ==================== 辅助核心方法 ====================

    private function getSanfangIndices(int $idx): array
    {
        return array_unique([$idx, ($idx + 4) % 12, ($idx + 8) % 12, ($idx + 6) % 12]);
    }

    private function getPalaceStarNames(int $palaceIdx): array
    {
        $names = [];
        foreach (['main_stars', 'assistant_stars', 'mini_stars'] as $cat) {
            foreach ($this->palaces[$palaceIdx][$cat] ?? [] as $s) {
                if (isset($s['name']) && ($s['type'] ?? '') !== 'small-text') {
                    $names[] = (string)$s['name'];
                }
            }
        }
        return $names;
    }

    private function palaceHasStar(int $palaceIdx, string $starName): bool
    {
        return in_array($starName, $this->getPalaceStarNames($palaceIdx), true);
    }

    private function getStarSihua(int $palaceIdx, string $starName): string
    {
        foreach ($this->palaces[$palaceIdx]['main_stars'] ?? [] as $s) {
            if (($s['name'] ?? '') === $starName) return (string)($s['sihua'] ?? '');
        }
        return '';
    }

    private function palaceZhi(int $palaceIdx): string
    {
        return (string)($this->palaces[$palaceIdx]['zhi'] ?? '');
    }

    private function getShaInPalace(int $palaceIdx): array
    {
        return array_values(array_intersect($this->getPalaceStarNames($palaceIdx), $this->shaStars));
    }

    private function isBrightnessXian(int $palaceIdx, string $starName): bool
    {
        return in_array($this->getStarBrightness($palaceIdx, $starName), ['陷', '不', '平'], true);
    }

    private function getStarBrightness(int $palaceIdx, string $starName): string
    {
        foreach (['main_stars', 'assistant_stars', 'mini_stars'] as $cat) {
            foreach ($this->palaces[$palaceIdx][$cat] ?? [] as $s) {
                if (($s['name'] ?? '') === $starName) return (string)($s['brightness'] ?? '');
            }
        }
        return '';
    }

    private function findStarPalace(string $starName): int
    {
        foreach ($this->palaces as $idx => $palace) {
            if ($this->palaceHasStar((int)$idx, $starName)) return (int)$idx;
        }
        return -1;
    }

    private function getMingZhi(): string
    {
        return (string)($this->palaces[$this->mingGongIdx]['zhi'] ?? '');
    }

    private function getAllSanfangStars(int $mingIdx): array
    {
        $stars = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            foreach ($this->getPalaceStarNames($idx) as $name) {
                $stars[$name] = $idx;
            }
        }
        return $stars;
    }

    private function palaceHasLu(int $palaceIdx): bool
    {
        if ($this->palaceHasStar($palaceIdx, '禄存')) return true;
        foreach ($this->palaces[$palaceIdx]['main_stars'] ?? [] as $s) {
            if (($s['sihua'] ?? '') === '禄') return true;
        }
        return false;
    }

    private function hasShaInPalace(int $idx): bool 
    { 
        return !empty($this->getShaInPalace($idx)); 
    }
    
    private function hasShaInSanfang(int $targetIdx): bool 
    {
        foreach ($this->getSanfangIndices($targetIdx) as $idx) {
            if ($this->hasShaInPalace($idx)) return true;
        }
        return false;
    }

    private function hasKongJieInPalace(int $idx): bool 
    {
        return $this->palaceHasStar($idx, '地空') || $this->palaceHasStar($idx, '地劫');
    }

    private function hasKongJieInSanfang(int $targetIdx): bool 
    {
        foreach ($this->getSanfangIndices($targetIdx) as $idx) {
            if ($this->hasKongJieInPalace($idx)) return true;
        }
        return false;
    }

    private function hasJiInPalace(int $idx): bool 
    {
        foreach (['main_stars', 'assistant_stars', 'mini_stars'] as $cat) {
            foreach ($this->palaces[$idx][$cat] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌') return true;
            }
        }
        return false;
    }

    private function hasJiInSanfang(int $targetIdx): bool 
    {
        foreach ($this->getSanfangIndices($targetIdx) as $idx) {
            if ($this->hasJiInPalace($idx)) return true;
        }
        return false;
    }

    private function passStrictChecks(array $rule, int $palaceIdx, array $stars = []): bool
    {
        if (!empty($rule['brightness']) && is_array($rule['brightness'])) {
            foreach ($stars as $star) {
                $bright = $this->getStarBrightness($palaceIdx, $star);
                if ($bright !== '' && !in_array($bright, $rule['brightness'], true)) return false;
            }
        }
        if (!empty($rule['no_sha'])) {
            if ($rule['no_sha'] === 'sanfang' && $this->hasShaInSanfang($palaceIdx)) return false;
            if ($rule['no_sha'] !== 'sanfang' && $this->hasShaInPalace($palaceIdx)) return false;
        }
        if (!empty($rule['no_kong_jie'])) {
            if ($rule['no_kong_jie'] === 'sanfang' && $this->hasKongJieInSanfang($palaceIdx)) return false;
            if ($rule['no_kong_jie'] !== 'sanfang' && $this->hasKongJieInPalace($palaceIdx)) return false;
        }
        if (!empty($rule['no_ji'])) {
            if ($rule['no_ji'] === 'sanfang' && $this->hasJiInSanfang($palaceIdx)) return false;
            if ($rule['no_ji'] !== 'sanfang' && $this->hasJiInPalace($palaceIdx)) return false;
        }
        return true;
    }

    // ==================== 规则检测 ====================

    private function checkStarsSamePalace(array $rule)
    {
        $stars = $rule['stars'] ?? [];
        $zhiFilter = $rule['zhi'] ?? null;
        $checkRange = !empty($rule['only_ming']) ? [$this->mingGongIdx] : array_keys($this->palaces);

        foreach ($checkRange as $idx) {
            $zhi = $this->palaces[$idx]['zhi'] ?? '';
            if ($zhiFilter && is_array($zhiFilter) && !in_array($zhi, $zhiFilter, true)) continue;

            $palaceStars = $this->getPalaceStarNames($idx);
            $found = count(array_intersect($stars, $palaceStars));
            
            if ($found === count($stars)) {
                if (!$this->passStrictChecks($rule, $idx, $stars)) continue;
                return ['palace' => $this->palaces[$idx]['name'] ?? '', 'stars' => $stars];
            }
        }
        return false;
    }

    private function checkStarInMingZhi(array $rule)
    {
        $star = (string)($rule['star'] ?? '');
        $zhiFilter = $rule['zhi'] ?? null;
        $mingIdx = $this->mingGongIdx;

        if ($star === '' || !$this->palaceHasStar($mingIdx, $star)) return false;
        if ($zhiFilter && is_array($zhiFilter) && !in_array($this->getMingZhi(), $zhiFilter, true)) return false;
        if (!$this->passStrictChecks($rule, $mingIdx, [$star])) return false;

        if (!empty($rule['has_sihua']) && is_array($rule['has_sihua'])) {
            if (!in_array($this->getStarSihua($mingIdx, $star), $rule['has_sihua'], true)) return false;
        }
        if (!empty($rule['has_sha']) && empty($this->getShaInPalace($mingIdx))) return false;

        if (!empty($rule['require_taohua'])) {
            $hasTaohua = false;
            foreach ($this->getSanfangIndices($mingIdx) as $idx) {
                if (count(array_intersect($this->taohuaStars, $this->getPalaceStarNames($idx))) > 0) {
                    $hasTaohua = true; break;
                }
            }
            if (!$hasTaohua) return false;
        }

        return ['palace' => $this->palaces[$mingIdx]['name'] ?? '', 'zhi' => $this->getMingZhi(), 'star' => $star];
    }

    private function checkStarsInSanfangMing(array $rule)
    {
        $stars       = $rule['stars'] ?? [];
        $minCount    = isset($rule['min_count']) ? (int)$rule['min_count'] : count($stars);
        $excludeMing = !empty($rule['exclude_ming']);
        $brightnessReq = (!empty($rule['brightness']) && is_array($rule['brightness'])) ? $rule['brightness'] : [];

        $mingIdx = $this->mingGongIdx;
        $ruleNobrightness = $rule; unset($ruleNobrightness['brightness']);
        if (!$this->passStrictChecks($ruleNobrightness, $mingIdx)) return false;

        $sanfangStars = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            if ($excludeMing && $idx === $mingIdx) continue;
            foreach ($this->getPalaceStarNames($idx) as $name) {
                $sanfangStars[$name] = $idx;
            }
        }

        $found = []; $inMing = [];
        foreach ($stars as $s) {
            if (!isset($sanfangStars[$s])) continue;
            if ($brightnessReq !== []) {
                $bright = $this->getStarBrightness($sanfangStars[$s], $s);
                if ($bright !== '' && !in_array($bright, $brightnessReq, true)) continue;
            }
            $found[] = $s;
            if ($sanfangStars[$s] === $mingIdx) $inMing[] = $s;
        }

        if (!empty($rule['with_lu'])) {
            $hasLu = false;
            foreach ($this->getSanfangIndices($mingIdx) as $idx) {
                if ($excludeMing && $idx === $mingIdx) continue;
                if (($rule['lu_type'] ?? '') === '禄存') {
                    if ($this->palaceHasStar($idx, '禄存')) $hasLu = true;
                } else {
                    if ($this->palaceHasLu($idx)) $hasLu = true;
                }
                if ($hasLu) break;
            }
            if (!$hasLu) return false;
        }

        if (count($found) < $minCount) return false;
        return ['found_stars' => $found, 'total_required' => count($stars), 'in_ming' => $inMing];
    }

    private function checkStarJiajiuMing(array $rule)
    {
        $starMing = $rule['star_ming'] ?? null;
        $jiaStars = $rule['jia_stars'] ?? [];
        $mingIdx = $this->mingGongIdx;

        if ($starMing !== null && !$this->palaceHasStar($mingIdx, (string)$starMing)) return false;
        if (!$this->passStrictChecks($rule, $mingIdx, $starMing ? [(string)$starMing] : [])) return false;

        $leftIdx = ($mingIdx - 1 + 12) % 12;
        $rightIdx = ($mingIdx + 1) % 12;
        $leftStars = $this->getPalaceStarNames($leftIdx);
        $rightStars = $this->getPalaceStarNames($rightIdx);

        if (count($jiaStars) === 2) {
            $s1 = (string)$jiaStars[0]; $s2 = (string)$jiaStars[1];
            if ((in_array($s1, $leftStars, true) && in_array($s2, $rightStars, true)) || 
                (in_array($s2, $leftStars, true) && in_array($s1, $rightStars, true))) {
                return ['left_palace' => $this->palaces[$leftIdx]['name'] ?? '', 'right_palace' => $this->palaces[$rightIdx]['name'] ?? '', 'jia_stars' => $jiaStars];
            }
        }
        return false;
    }

    private function checkStarInMingWithSanfangStars(array $rule)
    {
        $star = (string)($rule['star'] ?? '');
        $groups = $rule['sanfang_groups'] ?? [];
        $minGroups = (int)($rule['min_groups'] ?? 1);

        if ($star === '' || !$this->palaceHasStar($this->mingGongIdx, $star)) return false;
        if (!$this->passStrictChecks($rule, $this->mingGongIdx, [$star])) return false;

        $sanfangStars = $this->getAllSanfangStars($this->mingGongIdx);
        $matchedGroups = [];
        foreach ($groups as $group) {
            if (!is_array($group)) continue;
            foreach ($group as $gs) {
                if (isset($sanfangStars[$gs])) { $matchedGroups[] = implode('+', $group); break; }
            }
        }
        $matchedGroups = array_unique($matchedGroups);

        if (count($matchedGroups) >= $minGroups) {
            return ['star' => $star, 'matched_groups' => $matchedGroups];
        }
        return false;
    }
    
    private function checkMingLuAnLu() 
    {
        $mingIdx = $this->mingGongIdx;
        if (!$this->palaceHasLu($mingIdx)) return false;
        $anHeIdx = (int)($this->palaces[$mingIdx]['an_he_index'] ?? -1);
        if ($anHeIdx >= 0 && $this->palaceHasLu($anHeIdx)) {
            return ['ming_palace' => $this->palaces[$mingIdx]['name'] ?? '', 'anhe_palace' => $this->palaces[$anHeIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkKeMingHuiLu() 
    {
        $mingIdx = $this->mingGongIdx;
        $hasKe = false;
        foreach ($this->palaces[$mingIdx]['main_stars'] ?? [] as $s) {
            if (($s['sihua'] ?? '') === '科') { $hasKe = true; break; }
        }
        if (!$hasKe) return false;
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            if ($this->palaceHasLu($idx)) return ['palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkDanFengChaoYang() 
    {
        $mingIdx = $this->mingGongIdx;
        $mingZhi = $this->palaces[$mingIdx]['zhi'] ?? '';
        if (!in_array($mingZhi, ['辰', '戌'], true)) return false;

        $sunIdx = $this->findStarPalace('太阳');
        $moonIdx = $this->findStarPalace('太阴');
        if ($sunIdx < 0 || $moonIdx < 0 || $sunIdx === $mingIdx || $moonIdx === $mingIdx) return false;

        $sunZhi = $this->palaceZhi($sunIdx);
        $moonZhi = $this->palaceZhi($moonIdx);
        if (!(($sunZhi === '辰' && $moonZhi === '戌') || ($sunZhi === '戌' && $moonZhi === '辰'))) return false;

        $sunBright = $this->getStarBrightness($sunIdx, '太阳');
        $moonBright = $this->getStarBrightness($moonIdx, '太阴');
        if (!in_array($sunBright, ['庙', '旺'], true)) return false;
        if (!in_array($moonBright, ['庙', '旺', '利'], true)) return false;

        return ['sun_at' => $sunZhi, 'moon_at' => $moonZhi];
    }

    private function checkLiangZhongHuaGai() 
    {
        $mingIdx = $this->mingGongIdx;
        $names = $this->getPalaceStarNames($mingIdx);
        if (in_array('华盖', $names, true) && (in_array('地空', $names, true) || in_array('地劫', $names, true))) {
            return ['palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkShaPoLang() 
    {
        $spl = ['七杀', '破军', '贪狼'];
        $found = array_intersect($spl, $this->getPalaceStarNames($this->mingGongIdx));
        if (empty($found)) return false;
        return ['positions' => '命中杀破狼'];
    }

    private function checkShuangLu() 
    {
        $sanfang = $this->getSanfangIndices($this->mingGongIdx);
        $luCunIdx = -1; $huaLuIdx = -1; $huaLuStar = '';
        foreach ($sanfang as $idx) {
            if ($this->palaceHasStar($idx, '禄存')) $luCunIdx = $idx;
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '禄' && ($s['type'] ?? '') !== 'small-text') {
                    $huaLuIdx = $idx; $huaLuStar = (string)($s['name'] ?? '');
                }
            }
        }
        if ($luCunIdx < 0 || $huaLuIdx < 0) return false;

        $brokenBy = null;
        foreach ([$luCunIdx, $huaLuIdx] as $idx) {
            $names = $this->getPalaceStarNames($idx);
            if (in_array('地空', $names, true) || in_array('地劫', $names, true)) $brokenBy = '空劫同宫，禄倒虚名';
            $oppIdx = ($idx + 6) % 12;
            foreach ($this->palaces[$oppIdx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌' && ($s['type'] ?? '') !== 'small-text') $brokenBy = '化忌对冲';
            }
            if (count(array_intersect(['擎羊','陀罗','火星','铃星'], $names)) >= 2) $brokenBy = '同宫煞重';
        }
        
        return [
            'type' => ($luCunIdx === $huaLuIdx) ? '同宫' : '三方相照', 
            'lu_cun_palace' => $this->palaces[$luCunIdx]['name'] ?? '',
            'hua_lu_palace' => $this->palaces[$huaLuIdx]['name'] ?? '', 
            'is_broken' => ($brokenBy !== null), 'broken_reason' => $brokenBy
        ];
    }

    private function getSanfangSihua(int $mingIdx, string $sihuaType): array 
    {
        $result = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === $sihuaType && ($s['type'] ?? '') !== 'small-text') {
                    $result[] = ['star' => (string)($s['name'] ?? ''), 'palace' => $this->palaces[$idx]['name'] ?? '', 'idx' => $idx];
                }
            }
        }
        return $result;
    }

    private function checkSanQi() 
    {
        $found = [];
        foreach (['禄', '权', '科'] as $type) {
            $r = $this->getSanfangSihua($this->mingGongIdx, $type);
            if (!empty($r)) $found[$type] = $r[0]['star'] . '（' . $r[0]['palace'] . '）';
        }
        if (count($found) !== 3) return false;
        
        $brokenReasons = [];
        foreach ($this->getSanfangIndices($this->mingGongIdx) as $idx) {
            $names = $this->getPalaceStarNames($idx);
            if (in_array('地空', $names, true)) $brokenReasons[] = '地空冲破';
            if (in_array('地劫', $names, true)) $brokenReasons[] = '地劫冲破';
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌') $brokenReasons[] = '化忌冲破';
            }
        }
        $isBroken = !empty($brokenReasons);
        return ['found' => $found, 'is_broken' => $isBroken, 'broken_reason' => $isBroken ? implode('；', $brokenReasons) : null];
    }

    private function checkYangTuoJiaJi() 
    {
        foreach ($this->palaces as $jiIdx => $palace) {
            $hasJi = false; $jiStarName = '';
            foreach ($palace['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌') { $hasJi = true; $jiStarName = (string)($s['name'] ?? ''); break; }
            }
            if (!$hasJi) continue;

            $left  = ($jiIdx - 1 + 12) % 12;
            $right = ($jiIdx + 1) % 12;
            $leftHasYang  = $this->palaceHasStar($left,  '擎羊');
            $leftHasTuo   = $this->palaceHasStar($left,  '陀罗');
            $rightHasYang = $this->palaceHasStar($right, '擎羊');
            $rightHasTuo  = $this->palaceHasStar($right, '陀罗');

            if (($leftHasYang && $rightHasTuo) || ($leftHasTuo && $rightHasYang)) {
                return ['ji_star' => $jiStarName, 'palace' => $palace['name'] ?? ''];
            }
        }
        return false;
    }

    // 核心修复3：刑囚夹印数学逻辑修正
    private function checkXingQiuJiaYin() 
    {
        $xianIdx = $this->findStarPalace('天相');
        if ($xianIdx < 0) return false;
        
        $stars = $this->getPalaceStarNames($xianIdx);
        // 廉贞天相不可能左右夹宫。真正的刑囚夹印指廉贞、天相、擎羊三者同宫。
        if (in_array('廉贞', $stars, true) && in_array('擎羊', $stars, true)) {
            return [
                'variant' => '主格',
                'desc'    => '廉贞天相与擎羊同宫',
                'palace'  => $this->palaces[$xianIdx]['name'] ?? '',
            ];
        }
        return false;
    }

    private function checkXingJiJiaYin() 
    {
        $xianIdx = $this->findStarPalace('天相');
        if ($xianIdx < 0) return false;
        
        $left = ($xianIdx - 1 + 12) % 12;
        $right = ($xianIdx + 1) % 12;
        $leftHasJi = false; $rightHasJi = false;
        
        foreach ($this->palaces[$left]['main_stars'] ?? [] as $s) {
            if (($s['sihua'] ?? '') === '忌') $leftHasJi = true;
        }
        foreach ($this->palaces[$right]['main_stars'] ?? [] as $s) {
            if (($s['sihua'] ?? '') === '忌') $rightHasJi = true;
        }
        
        $leftHasXing = $this->palaceHasStar($left, '天刑') || $this->palaceHasStar($left, '擎羊');
        $rightHasXing = $this->palaceHasStar($right, '天刑') || $this->palaceHasStar($right, '擎羊');
        
        if (($leftHasJi && $rightHasXing) || ($rightHasJi && $leftHasXing)) {
            return ['palace' => $this->palaces[$xianIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkJuFengSha() 
    {
        $mingIdx = $this->mingGongIdx;
        if (!$this->palaceHasStar($mingIdx, '巨门') || !$this->isBrightnessXian($mingIdx, '巨门')) return false;
        
        $foundSha = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            foreach ($this->shaStars as $s) {
                if ($this->palaceHasStar($idx, $s) && !in_array($s, $foundSha, true)) {
                    $foundSha[] = $s;
                }
            }
        }
        if (count($foundSha) >= 3) {
            return ['sha_stars' => $foundSha, 'palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkRiYueBingMing() 
    {
        $mingZhi = $this->getMingZhi();
        if (!in_array($mingZhi, ['丑', '未'], true)) return false;
        
        $sunIdx  = $this->findStarPalace('太阳');
        $moonIdx = $this->findStarPalace('太阴');
        if ($sunIdx < 0 || $moonIdx < 0 || $sunIdx === $this->mingGongIdx || $moonIdx === $this->mingGongIdx) return false;
        
        if ($this->palaceZhi($sunIdx) !== '辰' || $this->palaceZhi($moonIdx) !== '戌') return false;
        if ($this->getStarBrightness($sunIdx, '太阳') !== '庙' || $this->getStarBrightness($moonIdx, '太阴') !== '庙') return false;
        
        return ['ming_zhi' => $mingZhi, 'sun_palace' => $this->palaces[$sunIdx]['name'] ?? '', 'moon_palace' => $this->palaces[$moonIdx]['name'] ?? ''];
    }

    private function checkRiYueFanBei() 
    {
        $mingIdx = $this->mingGongIdx;
        $names = $this->getPalaceStarNames($mingIdx);
        if (in_array('太阳', $names, true) && $this->isBrightnessXian($mingIdx, '太阳')) {
            return ['star' => '太阳', 'palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        if (in_array('太阴', $names, true) && $this->isBrightnessXian($mingIdx, '太阴')) {
            return ['star' => '太阴', 'palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkLuChongPo() 
    {
        $luIdx = $this->findStarPalace('禄存');
        $results = [];
        if ($luIdx >= 0) {
            $opp = ($luIdx + 6) % 12;
            $localStars = $this->getPalaceStarNames($luIdx);
            $oppStars = $this->getPalaceStarNames($opp);
            foreach (['地空','地劫'] as $d) {
                if (in_array($d, $localStars, true) || in_array($d, $oppStars, true)) $results[] = "禄存被{$d}冲破";
            }
            foreach ($this->palaces[$opp]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌') $results[] = "禄存被化忌冲破";
            }
        }
        foreach ($this->palaces as $idx => $palace) {
            foreach ($palace['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') !== '禄') continue;
                $opp = ($idx + 6) % 12;
                $localStars2 = $this->getPalaceStarNames((int)$idx);
                $oppStars2 = $this->getPalaceStarNames($opp);
                foreach (['地空','地劫'] as $d) {
                    if (in_array($d, $localStars2, true) || in_array($d, $oppStars2, true)) {
                        $results[] = ($s['name'] ?? '') . "化禄被{$d}冲破";
                    }
                }
            }
        }
        return !empty($results) ? ['details' => $results] : false;
    }

    private function checkJuJiHuaYou() 
    {
        foreach ($this->palaces as $idx => $palace) {
            if (($palace['zhi'] ?? '') !== '酉') continue;
            $names = $this->getPalaceStarNames((int)$idx);
            foreach (['巨门','天机'] as $star) {
                if (in_array($star, $names, true) && $this->getStarSihua((int)$idx, $star) === '忌') {
                    return ['star' => $star, 'palace' => $palace['name'] ?? ''];
                }
            }
        }
        return false;
    }

    private function checkKeXingXunFeng() 
    {
        $mingIdx = $this->mingGongIdx;
        $keStars = $this->getSanfangSihua($mingIdx, '科');
        if (empty($keStars)) return false;
        
        foreach ($keStars as $ke) {
            if ($ke['idx'] === $mingIdx) return ['condition' => '命宫主星化科坐命'];
        }
        $sanfangAllStars = $this->getAllSanfangStars($mingIdx);
        if (isset($sanfangAllStars['文昌']) || isset($sanfangAllStars['文曲'])) {
            return ['condition' => '化科与文星同会三方'];
        }
        if (count($keStars) >= 2) return ['condition' => '多科会聚三方'];
        return false;
    }

    private function checkJieKongJiaJi() 
    {
        foreach ($this->palaces as $jiIdx => $palace) {
            $hasJi = false; $jiStarName = '';
            foreach ($palace['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌') { $hasJi = true; $jiStarName = (string)($s['name'] ?? ''); break; }
            }
            if (!$hasJi) continue;

            $left  = ($jiIdx - 1 + 12) % 12;
            $right = ($jiIdx + 1) % 12;
            $leftHasKong  = $this->palaceHasStar($left,  '地空');
            $leftHasJie   = $this->palaceHasStar($left,  '地劫');
            $rightHasKong = $this->palaceHasStar($right, '地空');
            $rightHasJie  = $this->palaceHasStar($right, '地劫');

            if (($leftHasKong && $rightHasJie) || ($leftHasJie && $rightHasKong)) {
                return ['ji_star' => $jiStarName, 'palace' => $palace['name'] ?? ''];
            }
        }
        return false;
    }

    private function checkShenMingTonggong() 
    {
        if ($this->mingGongIdx === $this->shenGongIdx) {
            return ['palace' => $this->palaces[$this->mingGongIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkSihuaInMingSanfang(array $rule) 
    {
        $star = (string)($rule['star'] ?? ''); 
        $sihua = (string)($rule['sihua'] ?? '');
        
        foreach ($this->getSanfangIndices($this->mingGongIdx) as $idx) {
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['name'] ?? '') === $star && ($s['sihua'] ?? '') === $sihua) {
                    return ['star' => $star, 'sihua' => $sihua, 'palace' => $this->palaces[$idx]['name'] ?? ''];
                }
            }
        }
        return false;
    }

    private function checkMingWuZhengYao() 
    {
        static $zhengYao = ['紫微','天机','太阳','武曲','天同','廉贞','天府','太阴','贪狼','巨门','天相','天梁','七杀','破军'];
        $mingIdx = $this->mingGongIdx;
        $mingStars = $this->getPalaceStarNames($mingIdx);
        foreach ($zhengYao as $star) {
            if (in_array($star, $mingStars, true)) return false;
        }
        
        $oppIdx = ($mingIdx + 6) % 12;
        $oppStars = $this->getPalaceStarNames($oppIdx);
        $oppValidStars = [];
        foreach ($zhengYao as $star) {
            if (in_array($star, $oppStars, true) && !$this->isBrightnessXian($oppIdx, $star)) {
                $oppValidStars[] = $star;
            }
        }

        $oppHasJi = false; 
        foreach ($this->palaces[$oppIdx]['main_stars'] ?? [] as $s) {
            if (($s['sihua'] ?? '') === '忌' && ($s['type'] ?? '') !== 'small-text') { $oppHasJi = true; break; }
        }

        $oppStrength = (!empty($oppValidStars) && !$oppHasJi && count($this->getShaInPalace($oppIdx)) < 2) ? '强' : '弱';
        return ['palace' => $this->palaces[$mingIdx]['name'] ?? '', 'opp_palace' => $this->palaces[$oppIdx]['name'] ?? '', 'opp_strength' => $oppStrength];
    }

    private function checkMingzhuChuhan() 
    {
        $mingIdx = $this->mingGongIdx;
        $mingZhi = $this->getMingZhi();
        if (!$this->palaceHasStar($mingIdx, '天同')) return false;
        if (!in_array($mingZhi, ['亥', '子'], true)) return false;
        
        $oppIdx = ($mingIdx + 6) % 12;
        if ($mingZhi === '亥') {
            if (!$this->palaceHasStar($oppIdx, '太阴') || $this->getStarBrightness($oppIdx, '太阴') !== '庙') return false;
            return ['palace' => $this->palaces[$mingIdx]['name'] ?? '', 'zhi' => '亥'];
        }
        if (!$this->palaceHasStar($oppIdx, '太阳') || $this->getStarBrightness($oppIdx, '太阳') !== '庙') return false;
        return ['palace' => $this->palaces[$mingIdx]['name'] ?? '', 'zhi' => '子'];
    }

    private function checkLuCunZuoMing() 
    {
        if (!$this->palaceHasStar($this->mingGongIdx, '禄存')) return false;
        return ['palace' => $this->palaces[$this->mingGongIdx]['name'] ?? '', 'zhi' => $this->getMingZhi()];
    }

    private function checkWenxingGongMing() 
    {
        $mingIdx = $this->mingGongIdx;
        $foundStars = []; $brokenStars = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            if ($idx === $mingIdx) continue;
            foreach (['文昌', '文曲'] as $star) {
                if ($this->palaceHasStar($idx, $star)) {
                    $foundStars[$star] = (string)($this->palaces[$idx]['name'] ?? '');
                    if ($this->getStarSihua($idx, $star) === '忌') $brokenStars[$star] = (string)($this->palaces[$idx]['name'] ?? '');
                }
            }
        }
        if (count($foundStars) < 2) return false;
        
        $isBroken = !empty($brokenStars);
        $brokenDesc = null;
        if ($isBroken) {
            $parts = [];
            foreach ($brokenStars as $star => $palace) $parts[] = "{$star}化忌在{$palace}";
            $brokenDesc = implode('，', $parts) . '，文书易生错漏';
        }
        return ['found_stars' => $foundStars, 'is_broken' => $isBroken, 'broken_reason' => $brokenDesc];
    }

    private function checkLuMaJiaochi() 
    {
        $sanfang = $this->getSanfangIndices($this->mingGongIdx);
        $maIdx = -1; $luIdx = -1; $luType = ''; $luStar = '';
        
        foreach ($sanfang as $idx) {
            if ($this->palaceHasStar($idx, '天马')) $maIdx = $idx;
            if ($this->palaceHasStar($idx, '禄存')) { $luIdx = $idx; $luType = '禄存'; $luStar = '禄存'; }
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '禄' && ($s['type'] ?? '') !== 'small-text') {
                    $luIdx = $idx; $luType = '化禄'; $luStar = (string)($s['name'] ?? '');
                }
            }
        }
        if ($maIdx < 0 || $luIdx < 0) return false;
        
        $brokenReason = null;
        foreach ([$maIdx, $luIdx] as $checkIdx) {
            foreach ($this->palaces[$checkIdx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌' && ($s['type'] ?? '') !== 'small-text') { $brokenReason = ($s['name'] ?? '').'化忌同宫'; break 2; }
            }
            $opp = ($checkIdx + 6) % 12;
            foreach ($this->palaces[$opp]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌' && ($s['type'] ?? '') !== 'small-text') { $brokenReason = ($s['name'] ?? '').'化忌对宫冲'; break 2; }
            }
        }
        return ['lu_type' => $luType, 'is_broken' => ($brokenReason !== null), 'broken_reason' => $brokenReason];
    }

    private function checkShaStarXianMing() 
    {
        $mingIdx = $this->mingGongIdx;
        foreach ($this->shaStars as $star) {
            if ($this->palaceHasStar($mingIdx, $star) && $this->isBrightnessXian($mingIdx, $star)) {
                return ['star' => $star, 'palace' => $this->palaces[$mingIdx]['name'] ?? ''];
            }
        }
        return false;
    }

    private function checkMaLuoKongWang() 
    {
        $mingIdx = $this->mingGongIdx;
        if (!$this->palaceHasStar($mingIdx, '天马')) return false;
        $mingStarNames = $this->getPalaceStarNames($mingIdx);
        if (in_array('地空', $mingStarNames, true) || in_array('地劫', $mingStarNames, true)) {
            return ['palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        }
        return false;
    }

    private function checkLuShangMaiShi() 
    {
        $mingIdx = $this->mingGongIdx;
        if (!($this->palaceHasStar($mingIdx, '廉贞') && $this->palaceHasStar($mingIdx, '七杀'))) return false;
        if (!in_array($this->getMingZhi(), ['丑', '未'], true)) return false;
        
        $shaFound = []; $jiFound = [];
        foreach ($this->getSanfangIndices($mingIdx) as $idx) {
            $names = $this->getPalaceStarNames($idx);
            if (in_array('擎羊', $names, true)) $shaFound[] = '擎羊';
            if (in_array('陀罗', $names, true)) $shaFound[] = '陀罗';
            foreach ($this->palaces[$idx]['main_stars'] ?? [] as $s) {
                if (($s['sihua'] ?? '') === '忌' && ($s['type'] ?? '') !== 'small-text') {
                    $jiFound[] = ($s['name'] ?? '') . '化忌在' . ($this->palaces[$idx]['name'] ?? '');
                }
            }
        }
        if (!empty($shaFound) || !empty($jiFound)) return ['palace' => $this->palaces[$mingIdx]['name'] ?? ''];
        return false;
    }
}