<?php
/**
 * 紫微斗数基础数据类
 * 包含所有静态表格数据、星曜亮度、四化、格局定义
 */
class ZiWeiData
{
    public static $DI_ZHI = ['子','丑','寅','卯','辰','巳','午','未','申','酉','戌','亥'];
    public static $SHI_ER_GONG = ['兄弟','夫妻','子女','财帛','疾厄','迁移','交友','官禄','田宅','福德','父母'];
    public static $NA_YIN = [
        '甲子'=>'海中金','乙丑'=>'海中金','丙寅'=>'炉中火','丁卯'=>'炉中火',
        '戊辰'=>'大林木','己巳'=>'大林木','庚午'=>'路旁土','辛未'=>'路旁土',
        '壬申'=>'剑锋金','癸酉'=>'剑锋金','甲戌'=>'山头火','乙亥'=>'山头火',
        '丙子'=>'涧下水','丁丑'=>'涧下水','戊寅'=>'城头土','己卯'=>'城头土',
        '庚辰'=>'白蜡金','辛巳'=>'白蜡金','壬午'=>'杨柳木','癸未'=>'杨柳木',
        '甲申'=>'泉中水','乙酉'=>'泉中水','丙戌'=>'屋上土','丁亥'=>'屋上土',
        '戊子'=>'霹雳火','己丑'=>'霹雳火','庚寅'=>'松柏木','辛卯'=>'松柏木',
        '壬辰'=>'长流水','癸巳'=>'长流水','甲午'=>'砂中金','乙未'=>'砂中金',
        '丙申'=>'山下火','丁酉'=>'山下火','戊戌'=>'平地木','己亥'=>'平地木',
        '庚子'=>'壁上土','辛丑'=>'壁上土','壬寅'=>'金箔金','癸卯'=>'金箔金',
        '甲辰'=>'佛灯火','乙巳'=>'佛灯火','丙午'=>'天河水','丁未'=>'天河水',
        '戊申'=>'大驿土','己酉'=>'大驿土','庚戌'=>'钗钏金','辛亥'=>'钗钏金',
        '壬子'=>'桑柘木','癸丑'=>'桑柘木','甲寅'=>'大溪水','乙卯'=>'大溪水',
        '丙辰'=>'沙中土','丁巳'=>'沙中土','戊午'=>'天上火','己未'=>'天上火',
        '庚申'=>'石榴木','辛酉'=>'石榴木','壬戌'=>'大海水','癸亥'=>'大海水'
    ];
    public static $LU_CUN = ['甲'=>'寅','乙'=>'卯','丙'=>'巳','丁'=>'午','戊'=>'巳','己'=>'午','庚'=>'申','辛'=>'酉','壬'=>'亥','癸'=>'子'];
    public static $TIAN_GUAN = ['甲'=>'未','乙'=>'辰','丙'=>'巳','丁'=>'寅','戊'=>'卯','己'=>'酉','庚'=>'亥','辛'=>'酉','壬'=>'戌','癸'=>'午'];
    public static $TIAN_FU = ['甲'=>'酉','乙'=>'申','丙'=>'子','丁'=>'亥','戊'=>'卯','己'=>'寅','庚'=>'午','辛'=>'巳','壬'=>'午','癸'=>'巳'];
    public static $TIAN_CHU = ['甲'=>'巳','乙'=>'午','丙'=>'子','丁'=>'巳','戊'=>'午','己'=>'申','庚'=>'寅','辛'=>'午','壬'=>'酉','癸'=>'亥'];
    public static $HONG_YAN = ['甲'=>'午','乙'=>'申','丙'=>'寅','丁'=>'未','戊'=>'辰','己'=>'辰','庚'=>'戌','辛'=>'酉','壬'=>'子','癸'=>'申'];
    public static $KUI_YUE = [
        '甲'=>['丑','未'],'戊'=>['丑','未'],'庚'=>['丑','未'],
        '乙'=>['子','申'],'己'=>['子','申'],
        '丙'=>['亥','酉'],'丁'=>['亥','酉'],
        '辛'=>['寅','午'],
        '壬'=>['卯','巳'],'癸'=>['卯','巳']
    ];
    public static $TIAN_DE = ['子'=>'酉','丑'=>'戌','寅'=>'亥','卯'=>'子','辰'=>'丑','巳'=>'寅','午'=>'卯','未'=>'辰','申'=>'巳','酉'=>'午','戌'=>'未','亥'=>'申'];
    public static $YUE_DE = ['子'=>'巳','丑'=>'午','寅'=>'未','卯'=>'申','辰'=>'酉','巳'=>'戌','午'=>'亥','未'=>'子','申'=>'丑','酉'=>'寅','戌'=>'卯','亥'=>'辰'];
    public static $GU_GUA = [
        '亥'=>['寅','戌'],'子'=>['寅','戌'],'丑'=>['寅','戌'],
        '寅'=>['巳','丑'],'卯'=>['巳','丑'],'辰'=>['巳','丑'],
        '巳'=>['申','辰'],'午'=>['申','辰'],'未'=>['申','辰'],
        '申'=>['亥','未'],'酉'=>['亥','未'],'戌'=>['亥','未']
    ];
    public static $TIAN_MA = ['申'=>'寅','子'=>'寅','辰'=>'寅','寅'=>'申','午'=>'申','戌'=>'申','巳'=>'亥','酉'=>'亥','丑'=>'亥','亥'=>'巳','卯'=>'巳','未'=>'巳'];
    public static $FEI_LIAN = ['子'=>'申','丑'=>'酉','寅'=>'戌','卯'=>'巳','辰'=>'午','巳'=>'未','午'=>'寅','未'=>'卯','申'=>'辰','酉'=>'亥','戌'=>'子','亥'=>'丑'];
    public static $PO_SUI = ['子'=>'巳','丑'=>'丑','寅'=>'酉','卯'=>'巳','辰'=>'丑','巳'=>'酉','午'=>'巳','未'=>'丑','申'=>'酉','酉'=>'巳','戌'=>'丑','亥'=>'酉'];
    public static $NIAN_JIE = ['子'=>'戌','丑'=>'酉','寅'=>'申','卯'=>'未','辰'=>'午','巳'=>'巳','午'=>'辰','未'=>'卯','申'=>'寅','酉'=>'丑','戌'=>'子','亥'=>'亥'];
    public static $JIE_SHA = ['申'=>'巳','子'=>'巳','辰'=>'巳','寅'=>'亥','午'=>'亥','戌'=>'亥','巳'=>'寅','酉'=>'寅','丑'=>'寅','亥'=>'申','卯'=>'申','未'=>'申'];
    public static $LONG_DE = ['子'=>'未','丑'=>'申','寅'=>'酉','卯'=>'戌','辰'=>'亥','巳'=>'子','午'=>'丑','未'=>'寅','申'=>'卯','酉'=>'辰','戌'=>'巳','亥'=>'午'];
    public static $MONTH_DA_HAO = [1=>'申',2=>'酉',3=>'戌',4=>'亥',5=>'子',6=>'丑',7=>'寅',8=>'卯',9=>'辰',10=>'巳',11=>'午',12=>'未'];
    public static $TIAN_YUE = [1=>'戌',2=>'巳',3=>'辰',4=>'寅',5=>'未',6=>'卯',7=>'亥',8=>'未',9=>'寅',10=>'午',11=>'戌',12=>'寅'];
    public static $TIAN_WU = [1=>'巳',2=>'申',3=>'寅',4=>'亥',5=>'巳',6=>'申',7=>'寅',8=>'亥',9=>'巳',10=>'申',11=>'寅',12=>'亥'];
    public static $SI_HUA = [
        '甲'=>['廉贞禄','破军权','武曲科','太阳忌'],
        '乙'=>['天机禄','天梁权','紫微科','太阴忌'],
        '丙'=>['天同禄','天机权','文昌科','廉贞忌'],
        '丁'=>['太阴禄','天同权','天机科','巨门忌'],
        '戊'=>['贪狼禄','太阴权','右弼科','天机忌'],
        '己'=>['武曲禄','贪狼权','天梁科','文曲忌'],
        '庚'=>['太阳禄','武曲权','太阴科','天同忌'], 
        '辛'=>['巨门禄','太阳权','文曲科','文昌忌'],
        '壬'=>['天梁禄','紫微权','左辅科','武曲忌'],
        '癸'=>['破军禄','巨门权','太阴科','贪狼忌']
    ];
    public static $BO_SHI_12 = ['博士','力士','青龙','小耗','将军','奏书','飞廉','喜神','病符','大耗','伏兵','官府'];
    public static $SUI_JIAN_12 = ['岁建','晦气','丧门','贯索','官符','小耗','岁破','龙德','白虎','天德','吊客','病符'];
    public static $JIANG_XING_12 = ['将星','攀鞍','岁驿','息神','华盖','劫煞','灾煞','天煞','指背','咸池','月煞','亡神'];
    public static $CHANG_SHENG_12 = ['长生','沐浴','冠带','临官','帝旺','衰','病','死','墓','绝','胎','养'];
    public static $JIE_KONG = [
        '甲'=>['申','酉'],'己'=>['申','酉'],'乙'=>['午','未'],'庚'=>['午','未'],
        '丙'=>['辰','巳'],'辛'=>['辰','巳'],'丁'=>['寅','卯'],'壬'=>['寅','卯'],
        '戊'=>['子','丑'],'癸'=>['子','丑'],
    ];
    public static $ZHU_XING_GUAN_XI = [
        '紫微'=>['平','庙','旺','旺','得','旺','庙','庙','旺','旺','得','旺'],
        '天机'=>['庙','陷','得','旺','利','平','庙','陷','得','旺','利','平'],
        '太阳'=>['陷','不','旺','庙','旺','旺','旺','得','得','陷','不','陷'],
        '武曲'=>['旺','庙','得','利','庙','平','旺','庙','得','利','庙','平'],
        '天同'=>['旺','不','利','平','平','庙','陷','不','旺','平','平','庙'],
        '廉贞'=>['平','利','庙','平','利','陷','平','利','庙','平','利','陷'],
        '天府'=>['庙','庙','庙','得','庙','得','旺','庙','得','旺','庙','得'],
        '太阴'=>['庙','庙','旺','陷','陷','陷','不','不','利','不','旺','庙'],
        '贪狼'=>['旺','庙','平','利','庙','陷','旺','庙','平','利','庙','陷'],
        '巨门'=>['旺','不','庙','庙','陷','旺','旺','不','庙','庙','陷','旺'],
        '天相'=>['庙','庙','庙','陷','得','得','庙','得','庙','陷','得','得'],
        '天梁'=>['庙','旺','庙','庙','庙','陷','庙','旺','陷','得','庙','陷'],
        '七杀'=>['旺','庙','庙','旺','庙','平','旺','庙','庙','庙','庙','平'],
        '破军'=>['庙','旺','得','陷','旺','平','庙','旺','得','陷','旺','平'],
    ];
    public static $MINOR_STAR_BRIGHTNESS = [
        '文昌'=>['得','庙','陷','利','得','庙','陷','利','得','庙','陷','利'],
        '文曲'=>['得','庙','平','旺','得','庙','陷','旺','得','庙','陷','旺'],
        '左辅'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '右弼'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '天魁'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '天钺'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '擎羊'=>['陷','庙','-','陷','庙','-','陷','庙','-','陷','庙','-'],
        '陀罗'=>['-','庙','陷','-','庙','陷','-','庙','陷','-','庙','陷'],
        '火星'=>['陷','得','庙','利','陷','得','庙','利','陷','得','庙','利'],
        '铃星'=>['陷','得','庙','利','陷','得','庙','利','陷','得','庙','利'],
        '地空'=>['陷','陷','陷','陷','陷','庙','陷','陷','陷','陷','陷','庙'],
        '地劫'=>['陷','陷','陷','陷','陷','庙','陷','陷','陷','陷','陷','庙'],
        '天姚'=>['陷','陷','陷','庙','陷','陷','利','陷','庙','庙','陷','陷'],
        '红鸾'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '天喜'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '禄存'=>['庙','旺','庙','庙','旺','庙','庙','旺','庙','庙','旺','庙'],
        '天马'=>['旺','-','旺','-','-','旺','-','-','旺','-','-','-'],
        '天刑'=>['陷','陷','庙','庙','陷','陷','陷','陷','庙','庙','陷','陷'],
        '天官'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '天福'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '解神'=>['庙','利','庙','利','庙','利','庙','利','庙','利','庙','利'],
        '天巫'=>['庙','平','庙','平','庙','平','庙','平','庙','平','庙','平'],
        '龙池'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
        '凤阁'=>['庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙','庙'],
    ];
    public static $MING_ZHU = ['子'=>'贪狼','丑'=>'巨门','寅'=>'禄存','卯'=>'文曲','辰'=>'廉贞','巳'=>'武曲','午'=>'破军','未'=>'武曲','申'=>'廉贞','酉'=>'文曲','戌'=>'禄存','亥'=>'巨门'];
    public static $SHEN_ZHU = ['子'=>'火星','丑'=>'天相','寅'=>'天梁','卯'=>'天同','辰'=>'文昌','巳'=>'天机','午'=>'火星','未'=>'天相','申'=>'天梁','酉'=>'天同','戌'=>'文昌','亥'=>'天机'];

