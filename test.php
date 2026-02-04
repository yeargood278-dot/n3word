<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>BOSS挑战关 - 50题模式</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .progress-container { width: 100%; height: 20px; background: #eee; border-radius: 10px; margin-bottom: 20px; position: relative; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, #d35400, #e67e22); width: 0%; border-radius: 10px; transition: 0.3s; }
        .progress-text { position: absolute; width: 100%; text-align: center; font-size: 0.7rem; line-height: 20px; color: #333; font-weight: bold; }
        .option-btn { background:#fff; padding:15px; border:2px solid #eee; border-radius:10px; cursor:pointer; margin-bottom:10px; font-weight:bold; transition: 0.1s;}
        .option-btn:hover { border-color: #d35400; }
        .wrong-item { background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 10px; border-left: 5px solid #f44336; text-align: left; }
        .correct-ans { color: #4caf50; font-weight: bold; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">⬅ 退出挑战</a>
        <div id="header-title">BOSS挑战中 (0/50) 👹</div>
    </header>

    <div class="container" id="quiz-screen">
        <div class="score-box" style="text-align:right; font-size:1.5rem; color:#d35400; font-weight:bold; margin-bottom:10px;">当前得分: <span id="current-score">0</span></div>
        <div class="progress-container">
            <div class="progress-bar" id="p-fill"></div>
            <div class="progress-text" id="p-text">剩余 50 题</div>
        </div>

        <div class="vocab-card" style="padding: 40px; background:#fff; border-radius:20px; text-align:center;">
            <div style="font-size:0.9rem; color:#999;">听音辨义 / 看字选意</div>
            <h1 id="q-kanji" style="font-size:4rem; margin:20px 0;">Ready?</h1>
            <div id="q-kana" style="font-size:1.5rem; color:#666;"></div>
        </div>

        <div id="options-area" style="display:grid; grid-template-columns:1fr; gap:10px; margin-top:25px;"></div>
    </div>

    <div class="container hidden" id="result-screen" style="text-align:center;">
        <h1 style="font-size:3rem; margin-top:30px;">挑战完成!</h1>
        <div style="font-size:5rem; color:#d35400; font-weight:bold;" id="total-score">0</div>
        <p id="evaluation" style="font-size:1.2rem; color:#666;"></p>
        
        <div id="review-area" style="margin-top:40px;">
            <h2 style="border-bottom: 2px solid #f44336; display: inline-block; padding-bottom: 5px;">错题复盘录</h2>
            <div id="wrong-list" style="margin-top:20px;"></div>
        </div>

        <div style="margin: 40px 0;">
            <button class="btn btn-primary" onclick="location.reload()" style="background:#839b72; padding:15px 40px; font-size:1.2rem;">重新挑战</button>
            <a href="index.php" style="display:block; margin-top:20px; color:#839b72;">返回大厅</a>
        </div>
    </div>

    <script>
        let score = 0;
        let qCount = 0;
        const maxQ = 50;
        let wrongWords = [];
        let isProcessing = false;

        async function fetchQuestion() {
            if (qCount >= maxQ) { showResults(); return; }
            qCount++;
            isProcessing = false;

            // 更新UI
            document.getElementById('header-title').innerText = `BOSS挑战中 (${qCount}/${maxQ}) 👹`;
            document.getElementById('p-fill').style.width = (qCount / maxQ * 100) + '%';
            document.getElementById('p-text').innerText = `已完成 ${qCount} 题 / 剩余 ${maxQ - qCount} 题`;

            const res = await fetch('api.php?action=get_random_question');
            const data = await res.json();
            const q = data.question;

            document.getElementById('q-kanji').innerText = q.kanji;
            document.getElementById('q-kana').innerText = q.kana;

            // 自动发音
            speak(q.kanji);

            const optsDiv = document.getElementById('options-area');
            optsDiv.innerHTML = '';
            data.options.forEach(opt => {
                let btn = document.createElement('div');
                btn.className = 'option-btn';
                btn.innerText = opt.meaning;
                btn.onclick = () => submitAnswer(btn, opt.id, q);
                optsDiv.appendChild(btn);
            });
        }

        function submitAnswer(btn, selectedId, qObj) {
            if (isProcessing) return;
            isProcessing = true;

            if (selectedId == qObj.id) {
                btn.style.background = "#4caf50"; btn.style.color = "#fff";
                score += 2;
                document.getElementById('current-score').innerText = score;
            } else {
                btn.style.background = "#f44336"; btn.style.color = "#fff";
                wrongWords.push(qObj); // 记录错题
            }

            setTimeout(fetchQuestion, 800);
        }

        function showResults() {
            document.getElementById('quiz-screen').classList.add('hidden');
            document.getElementById('result-screen').classList.remove('hidden');
            document.getElementById('total-score').innerText = score;
            
            // 记录挑战记录到本地
            let history = JSON.parse(localStorage.getItem('n3_test_history') || '[]');
            history.push({ date: new Date().toLocaleString(), score: score });
            localStorage.setItem('n3_test_history', JSON.stringify(history));

            // 生成评价
            let eval = score >= 90 ? "神之领域！👑" : (score >= 60 ? "合格水平！✨" : "仍需努力！💪");
            document.getElementById('evaluation').innerText = eval;

            // 生成复盘列表
            const listDiv = document.getElementById('wrong-list');
            if (wrongWords.length === 0) {
                listDiv.innerHTML = "<p style='color:#4caf50;'>太棒了！本次挑战没有错题。</p>";
            } else {
                wrongWords.forEach(w => {
                    listDiv.innerHTML += `
                        <div class="wrong-item">
                            <strong>${w.kanji}</strong> (${w.kana}) <br>
                            正确含义：<span class="correct-ans">${w.meaning}</span> <br>
                            <small style="color:#999;">例句：${w.example_sentence}</small>
                        </div>
                    `;
                });
            }
        }

        function speak(text) {
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.lang = 'ja-JP';
            window.speechSynthesis.speak(msg);
        }

        window.onload = fetchQuestion;
    </script>
</body>
</html>