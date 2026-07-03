<?php
function getCSS() {
    return '
        :root {
            --border-color: #a1887f;
            --bg: #faf9f6;
            --text: #3e2723;
            --accent: #b71c1c;
            --ming-color: #c2185b;
            --shen-color: #388e3c;
            --major-bg: linear-gradient(180deg, #f3e5f5, #e1bee7);
            --major-text: #4a148c;
            --major-border: #ab47bc;
            --ji-bg: linear-gradient(180deg, #e0f2f1, #b2dfdb);
            --ji-text: #00695c;
            --ji-border: #26a69a;
            --sha-bg: linear-gradient(180deg, #fbe9e7, #ffccbc);
            --sha-text: #bf360c;
            --sha-border: #ff7043;
            --peach-bg: linear-gradient(180deg, #fce4ec, #f8bbd0);
            --peach-text: #880e4f;
            --peach-border: #ec407a;
            --luck-bg: linear-gradient(180deg, #f1f8e9, #dcedc8);
            --luck-text: #33691e;
            --luck-border: #8bc34a;
            --bad-bg: linear-gradient(180deg, #f5f5f5, #e0e0e0);
            --bad-text: #455a64;
            --bad-border: #90a4ae;
            --minor-bg: linear-gradient(180deg, #fafafa, #f5f5f5);
            --minor-text: #757575;
            --minor-border: #e0e0e0;
            --lu: #2e7d32; --quan: #1565c0; --ke: #ef6c00; --ji: #c62828;
            --highlight-color: rgba(255,245,157,0.7);
            --highlight-border: #ffca28;
            --hl-sanfang: rgba(255,243,224,0.9);
            --hl-current: rgba(255,235,238,0.9);
            --sp-xs:4px;--sp-sm:8px;--sp-md:16px;--sp-lg:24px;--sp-xl:32px;
            --rd-sm:4px;--rd-md:8px;--rd-lg:12px;--rd-full:50%;
            --shadow-card:0 10px 30px rgba(141,110,99,0.15);
            --trans:0.3s ease;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{font-size:16px;scroll-behavior:smooth;-webkit-text-size-adjust:100%;}
        body{background:var(--bg);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","Microsoft YaHei","Helvetica Neue",Helvetica,Arial,sans-serif;line-height:1.6;padding:var(--sp-md);min-height:100vh;overflow-x:hidden;touch-action:manipulation;}
        input,select,button,textarea{font-size:16px!important;max-width:100%;}
        .sidebar::-webkit-scrollbar{width:6px;}.sidebar::-webkit-scrollbar-track{background:transparent;}.sidebar::-webkit-scrollbar-thumb{background-color:rgba(0,0,0,0.2);border-radius:var(--rd-full);}
        .wrapper{display:flex;gap:var(--sp-lg);max-width:1440px;margin:0 auto;align-items:flex-start;transition:transform var(--trans);position:relative;}
        .sidebar{width:340px;flex-shrink:0;background:#fff;border-radius:var(--rd-lg);box-shadow:var(--shadow-card);position:sticky;top:var(--sp-md);z-index:100;max-height:calc(100vh - var(--sp-md)*2);overflow-y:auto;padding:0;}
        .sidebar-header{display:flex;justify-content:space-between;align-items:center;padding:var(--sp-lg) var(--sp-xl);border-bottom:1px solid #eee;background:#fff;position:sticky;top:0;z-index:10;}
        .sidebar h2{color:var(--text);font-size:1.75rem;margin:0;letter-spacing:1px;}
        .close-sidebar-btn{background:none;border:none;font-size:1.5rem;color:#666;padding:8px;cursor:pointer;display:none;border-radius:var(--rd-full);width:40px;height:40px;align-items:center;justify-content:center;}
        .close-sidebar-btn:hover{background:#f5f5f5;}
        .sidebar form{padding:var(--sp-xl);padding-top:0;}
        .main-content{flex:1;min-width:0;background:#fff;padding:var(--sp-lg);border-radius:var(--rd-lg);box-shadow:var(--shadow-card);overflow-x:auto;}
        .form-item{margin-bottom:var(--sp-md);}
        .form-item label{display:block;margin-bottom:var(--sp-xs);font-weight:600;color:#5d4037;font-size:0.875rem;}
        .form-item :is(input[type="text"],input[type="datetime-local"],select){width:100%;padding:var(--sp-sm) var(--sp-md);border:1px solid #d7ccc8;border-radius:var(--rd-md);font-size:0.9375rem;transition:var(--trans);background:#fff;-webkit-appearance:none;-moz-appearance:none;appearance:none;}
        .form-item :is(input[type="text"],input[type="datetime-local"],select):focus{outline:none;border-color:var(--border-color);box-shadow:0 0 0 3px rgba(141,110,99,0.2);}
        .checkbox-label{display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:0.9375rem;}
        .checkbox-label input[type="checkbox"]{width:18px;height:18px;cursor:pointer;margin:0;}
        .radio-group.inline{display:flex;flex-wrap:wrap;gap:var(--sp-md);margin-top:var(--sp-xs);}
        .radio-group.inline .radio-label{cursor:pointer;margin:0;position:relative;}
        .radio-group.inline .radio-label input{position:absolute;opacity:0;width:0;height:0;}
        .radio-group.inline .radio-label span{display:inline-block;padding:8px 24px;background:#f5f5f5;border:1px solid #d7ccc8;border-radius:var(--rd-md);transition:var(--trans);font-size:0.9375rem;color:#5d4037;}
        .radio-group.inline input:checked+span{background:var(--accent);color:#fff;font-weight:700;border-color:var(--accent);box-shadow:0 2px 8px rgba(183,28,28,0.3);}
        .button-container{display:flex;flex-direction:column;gap:var(--sp-sm);margin-top:var(--sp-lg);padding:0;background:#fff;}
        .submit-btn{width:100%;padding:var(--sp-md);background:var(--text);color:white;border:none;border-radius:var(--rd-md);font-weight:700;cursor:pointer;transition:var(--trans);display:flex;justify-content:center;gap:var(--sp-xs);font-size:1rem;-webkit-tap-highlight-color:transparent;}
        .submit-btn:hover:not(:disabled){background:#3e2723;transform:translateY(-1px);}
        .submit-btn:disabled{background:#bcaaa4;cursor:not-allowed;transform:none;}
        .submit-btn.ai-report-btn{background:#1565c0;}
        .submit-btn.ai-report-btn:hover{background:#0d47a1;}
        .pan-grid-container{width:100%;position:relative;}
        .pan-grid{display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:repeat(4,auto);gap:2px;background:var(--border-color);border:6px solid var(--border-color);border-radius:var(--rd-md);box-shadow:0 10px 25px rgba(0,0,0,0.1);width:100%;}
        .cell{background:#fff;padding:10px 6px 84px 6px;display:flex;flex-direction:column;position:relative;overflow:hidden;-webkit-tap-highlight-color:transparent;user-select:none;min-height:210px;}
        .gong-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;height:28px;border-bottom:1px solid #eee;padding-bottom:4px;}
        .gong-name{font-weight:900;font-size:1.05rem;color:var(--text);line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .tag{font-size:0.75rem;margin-left:2px;}
        .ming-tag{color:var(--ming-color);}.shen-tag{color:var(--shen-color);}
        .changsheng-container{display:flex;gap:2px;justify-content:flex-end;flex-wrap:wrap;}
        .changsheng-item{font-size:0.875rem;font-weight:600;background:#e3f2fd;color:#1565c0;padding:2px 6px;border-radius:4px;margin-bottom:2px;}
        .stars-container{flex:1;display:flex;flex-wrap:nowrap!important;align-content:flex-start;align-items:flex-start!important;gap:0!important;overflow:visible!important;padding:2px 0;min-height:60px;}
        .star{font-size:0.8125rem;font-weight:bold;display:inline-flex;flex-direction:column;align-items:center;position:relative;transition:transform 0.2s,box-shadow 0.2s;flex:0 1 20px!important;width:auto!important;min-width:14px!important;padding:1px 0 14px 0;margin:0 1px 8px 0;border-radius:var(--rd-sm);border:1px solid rgba(0,0,0,0.05);cursor:pointer;height:max-content!important;}
        .star-name{display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;min-height:20px;}
        .star-name span{display:block;line-height:1;height:1.2em;}
        .star:hover{transform:translateY(-2px);z-index:10;box-shadow:0 4px 8px rgba(0,0,0,0.1);}
        .star.major{background:var(--major-bg);color:var(--major-text);border-color:var(--major-border);box-shadow:0 0 6px rgba(156,39,176,0.3);}
        .star.ji{background:var(--ji-bg);color:var(--ji-text);border-color:var(--ji-border);}
        .star.sha{background:var(--sha-bg);color:var(--sha-text);border-color:var(--sha-border);}
        .star.peach{background:var(--peach-bg);color:var(--peach-text);border-color:var(--peach-border);}
        .star.luck{background:var(--luck-bg);color:var(--luck-text);border-color:var(--luck-border);}
        .star.bad{background:var(--bad-bg);color:var(--bad-text);border-color:var(--bad-border);}
        .star.minor{background:var(--minor-bg);color:var(--minor-text);border-color:var(--minor-border);}
        .star.small-text{font-size:0.75rem;min-height:36px;padding-bottom:16px;background:#f0f0f0;color:#666;}
        .star-brightness{position:absolute;bottom:2px;left:0;right:0;font-size:0.625rem;text-align:center;color:rgba(0,0,0,0.5);font-weight:normal;line-height:1;}
        .star-sihua{position:absolute;bottom:-22px;left:50%;transform:translateX(-50%);width:20px;height:20px;line-height:20px;border-radius:50%;color:white;font-size:0.7rem;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.2);border:1px solid #fff;font-weight:bold;z-index:5;display:flex;align-items:center;justify-content:center;}
        .star.sihua-禄 .star-sihua{background:var(--lu);}
        .star.sihua-权 .star-sihua{background:var(--quan);}
        .star.sihua-科 .star-sihua{background:var(--ke);}
        .star.sihua-忌 .star-sihua{background:var(--ji);}
        .gong-shensha{position:absolute;bottom:36px;left:6px;right:6px;display:flex;justify-content:space-between;align-items:center;gap:4px;padding:2px 0;border-top:1px solid #f0f0f0;border-bottom:1px solid #f0f0f0;background:rgba(255,255,255,0.9);z-index:2;height:24px;}
        .shensha-item{font-size:0.6875rem;font-weight:500;color:#616161;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;text-align:center;padding:1px 0;}
        .boshi-group{color:#7b1fa2;background:rgba(123,31,162,0.1);border-radius:2px;}
        .suijian-group{color:#ef6c00;background:rgba(239,108,0,0.1);border-radius:2px;}
        .jiang-group{color:#00796b;background:rgba(0,121,107,0.1);border-radius:2px;}
        .gong-footer{position:absolute;bottom:8px;left:8px;right:8px;display:flex;justify-content:space-between;align-items:center;padding-top:2px;border-top:1px solid #eee;}
        .gong-gz{font-size:0.9375rem;font-weight:bold;color:var(--text);}
        .gong-daxian{background:var(--text);color:#fff;font-size:0.75rem;padding:1px 8px;border-radius:10px;font-weight:bold;}
        .gong-liunian,.gong-ages{position:absolute;left:2px;right:2px;text-align:center;font-size:0.75rem;font-family:"Courier New",Consolas,monospace;letter-spacing:-0.5px;white-space:nowrap;z-index:2;}
        .gong-liunian{bottom:70px;color:#d84315;border-top:1px dashed #eee;padding-top:2px;}
        .gong-ages{bottom:56px;color:#607d8b;}
        .gong-shensha{bottom:36px!important;}
        .center-cell{grid-column:2/4;grid-row:2/4;background:linear-gradient(135deg,#fffbf0,#fff8e1);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:var(--sp-lg);border:2px solid #e0d6c2;border-radius:var(--rd-md);text-align:center;-webkit-tap-highlight-color:transparent;min-height:200px;}
        .cc-name{font-size:1.15rem;font-weight:900;color:var(--text);border-bottom:2px solid #dacbb8;padding-bottom:var(--sp-md);margin-bottom:var(--sp-md);width:100%;}
        .cc-row{display:flex;gap:var(--sp-sm);align-items:center;margin-bottom:var(--sp-xs);flex-wrap:wrap;justify-content:center;}
        .cc-bazi-grid{display:flex;justify-content:center;gap:var(--sp-md);width:100%;background:rgba(255,255,255,0.8);padding:var(--sp-md);border:1px solid #d7ccc8;border-radius:var(--rd-md);margin:var(--sp-md) 0;flex-wrap:wrap;}
        .bazi-col{display:flex;flex-direction:column;align-items:center;min-width:50px;}
        .bz-val{font-size:1.05rem;font-weight:900;color:var(--text);}
        .bz-label{font-size:0.75rem;color:#888;}
        .cc-birth-check,.cc-habit-lunar{font-size:0.8125rem;color:#666;margin:2px 0;}
        .cc-owners{margin-top:var(--sp-xs)!important;}
        .owner-item{display:flex;align-items:center;gap:4px;}
        .ol{font-size:0.75rem;color:#888;}.ov{font-weight:700;color:var(--text);}
        .cc-laiyin{background:rgba(46,125,50,0.08);border-radius:6px;padding:4px 10px;font-size:0.8125rem;}
        .laiyin-label{color:#2e7d32;font-weight:600;margin-right:6px;}
        .laiyin-value{color:#1b5e20;font-weight:700;}
        .cc-leap-note{font-size:0.8125rem;color:#e65100;margin-top:4px;}
        .divider{color:#ccc;}
        .cell.highlight,.center-cell.highlight{background:var(--hl-sanfang)!important;box-shadow:0 0 0 2px var(--highlight-border),0 0 15px rgba(255,152,0,0.3);border-color:var(--highlight-border)!important;z-index:10;}
        .cell.current-highlight,.center-cell.current-highlight{background:var(--hl-current)!important;box-shadow:0 0 0 2px #f44336,0 0 20px rgba(244,67,54,0.4);border-color:#f44336!important;z-index:15;}
        .shape-square .star{border-radius:2px;}.shape-square .gong-daxian{border-radius:2px;}.shape-square .star-sihua{border-radius:2px;}
        .shape-round .star{border-radius:6px;}.shape-round .gong-daxian{border-radius:10px;}.shape-round .star-sihua{border-radius:50%;}
        .mobile-menu-btn{display:none;position:fixed;top:var(--sp-md);right:var(--sp-md);width:50px;height:50px;background:var(--text);color:white;border:none;border-radius:var(--rd-full);z-index:999;justify-content:center;align-items:center;box-shadow:var(--shadow-card);cursor:pointer;-webkit-tap-highlight-color:transparent;}
        .mobile-menu-btn .menu-text{display:none;font-size:14px;margin-left:5px;}
        .mobile-help-panel{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;z-index:1000;opacity:0;visibility:hidden;transition:var(--trans);}
        .mobile-help-panel.show{opacity:1;visibility:visible;}
        .help-content{background:white;padding:var(--sp-xl);border-radius:var(--rd-lg);max-width:90%;max-height:80%;overflow-y:auto;box-shadow:var(--shadow-card);}
        .help-content h3{color:var(--text);margin-bottom:var(--sp-md);display:flex;align-items:center;gap:var(--sp-sm);}
        .help-content ul{list-style:none;padding:0;margin-bottom:var(--sp-lg);}
        .help-content li{padding:var(--sp-sm) 0;border-bottom:1px solid #eee;}
        .help-content li strong{color:var(--accent);}
        .close-help-btn{width:100%;padding:var(--sp-md);background:var(--text);color:white;border:none;border-radius:var(--rd-md);font-weight:bold;cursor:pointer;}
        .form-note{font-size:12px;color:#888;margin-top:3px;font-style:italic;}
        .form-hint{font-size:12px;color:#888;margin-top:3px;}
        .loading{text-align:center;padding:100px 0;color:#8d6e63;}
        .info-tip{font-size:13px;color:#666;background:#f5f5f5;padding:8px 12px;border-radius:6px;margin:15px 0;border-left:3px solid var(--accent);}

        /* ===== 格局面板样式 ===== */
        .geju-panel{margin-top:20px;border-radius:var(--rd-lg);border:1px solid #e0d6c2;background:#fffbf5;overflow:hidden;}
        .geju-panel.geju-empty{padding:20px;text-align:center;color:#888;font-size:0.9375rem;}
        .geju-header{background:linear-gradient(135deg,#3e2723,#5d4037);color:#fff;padding:14px 20px;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:10px;}
        .geju-count{margin-left:auto;font-size:0.8125rem;background:rgba(255,255,255,0.2);padding:2px 10px;border-radius:10px;font-weight:400;}
        .geju-note{background:#fff8e1;border-left:3px solid #ffca28;padding:8px 16px;font-size:0.8125rem;color:#5d4037;}
        .geju-category{padding:12px 16px 4px;}
        .geju-cat-title{font-weight:700;font-size:0.9375rem;color:var(--text);margin-bottom:10px;padding-bottom:6px;border-bottom:1px dashed #d7ccc8;}
        .geju-cards{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:10px;}
        .geju-card{flex:1 1 280px;border-radius:var(--rd-md);border:1px solid #e0d6c2;padding:12px;background:#fff;transition:box-shadow 0.2s;}
        .geju-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.1);}
        .geju-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
        .geju-name{font-weight:800;font-size:0.9375rem;color:var(--text);}
        .geju-jixiong{font-size:0.75rem;font-weight:700;padding:2px 8px;border-radius:10px;}
        .geju-desc{font-size:0.8125rem;color:#666;line-height:1.6;}
        .geju-detail{margin-top:6px;font-size:0.75rem;color:#1565c0;background:#e3f2fd;padding:4px 8px;border-radius:4px;}
        /* 吉凶色 */
        .jx-daji{border-color:#a5d6a7;background:linear-gradient(135deg,#f9fff9,#f1fff1);}
        .jx-daji .geju-jixiong{background:#2e7d32;color:#fff;}
        .jx-ji{border-color:#c8e6c9;}
        .jx-ji .geju-jixiong{background:#388e3c;color:#fff;}
        .jx-zhong{border-color:#ffe0b2;}
        .jx-zhong .geju-jixiong{background:#f57c00;color:#fff;}
        .jx-xiong{border-color:#ffcdd2;background:linear-gradient(135deg,#fff9f9,#fff5f5);}
        .jx-xiong .geju-jixiong{background:#c62828;color:#fff;}
        .jx-daxiong{border-color:#ef9a9a;background:linear-gradient(135deg,#fff5f5,#ffefef);}
        .jx-daxiong .geju-jixiong{background:#b71c1c;color:#fff;animation:pulse 1.5s infinite;}

        /* AI 报告面板 */
        .ai-report-card{background:#fff;padding:var(--sp-lg);border-radius:var(--rd-lg);max-width:900px;margin:0 auto;box-shadow:var(--shadow-card);}
        .ai-report-header{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #eee;padding-bottom:16px;margin-bottom:16px;gap:12px;flex-wrap:wrap;}
        .ai-title-area h3{margin:0;color:#3e2723;display:flex;align-items:center;gap:8px;font-size:1.05rem;}
        .ai-title-area h3 i{color:#1565c0;}
        .ai-title-area p{margin:4px 0 0;color:#666;font-size:0.8125rem;}
        .ai-action-area{display:flex;gap:8px;}
        .ai-btn{border:none;padding:6px 14px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-size:0.875rem;font-weight:600;transition:all 0.2s;white-space:nowrap;-webkit-tap-highlight-color:transparent;}
        .ai-btn-copy{background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9;}
        .ai-btn-copy:hover{background:#c8e6c9;}
        .ai-btn-back{background:#f3e5f5;color:#6a1b9a;border:1px solid #e1bee7;}
        .ai-btn-back:hover{background:#e1bee7;}
        .ai-hint-box{background:#f8f9fa;border-radius:8px;padding:12px;margin-bottom:16px;border-left:4px solid #1565c0;display:flex;align-items:flex-start;gap:8px;color:#555;font-size:0.875rem;line-height:1.5;}
        .ai-hint-box i{color:#1565c0;margin-top:3px;}
        .ai-textarea{width:100%;height:350px;padding:16px;border:1px solid #ddd;border-radius:8px;font-family:"Courier New",Consolas,monospace;line-height:1.6;font-size:0.875rem;resize:vertical;background:#fafafa;color:#333;}
        .ai-textarea:focus{outline:none;border-color:#1565c0;box-shadow:0 0 0 3px rgba(21,101,192,0.1);}
        .ai-report-footer{display:flex;justify-content:space-between;align-items:center;margin-top:16px;gap:12px;flex-wrap:wrap;}
        .ai-footer-hint{font-size:0.8125rem;color:#666;margin:0;display:flex;align-items:center;gap:4px;}
        .ai-btn-copy-main{background:#2e7d32;color:#fff;padding:10px 20px;box-shadow:0 2px 8px rgba(46,125,50,0.3);border-radius:8px;}
        .ai-btn-copy-main:hover{background:#1b5e20;transform:translateY(-1px);}

        @keyframes pulse{0%,100%{transform:translateX(-50%) scale(1)}50%{transform:translateX(-50%) scale(1.1)}}

        /* 响应式 */
        @media(max-width:1024px){
            body{padding:10px;padding-top:80px;}
            .wrapper{flex-direction:column;gap:var(--sp-md);}
            .mobile-menu-btn{display:flex;width:auto;padding:0 15px;border-radius:22px;}
            .mobile-menu-btn .menu-text{display:inline;}
            .sidebar{width:100%;max-width:100%;position:fixed;top:0;left:0;height:100vh;z-index:1000;transform:translateX(-100%);transition:transform var(--trans);border-radius:0;padding:0;max-height:100vh;box-shadow:none;border-right:1px solid #eee;}
            .sidebar.open{transform:translateX(0);}
            .close-sidebar-btn{display:flex;}
            .main-content{width:100%;margin-top:0;padding:var(--sp-md);}
            .pan-grid-container{overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;padding-bottom:25px!important;}
            .pan-grid{min-width:860px!important;}
            .cell{min-height:210px!important;padding-bottom:88px!important;}
            .gong-name{font-size:1.125rem!important;}
            .star{font-size:0.8125rem!important;flex:0 1 18px!important;min-width:14px!important;width:auto!important;margin-bottom:8px!important;}
            .star-sihua{width:20px!important;height:20px!important;font-size:0.7rem!important;line-height:20px!important;bottom:-22px!important;}
            .gong-liunian{bottom:70px!important;font-size:0.65rem!important;}
            .gong-ages{bottom:54px!important;font-size:0.65rem!important;}
            .gong-shensha{bottom:32px!important;height:20px!important;}
            .button-container{margin-top:auto;padding-top:var(--sp-lg);position:sticky;bottom:0;background:#fff;padding-bottom:var(--sp-sm);}
            .geju-cards{flex-direction:column;}
            .geju-card{flex:none;}
        }
        @media(max-width:768px){
            body{padding-top:70px;padding-left:5px;padding-right:5px;}
            .star{font-size:0.6875rem!important;}
            .gong-gz{font-size:0.8125rem;}
            .geju-header{font-size:0.9rem;}
        }
        @media(max-width:480px){
            body{padding-top:60px;padding-left:0;padding-right:0;}
            .pan-grid{border-width:4px;}
        }
        @media(max-width:600px){
            .ai-report-card{padding:16px;border-radius:12px;}
            .ai-report-header{flex-direction:column;align-items:flex-start;gap:16px;}
            .ai-action-area{width:100%;justify-content:flex-end;}
            .ai-action-area .ai-btn{flex:1;padding:10px;}
            .ai-textarea{height:280px;font-size:0.8125rem;}
            .ai-report-footer{flex-direction:column;align-items:stretch;gap:16px;}
            .ai-btn-copy-main{width:100%;padding:14px;font-size:1rem;}
        }
    ';
}
