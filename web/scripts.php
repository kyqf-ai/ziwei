<?php
function getJavaScript() {
    return '
        function setupMobileMenuEvents() {
            const menuBtn = document.getElementById("mobileMenuBtn");
            const sidebar = document.getElementById("sidebar");
            const closeSidebarBtn = document.getElementById("closeSidebarBtn");
            if (!menuBtn || !sidebar) return;
            menuBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                sidebar.classList.toggle("open");
                document.body.style.overflow = sidebar.classList.contains("open") ? "hidden" : "";
            });
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener("click", function(e) {
                    e.stopPropagation();
                    sidebar.classList.remove("open");
                    document.body.style.overflow = "";
                });
            }
            document.addEventListener("click", function(e) {
                if (window.innerWidth <= 1024 && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                    sidebar.classList.remove("open");
                    document.body.style.overflow = "";
                }
            });
            sidebar.addEventListener("click", function(e) { e.stopPropagation(); });
        }

        function updateMobileMenuLayout() {
            const menuBtn = document.getElementById("mobileMenuBtn");
            const sidebar = document.getElementById("sidebar");
            const closeSidebarBtn = document.getElementById("closeSidebarBtn");
            if (!menuBtn || !sidebar) return;
            if (window.innerWidth <= 1024) {
                menuBtn.style.display = "flex";
                if (closeSidebarBtn) closeSidebarBtn.style.display = "flex";
            } else {
                sidebar.classList.add("open");
                menuBtn.style.display = "none";
                if (closeSidebarBtn) closeSidebarBtn.style.display = "none";
                document.body.style.overflow = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            setupMobileMenuEvents();
            updateMobileMenuLayout();
            initMobileFeatures();
        });
        window.addEventListener("resize", updateMobileMenuLayout);

        function initMobileFeatures() {
            const helpPanel = document.getElementById("mobileHelpPanel");
            if (helpPanel) {
                const closeHelpBtn = helpPanel.querySelector(".close-help-btn");
                if (closeHelpBtn) {
                    closeHelpBtn.addEventListener("click", function() {
                        helpPanel.classList.remove("show");
                    });
                }
                helpPanel.addEventListener("click", function(e) {
                    if (e.target === helpPanel) helpPanel.classList.remove("show");
                });
            }
            // 长按复制星曜名称
            document.addEventListener("touchstart", function(e) {
                const star = e.target.closest(".star");
                if (star) {
                    const touchTimer = setTimeout(function() {
                        const starName = star.querySelector(".star-name")?.textContent || "";
                        if (starName && navigator.clipboard) {
                            navigator.clipboard.writeText(starName).then(function() {
                                showToast("已复制: " + starName);
                            });
                        }
                    }, 800);
                    const cancelTimer = function() {
                        clearTimeout(touchTimer);
                        document.removeEventListener("touchend", cancelTimer);
                        document.removeEventListener("touchmove", cancelTimer);
                    };
                    document.addEventListener("touchend", cancelTimer);
                    document.addEventListener("touchmove", cancelTimer);
                }
            });
        }

        function showToast(msg) {
            const toast = document.createElement("div");
            toast.textContent = msg;
            toast.style.cssText = "position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:white;padding:12px 24px;border-radius:25px;z-index:10000;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,0.3);";
            document.body.appendChild(toast);
            setTimeout(function() { document.body.removeChild(toast); }, 2000);
        }

        function toggleDateType() {
            const type = document.getElementById("dateType").value;
            document.getElementById("leapMonthContainer").style.display = (type === "lunar") ? "block" : "none";
            if (type === "solar") document.getElementById("isLeapMonth").checked = false;
        }

        function getHourGan(dayGan, hourIdx) {
            const map = {"甲":0,"己":0,"乙":2,"庚":2,"丙":4,"辛":4,"丁":6,"壬":6,"戊":8,"癸":8};
            const gans = ["甲","乙","丙","丁","戊","己","庚","辛","壬","癸"];
            return gans[(map[dayGan] + hourIdx) % 10];
        }

        function prepareData() {
            const dtStr = document.getElementById("birth_datetime").value;
            if (!dtStr) throw new Error("请选择日期");
            const dateType = document.getElementById("dateType").value;
            const ziMethod = document.getElementById("zi_shi_method").value;
            const isLeapCheck = document.getElementById("isLeapMonth").checked;
            const [dPart, tPart] = dtStr.split("T");
            const [Y, M, D] = dPart.split("-").map(Number);
            const [h, m] = tPart.split(":").map(Number);
            let solar, lunar, panLunar;
            let isLateZi = false;
            if (dateType === "solar") {
                solar = Solar.fromYmd(Y, M, D);
                lunar = solar.getLunar();
            } else {
                try {
                    lunar = Lunar.fromYmd(Y, isLeapCheck ? -M : M, D);
                } catch (err) {
                    alert("【日期错误】该农历日期无效。可能是" + Y + "年不存在闰" + M + "月，或该月没有" + D + "日。");
                    throw new Error("日期错误");
                }
                if (isLeapCheck && lunar.getMonth() !== -M) {
                    alert("【日期错误】" + Y + "年农历不存在闰" + M + "月，请取消勾选闰月！");
                    throw new Error("日期错误");
                }
                solar = lunar.getSolar();
            }
            if (h === 23 && ziMethod === "auto") {
                isLateZi = true;
                panLunar = solar.next(1).getLunar();
            } else {
                panLunar = lunar;
            }
            const bazi = panLunar.getBaZi();
            const yearGZ = bazi[0];
            const zhiIdx = (h >= 23 || h < 1) ? 0 : Math.floor((h + 1) / 2);
            const zhis = ["子","丑","寅","卯","辰","巳","午","未","申","酉","戌","亥"];
            const hourZhi = zhis[zhiIdx];
            const dayGZ = panLunar.getDayInGanZhi();
            const dayGan = dayGZ.charAt(0);
            const hourGan = getHourGan(dayGan, zhiIdx);
            document.getElementById("year_gan").value = yearGZ.charAt(0);
            document.getElementById("year_zhi").value = yearGZ.charAt(1);
            document.getElementById("hour_gan").value = hourGan;
            document.getElementById("hour_zhi").value = hourZhi;
            const isLeapNow = panLunar.getMonth() < 0;
            document.getElementById("isLeapMonth").checked = isLeapNow;
            document.getElementById("lunar_month").value = Math.abs(panLunar.getMonth());
            document.getElementById("lunar_day").value = panLunar.getDay();
            const fullBazi = yearGZ + "年 " + panLunar.getMonthInGanZhi() + "月 " + dayGZ + "日 " + hourGan + hourZhi + "时";
            document.getElementById("bazi_str").value = fullBazi;
            document.getElementById("birth_date").value = solar.toYmd();
            document.getElementById("is_late_zi").value = isLateZi ? 1 : 0;
            return true;
        }

        async function generateChart() {
            try {
                prepareData();
                const btn = document.getElementById("submitBtn");
                const resultDiv = document.getElementById("panResult");
                btn.disabled = true;
                btn.innerHTML = "<i class=\"fas fa-spinner fa-spin\"></i> 计算中...";
                const formData = new FormData(document.getElementById("mainForm"));
                const response = await fetch("", { method: "POST", body: formData });
                const html = await response.text();
                resultDiv.innerHTML = html;
                if (window.innerWidth <= 1024) {
                    setTimeout(() => {
                        const sidebar = document.getElementById("sidebar");
                        if (sidebar) sidebar.classList.remove("open");
                        document.body.style.overflow = "";
                    }, 100);
                }
                updateShape();
                setTimeout(() => { setupPalaceClickHandlers(); initMobileFeatures(); }, 100);
                if (window.innerWidth < 1024) {
                    resultDiv.scrollIntoView({ behavior: "smooth" });
                }
            } catch (e) {
                if (!e.message.includes("日期错误")) alert("排盘出错：" + e.message);
                console.error(e);
            } finally {
                document.getElementById("submitBtn").disabled = false;
                document.getElementById("submitBtn").innerHTML = "<i class=\"fas fa-calculator\"></i> 生成命盘";
            }
        }

        function updateShape() {
            const val = document.querySelector("input[name=\"shape_setting\"]:checked").value;
            const grid = document.getElementById("panGrid");
            if (grid) grid.className = "pan-grid " + (val === "square" ? "shape-square" : "shape-round");
        }

        function calculateSanFangSiZheng(index) {
            return [...new Set([index, (index+4)%12, (index+8)%12, (index+6)%12])];
        }

        function setupPalaceClickHandlers() {
            const cells = document.querySelectorAll(".cell, .center-cell");
            cells.forEach(cell => {
                cell.removeEventListener("click", handleCellClick);
                cell.style.cursor = "pointer";
                cell.addEventListener("click", handleCellClick);
            });
            function handleCellClick(e) {
                e.stopPropagation();
                document.querySelectorAll(".cell, .center-cell").forEach(el => el.classList.remove("highlight","current-highlight"));
                this.classList.add("current-highlight");
                const index = parseInt(this.dataset.index);
                if (isNaN(index)) return;
                calculateSanFangSiZheng(index).forEach(idx => {
                    if (idx !== index) {
                        const t = document.querySelector("[data-index=\"" + idx + "\"]");
                        if (t) t.classList.add("highlight");
                    }
                });
            }
            document.addEventListener("click", function(e) {
                if (!e.target.closest(".cell") && !e.target.closest(".center-cell")) {
                    document.querySelectorAll(".cell, .center-cell").forEach(el => el.classList.remove("highlight","current-highlight"));
                }
            });
        }

        // ===== AI 文本报告生成 =====
        async function getTextReport() {
            const resultDiv = document.getElementById("panResult");
            const btn = document.querySelector(".ai-report-btn");
            try {
                prepareData();
                if (window.innerWidth <= 1024) {
                    setTimeout(() => {
                        const sidebar = document.getElementById("sidebar");
                        if (sidebar) sidebar.classList.remove("open");
                        document.body.style.overflow = "";
                    }, 100);
                }
                btn.disabled = true;
                btn.innerHTML = "<i class=\"fas fa-spinner fa-spin\"></i> 生成报告中...";
                resultDiv.innerHTML = "<div class=\"loading\"><i class=\"fas fa-robot fa-spin\" style=\"font-size:40px;margin-bottom:20px;\"></i><p>正在连接排盘接口，生成详细解读数据...</p></div>";
                const formData = new FormData(document.getElementById("mainForm"));
                const response = await fetch("?action=api", { method: "POST", body: formData });
                const json = await response.json();
                if (!json.success) throw new Error(json.error || "接口返回错误");
                const basic = json.basic;
                const palaces = json.palaces;
                const info = json.info;
                const geJu = json.ge_ju || [];

                let r = "### 紫微斗数命理分析报告\\n\\n";
                const solarY = (basic.birth_info.solar_date || "").substring(0, 4);
                r += "#### 🔮 基本信息\\n";
                r += "- **姓名**：" + basic.name + " (" + basic.gender + ")\\n";
                r += "- **出生年份**：" + basic.birth_info.year_gan + basic.birth_info.year_zhi + "年 " + (solarY ? "(" + solarY + "年)" : "") + "\\n";
                r += "- **出生时间**：公历 " + (basic.birth_info.solar_date || "") + " / 农历 " + (basic.birth_info.traditional_lunar_date || "") + " " + basic.birth_info.hour_zhi + "时\\n";
                r += "- **四柱八字**：" + basic.bazi + "\\n";
                r += "- **五行局数**：" + basic.ming_ju + "\\n";
                r += "- **命主/身主**：" + basic.ming_zhu + " / " + basic.shen_zhu + "\\n";
                r += "- **命宫/身宫**：" + basic.ming_gong + " / " + basic.shen_gong + "\\n";
                r += "- **生肖**：" + (basic.birth_info.zodiac || "") + "\\n";
                if (info.lai_yin && info.lai_yin.gong) {
                    r += "- **来因宫**：" + info.lai_yin.gong + " (" + info.lai_yin.gan + info.lai_yin.zhi + ")\\n";
                }
                r += "\\n";

                // 格局信息
                if (geJu.length > 0) {
                    r += "#### ⭐ 命盘格局（系统自动扫描）\\n";
                    geJu.forEach(g => {
                        r += "- **" + g.name + "**【" + g.jiXiong + "】：" + g.desc;
                          if (g.detail && g.detail.is_broken) {
                            r += "（⚠️ 破格：" + g.detail.broken_reason + "）";
                          }
                        r += "\\n";
                    });
                    r += "\\n";
                }

                r += "#### 🏛️ 十二宫位星曜配置\\n";
                palaces.forEach(p => {
                    let tags = [];
                    if (p.is_ming) tags.push("命宫");
                    if (p.is_shen) tags.push("身宫");
                    const tagStr = tags.length > 0 ? "【" + tags.join("+") + "】" : "";
                    r += "##### " + p.name + " (" + p.pos + ") " + tagStr + "\\n";
                    let majorStars = [];
                    if (p.stars.major && p.stars.major.length > 0) {
                        p.stars.major.forEach(s => {
                            let str = "**" + s.name + "**";
                            if (s.brightness && s.brightness !== "-") str += "[" + s.brightness + "]";
                            if (s.sihua) str += "(化" + s.sihua + ")";
                            majorStars.push(str);
                        });
                    } else if (p.stars.borrowed && p.stars.borrowed.length > 0) {
                        majorStars.push("*空宫，借对宫主星*：");
                        p.stars.borrowed.forEach(s => {
                            let str = s.name;
                            if (s.brightness && s.brightness !== "-") str += "[" + s.brightness + "]";
                            if (s.sihua) str += "(化" + s.sihua + ")";
                            majorStars.push(str);
                        });
                    } else { majorStars.push("*无主星*"); }
                    r += "- **主星**：" + majorStars.join("、") + "\\n";
                    let minorStars = [];
                    if (p.stars.minor && p.stars.minor.length > 0) {
                        p.stars.minor.forEach(s => {
                            let str = s.name;
                            if (s.brightness && s.brightness !== "-") str += "[" + s.brightness + "]";
                            if (s.sihua) str += "(化" + s.sihua + ")";
                            minorStars.push(str);
                        });
                    }
                    if (minorStars.length > 0) r += "- **辅佐煞曜**：" + minorStars.join("、") + "\\n";
                    let extraStars = (p.stars.extra || []).map(s => s.name);
                    if (extraStars.length > 0) r += "- **杂曜**：" + extraStars.join("、") + "\\n";
                    const g = p.gods;
                    if (g.cs && g.cs.trim()) r += "- **长生十二神**：" + g.cs + "\\n";
                    let otherShensha = [];
                    if (g.boshi && g.boshi.trim()) otherShensha.push("博士:" + g.boshi);
                    if (g.suijian && g.suijian.trim()) otherShensha.push("岁建:" + g.suijian);
                    if (g.jiangxing && g.jiangxing.trim()) otherShensha.push("将星:" + g.jiangxing);
                    if (otherShensha.length > 0) r += "- **其他神煞**：" + otherShensha.join(" | ") + "\\n";
                    if (p.liu_nian_ages) r += "- **流年**：" + p.liu_nian_ages + "\\n";
                    if (p.ages) r += "- **小限**：" + p.ages + "\\n";
                    if (p.daxian) r += "- **大限**：" + p.daxian + "岁\\n";
                    r += "\\n";
                });

                // 来因宫与暗合宫分析
                r += "#### 🌿 来因宫与暗合宫分析\\n";
                if (info.lai_yin && info.lai_yin.gong) {
                    const laiGong = info.lai_yin.gong;
                    r += "**来因宫**：位于 **" + laiGong + "**。\\n";
                    const laiMeanings = {"命宫":"自我实现、个人意志是核心功课。","兄弟宫":"手足情谊、竞争合作为今生重点。","夫妻宫":"情感模式、婚姻关系是主要修行。","子女宫":"家庭传承、合伙、桃花缘分为核心。","财帛宫":"金钱观、赚钱方式带来成长。","疾厄宫":"身体是最大资本，需注意健康压力平衡。","迁移宫":"异地发展、外界评价、机遇把握是主题。","交友宫":"人际关系、部属缘，易成他人依靠。","官禄宫":"事业成就、社会地位是价值感来源。","田宅宫":"家庭责任、房产祖荫，为家人撑天。","福德宫":"精神世界、因果福报，内心富足更重要。","父母宫":"父母缘、长辈关系，孝顺与独立需平衡。"};
                    r += "> " + (laiMeanings[laiGong] || "此宫位需结合主星四化深入分析。") + "\\n\\n";
                }
                const mingPalace = palaces.find(p => p.is_ming === true);
                if (mingPalace && mingPalace.an_he_gong) {
                    r += "**命宫暗合**：命宫暗合 **" + mingPalace.an_he_gong + "**，此宫位是你潜意识里割舍不断的牵连。\\n\\n";
                }

                r += "#### ⭐ AI分析指令\\n```\\n";
                r += "你是紫微斗数命理大师。请严格基于上方完整排盘数据进行深度分析，输出不少于2500字专业报告。\\n\\n";
                r += "【核心分析原则】\\n";
                r += "1. 坚守数据：所有论断必须有具体宫位星曜四化数据支撑，绝不捏造格局。\\n";
                r += "2. 格局优先：已检测到格局请在模块四重点深度解读，逐一说明对命主现实命运的投射。\\n";
                r += "3. 辩证解盘：吉星需看制化，凶星需看调和，空宫需借对宫综合论断。\\n\\n";
                r += "请按以下七大模块输出报告：\\n";
                r += "【模块一：命盘总纲】核心三大特质，最强/最弱宫位\\n";
                r += "【模块二：先天命格】命宫三方深度解析、四化能量、来因宫暗合宫\\n";
                r += "【模块三：十二宫穿透】逐宫深度分析（包含主星庙旺、四化、辅星、三方影响）\\n";
                r += "【模块四：格局定性】已检测格局深度解读+煞星淬炼分析\\n";
                r += "【模块五：大限流年】当前大限+近期流年+小限互动\\n";
                r += "【模块六：破局之道】3-5条定制实操建议（职业/心智/人际/健康/方位）\\n";
                r += "【模块七：结语】知命不认命的智慧总结\\n```\\n";

                const html = `
                    <div class="ai-report-card">
                        <div class="ai-report-header">
                            <div class="ai-title-area">
                                <h3><i class="fas fa-file-alt"></i> 紫微斗数文本报告</h3>
                                <p>完整排盘数据 + 格局分析，可直接用于深度解析</p>
                            </div>
                            <div class="ai-action-area">
                                <button class="ai-btn ai-btn-back" onclick="generateChart()">
                                    <i class="fas fa-redo"></i> 返回排盘
                                </button>
                                <button class="ai-btn ai-btn-copy" onclick="copyReport()">
                                    <i class="fas fa-copy"></i> 复制全部
                                </button>
                            </div>
                        </div>
                        <div class="ai-hint-box">
                            <i class="fas fa-info-circle"></i>
                            <span>一键复制下方完整文本，粘贴到 DeepSeek、Kimi、豆包等大模型中，即可获取大师级解析。</span>
                        </div>
                        <textarea id="reportTextarea" class="ai-textarea" spellcheck="false">${r}</textarea>
                        <div class="ai-report-footer">
                            <p class="ai-footer-hint"><i class="fas fa-lightbulb"></i> 提示：尽量避免手动删减文本，以免影响AI准确度</p>
                            <button class="ai-btn ai-btn-copy-main" onclick="copyReport()">
                                <i class="fas fa-copy"></i> 一键复制报告
                            </button>
                        </div>
                    </div>
                `;
                resultDiv.innerHTML = html;
                resultDiv.scrollIntoView({ behavior: "smooth" });
            } catch (e) {
                console.error(e);
                resultDiv.innerHTML = `<div class="loading" style="color:var(--accent)"><i class="fas fa-exclamation-triangle" style="font-size:40px;margin-bottom:20px;"></i><h3>生成失败</h3><p>${e.message}</p><button onclick="generateChart()" style="margin-top:20px;padding:10px 20px;background:var(--accent);color:white;border:none;border-radius:6px;cursor:pointer;">返回排盘</button></div>`;
            } finally {
                btn.disabled = false;
                btn.innerHTML = "<i class=\"fas fa-robot\"></i> 生成AI解读文本";
            }
        }

        async function copyReport() {
            const textarea = document.getElementById("reportTextarea");
            if (!textarea) return;
            const textToCopy = textarea.value.trim();
            const showSuccess = (msg = "✅ 已复制成功！") => {
                document.querySelectorAll(".ai-btn-copy,.ai-btn-copy-main").forEach(btn => {
                    const oldHTML = btn.innerHTML;
                    btn.innerHTML = "<i class=\"fas fa-check\"></i> " + msg;
                    btn.style.background = "#1b5e20"; btn.style.color = "#fff";
                    setTimeout(() => { btn.innerHTML = oldHTML; btn.style.background = ""; btn.style.color = ""; }, 2200);
                });
            };
            if (navigator.clipboard && window.isSecureContext) {
                try { await navigator.clipboard.writeText(textToCopy); showSuccess(); return; }
                catch(e) {}
            }
            try {
                const tmp = document.createElement("textarea");
                tmp.value = textToCopy;
                tmp.style.cssText = "position:fixed;top:0;left:0;opacity:0;width:1px;height:1px;";
                document.body.appendChild(tmp);
                tmp.focus(); tmp.select();
                if (tmp.setSelectionRange) tmp.setSelectionRange(0, textToCopy.length);
                const ok = document.execCommand("copy");
                document.body.removeChild(tmp);
                if (ok) { showSuccess("已复制成功！"); return; }
            } catch(err) {}
            textarea.focus(); textarea.select();
            if (textarea.setSelectionRange) textarea.setSelectionRange(0, textToCopy.length);
            showToast("✅ 已全选，请长按复制");
        }
    ';
}
