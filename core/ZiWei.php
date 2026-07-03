<?php
/**
 * 紫微斗数排盘引擎 - 强类型与多流派拓展版
 * 整合十四主星、辅佐煞曜、神煞、四化推演及自动格局扫描
 */
class ZiWei
{
    private array $input;
    private array $palaces = [];
    private int $mingGongIdx = 0;
    private int $shenGongIdx = 0;
    private int $wuXingJu = 2;
    private string $mingZhu = '';
    private string $shenZhu = '';
    private int $ziweiStarIdx = 0;

    private ?int $laiYinGongIdx = null;
    private string $laiYinGongName = '';
    private string $laiYinGongGan = '';

    private const ZHI_MAP = [
        '寅'=>0,'卯'=>1,'辰'=>2,'巳'=>3,'午'=>4,'未'=>5,
        '申'=>6,'酉'=>7,'戌'=>8,'亥'=>9,'子'=>10,'丑'=>11
    ];
    
    private const ZHI_ARRAY = [
        '寅','卯','辰','巳','午','未',
        '申','酉','戌','亥','子','丑'
    ];
    
    private const STD_ZHI_MAP = [
        '子'=>0,'丑'=>1,'寅'=>2,'卯'=>3,'辰'=>4,'巳'=>5,
        '午'=>6,'未'=>7,'申'=>8,'酉'=>9,'戌'=>10,'亥'=>11
    ];

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * 核心计算主编排流程
     */
    public function calculate(): array
    {
        $this->initPalaces();
        $this->arrangeMingShen();
        $this->calculateWuXingJu();
        $this->setGongNames();
        $this->arrangeMajorStars();
        $this->arrangeMinorStars();
        $this->arrangeMiscStars();
        $this->arrangeShenSha();
        $this->arrangeChangSheng();
        
        // 核心生年四化推演
        $this->applySiHua();
        
        // 预留运势动态四化（大限/流年）推演入口
        $this->applyDynamicSiHua();

        $this->calculateDaXian();
        $this->calculateMingShenZhu();
        $this->calculateLaiYinGong();
        $this->addAnHeToPalaces();
        $this->calculateAges();

        // 引入严谨的自动格局扫描
        $scanner = new GeJuScanner(
            $this->palaces,
            $this->mingGongIdx,
            $this->shenGongIdx,
            $this->input['year_gan'] ?? '甲',
            $this->input['year_zhi'] ?? '子'
        );
        $geJuList = $scanner->scan();

        return [
            'palaces' => $this->palaces,
            'ge_ju' => $geJuList,
            'info' => [
                'bureau' => $this->getBureauName($this->wuXingJu),
                'ming_zhu' => $this->mingZhu,
                'shen_zhu' => $this->shenZhu,
                'ming_gong' => '命宫：' . (self::ZHI_ARRAY[$this->mingGongIdx] ?? '') . '宫',
                'shen_gong' => '身宫：' . (self::ZHI_ARRAY[$this->shenGongIdx] ?? '') . '宫',
                'ziwei_pos' => '紫微星：' . ($this->palaces[$this->ziweiStarIdx]['zhi'] ?? '') . '宫',
                'ming_gong_index' => $this->mingGongIdx,
                'shen_gong_index' => $this->shenGongIdx,
                'lai_yin' => [
                    'index' => $this->laiYinGongIdx,
                    'gong'  => $this->laiYinGongName,
                    'zhi'   => ($this->laiYinGongIdx !== null) ? (self::ZHI_ARRAY[$this->laiYinGongIdx] ?? '') : '',
                    'gan'   => $this->laiYinGongGan,
                ]
            ]
        ];
    }

