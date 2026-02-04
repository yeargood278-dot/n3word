<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>大力磨耳朵 - 听力训练</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .audio-icon-large { font-size: 6rem; cursor: pointer; transition: transform 0.2s; display: inline-block; }
        .audio-icon-large:hover { transform: scale(1.1); }
        .audio-icon-large:active { transform: scale(0.9); }
        .option-btn { background:#fff; padding:15px; border:2px solid #eee; border-radius:10px; cursor:pointer; text-align:center; transition:0.2s; margin-bottom: 10px;}
        .option-btn:hover { border-color: #839b72; }
        .correct { background: #e8f5e9 !important; border-color: #4caf50 !important; color: #2e7d32; }
        .wrong { background: #ffebee !important; border-color: #f44336 !important; color: #c62828; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">⬅ 退出训练</a>
        <div>大力磨耳朵 👂</div>
    </header>

    <div class="container">
        <div class="vocab-card" style="padding: 2.5rem; text-align: center; background: #fff; border-radius: 20px;">
            <div class="audio-icon-large" onclick="playQuestionAudio()">🔊</div>
            <p style="color:#999; margin-top: 10px;">点击喇叭重听</p>
            
            <div id="answer-reveal" class="hidden" style="margin-top:20px; border-top: 1px dashed #eee; padding-top: 15px;">
                <h2 id="reveal-kanji" style="font-size: 2.5rem; margin-bottom: 5px;"></h2>
                <p id="reveal-kana" style="font-size: 1.2rem; color: #666;"></p>
            </div>
        </div>

        <div class="options-grid" id="options-area" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:20px;">
            </div>
        
        <div style="text-align: center; margin-top:30px;">
            <button class="btn btn-primary hidden" id="next-btn" style="padding:12px 30px; background:#839b72; color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer;" onclick="loadListening()">下一题</button>
        </div>
    </div>

    <script>
        let currentAudioText = "";
        let isAnswered = false;

        async function loadListening() {
            isAnswered = false;
            document.getElementById('answer-reveal').classList.add('hidden');
            document.getElementById('next-btn').classList.add('hidden');
            
            try {
                const res = await fetch('api.php?action=get_random_question');
                const data = await res.json();
                const q = data.question;

                // 准备发音文本：假名 + 例句（更有助于语境理解）
                currentAudioText = q.kana; 
                document.getElementById('reveal-kanji').innerText = q.kanji;
                document.getElementById('reveal-kana').innerText = q.kana;

                // 渲染选项
                const optsDiv = document.getElementById('options-area');
                optsDiv.innerHTML = '';
                data.options.forEach(opt => {
                    let btn = document.createElement('div');
                    btn.className = 'option-btn';
                    btn.innerText = opt.meaning;
                    btn.onclick = () => checkAnswer(btn, opt.id, q.id);
                    optsDiv.appendChild(btn);
                });

                // 自动播放音频
                setTimeout(playQuestionAudio, 500);

            } catch (e) {
                console.error("加载失败", e);
            }
        }

        function playQuestionAudio() {
            if (!currentAudioText) return;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(currentAudioText);
            msg.lang = 'ja-JP';
            msg.rate = 0.9; // 听力练习语速稍慢一点
            window.speechSynthesis.speak(msg);
        }

        function checkAnswer(btn, selectedId, correctId) {
            if (isAnswered) return;
            isAnswered = true;
            
            document.getElementById('answer-reveal').classList.remove('hidden');

            if (selectedId == correctId) {
                btn.classList.add('correct');
            } else {
                btn.classList.add('wrong');
                // 找出正确的那个高亮显示
                Array.from(document.querySelectorAll('.option-btn')).forEach(b => {
                    // 逻辑：由于干扰项不带ID，我们通过文字比对或让API返回更多信息，这里简单处理
                });
            }
            document.getElementById('next-btn').classList.remove('hidden');
        }

        window.onload = loadListening;
    </script>
</body>
</html>