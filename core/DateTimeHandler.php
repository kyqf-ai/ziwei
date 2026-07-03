<?php
/**
 * 日期时间处理器 - 安全校验强化版
 * 负责前端传入数据的解析、白名单严格过滤、农历转换与展示格式化
 */
class DateTimeHandler
{
    private array $input = [];
    private array $data = [];

    // 静态白名单定义
    private const TIAN_GAN = ['甲','乙','丙','丁','戊','己','庚','辛','壬','癸'];
    private const DI_ZHI = ['子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'];

    public function __construct(array $input)
    {
        $this->input = $input;
        $this->process();
    }

    /**
     * 核心处理与严格过滤流程
     */
    private function process(): void
    {
        // 1. 基础信息安全过滤
        $this->data['name'] = htmlspecialchars(trim((string)($this->input['name'] ?? '命主')), ENT_QUOTES, 'UTF-8');
        if ($this->data['name'] === '') {
            $this->data['name'] = '命主';
        }
        
        $this->data['sex'] = isset($this->input['sex']) && (int)$this->input['sex'] === 0 ? 0 : 1;

        // 2. 天干地支严格白名单校验
        $yearGan = trim((string)($this->input['year_gan'] ?? ''));
        $yearZhi = trim((string)($this->input['year_zhi'] ?? ''));
        $hourGan = trim((string)($this->input['hour_gan'] ?? ''));
        $hourZhi = trim((string)($this->input['hour_zhi'] ?? ''));

        if (!in_array($yearGan, self::TIAN_GAN, true)) {
            throw new InvalidArgumentException("非法的出生年干参数：[" . htmlspecialchars($yearGan) . "]");
        }
        if (!in_array($yearZhi, self::DI_ZHI, true)) {
            throw new InvalidArgumentException("非法的出生年支参数：[" . htmlspecialchars($yearZhi) . "]");
        }
        if ($hourGan !== '' && !in_array($hourGan, self::TIAN_GAN, true)) {
            throw new InvalidArgumentException("非法的出生时干参数：[" . htmlspecialchars($hourGan) . "]");
        }
        if (!in_array($hourZhi, self::DI_ZHI, true)) {
            throw new InvalidArgumentException("非法的出生时支参数：[" . htmlspecialchars($hourZhi) . "]");
        }

        $this->data['year_gan'] = $yearGan;
        $this->data['year_zhi'] = $yearZhi;
        $this->data['hour_gan'] = $hourGan === '' ? '甲' : $hourGan; // 备用兜底
        $this->data['hour_zhi'] = $hourZhi;

        // 3. 农历月、日边界与合法性限制
        $rawLunarMonth = isset($this->input['lunar_month']) ? (int)$this->input['lunar_month'] : 1;
        $lunarDay = isset($this->input['lunar_day']) ? (int)$this->input['lunar_day'] : 1;

        $isLeap = false;
        $lunarMonth = $rawLunarMonth;
        
        if ($rawLunarMonth < 0) {
            $isLeap = true;
            $lunarMonth = abs($rawLunarMonth);
        } elseif (!empty($this->input['is_leap_month'])) {
            $isLeap = true;
        }

        if ($lunarMonth < 1 || $lunarMonth > 12) {
            throw new InvalidArgumentException("农历月份超出有效范围 (1-12)：{$lunarMonth}");
        }
        if ($lunarDay < 1 || $lunarDay > 30) {
            throw new InvalidArgumentException("农历日期超出有效范围 (1-30)：{$lunarDay}");
        }

        $this->data['display_lunar_month'] = $lunarMonth;
        $this->data['display_lunar_day'] = $lunarDay;
        $this->data['is_leap_month'] = $isLeap;

        // 4. 闰月流派排盘策略处理（默认采用经典拆分派：15日后算次月）
        // 预留后续通过参数切换不同流派规则的空间
        $panMonth = $lunarMonth;
        if ($isLeap) {
            $leapStrategy = trim((string)($this->input['leap_strategy'] ?? 'split'));
            
            if ($leapStrategy === 'next') {
                // 全月算次月
                $panMonth = $lunarMonth + 1;
            } elseif ($leapStrategy === 'current') {
                // 全月算本月
                $panMonth = $lunarMonth;
            } else {
                // 默认 split：下半月拆分派
                if ($lunarDay > 15) {
                    $panMonth = $lunarMonth + 1;
                }
            }
            
            if ($panMonth > 12) {
                $panMonth = 1;
            }
        }
        $this->data['pan_lunar_month'] = $panMonth;
        $this->data['pan_lunar_day'] = $lunarDay;

        // 5. 杂项字段与格式化处理
        $this->data['bazi_str'] = htmlspecialchars(trim((string)($this->input['bazi_str'] ?? '')), ENT_QUOTES, 'UTF-8');
        $this->data['solar_date'] = trim((string)($this->input['birth_date'] ?? ''));
        $this->data['is_late_zi'] = !empty($this->input['is_late_zi']);
        $this->data['date_type'] = trim((string)($this->input['date_type'] ?? 'solar')) === 'lunar' ? 'lunar' : 'solar';

        $zodiacs = [
            '子'=>'鼠','丑'=>'牛','寅'=>'虎','卯'=>'兔',
            '辰'=>'龙','巳'=>'蛇','午'=>'马','未'=>'羊',
            '申'=>'猴','酉'=>'鸡','戌'=>'狗','亥'=>'猪'
        ];
        $this->data['zodiac'] = $zodiacs[$this->data['year_zhi']] ?? '';
        $this->data['age'] = $this->calculateNominalAge();

        // 6. 农历转传统中文格式
        $solarYear = (int)substr($this->data['solar_date'], 0, 4);
        $solarMonth = (int)substr($this->data['solar_date'], 5, 2);
        $lunarYearNum = $solarYear > 0 ? $solarYear : (int)date('Y');
        
        if ($solarMonth > 0 && $solarMonth <= 2 && $lunarMonth >= 10) {
            $lunarYearNum--;
        }

        $cnNum = ['〇','一','二','三','四','五','六','七','八','九'];
        $yearStr = '';
        foreach (str_split((string)$lunarYearNum) as $d) {
            $yearStr .= $cnNum[(int)$d] ?? '';
        }

        $cnMonthMap = [1=>'正月',2=>'二月',3=>'三月',4=>'四月',5=>'五月',6=>'六月',7=>'七月',8=>'八月',9=>'九月',10=>'十月',11=>'冬月',12=>'腊月'];
        $cnDayMap = [1=>'初一',2=>'初二',3=>'初三',4=>'初四',5=>'初五',6=>'初六',7=>'初七',8=>'初八',9=>'初九',10=>'初十',11=>'十一',12=>'十二',13=>'十三',14=>'十四',15=>'十五',16=>'十六',17=>'十七',18=>'十八',19=>'十九',20=>'二十',21=>'廿一',22=>'廿二',23=>'廿三',24=>'廿四',25=>'廿五',26=>'廿六',27=>'廿七',28=>'廿八',29=>'廿九',30=>'三十'];

        $mStr = $cnMonthMap[$lunarMonth] ?? $lunarMonth . '月';
        $dStr = $cnDayMap[$lunarDay] ?? $lunarDay . '日';
        $leapStr = $isLeap ? '闰' : '';
        $this->data['traditional_lunar_date'] = $yearStr . '年' . $leapStr . $mStr . $dStr;
    }

    /**
     * 计算虚岁（健壮处理）
     */
    private function calculateNominalAge(): string
    {
        if ($this->data['solar_date'] !== '') {
            try {
                $birthDate = new DateTime($this->data['solar_date']);
                $currentDate = new DateTime();
                if ($birthDate > $currentDate) {
                    return '1岁';
                }
                $diff = $currentDate->diff($birthDate);
                return ($diff->y + 1) . '岁';
            } catch (Exception $e) {
                return '-';
            }
        }
        return '-';
    }

    /**
     * 获取用于核心引擎计算的数据
     */
    public function getPanData(): array
    {
        return [
            'sex' => $this->data['sex'],
            'year_gan' => $this->data['year_gan'],
            'year_zhi' => $this->data['year_zhi'],
            'hour_gan' => $this->data['hour_gan'],
            'hour_zhi' => $this->data['hour_zhi'],
            'lunar_month' => $this->data['pan_lunar_month'],
            'lunar_day' => $this->data['pan_lunar_day'],
        ];
    }

    /**
     * 获取用于前端或接口展示的数据
     */
    public function getDisplayData(): array
    {
        return $this->data;
    }
}