    /**
     * 初始化十二宫基础盘面（五虎遁起寅首）
     */
    private function initPalaces(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $tigerHeads = [
            '甲'=>'丙','己'=>'丙','乙'=>'戊','庚'=>'戊',
            '丙'=>'庚','辛'=>'庚','丁'=>'壬','壬'=>'壬',
            '戊'=>'甲','癸'=>'甲'
        ];
        $startGan = $tigerHeads[$yearGan] ?? '丙';
        $ganOrder = ['甲','乙','丙','丁','戊','己','庚','辛','壬','癸'];
        $startGanIdx = (int)array_search($startGan, $ganOrder, true);

        for ($i = 0; $i < 12; $i++) {
            $ganIdx = ($startGanIdx + $i) % 10;
            $this->palaces[$i] = [
                'index' => $i,
                'zhi' => self::ZHI_ARRAY[$i],
                'gan' => $ganOrder[$ganIdx],
                'name' => '',
                'is_ming' => false,
                'is_shen' => false,
                'daxian' => '',
                'main_stars' => [],
                'boshi' => '',
                'suijian' => '',
                'jiangxing' => '',
                'an_he_index' => -1,
                'an_he_gong' => ''
            ];
        }
    }

    /**
     * 推演来因宫
     */
    private function calculateLaiYinGong(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $zhi = ZiWeiData::$LAI_YIN_POSITION[$yearGan] ?? null;
        if (!$zhi) {
            return;
        }
        $zhiIndex = self::ZHI_MAP[$zhi] ?? null;
        if ($zhiIndex === null) {
            return;
        }
        $this->laiYinGongIdx = $zhiIndex;
        $this->laiYinGongName = $this->palaces[$zhiIndex]['name'] ?? '';
        $this->laiYinGongGan = $this->palaces[$zhiIndex]['gan'] ?? '';
    }

    /**
     * 映射六合暗合宫关系
     */
    private function addAnHeToPalaces(): void
    {
        foreach ($this->palaces as &$palace) {
            $idx = $palace['index'];
            $anHeIdx = ZiWeiData::$AN_HE_MAP[$idx] ?? null;
            if ($anHeIdx !== null) {
                $palace['an_he_index'] = $anHeIdx;
                $palace['an_he_gong'] = $this->palaces[$anHeIdx]['name'] ?? '';
            }
        }
    }

    /**
     * 安命宫与身宫
     */
    private function arrangeMingShen(): void
    {
        $month = (int)($this->input['lunar_month'] ?? 1);
        $hourZhi = $this->input['hour_zhi'] ?? '子';
        $hourStep = self::STD_ZHI_MAP[$hourZhi] ?? 0;

        // 命宫顺月逆时，身宫顺月顺时
        $mingIdx = ($month - 1) - $hourStep;
        $this->mingGongIdx = ($mingIdx % 12 + 12) % 12;

        $shenIdx = ($month - 1) + $hourStep;
        $this->shenGongIdx = ($shenIdx % 12 + 12) % 12;

        $this->palaces[$this->mingGongIdx]['is_ming'] = true;
        $this->palaces[$this->shenGongIdx]['is_shen'] = true;
    }

    /**
     * 纳音推演五行局
     */
    private function calculateWuXingJu(): void
    {
        $mingGan = $this->palaces[$this->mingGongIdx]['gan'] ?? '甲';
        $mingZhi = $this->palaces[$this->mingGongIdx]['zhi'] ?? '子';
        $key = $mingGan . $mingZhi;
        $naYin = ZiWeiData::$NA_YIN[$key] ?? '';

        if (mb_strpos($naYin, '金') !== false) {
            $this->wuXingJu = 4;
        } elseif (mb_strpos($naYin, '木') !== false) {
            $this->wuXingJu = 3;
        } elseif (mb_strpos($naYin, '水') !== false) {
            $this->wuXingJu = 2;
        } elseif (mb_strpos($naYin, '土') !== false) {
            $this->wuXingJu = 5;
        } elseif (mb_strpos($naYin, '火') !== false) {
            $this->wuXingJu = 6;
        } else {
            $this->wuXingJu = 2;
        }
    }

    /**
     * 依次排定十二宫位名称
     */
    private function setGongNames(): void
    {
        $names = ZiWeiData::$SHI_ER_GONG;
        $this->palaces[$this->mingGongIdx]['name'] = '命宫';
        for ($i = 0; $i < 11; $i++) {
            $idx = ($this->mingGongIdx - 1 - $i + 12) % 12;
            $this->palaces[$idx]['name'] = $names[$i] . '宫';
        }
    }