    // 来因宫固定位置（五虎遁）
    public static $LAI_YIN_POSITION = [
        '甲'=>'戌','乙'=>'酉','丙'=>'申','丁'=>'未',
        '戊'=>'午','己'=>'巳','庚'=>'辰','辛'=>'卯',
        '壬'=>'寅','癸'=>'亥'
    ];

    // 暗合宫映射表（六合关系：寅亥、卯戌、辰酉、巳申、午未、子丑）
    public static $AN_HE_MAP = [
        0=>9, 1=>8, 2=>7, 3=>6, 4=>5, 5=>4,
        6=>3, 7=>2, 8=>1, 9=>0, 10=>11, 11=>10,
    ];

    // ============================================================
    // 格局定义表（加入严谨性参数：only_ming, brightness, no_sha, no_kong_jie, no_ji）
    // ============================================================
    public static $GE_JU = [
        // ======= 皇权贵胄/统帅格 =======
        [
            'id' => 'ZF_TONGGONG',
            'name' => '紫府同宫格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '紫微与天府同在寅或申坐命，主大富大贵，福寿双全，领袖之命。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['紫微', '天府'], 'zhi' => ['寅', '申'], 'only_ming' => true],
        ],
        [
            'id' => 'JI_XIANG_LIMING',
            'name' => '极向离明格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '紫微在午宫坐命，无六煞同宫，主格局宏大，极具领导力，非富即贵。',
            'rule' => [
                'type' => 'star_in_ming_zhi', 'star' => '紫微', 'zhi' => ['午'], 
                'brightness' => ['庙', '旺'], 'no_sha' => 'same_palace', 'no_kong_jie' => 'same_palace'
            ],
        ],
        [
            'id' => 'JUN_CHEN_QINGHUI',
            'name' => '君臣庆会格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '紫微坐命，三方有辅弼、魁钺、昌曲等会照，得鼎力相助，天生领袖。',
            'rule' => [
                'type' => 'star_in_ming_with_sanfang_stars', 'star' => '紫微', 
                'sanfang_groups' => [['左辅','右弼'],['天魁','天钺'],['文昌','文曲']], 'min_groups' => 2,
                'no_sha' => 'sanfang', 'no_ji' => 'sanfang' // 三方四正不宜有煞忌
            ],
        ],
        [
            'id' => 'ZIFU_ZHAOYI',
            'name' => '紫府朝垣格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '紫微、天府在三方合照命宫，主食禄万钟，稳健富贵。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['紫微', '天府'], 'no_sha' => 'sanfang'],
        ],
        [
            'id' => 'FUXIANG_ZHAOYI',
            'name' => '府相朝垣格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '天府与天相在三方合照命宫，事业平顺，多为高管或要员。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['天府', '天相'], 'no_sha' => 'sanfang'],
        ],
        [
            'id' => 'QISHA_CHAODOU',
            'name' => '七杀朝斗格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '七杀在子/午/寅/申宫坐命，主将帅之才，统领全局。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '七杀', 'zhi' => ['子','午','寅','申'], 'only_ming' => true, 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'XIONGSU_CHAOYUAN',
            'name' => '雄宿朝元格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '廉贞在申宫坐命，主气魄宏大，擅长交际与权谋。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '廉贞', 'zhi' => ['申'], 'only_ming' => true, 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'FUPI_JIADI',
            'name' => '辅弼夹帝格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '紫微坐命，左辅右弼夹持，一生逢凶化吉，贵人相助极强。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => '紫微', 'jia_stars' => ['左辅', '右弼']],
        ],

        // ======= 巨富/暴发/生财格 =======
        [
            'id' => 'HUO_TAN',
            'name' => '火贪格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '贪狼与火星同宫，最强暴发格！主突发横财，极具爆发力。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['贪狼', '火星'], 'no_kong_jie' => 'same_palace', 'no_ji' => 'same_palace'],
        ],
        [
            'id' => 'LING_TAN',
            'name' => '铃贪格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '贪狼与铃星同宫，强劲暴发格，中晚年大发。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['贪狼', '铃星'], 'no_kong_jie' => 'same_palace', 'no_ji' => 'same_palace'],
        ],
        [
            'id' => 'WU_TAN_TONGXING',
            'name' => '武贪同行格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '武曲与贪狼同在丑或未宫坐命，"武贪不发少年人"，晚年大发。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['武曲', '贪狼'], 'zhi' => ['丑', '未'], 'only_ming' => true],
        ],
        [
            'id' => 'SHUANG_LU_JIAOLIU',
            'name' => '双禄交流格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '化禄与禄存同宫或在三方合照，主财源滚滚。',
            'rule' => ['type' => 'shuang_lu'], // 核心逻辑已内置破格拦截
        ],
        [
            'id' => 'LU_MA_JIAOCHI',
            'name' => '禄马交驰格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '禄存（或化禄）与天马同宫或三方相会，奔波发大财。',
            'rule' => ['type' => 'lu_ma_jiaochi'],
        ],
        [
            'id' => 'SAN_QI_JIAHUI',
            'name' => '三奇嘉会格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '化禄、化权、化科在命宫三方四正齐会，名利双收。',
            'rule' => ['type' => 'san_qi'],
        ],
        [
            'id' => 'RIYUE_JIA_CAI',
            'name' => '日月夹财格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '武曲坐命，太阳与太阴在左右相邻宫位夹持，一生财源不断。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => '武曲', 'jia_stars' => ['太阳', '太阴']],
        ],
        [
            'id' => 'LU_MA_PEI_YIN',
            'name' => '禄马佩印格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '禄存（或化禄）与天马、天相同宫守命，奔波中得权。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['天相', '天马'], 'with_lu' => true],
        ],

        // ======= 文翰/才华/专业格 =======
        [
            'id' => 'JI_YUE_TONGLIANG',
            'name' => '机月同梁格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天机、太阴、天同、天梁四星齐会三方，极佳的幕僚/公职人才。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['天机', '太阴', '天同', '天梁'], 'min_count' => 4, 'no_ji' => 'sanfang'],
        ],
        [
            'id' => 'YANG_LIANG_CHANG_LU',
            'name' => '阳梁昌禄格',
            'category' => '文才格',
            'jiXiong' => '大吉',
            'desc' => '太阳、天梁、文昌、禄同会三方四正，考运极佳。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['太阳', '天梁', '文昌'], 'with_lu' => true, 'no_sha' => 'sanfang', 'no_ji' => 'sanfang'],
        ],
        [
            'id' => 'RI_YUE_BINGMING',
            'name' => '日月并明格',
            'category' => '文才格',
            'jiXiong' => '大吉',
            'desc' => '命在丑未，太阳在辰（庙）、太阴在戌（庙）拱照，少年得志。',
            'rule' => ['type' => 'ri_yue_bingming'],
        ],
        [
            'id' => 'MINGZHU_CHUHAN',
            'name' => '明珠出海格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天同在亥命宫或子命宫，对宫日月庙旺，才华洋溢。',
            'rule' => ['type' => 'mingzhu_chuhan'],
        ],
        [
            'id' => 'SHI_ZHONG_YINYU',
            'name' => '石中隐玉格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '巨门在子午坐命，有科权禄，三方无煞，中晚年一鸣惊人。',
            'rule' => [
                'type' => 'star_in_ming_zhi', 'star' => '巨门', 'zhi' => ['子', '午'], 
                'has_sihua' => ['禄','权','科'], 'only_ming' => true, 'brightness' => ['旺', '庙'], 'no_sha' => 'sanfang'
            ],
        ],
        [
            'id' => 'JU_RI_TONGGONG',
            'name' => '巨日同宫格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '巨门太阳同在寅或申坐命，口才极佳，宜教育、外交。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['巨门', '太阳'], 'zhi' => ['寅', '申'], 'only_ming' => true, 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'JI_JU_TONGLIN',
            'name' => '机巨同临格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天机与巨门同在卯酉坐命，心思极其敏捷。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['天机', '巨门'], 'zhi' => ['卯', '酉'], 'only_ming' => true],
        ],
        [
            'id' => 'WENXING_GONGMING',
            'name' => '文星拱命格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '文昌、文曲在三方合照命宫，极具文艺才华。',
            'rule' => ['type' => 'wenxing_gongming'],
        ],
        [
            'id' => 'SHOUXING_RUMIAO',
            'name' => '寿星入庙格',
            'category' => '文才格',
            'jiXiong' => '大吉',
            'desc' => '天梁在午宫坐命，正直无私，多逢凶化吉。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '天梁', 'zhi' => ['午'], 'only_ming' => true, 'brightness' => ['庙'], 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'CHANG_QU_JIA_MING',
            'name' => '昌曲夹命格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '文昌与文曲夹命宫，必定在学业或文艺上有过人之处。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['文昌', '文曲']],
        ],
        [
            'id' => 'WEN_LIANG_ZHENJI',
            'name' => '文梁振纪格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '文曲与天梁在旺地守身命，主监察、纪检、清贵之职。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['文曲', '天梁'], 'only_ming' => true],
        ],

        // ======= 刚毅/动荡/异路格 =======
        [
            'id' => 'SHA_PO_LANG',
            'name' => '杀破狼格',
            'category' => '动荡格',
            'jiXiong' => '中性',
            'desc' => '七杀、破军、贪狼分落命、财、官，一生大起大落，乱世英雄。',
            'rule' => ['type' => 'sha_po_lang'],
        ],
        [
            'id' => 'MA_TOU_DAI_JIAN',
            'name' => '马头带剑格',
            'category' => '动荡格',
            'jiXiong' => '中性',
            'desc' => '擎羊在午宫坐命，威镇边疆，富贵险中求。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '擎羊', 'zhi' => ['午'], 'only_ming' => true],
        ],
        [
            'id' => 'QINGYANG_RUMIAO',
            'name' => '擎羊入庙格',
            'category' => '动荡格',
            'jiXiong' => '吉',
            'desc' => '擎羊在辰、戌、丑、未坐命，化煞为权，能开创局面。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '擎羊', 'zhi' => ['辰','戌','丑','未'], 'only_ming' => true],
        ],
        [
            'id' => 'XING_QIU_JIA_YIN',
            'name' => '刑囚夹印格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '廉贞天相被擎羊相夹，极险恶，易惹官非刑伤。',
            'rule' => ['type' => 'xing_qiu_jia_yin'],
        ],
        [
            'id' => 'YINGXING_RUMIAO',
            'name' => '英星入庙格',
            'category' => '动荡格',
            'jiXiong' => '吉',
            'desc' => '破军在子午坐命，英气勃发，果敢有为。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '破军', 'zhi' => ['子', '午'], 'only_ming' => true, 'brightness' => ['庙', '旺'], 'no_sha' => 'same_palace'],
        ],

        // ======= 凶险/破败/内耗格 =======
        [
            'id' => 'YANG_TUO_JIA_JI',
            'name' => '羊陀夹忌格',
            'category' => '凶险格',
            'jiXiong' => '大凶',
            'desc' => '化忌被羊陀夹，施展不开，暗箭难防，重大挫折。',
            'rule' => ['type' => 'yang_tuo_jia_ji'],
        ],
        [
            'id' => 'XING_JI_JIA_YIN',
            'name' => '刑忌夹印格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '天相被忌与刑（羊）夹，官非、背黑锅。',
            'rule' => ['type' => 'xing_ji_jia_yin'],
        ],
        [
            'id' => 'KONG_JIE_JIA_MING',
            'name' => '空劫夹命格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '地空、地劫夹命，起伏极大，理想多成空。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['地空', '地劫']],
        ],
        [
            'id' => 'JU_FENG_SISHA',
            'name' => '巨逢四煞格',
            'category' => '凶险格',
            'jiXiong' => '大凶',
            'desc' => '巨门落陷逢羊陀火铃二星以上，是非极重，人际恶劣。',
            'rule' => ['type' => 'ju_feng_sha'],
        ],
        [
            'id' => 'LING_CHANG_TUO_WU',
            'name' => '铃昌陀武格',
            'category' => '凶险格',
            'jiXiong' => '大凶',
            'desc' => '武昌铃陀齐会，绝境，重大财务破败或倾覆。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['武曲','文昌','铃星','陀罗'], 'min_count' => 4],
        ],
        [
            'id' => 'FAN_SHUI_TAOHUA',
            'name' => '泛水桃花格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '贪狼在子亥坐命，三方见桃花星，易因色破财或身败名裂。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '贪狼', 'zhi' => ['子', '亥'], 'only_ming' => true, 'require_taohua' => true],
        ],
        [
            'id' => 'FENG_LIU_CAI_ZHANG',
            'name' => '风流彩杖格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '贪狼在寅宫逢陀罗，贪图享乐，感情纠葛极其复杂。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['贪狼', '陀罗'], 'zhi' => ['寅'], 'only_ming' => true],
        ],
        [
            'id' => 'RI_YUE_FAN_BEI',
            'name' => '日月反背格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '日月反背坐命，六亲缘薄，劳碌奔波。',
            'rule' => ['type' => 'ri_yue_fan_bei'],
        ],
        [
            'id' => 'YIN_CAI_CAO_DAO',
            'name' => '因财操刀格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '武曲、七杀、擎羊同宫，为求财不择手段引发冲突。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['武曲', '七杀', '擎羊']],
        ],
        [
            'id' => 'LU_FENG_CHONG_PO',
            'name' => '禄逢冲破格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '禄被空劫化忌冲破，吉处藏凶，得而复失。',
            'rule' => ['type' => 'lu_chong_po'],
        ],
        [
            'id' => 'JU_JI_HUA_YOU',
            'name' => '巨机化酉格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '机巨酉宫化忌，奔波劳碌，口舌是非。',
            'rule' => ['type' => 'ju_ji_hua_you'],
        ],

        // ======= 特殊吉格/杂格 =======
        // 注：文桂文华格（昌曲夹命）已与"昌曲夹命格(CHANG_QU_JIA_MING)"规则完全相同，已合并删除。
        [
            'id' => 'YUE_LANG_TIAN_MEN',
            'name' => '月朗天门格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '太阴在亥宫守命，清白温雅，男得贤妻女贵命妇。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '太阴', 'zhi' => ['亥'], 'only_ming' => true, 'brightness' => ['庙'], 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'RI_LI_ZHONGTIAN',
            'name' => '日丽中天格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '太阳在午宫守命，大富大贵，光明磊落。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '太阳', 'zhi' => ['午'], 'only_ming' => true, 'brightness' => ['庙'], 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'WU_QU_SHOU_YUAN',
            'name' => '武曲守垣格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '武曲在戌辰丑未坐命，刚毅果决，适合金融技术。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '武曲', 'zhi' => ['戌','辰','丑','未'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'TIAN_YI_GONG_MING',
            'name' => '天乙拱命格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '天魁、天钺三方四正拱照命宫，一生多逢贵人相助；若魁钺之一直坐命宫，贵气尤为直接有力。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['天魁', '天钺']],
        ],

        // ======= 特殊凶格/破格 =======
        [
            'id' => 'HUO_LING_JIA_MING',
            'name' => '火铃夹命格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '火铃夹命，突发状况、暴躁、血光。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['火星', '铃星']],
        ],
        [
            'id' => 'YANG_TUO_JIA_MING',
            'name' => '羊陀夹命格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '羊陀夹命，压力大、是非多、一生劳碌。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['擎羊', '陀罗']],
        ],
        [
            'id' => 'JIE_KONG_JIA_JI',
            'name' => '劫空夹忌格',
            'category' => '凶险格',
            'jiXiong' => '大凶',
            'desc' => '空劫夹化忌，钱财破败极为严重。',
            'rule' => ['type' => 'jie_kong_jia_ji'],
        ],
        [
            'id' => 'JI_JU_MAOYOU',
            'name' => '极居卯酉格',
            'category' => '动荡格',
            'jiXiong' => '中性',
            'desc' => '紫微卯酉坐命，对宫破军相照，性格特立独行，大起大落。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '紫微', 'zhi'  => ['卯', '酉'], 'only_ming' => true],
        ],
        [
            'id' => 'KE_XING_XUN_FENG',
            'name' => '科星巡逢格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '化科会聚，名声显扬，适合文化传播。',
            'rule' => ['type' => 'ke_xing_xun_feng'],
        ],
        [
            'id' => 'CAI_YIN_ZUO_YUAN',
            'name' => '财印坐垣格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '武曲天相寅申巳亥守命，精于理财。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['武曲', '天相'], 'zhi' => ['寅','申','巳','亥'], 'only_ming' => true],
        ],

        // ======= 其他原有格局 =======
        [
            'id' => 'ZI_TAN_TONGXING',
            'name' => '紫贪同宫格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '紫微与贪狼同宫，才艺卓绝或桃花旺盛。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['紫微', '贪狼'], 'only_ming' => true],
        ],
        [
            'id' => 'ZI_SHA_TONGXING',
            'name' => '紫杀同宫格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '紫微与七杀同宫，决断力超凡，铁腕领导。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['紫微', '七杀'], 'only_ming' => true],
        ],
        [
            'id' => 'ZI_PO_TONGXING',
            'name' => '紫破同宫格',
            'category' => '动荡格',
            'jiXiong' => '中性',
            'desc' => '紫微与破军同宫，颠覆与创新并存，逆流而上。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['紫微', '破军'], 'only_ming' => true],
        ],
        [
            'id' => 'LIAN_FU_TONGXING',
            'name' => '廉府同宫格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '廉贞天府同宫，理财能力极强，能守能攻。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['廉贞', '天府'], 'only_ming' => true],
        ],
        [
            'id' => 'TONG_YIN_TONGXING',
            'name' => '同阴同宫格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天同太阴子宫，情感细腻，宜艺术工作。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['天同', '太阴'], 'zhi' => ['子'], 'only_ming' => true],
        ],
        [
            'id' => 'TONG_LIANG_TONGXING',
            'name' => '同梁同宫格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天同天梁丑未，性格温厚，多为幕僚参谋。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['天同', '天梁'], 'zhi' => ['丑', '未'], 'only_ming' => true],
        ],
        [
            'id' => 'TIAN_JI_RUYUAN',
            'name' => '天机入垣格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天机卯辰坐命，思维活跃，谋略过人。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '天机', 'zhi' => ['卯', '辰'], 'only_ming' => true],
        ],
        [
            'id' => 'LIAN_SHA_TONGXING',
            'name' => '廉杀同宫格',
            'category' => '动荡格',
            'jiXiong' => '中性',
            'desc' => '廉贞与七杀同宫，魄力超群，富贵险中求。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['廉贞', '七杀'], 'only_ming' => true],
        ],
        [
            'id' => 'RIYUE_SHOUGONG',
            'name' => '日月守拱命格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '太阳太阴三方拱照命宫，名誉极佳。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['太阳', '太阴'], 'exclude_ming' => true],
        ],
        [
            'id' => 'SHEN_MING_TONGGONG',
            'name' => '身命同宫格',
            'category' => '贵权格',
            'jiXiong' => '吉',
            'desc' => '命宫与身宫同宫，意志坚定，能量集中。',
            'rule' => ['type' => 'shen_ming_tonggong'],
        ],
        [
            'id' => 'TIAN_LIANG_ZAICHEN',
            'name' => '天梁坐辰格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天梁坐命在辰，清廉正直，宜清贵行业。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '天梁', 'zhi' => ['辰'], 'only_ming' => true],
        ],
        [
            'id' => 'PO_JUN_HUALU',
            'name' => '破军化禄格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '破军化禄，革新变动求财，财运极佳。',
            'rule' => ['type' => 'sihua_in_ming_sanfang', 'star' => '破军', 'sihua' => '禄'],
        ],
        [
            'id' => 'TANGLANG_HUAQUAN',
            'name' => '贪狼化权格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '贪狼化权，才艺且掌权，极具个人魅力。',
            'rule' => ['type' => 'sihua_in_ming_sanfang', 'star' => '贪狼', 'sihua' => '权'],
        ],
        [
            'id' => 'TAIYIN_RUMIAO',
            'name' => '太阴入庙格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '太阴子丑坐命，直觉敏锐，财帛丰盈。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '太阴', 'zhi' => ['子', '丑'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'TIANFU_ZUOCHENG',
            'name' => '天府坐垣格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '天府申子坐命，善于积累，多有房产。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '天府', 'zhi' => ['申', '子', '午'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'ZUO_GUI_XIANG_GUI',
            'name' => '坐贵向贵格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '天魁、天钺夹命，如众星拱月，贵人不断。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['天魁', '天钺']],
        ],
        [
            'id' => 'ZUO_YOU_GONG_MING',
            'name' => '左右拱命格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '左辅、右弼三方拱照，左右逢源，领导力强。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['左辅', '右弼'], 'exclude_ming' => true],
        ],
        [
            'id' => 'LUAN_XI_JIA_MING',
            'name' => '鸾喜夹命格',
            'category' => '桃花格',
            'jiXiong' => '吉',
            'desc' => '红鸾、天喜夹命，异性缘佳，桃花不断。',
            'rule' => ['type' => 'star_jiajiu_ming', 'star_ming' => null, 'jia_stars' => ['红鸾', '天喜']],
        ],
        [
            'id' => 'TAN_LANG_RUMIAO',
            'name' => '贪狼入庙格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '贪狼子午坐命，多才多艺，善交际。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '贪狼', 'zhi' => ['子', '午'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'LU_CUN_ZUO_MING',
            'name' => '禄存坐命格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '禄存守命，守财稳健，理财得宜。',
            'rule' => ['type' => 'lu_cun_zuo_ming'],
        ],
        [
            'id' => 'WEN_CHANG_RUMIAO',
            'name' => '文昌坐命格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '文昌辰酉坐命，文采出众，利升学。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '文昌', 'zhi' => ['辰', '酉'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'LIAN_ZHEN_HUALU',
            'name' => '廉贞化禄格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '廉贞化禄，以权谋财，适合政商两栖。',
            'rule' => ['type' => 'sihua_in_ming_sanfang', 'star' => '廉贞', 'sihua' => '禄'],
        ],
        [
            'id' => 'MING_WU_ZHENG_YAO',
            'name' => '命无正曜格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '命宫无十四主星坐守，性格多变，漂泊奔波。',
            'rule' => ['type' => 'ming_wu_zheng_yao'],
        ],
        [
            'id' => 'RI_CHU_FU_SANG',
            'name' => '日出扶桑格',
            'category' => '贵权格',
            'jiXiong' => '大吉',
            'desc' => '太阳在卯宫坐命，朝气蓬勃，少年成名。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '太阳', 'zhi' => ['卯'], 'only_ming' => true, 'brightness' => ['庙'], 'no_sha' => 'same_palace'],
        ],
        [
            'id' => 'JUNZI_ZAIYE',
            'name' => '君子在野格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '擎羊/陀罗/火星/铃星之一入命落陷，困顿奔波。',
            'rule' => ['type' => 'sha_star_xian_ming'],
        ],
        [
            'id' => 'MA_LUO_KONG_WANG',
            'name' => '马落空亡格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '天马坐命同宫见空劫，奔波却终成空。',
            'rule' => ['type' => 'ma_luo_kong_wang'],
        ],
        [
            'id' => 'LU_SHANG_MAI_SHI',
            'name' => '路上埋尸格',
            'category' => '凶险格',
            'jiXiong' => '大凶',
            'desc' => '廉贞七杀丑未坐命见煞忌，主交通意外、血光。',
            'rule' => ['type' => 'lu_shang_mai_shi'],
        ],

        // ======= 全新补充格局 =======
        [
            'id' => 'MING_LU_AN_LU',
            'name' => '明禄暗禄格',
            'category' => '财富格',
            'jiXiong' => '大吉',
            'desc' => '命宫有禄且暗合宫亦有禄，一生财源隐秘而丰厚。',
            'rule' => ['type' => 'ming_lu_an_lu'],
        ],
        [
            'id' => 'LU_WEN_GONG_MING',
            'name' => '禄文拱命格',
            'category' => '财富格',
            'jiXiong' => '吉',
            'desc' => '禄存守命，三方有昌曲拱照，富贵且文采出众。',
            'rule' => ['type' => 'stars_in_sanfang_ming', 'stars' => ['文昌', '文曲'], 'with_lu' => true, 'lu_type' => '禄存'],
        ],
        // 注：魁钺夹命格（KUI_YUE_JIA_MING）已与"坐贵向贵格(ZUO_GUI_XIANG_GUI)"规则完全相同，已合并删除。
        [
            'id' => 'KE_MING_HUI_LU',
            'name' => '科名会禄格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '化科坐命，三方见化禄或禄存，因才学得名利。',
            'rule' => ['type' => 'ke_ming_hui_lu'],
        ],
        [
            'id' => 'JI_LIANG_TONGGONG',
            'name' => '机梁同宫格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '天机天梁辰戌同守命宫，机谋多辩，善统筹。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['天机', '天梁'], 'zhi' => ['辰', '戌'], 'only_ming' => true],
        ],
        [
            'id' => 'YUE_SHENG_CANG_HAI',
            'name' => '月生沧海格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '太阴子宫坐命（庙旺），仪表堂堂，一生清贵。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '太阴', 'zhi' => ['子'], 'only_ming' => true, 'brightness' => ['庙', '旺']],
        ],
        [
            'id' => 'DAN_FENG_CHAO_YANG',
            'name' => '丹凤朝阳格',
            'category' => '文才格',
            'jiXiong' => '吉',
            'desc' => '日月辰戌拱照，仪表出众，少年成名。',
            'rule' => ['type' => 'dan_feng_chao_yang'],
        ],
        [
            'id' => 'JU_LIANG_PIAO_DANG',
            'name' => '巨梁飘荡格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '天梁巳亥坐命，放荡不羁，一生多飘泊。',
            'rule' => ['type' => 'star_in_ming_zhi', 'star' => '天梁', 'zhi' => ['巳', '亥'], 'only_ming' => true],
        ],
        [
            'id' => 'ZHEN_TAN_XIAN_DI',
            'name' => '贞贪陷地格',
            'category' => '凶险格',
            'jiXiong' => '凶',
            'desc' => '廉贞贪狼巳亥同宫坐命，极其不安定，易招是非。',
            'rule' => ['type' => 'stars_same_palace', 'stars' => ['廉贞', '贪狼'], 'zhi' => ['巳', '亥'], 'only_ming' => true],
        ],
        [
            'id' => 'LIANG_ZHONG_HUA_GAI',
            'name' => '两重华盖格',
            'category' => '特殊格',
            'jiXiong' => '中性',
            'desc' => '命宫华盖伴空劫，清高孤独，与宗教玄学有缘。',
            'rule' => ['type' => 'liang_zhong_hua_gai'],
        ],
    ];
}