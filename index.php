<?php
/**
 * 紫微斗数排盘系统 - 主入口 (安全加固与全局异常接管版)
 * * 文件结构：
 * index.php              — 主入口（本文件）
 * core/ZiWeiData.php     — 静态数据 + 格局定义表
 * core/GeJuScanner.php   — 格局自动扫描引擎
 * core/ZiWei.php         — 排盘核心计算引擎
 * core/DateTimeHandler.php — 日期时间处理
 * web/Renderer.php       — HTML渲染函数
 * web/styles.php         — CSS样式
 * web/scripts.php        — JavaScript
 */

// ============================================================
// 1. 环境配置与错误接管
// ============================================================

define('ZW_DEBUG', false); // 设为 true 可在页面/接口中直接输出详细调用堆栈，设为 false 则只记录日志对外友好提示

if (ZW_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

/**
 * 全局日志记录函数
 */
function logZwError($message, $exception = null) {
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logFile = $logDir . '/error.log';
    $time = date('Y-m-d H:i:s');
    $req = $_SERVER['REQUEST_URI'] ?? '';
    
    $logData = "[$time] [$req] ERROR: " . $message . PHP_EOL;
    if ($exception instanceof Throwable) {
        $logData .= "Stack Trace:" . PHP_EOL . $exception->getTraceAsString() . PHP_EOL;
        $logData .= "Input Data: " . json_encode($_REQUEST, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
    $logData .= str_repeat('-', 60) . PHP_EOL;
    
    @file_put_contents($logFile, $logData, FILE_APPEND);
}

// 统一异常处理器
set_exception_handler(function (Throwable $e) {
    logZwError($e->getMessage(), $e);
    
    $isApi = (isset($_GET['action']) && $_GET['action'] === 'api') || 
             (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
             
    if ($isApi) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => ZW_DEBUG ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : '排盘参数或处理出现异常，请检查输入或稍后重试。',
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // 页面端美观报错输出
    $msg = ZW_DEBUG ? htmlspecialchars($e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')') : '系统排盘处理时遇到异常情况。';
    echo "<div style='max-width:600px;margin:30px auto;padding:20px;border-left:5px solid #b71c1c;background:#fff9f9;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);font-family:sans-serif;'>";
    echo "<h3 style='color:#b71c1c;margin-top:0;'><i class='fas fa-exclamation-triangle'></i> 排盘计算异常</h3>";
    echo "<p style='color:#333;font-size:15px;'>详细信息：" . $msg . "</p>";
    echo "<p style='color:#888;font-size:12px;margin-bottom:0;'>若持续发生，请检查输入的日期时间格式是否合规。日志已自动记录。</p>";
    echo "</div>";
    exit;
});

// 统一错误处理器转异常
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// ============================================================
// 2. 自动加载核心类与路由分发
// ============================================================

require_once __DIR__ . '/core/ZiWeiData.php';
require_once __DIR__ . '/core/GeJuScanner.php';
require_once __DIR__ . '/core/ZiWei.php';
require_once __DIR__ . '/core/DateTimeHandler.php';
require_once __DIR__ . '/web/Renderer.php';
require_once __DIR__ . '/web/styles.php';
require_once __DIR__ . '/web/scripts.php';

// 路由分发
if (isset($_GET['action']) && $_GET['action'] === 'api') {
    handleApiRequest();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action'])) {
    handleProcessRequest();
    exit;
}

displayMainPage();

// ============================================================
// 3. 请求处理函数
// ============================================================

/**
 * API 请求处理（返回 JSON 数据供前端 AI 报告使用）
 */
function handleApiRequest()
{
    header('Content-Type: application/json; charset=utf-8');
    $input = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;

    $required = ['year_gan','year_zhi','hour_gan','hour_zhi','lunar_month','lunar_day'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("缺少必需排盘基础参数: {$field}");
        }
    }

    $handler = new DateTimeHandler($input);
    $ziwei = new ZiWei($handler->getPanData());
    $result = $ziwei->calculate();
    $displayData = $handler->getDisplayData();

    $palacesRaw = $result['palaces'];
    $infoRaw = $result['info'];
    $geJuList = $result['ge_ju'];

    $fu_xing_list = ['文昌','文曲','左辅','右弼','天魁','天钺','禄存','天马','擎羊','陀罗','火星','铃星','地空','地劫'];

    $response = [
        'success' => true,
        'basic' => [
            'name' => $displayData['name'] ?? '命主',
            'gender' => (($displayData['sex'] ?? 1) == 1) ? '男' : '女',
            'bazi' => $displayData['bazi_str'] ?? '',
            'ming_ju' => $infoRaw['bureau'] ?? '',
            'ming_zhu' => $infoRaw['ming_zhu'] ?? '',
            'shen_zhu' => $infoRaw['shen_zhu'] ?? '',
            'ming_gong' => $infoRaw['ming_gong'] ?? '',
            'shen_gong' => $infoRaw['shen_gong'] ?? '',
            'birth_info' => $displayData ?? [],
        ],
        'palaces' => [],
        'ge_ju' => $geJuList,
        'info' => [
            'ming_gong_index' => $infoRaw['ming_gong_index'] ?? 0,
            'shen_gong_index' => $infoRaw['shen_gong_index'] ?? 0,
            'lai_yin' => $infoRaw['lai_yin'] ?? null,
        ]
    ];

    foreach ($palacesRaw as $p) {
        $major = []; $minor = []; $extra = []; $borrowed = []; $changsheng = [];

        foreach ($p['main_stars'] as $s) {
            if (isset($s['type']) && $s['type'] === 'small-text') {
                $changsheng[] = $s['name'];
            }
        }

        $hasMajor = false;
        foreach ($p['main_stars'] as $s) {
            if (isset($s['type']) && $s['type'] == 'major') { $hasMajor = true; break; }
        }

        if (!$hasMajor) {
            $oppIdx = ($p['index'] + 6) % 12;
            foreach ($palacesRaw[$oppIdx]['main_stars'] ?? [] as $os) {
                if (isset($os['type']) && $os['type'] == 'major') {
                    $borrowed[] = ['name'=>$os['name']??'','brightness'=>$os['brightness']??'','sihua'=>$os['sihua']??'','type'=>'major','is_borrowed'=>true];
                }
            }
        }

        foreach ($p['main_stars'] as $s) {
            if (isset($s['type']) && $s['type'] === 'small-text') continue;
            $item = ['name'=>$s['name']??'','brightness'=>$s['brightness']??'','sihua'=>$s['sihua']??'','type'=>$s['type']??'minor'];
            if (isset($s['type']) && $s['type'] == 'major') {
                $major[] = $item;
            } elseif (in_array($s['name']??'', $fu_xing_list) || (isset($s['type']) && ($s['type']=='ji'||$s['type']=='sha'))) {
                $minor[] = $item;
            } else {
                $extra[] = $item;
            }
        }

        $response['palaces'][] = [
            'index' => $p['index'],
            'pos' => $p['gan'] . $p['zhi'],
            'name' => $p['name'],
            'is_ming' => $p['is_ming'],
            'is_shen' => $p['is_shen'],
            'daxian' => $p['daxian'],
            'stars' => ['major'=>$major,'minor'=>$minor,'extra'=>$extra,'borrowed'=>$borrowed],
            'ages' => $p['ages'] ?? '',
            'liu_nian_ages' => $p['liu_nian_ages'] ?? '',
            'gods' => [
                'cs' => implode('、', $changsheng),
                'boshi' => $p['boshi'] ?? '',
                'suijian' => $p['suijian'] ?? '',
                'jiangxing' => $p['jiangxing'] ?? '',
            ],
            'sanfang_indices' => getSanFangSiZheng($p['index']),
            'an_he_index' => $p['an_he_index'] ?? -1,
            'an_he_gong' => $p['an_he_gong'] ?? '',
        ];
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

function getSanFangSiZheng($idx)
{
    return array_unique([$idx, ($idx+4)%12, ($idx+8)%12, ($idx+6)%12]);
}

/**
 * 排盘请求处理（返回 HTML 命盘）
 */
function handleProcessRequest()
{
    $dateHandler = new DateTimeHandler($_POST);
    $panData = $dateHandler->getPanData();
    $displayInfo = $dateHandler->getDisplayData();

    $ziwei = new ZiWei($panData);
    $result = $ziwei->calculate();

    $palaces = $result['palaces'];
    $info = $result['info'];
    $geJuList = $result['ge_ju'];

    $shapeSetting = $_POST['shape_setting'] ?? 'square';
    $shapeClass = ($shapeSetting === 'round') ? 'shape-round' : 'shape-square';

    $gridMap = [
        '0-0'=>3,'0-1'=>4,'0-2'=>5,'0-3'=>6,
        '1-0'=>2,'1-3'=>7,
        '2-0'=>1,'2-3'=>8,
        '3-0'=>0,'3-1'=>11,'3-2'=>10,'3-3'=>9
    ];

    renderPanGrid($palaces, $info, $displayInfo, $gridMap, $shapeClass, $geJuList);
}

/**
 * 显示主页面
 */
function displayMainPage()
{
    $now = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#b71c1c">
    <title>紫微斗数排盘系统 - 专业命理分析</title>
    <script src="lunar.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style><?php echo getCSS(); ?></style>
</head>
<body>
    <button class="mobile-menu-btn" id="mobileMenuBtn" style="border:none;cursor:pointer;">
        <i class="fas fa-bars"></i>
        <span class="menu-text">菜单</span>
    </button>

    <div class="wrapper">
        <div class="sidebar open" id="sidebar">
            <div class="sidebar-header">
                <h2>紫微斗数排盘</h2>
                <button class="close-sidebar-btn" id="closeSidebarBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="mainForm">
                <div class="form-item" style="margin-top:16px;">
                    <label>姓名</label>
                    <input type="text" name="name" value="命主" placeholder="请输入姓名">
                    <div class="form-note">仅用于显示，不影响排盘</div>
                </div>
                <div class="form-item">
                    <label>性别</label>
                    <select name="sex">
                        <option value="1">男（乾造）</option>
                        <option value="0">女（坤造）</option>
                    </select>
                </div>
                <div class="form-item">
                    <label>日期类型</label>
                    <select id="dateType" name="date_type" onchange="toggleDateType()">
                        <option value="solar">公历（阳历）</option>
                        <option value="lunar">农历（阴历）</option>
                    </select>
                </div>
                <div class="form-item">
                    <label>出生日期时间</label>
                    <input type="datetime-local" id="birth_datetime" name="birth_datetime" required value="<?php echo $now; ?>">
                    <div class="form-hint"><i class="fas fa-clock"></i> 请自己查询真太阳时输入会更准。</div>
                </div>
                <div class="form-item" id="leapMonthContainer" style="display:none;">
                    <label class="checkbox-label">
                        <input type="checkbox" id="isLeapMonth" name="is_leap_month" value="1">
                        <span>是闰月</span>
                    </label>
                    <div class="form-note">仅当农历输入且为闰月时勾选</div>
                </div>
                <div class="form-item">
                    <label>子时处理 (23:00-01:00)</label>
                    <select id="zi_shi_method" name="zi_shi_method">
                        <option value="auto">自动 (23点后算次日干支)</option>
                        <option value="early">早子时 (23点后仍算当日)</option>
                    </select>
                    <div class="form-note">紫微斗数传统多采用晚子时规则</div>
                </div>
                <div class="form-item">
                    <label>排盘风格</label>
                    <div class="radio-group inline">
                        <label class="radio-label">
                            <input type="radio" name="shape_setting" value="round" onchange="updateShape()">
                            <span>圆形</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="shape_setting" value="square" checked onchange="updateShape()">
                            <span>方形</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" name="year_gan" id="year_gan">
                <input type="hidden" name="year_zhi" id="year_zhi">
                <input type="hidden" name="hour_gan" id="hour_gan">
                <input type="hidden" name="hour_zhi" id="hour_zhi">
                <input type="hidden" name="lunar_month" id="lunar_month">
                <input type="hidden" name="lunar_day" id="lunar_day">
                <input type="hidden" name="bazi_str" id="bazi_str">
                <input type="hidden" name="birth_date" id="birth_date">
                <input type="hidden" name="is_late_zi" id="is_late_zi">

                <div class="button-container">
                    <button type="button" class="submit-btn" id="submitBtn" onclick="generateChart()">
                        <i class="fas fa-calculator"></i> 生成命盘
                    </button>
                    <button type="button" class="submit-btn ai-report-btn" onclick="getTextReport()">
                        <i class="fas fa-robot"></i> 生成AI解读文本
                    </button>
                </div>
            </form>
        </div>

        <div id="panResult" class="main-content">
            <div class="loading">
                <div style="font-size:48px;margin-bottom:20px;color:#b71c1c;">
                    <i class="fas fa-yin-yang"></i>
                </div>
                <h3 style="margin-bottom:10px;">紫微斗数排盘系统</h3>
                <p>请填写左侧信息并点击"生成命盘"</p>
                <p style="font-size:14px;color:#888;margin-top:20px;">
                    <i class="fas fa-star"></i> 系统自动扫描50+格局，无需AI即可识别命盘特征
                </p>
                <p style="font-size:13px;color:#aaa;margin-top:8px;">
                    <i class="fas fa-mobile-alt"></i> 移动端已优化，点击宫位查看三方四正
                </p>
            </div>
        </div>
    </div>

    <script><?php echo getJavaScript(); ?></script>
</body>
</html>
<?php
}