    /**
     * 安紫微天府十四主星
     */
    private function arrangeMajorStars(): void
    {
        $day = (int)($this->input['lunar_day'] ?? 1);
        $bureau = $this->wuXingJu;

        $this->ziweiStarIdx = $this->getZiWeiLocation($day, $bureau);
        $tianfuIndex = (12 - $this->ziweiStarIdx) % 12;

        $zwOffsets = [0=>'紫微',11=>'天机',9=>'太阳',8=>'武曲',7=>'天同',4=>'廉贞'];
        foreach ($zwOffsets as $offset => $star) {
            $this->addStar(($this->ziweiStarIdx + $offset) % 12, $star, 'major');
        }

        $tfOffsets = [0=>'天府',1=>'太阴',2=>'贪狼',3=>'巨门',4=>'天相',5=>'天梁',6=>'七杀',10=>'破军'];
        foreach ($tfOffsets as $offset => $star) {
            $this->addStar(($tianfuIndex + $offset) % 12, $star, 'major');
        }
    }

    /**
     * 安辅佐煞曜等核心吉凶星
     */
    private function arrangeMinorStars(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $yearZhi = $this->input['year_zhi'] ?? '子';
        $month = (int)($this->input['lunar_month'] ?? 1);
        $hourZhi = $this->input['hour_zhi'] ?? '子';
        $hStep = self::STD_ZHI_MAP[$hourZhi] ?? 0;

        $luZhi = ZiWeiData::$LU_CUN[$yearGan] ?? null;
        if ($luZhi !== null && isset(self::ZHI_MAP[$luZhi])) {
            $luIdx = self::ZHI_MAP[$luZhi];
            $this->addStar($luIdx, '禄存', 'ji');
            $this->addStar(($luIdx + 1) % 12, '擎羊', 'sha');
            $this->addStar(($luIdx - 1 + 12) % 12, '陀罗', 'sha');
        }

        $changIdx = (8 - $hStep + 12) % 12;
        $quIdx = (2 + $hStep) % 12;
        $this->addStar($changIdx, '文昌', 'ji');
        $this->addStar($quIdx, '文曲', 'ji');

        $zuoIdx = (2 + ($month - 1)) % 12;
        $youIdx = (8 - ($month - 1) + 12) % 12;
        $this->addStar($zuoIdx, '左辅', 'ji');
        $this->addStar($youIdx, '右弼', 'ji');

        if (isset(ZiWeiData::$KUI_YUE[$yearGan])) {
            $kuiZhi = ZiWeiData::$KUI_YUE[$yearGan][0];
            $yueZhi = ZiWeiData::$KUI_YUE[$yearGan][1];
            $this->addStar(self::ZHI_MAP[$kuiZhi] ?? 0, '天魁', 'ji');
            $this->addStar(self::ZHI_MAP[$yueZhi] ?? 0, '天钺', 'ji');
        }

        $kongIdx = (9 - $hStep + 12) % 12;
        $jieIdx = (9 + $hStep) % 12;
        $this->addStar($kongIdx, '地空', 'sha');
        $this->addStar($jieIdx, '地劫', 'sha');

        $huoStart = 0; $lingStart = 0;
        if (in_array($yearZhi, ['寅','午','戌'], true)) { $huoStart = 11; $lingStart = 1; }
        elseif (in_array($yearZhi, ['申','子','辰'], true)) { $huoStart = 0; $lingStart = 8; }
        elseif (in_array($yearZhi, ['巳','酉','丑'], true)) { $huoStart = 1; $lingStart = 8; }
        elseif (in_array($yearZhi, ['亥','卯','未'], true)) { $huoStart = 7; $lingStart = 8; }
        
        $this->addStar(($huoStart + $hStep) % 12, '火星', 'sha');
        $this->addStar(($lingStart + $hStep) % 12, '铃星', 'sha');

        $maZhi = ZiWeiData::$TIAN_MA[$yearZhi] ?? null;
        if ($maZhi !== null && isset(self::ZHI_MAP[$maZhi])) {
            $this->addStar(self::ZHI_MAP[$maZhi], '天马', 'ji');
        }
    }

