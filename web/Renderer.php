<?php
/**
 * 命盘 HTML 强类型安全渲染层
 * 负责宫位、中宫、格局面板的无缝混编与防 XSS 呈现
 */

/**
 * 渲染单个星曜
 */
function renderStar(array $star): void
{
    $type = htmlspecialchars($star['type'] ?? 'minor', ENT_QUOTES, 'UTF-8');
    $className = 'star ' . $type;
    $style = htmlspecialchars($star['style'] ?? '', ENT_QUOTES, 'UTF-8');
    $sihuaClass = $star['sihua'] ?? '';
    
    if ($sihuaClass !== '') {
        $className .= ' sihua-' . htmlspecialchars($sihuaClass, ENT_QUOTES, 'UTF-8');
    }
    
    $name = (string)($star['name'] ?? '');
    $len = mb_strlen($name, 'UTF-8');
?>
    <span class="<?php echo $className; ?>" style="<?php echo $style; ?>">
        <div class="star-name">
            <?php for ($i = 0; $i < $len; $i++): ?>
                <span><?php echo htmlspecialchars(mb_substr($name, $i, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endfor; ?>
        </div>

        <?php if (!empty($star['brightness']) && $star['brightness'] !== '-'): ?>
            <div class="star-brightness"><?php echo htmlspecialchars($star['brightness'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($sihuaClass !== ''): ?>
            <?php $map = ['禄' => '禄', '权' => '权', '科' => '科', '忌' => '忌']; ?>
            <div class="star-sihua"><?php echo htmlspecialchars($map[$sihuaClass] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
    </span>
<?php
}

/**
 * 渲染命盘中宫单元格 (个人基础信息面板)
 */
function renderCenterCell(array $data, array $info): void
{
    $name = htmlspecialchars($data['name'] ?? '命主', ENT_QUOTES, 'UTF-8');
    $sexText = ((int)($data['sex'] ?? 1) === 1) ? '男' : '女';
    $yearGan = $data['year_gan'] ?? '甲';
    $isYang = in_array($yearGan, ['甲', '丙', '戊', '庚', '壬'], true);
    $yinYang = $isYang ? '阳' : '阴';

    $dateVal = htmlspecialchars($data['solar_date'] ?? '', ENT_QUOTES, 'UTF-8');
    $lunarStr = "农历：" . htmlspecialchars($data['traditional_lunar_date'] ?? '', ENT_QUOTES, 'UTF-8');

    $baziRaw = (string)($data['bazi_str'] ?? '');
    $cleanBazi = preg_replace('/\s*\([^)]*\)/', '', $baziRaw);
    $baziParts = explode(' ', $cleanBazi);
    $baziCols = array_slice($baziParts, 0, 4);
    $labels = ['年', '月', '日', '时'];
?>
    <div class="center-cell" data-index="-1">
        <h2 class="cc-name"><?php echo $name; ?></h2>
        
        <div class="cc-row cc-meta">
            <span><?php echo $yinYang . $sexText; ?></span>
            <span class="divider">|</span>
            <span><?php echo htmlspecialchars($data['zodiac'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="divider">|</span>
            <span><?php echo htmlspecialchars($data['age'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="cc-row cc-specs">
            <span><?php echo htmlspecialchars($info['bureau'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="divider">•</span>
            <span><?php echo htmlspecialchars($info['ziwei_pos'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>

        <div class="cc-bazi-grid">
            <?php foreach ($baziCols as $idx => $gz): ?>
                <?php 
                    $label = $labels[$idx] ?? '';
                    $val = htmlspecialchars(mb_substr($gz, 0, 2, 'UTF-8'), ENT_QUOTES, 'UTF-8'); 
                ?>
                <div class="bazi-col">
                    <span class="bz-label"><?php echo $label; ?></span>
                    <span class="bz-val"><?php echo $val; ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cc-birth-check">公历生日：<?php echo $dateVal; ?></div>
        <div class="cc-habit-lunar"><?php echo $lunarStr; ?></div>

        <?php 
            $mz = htmlspecialchars(str_replace('命主：', '', $info['ming_zhu'] ?? ''), ENT_QUOTES, 'UTF-8');
            $sz = htmlspecialchars(str_replace('身主：', '', $info['shen_zhu'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="cc-row cc-owners">
            <span class="owner-item"><span class="ol">命主</span><span class="ov"><?php echo $mz; ?></span></span>
            <span class="owner-item"><span class="ol">身主</span><span class="ov"><?php echo $sz; ?></span></span>
        </div>

        <?php if (!empty($info['lai_yin']['gong'])): ?>
            <div class="cc-row cc-laiyin">
                <span class="laiyin-label"><i class="fas fa-seedling"></i> 来因宫</span>
                <span class="laiyin-value">
                    <?php echo htmlspecialchars($info['lai_yin']['gong'] . ' (' . $info['lai_yin']['gan'] . $info['lai_yin']['zhi'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($data['is_late_zi'])): ?>
            <div class="cc-leap-note"><i class="fas fa-clock"></i> 晚子时 (日干支算次日)</div>
        <?php endif; ?>
    </div>
<?php
}

/**
 * 渲染单个十二宫位单元格
 */
function renderPalaceCell(array $gong): void
{
    $index = (int)($gong['index'] ?? 0);
    $name = htmlspecialchars($gong['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $pos = htmlspecialchars(($gong['gan'] ?? '') . ($gong['zhi'] ?? ''), ENT_QUOTES, 'UTF-8');
    $daxian = htmlspecialchars($gong['daxian'] ?? '', ENT_QUOTES, 'UTF-8');
?>
    <div class="cell" data-index="<?php echo $index; ?>" data-gong-name="<?php echo $name; ?>" data-pos="<?php echo $pos; ?>" data-daxian="<?php echo $daxian; ?>">
        
        <div class="gong-header">
            <div class="gong-name">
                <?php echo $name; ?>
                <?php if (!empty($gong['is_ming'])): ?>
                    <span class="tag ming-tag">(命)</span>
                <?php endif; ?>
                <?php if (!empty($gong['is_shen'])): ?>
                    <span class="tag shen-tag">(身)</span>
                <?php endif; ?>
            </div>

            <div class="changsheng-container">
                <?php foreach ($gong['main_stars'] ?? [] as $star): ?>
                    <?php if (($star['type'] ?? '') === 'small-text'): ?>
                        <span class="changsheng-item"><?php echo htmlspecialchars($star['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="stars-container">
            <?php 
                $stars = array_filter($gong['main_stars'] ?? [], fn($s) => ($s['type'] ?? '') !== 'small-text');
                usort($stars, function (array $a, array $b) {
                    $order = ['major' => 1, 'ji' => 2, 'sha' => 3, 'peach' => 4, 'luck' => 5, 'bad' => 6, 'minor' => 99];
                    return ($order[$a['type'] ?? 'minor'] ?? 50) - ($order[$b['type'] ?? 'minor'] ?? 50);
                });
                foreach ($stars as $star) {
                    renderStar($star);
                }
            ?>
        </div>

        <?php if (!empty($gong['liu_nian_ages'])): ?>
            <div class="gong-liunian">流年: <?php echo htmlspecialchars($gong['liu_nian_ages'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($gong['ages'])): ?>
            <div class="gong-ages">小限: <?php echo htmlspecialchars($gong['ages'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($gong['boshi']) || !empty($gong['suijian']) || !empty($gong['jiangxing'])): ?>
            <div class="gong-shensha">
                <?php if (!empty($gong['boshi'])): ?>
                    <span class="shensha-item boshi-group" title="博士"><?php echo htmlspecialchars($gong['boshi'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if (!empty($gong['jiangxing'])): ?>
                    <span class="shensha-item jiang-group" title="将星"><?php echo htmlspecialchars($gong['jiangxing'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if (!empty($gong['suijian'])): ?>
                    <span class="shensha-item suijian-group" title="岁建"><?php echo htmlspecialchars($gong['suijian'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="gong-footer">
            <div class="gong-gz"><?php echo $pos; ?></div>
            <?php if ($daxian !== ''): ?>
                <div class="gong-daxian"><?php echo $daxian; ?></div>
            <?php endif; ?>
        </div>

    </div>
<?php
}

/**
 * 渲染底部格局扫描检测面板
 */
function renderGeJuPanel(array $geJuList): void
{
    if (empty($geJuList)) {
?>
        <div class="geju-panel geju-empty">
            <i class="fas fa-search"></i> 未检测到标准格局（命盘格局需结合大限流年综合判断）
        </div>
<?php
        return;
    }

    $categoryOrder = ['贵权格', '财富格', '文才格', '动荡格', '凶险格'];
    $categoryIcon = [
        '贵权格' => '👑', '财富格' => '💰', '文才格' => '🧠',
        '动荡格' => '⚔️', '凶险格' => '⚠️'
    ];
    $grouped = [];
    foreach ($geJuList as $g) {
        $cat = $g['category'] ?? '其他';
        $grouped[$cat][] = $g;
    }
?>
    <div class="geju-panel">
        <div class="geju-header">
            <i class="fas fa-star"></i> 格局扫描结果 
            <span class="geju-count">共检测到 <?php echo count($geJuList); ?> 个格局</span>
        </div>
        <div class="geju-note">
            <i class="fas fa-info-circle"></i> 以下格局由系统自动扫描，已按紫微斗数经典规则判断，请结合实际命盘深度解读。
        </div>

        <?php foreach ($categoryOrder as $cat): ?>
            <?php if (empty($grouped[$cat])) continue; ?>
            <?php $icon = $categoryIcon[$cat] ?? '⭐'; ?>
            
            <div class="geju-category">
                <div class="geju-cat-title"><?php echo $icon . ' ' . htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="geju-cards">
                    <?php foreach ($grouped[$cat] as $g): ?>
                        <?php 
                            $jiXiong = $g['jiXiong'] ?? '';
                            $jiXiongClass = match($jiXiong) {
                                '大吉' => 'jx-daji',
                                '吉' => 'jx-ji',
                                '中性' => 'jx-zhong',
                                '凶' => 'jx-xiong',
                                '大凶' => 'jx-daxiong',
                                default => ''
                            };
                        ?>
                        <div class="geju-card <?php echo $jiXiongClass; ?>">
                            <div class="geju-card-header">
                                <span class="geju-name"><?php echo htmlspecialchars($g['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="geju-jixiong"><?php echo htmlspecialchars($jiXiong, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="geju-desc"><?php echo htmlspecialchars($g['desc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>

                            <?php if (!empty($g['detail']) && is_array($g['detail'])): ?>
                                <?php 
                                    $detailStr = [];
                                    foreach ($g['detail'] as $k => $v) {
                                        if (is_array($v)) {
                                            $detailStr[] = implode('、', $v);
                                        } elseif (is_string($v) && $v !== '') {
                                            $detailStr[] = $v;
                                        }
                                    }
                                ?>
                                <?php if (!empty($detailStr)): ?>
                                    <div class="geju-detail">
                                        <i class="fas fa-check-circle"></i> 
                                        <?php echo htmlspecialchars(implode(' | ', $detailStr), ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
}

/**
 * 渲染整个命盘主视图网格
 */
function renderPanGrid(array $palaces, array $info, array $displayInfo, array $gridMap, string $shapeClass, array $geJuList = []): void
{
?>
    <div class="pan-grid-container">
        <div class="pan-grid <?php echo htmlspecialchars($shapeClass, ENT_QUOTES, 'UTF-8'); ?>" id="panGrid">
            <?php
                for ($r = 0; $r < 4; $r++) {
                    for ($c = 0; $c < 4; $c++) {
                        if (($r === 1 || $r === 2) && ($c === 1 || $c === 2)) {
                            if ($r === 1 && $c === 1) {
                                renderCenterCell($displayInfo, $info);
                            }
                            continue;
                        }
                        $key = "{$r}-{$c}";
                        if (isset($gridMap[$key])) {
                            renderPalaceCell($palaces[$gridMap[$key]]);
                        }
                    }
                }
            ?>
        </div>

        <?php renderGeJuPanel($geJuList); ?>

        <div class="mobile-help-panel" id="mobileHelpPanel">
            <div class="help-content">
                <h3><i class="fas fa-info-circle"></i> 使用说明</h3>
                <ul>
                    <li><strong>查看三方四正</strong>：点击任意宫位，其三方四正宫位会高亮显示</li>
                    <li><strong>格局扫描</strong>：生成命盘后，命盘下方自动显示检测到的格局</li>
                    <li><strong>复制星曜</strong>：长按星曜名称即可复制</li>
                    <li><strong>切换风格</strong>：侧边菜单可选择圆形/方形宫格</li>
                </ul>
                <button class="close-help-btn">我知道了</button>
            </div>
        </div>
    </div>
<?php
}