    /**
     * 安各类杂曜与流曜
     */
    private function arrangeMiscStars(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $yearZhi = $this->input['year_zhi'] ?? '子';
        $month = (int)($this->input['lunar_month'] ?? 1);
        $day = (int)($this->input['lunar_day'] ?? 1);
        $hStep = self::STD_ZHI_MAP[$this->input['hour_zhi'] ?? '子'] ?? 0;
        $yIdx = self::STD_ZHI_MAP[$yearZhi] ?? 0;

        if (isset(ZiWeiData::$TIAN_GUAN[$yearGan])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_GUAN[$yearGan]] ?? 0, '天官', 'luck'); }
        if (isset(ZiWeiData::$TIAN_FU[$yearGan])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_FU[$yearGan]] ?? 0, '天福', 'luck'); }
        if (isset(ZiWeiData::$TIAN_CHU[$yearGan])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_CHU[$yearGan]] ?? 0, '天厨', 'luck'); }

        if (isset(ZiWeiData::$TIAN_DE[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_DE[$yearZhi]] ?? 0, '天德', 'luck'); }
        if (isset(ZiWeiData::$YUE_DE[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$YUE_DE[$yearZhi]] ?? 0, '月德', 'luck'); }
        if (isset(ZiWeiData::$FEI_LIAN[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$FEI_LIAN[$yearZhi]] ?? 0, '蜚廉', 'bad'); }
        if (isset(ZiWeiData::$PO_SUI[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$PO_SUI[$yearZhi]] ?? 0, '破碎', 'bad'); }
        if (isset(ZiWeiData::$NIAN_JIE[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$NIAN_JIE[$yearZhi]] ?? 0, '年解', 'luck'); }
        if (isset(ZiWeiData::$LONG_DE[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$LONG_DE[$yearZhi]] ?? 0, '龙德', 'luck'); }
        if (isset(ZiWeiData::$JIE_SHA[$yearZhi])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$JIE_SHA[$yearZhi]] ?? 0, '劫煞', 'bad'); }

        if (isset(ZiWeiData::$GU_GUA[$yearZhi])) {
            $this->addStar(self::ZHI_MAP[ZiWeiData::$GU_GUA[$yearZhi][0]] ?? 0, '孤辰', 'bad');
            $this->addStar(self::ZHI_MAP[ZiWeiData::$GU_GUA[$yearZhi][1]] ?? 0, '寡宿', 'bad');
        }

        $nianDaHaoMap = ['子'=>'未','丑'=>'午','寅'=>'酉','卯'=>'申','辰'=>'亥','巳'=>'戌','午'=>'丑','未'=>'子','申'=>'卯','酉'=>'寅','戌'=>'巳','亥'=>'辰'];
        if (isset($nianDaHaoMap[$yearZhi])) { $this->addStar(self::ZHI_MAP[$nianDaHaoMap[$yearZhi]] ?? 0, '大耗', 'bad'); }

        $huagaiMap = ['子'=>'辰','辰'=>'辰','申'=>'辰','丑'=>'丑','巳'=>'丑','酉'=>'丑','寅'=>'戌','午'=>'戌','戌'=>'戌','卯'=>'未','未'=>'未','亥'=>'未'];
        if (isset($huagaiMap[$yearZhi])) { $this->addStar(self::ZHI_MAP[$huagaiMap[$yearZhi]] ?? 0, '华盖', 'luck'); }

        $xianchiMap = ['子'=>'酉','辰'=>'酉','申'=>'酉','丑'=>'午','巳'=>'午','酉'=>'午','寅'=>'卯','午'=>'卯','戌'=>'卯','卯'=>'子','未'=>'子','亥'=>'子'];
        if (isset($xianchiMap[$yearZhi])) { $this->addStar(self::ZHI_MAP[$xianchiMap[$yearZhi]] ?? 0, '咸池', 'peach'); }

        $this->addStar((2 + $yIdx) % 12, '龙池', 'luck');
        $this->addStar((8 - $yIdx + 12) % 12, '凤阁', 'luck');
        $this->addStar((4 - $yIdx + 12) % 12, '天哭', 'bad');
        $this->addStar((4 + $yIdx) % 12, '天虚', 'bad');
        $this->addStar((1 - $yIdx + 12) % 12, '红鸾', 'peach');
        $this->addStar((1 - $yIdx + 6 + 12) % 12, '天喜', 'peach');
        $this->addStar(($yIdx + 11) % 12, '天空', 'bad');

        $yangGans = ['甲','丙','戊','庚','壬'];
        $yangZhis = ['子','寅','辰','午','申','戌'];
        $isYangGan = in_array($yearGan, $yangGans, true);
        
        if (isset(ZiWeiData::$JIE_KONG[$yearGan])) {
            foreach (ZiWeiData::$JIE_KONG[$yearGan] as $zhi) {
                $isYangZhi = in_array($zhi, $yangZhis, true);
                $isZhengKong = ($isYangGan && $isYangZhi) || (!$isYangGan && !$isYangZhi);
                $name = $isZhengKong ? '截空' : '副截';
                $this->addStar(self::ZHI_MAP[$zhi] ?? 0, $name, 'bad');
            }
        }

        $stdZhiOrder = ['子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'];
        $ganOrder = ['甲','乙','丙','丁','戊','己','庚','辛','壬','癸'];
        $ganIndex = (int)array_search($yearGan, $ganOrder, true);
        $zhiIndex = (int)array_search($yearZhi, $stdZhiOrder, true);
        
        $kong1Idx = ($zhiIndex - $ganIndex + 10 + 12) % 12;
        $kong2Idx = ($zhiIndex - $ganIndex + 11 + 12) % 12;
        if ($isYangGan) { $xunKongIndex = $kong1Idx; $fuXunIndex = $kong2Idx; }
        else { $xunKongIndex = $kong2Idx; $fuXunIndex = $kong1Idx; }
        
        $this->addStar(self::ZHI_MAP[$stdZhiOrder[$xunKongIndex]] ?? 0, '旬空', 'bad');
        $this->addStar(self::ZHI_MAP[$stdZhiOrder[$fuXunIndex]] ?? 0, '副旬', 'bad');

        if (isset(ZiWeiData::$MONTH_DA_HAO[$month])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$MONTH_DA_HAO[$month]] ?? 0, '月耗', 'bad'); }
        if (isset(ZiWeiData::$TIAN_WU[$month])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_WU[$month]] ?? 0, '天巫', 'luck'); }
        if (isset(ZiWeiData::$TIAN_YUE[$month])) { $this->addStar(self::ZHI_MAP[ZiWeiData::$TIAN_YUE[$month]] ?? 0, '天月', 'bad'); }

        $tianxingIdx = (7 + $month - 1) % 12;
        $this->addStar(($tianxingIdx + 12) % 12, '天刑', 'bad');

        $tianYaoIndex = (11 + $month - 1) % 12;
        $this->addStar($tianYaoIndex, '天姚', 'peach');

        $yinshaMap = [1=>'寅',2=>'子',3=>'戌',4=>'申',5=>'午',6=>'辰',7=>'寅',8=>'子',9=>'戌',10=>'申',11=>'午',12=>'辰'];
        if (isset($yinshaMap[$month])) { $this->addStar(self::ZHI_MAP[$yinshaMap[$month]] ?? 0, '阴煞', 'bad'); }

        $yuejieMap = [1=>'申',2=>'申',3=>'戌',4=>'戌',5=>'子',6=>'子',7=>'寅',8=>'寅',9=>'辰',10=>'辰',11=>'午',12=>'午'];
        if (isset($yuejieMap[$month])) { $this->addStar(self::ZHI_MAP[$yuejieMap[$month]] ?? 0, '解神', 'luck'); }

        $zuoIdx = (2 + ($month - 1)) % 12;
        $youIdx = (8 - ($month - 1) + 12) % 12;
        $this->addStar(($zuoIdx + $day - 1) % 12, '三台', 'luck');
        $this->addStar(($youIdx - ($day - 1) + 120) % 12, '八座', 'luck');

        $changIdx = (8 - $hStep + 12) % 12;
        $quIdx = (2 + $hStep) % 12;
        $this->addStar(($changIdx + $day - 2 + 120) % 12, '恩光', 'luck');
        $this->addStar(($quIdx + $day - 2 + 120) % 12, '天贵', 'luck');

        $hIdx = self::STD_ZHI_MAP[$this->input['hour_zhi'] ?? '子'] ?? 0;
        $this->addStar((4 + $hIdx) % 12, '台辅', 'luck');
        $this->addStar((0 + $hIdx) % 12, '封诰', 'luck');

        $this->addStar(($this->mingGongIdx + $yIdx) % 12, '天才', 'luck');
        $this->addStar(($this->shenGongIdx + $yIdx) % 12, '天寿', 'luck');
        $this->addStar(($this->mingGongIdx - 7 + 12) % 12, '天伤', 'bad');
        $this->addStar(($this->mingGongIdx - 5 + 12) % 12, '天使', 'bad');
    }

    /**
     * 排定流年诸神煞
     */
    private function arrangeShenSha(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $yearZhi = $this->input['year_zhi'] ?? '子';
        $sex = (int)($this->input['sex'] ?? 1);
        $isYangGan = in_array($yearGan, ['甲','丙','戊','庚','壬'], true);
        $isClockwise = ($sex === ($isYangGan ? 1 : 0));

        $luZhi = ZiWeiData::$LU_CUN[$yearGan] ?? null;
        if ($luZhi !== null && isset(self::ZHI_MAP[$luZhi])) {
            $luIdx = self::ZHI_MAP[$luZhi];
            foreach (ZiWeiData::$BO_SHI_12 as $i => $name) {
                $idx = $isClockwise ? ($luIdx + $i) % 12 : ($luIdx - $i + 12) % 12;
                $this->palaces[$idx]['boshi'] = $name;
            }
        }

        $yIdx = self::ZHI_MAP[$yearZhi] ?? 0;
        foreach (ZiWeiData::$SUI_JIAN_12 as $i => $name) {
            $idx = ($yIdx + $i) % 12;
            $this->palaces[$idx]['suijian'] = $name;
        }

        $jiangStart = 0;
        if (in_array($yearZhi, ['寅','午','戌'], true)) { $jiangStart = 4; }
        elseif (in_array($yearZhi, ['申','子','辰'], true)) { $jiangStart = 10; }
        elseif (in_array($yearZhi, ['巳','酉','丑'], true)) { $jiangStart = 7; }
        elseif (in_array($yearZhi, ['亥','卯','未'], true)) { $jiangStart = 1; }

        foreach (ZiWeiData::$JIANG_XING_12 as $i => $name) {
            $idx = ($jiangStart + $i) % 12;
            $this->palaces[$idx]['jiangxing'] = $name;
        }
    }

    /**
     * 安长生十二神
     */
    private function arrangeChangSheng(): void
    {
        $bureau = $this->wuXingJu;
        $gender = (int)($this->input['sex'] ?? 1);
        $yearGan = $this->input['year_gan'] ?? '甲';
        $isYangGan = in_array($yearGan, ['甲','丙','戊','庚','壬'], true);
        $isClockwise = (($gender === 1 && $isYangGan) || ($gender === 0 && !$isYangGan));

        $changShengMap = [2=>6, 3=>9, 4=>3, 5=>6, 6=>0];
        $startIdx = $changShengMap[$bureau] ?? 6;

        foreach (ZiWeiData::$CHANG_SHENG_12 as $i => $starName) {
            $idx = $isClockwise ? ($startIdx + $i) % 12 : ($startIdx - $i + 12) % 12;
            $this->addStar($idx, $starName, 'small-text');
        }
    }

    /**
     * 推演生年四化（禄权科忌）
     */
    private function applySiHua(): void
    {
        $yearGan = $this->input['year_gan'] ?? '甲';
        $sihua = ZiWeiData::$SI_HUA[$yearGan] ?? [];

        foreach ($sihua as $entry) {
            $starName = mb_substr($entry, 0, -1);
            $type = mb_substr($entry, -1);
            
            foreach ($this->palaces as &$palace) {
                foreach ($palace['main_stars'] as &$star) {
                    if ($star['name'] === $starName) {
                        $star['sihua'] = $type;
                        if (in_array($star['type'], ['minor','luck','bad'], true)) {
                            $star['style'] = 'border: 1px solid #f00;';
                        }
                    }
                }
            }
        }
    }

    /**
     * 预留大限与流年等运势动态四化的底层推演接口
     * 允许系统依据请求动态注入特定天干，计算飞化情况
     */
    private function applyDynamicSiHua(): void
    {
        $dynamicGan = $this->input['dynamic_gan'] ?? '';
        if ($dynamicGan === '' || !isset(ZiWeiData::$SI_HUA[$dynamicGan])) {
            return;
        }
        
        $dynamicSihua = ZiWeiData::$SI_HUA[$dynamicGan];
        foreach ($dynamicSihua as $entry) {
            $starName = mb_substr($entry, 0, -1);
            $type = mb_substr($entry, -1);
            
            foreach ($this->palaces as &$palace) {
                foreach ($palace['main_stars'] as &$star) {
                    if ($star['name'] === $starName) {
                        // 记录运势飞星标记，保留原生生年四化的同时注入动态层
                        $star['dynamic_sihua'] = $type;
                    }
                }
            }
        }
    }

    /**
     * 计算大限区间起止
     */
    private function calculateDaXian(): void
    {
        $sex = (int)($this->input['sex'] ?? 1);
        $yearGan = $this->input['year_gan'] ?? '甲';
        $isYangGan = in_array($yearGan, ['甲','丙','戊','庚','壬'], true);
        $isClockwise = ($sex === 1 && $isYangGan) || ($sex === 0 && !$isYangGan);
        $startAge = $this->wuXingJu;

        for ($i = 0; $i < 12; $i++) {
            $offset = $isClockwise ? $i : -$i;
            $idx = ($this->mingGongIdx + $offset + 12) % 12;
            $s = $startAge + $i * 10;
            $e = $s + 9;
            $this->palaces[$idx]['daxian'] = "{$s}-{$e}";
        }
    }

    /**
     * 推演命主与身主
     */
    private function calculateMingShenZhu(): void
    {
        $yearZhi = $this->input['year_zhi'] ?? '子';
        $mingZhi = $this->palaces[$this->mingGongIdx]['zhi'] ?? '子';
        $this->mingZhu = '命主：' . (ZiWeiData::$MING_ZHU[$mingZhi] ?? '待定');
        $this->shenZhu = '身主：' . (ZiWeiData::$SHEN_ZHU[$yearZhi] ?? '待定');
    }

    /**
     * 安全地向指定宫位追加星曜
     */
    private function addStar(int $idx, string $name, string $type): void
    {
        $idx = ($idx % 12 + 12) % 12;
        $zhi = $this->palaces[$idx]['zhi'] ?? '子';
        $pos = self::STD_ZHI_MAP[$zhi] ?? 0;

        $brightness = '-';
        if ($type === 'major' && isset(ZiWeiData::$ZHU_XING_GUAN_XI[$name])) {
            $brightness = ZiWeiData::$ZHU_XING_GUAN_XI[$name][$pos] ?? '-';
        } elseif (isset(ZiWeiData::$MINOR_STAR_BRIGHTNESS[$name])) {
            $brightness = ZiWeiData::$MINOR_STAR_BRIGHTNESS[$name][$pos] ?? '-';
        }

        $this->palaces[$idx]['main_stars'][] = [
            'name' => $name,
            'raw_name' => $name,
            'type' => $type,
            'brightness' => $brightness,
            'sihua' => '',
            'dynamic_sihua' => '', // 动态四化兜底字段
            'style' => ''
        ];
    }

    /**
     * 获取五行局显示名称
     */
    private function getBureauName(int $num): string
    {
        $names = [2=>'水二局',3=>'木三局',4=>'金四局',5=>'土五局',6=>'火六局'];
        return $names[$num] ?? $num . '局';
    }

    /**
     * 查算紫微星所落宫位
     */
    private function getZiWeiLocation(int $day, int $bureau): int
    {
        $maps = [
            2 => [1=>11,2=>0,3=>0,4=>1,5=>1,6=>2,7=>2,8=>3,9=>3,10=>4,11=>4,12=>5,13=>5,14=>6,15=>6,16=>7,17=>7,18=>8,19=>8,20=>9,21=>9,22=>10,23=>10,24=>11,25=>11,26=>0,27=>0,28=>1,29=>1,30=>2],
            3 => [1=>2,2=>11,3=>0,4=>3,5=>0,6=>1,7=>4,8=>1,9=>2,10=>5,11=>2,12=>3,13=>6,14=>3,15=>4,16=>7,17=>4,18=>5,19=>8,20=>5,21=>6,22=>9,23=>6,24=>7,25=>10,26=>7,27=>8,28=>11,29=>8,30=>9],
            4 => [1=>9,2=>2,3=>11,4=>0,5=>10,6=>3,7=>0,8=>1,9=>11,10=>4,11=>1,12=>2,13=>0,14=>5,15=>2,16=>3,17=>1,18=>6,19=>3,20=>4,21=>2,22=>7,23=>4,24=>5,25=>3,26=>8,27=>5,28=>6,29=>4,30=>9],
            5 => [1=>4,2=>9,3=>2,4=>11,5=>0,6=>5,7=>10,8=>3,9=>0,10=>1,11=>6,12=>11,13=>4,14=>5,15=>2,16=>7,17=>0,18=>5,19=>2,20=>3,21=>8,22=>1,23=>6,24=>3,25=>4,26=>9,27=>2,28=>7,29=>4,30=>5],
            6 => [1=>7,2=>4,3=>9,4=>2,5=>11,6=>0,7=>8,8=>5,9=>10,10=>3,11=>0,12=>1,13=>9,14=>6,15=>11,16=>4,17=>1,18=>2,19=>10,20=>7,21=>0,22=>5,23=>2,24=>3,25=>11,26=>8,27=>1,28=>6,29=>3,30=>4],
        ];
        return $maps[$bureau][$day] ?? 0;
    }

    /**
     * 推演小限与流年对应岁数
     */
    private function calculateAges(): void
    {
        $yearZhi = $this->input['year_zhi'] ?? '子';
        $sex = (int)($this->input['sex'] ?? 1);

        $xiaoStart = '辰';
        if (in_array($yearZhi, ['寅','午','戌'], true)) { $xiaoStart = '辰'; }
        elseif (in_array($yearZhi, ['申','子','辰'], true)) { $xiaoStart = '戌'; }
        elseif (in_array($yearZhi, ['巳','酉','丑'], true)) { $xiaoStart = '未'; }
        elseif (in_array($yearZhi, ['亥','卯','未'], true)) { $xiaoStart = '丑'; }

        $xiaoIdx = self::ZHI_MAP[$xiaoStart] ?? 2;
        $isClockwise = ($sex === 1);

        $xiaoArr = array_fill(0, 12, []);
        for ($age = 1; $age <= 84; $age++) {
            $step = ($age - 1) % 12;
            $idx = $isClockwise ? ($xiaoIdx + $step) % 12 : ($xiaoIdx - $step + 12) % 12;
            $xiaoArr[$idx][] = $age;
        }

        $liuStartIdx = self::ZHI_MAP[$yearZhi] ?? 0;
        $liuArr = array_fill(0, 12, []);
        for ($age = 1; $age <= 84; $age++) {
            $step = ($age - 1) % 12;
            $idx = ($liuStartIdx + $step) % 12;
            $liuArr[$idx][] = $age;
        }

        foreach ($this->palaces as $i => &$palace) {
            $palace['ages'] = implode(' ', $xiaoArr[$i]);
            $palace['liu_nian_ages'] = implode(' ', $liuArr[$i]);
        }
    }
}