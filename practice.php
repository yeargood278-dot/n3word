<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>记忆修炼场</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">⬅ 退出修炼</a>
        <div>记忆修炼场 ⚔️</div>
    </header>

    <div class="container">
        <div class="vocab-card" style="padding: 2rem; background:#fff; border-radius:15px; text-align:center;">
            <h2 style="color:#999; font-size:1rem;">请选择正确的含义</h2>
            <div class="kanji-main" id="quiz-kanji" style="font-size:3.5rem; margin:10px 0;">加载中...</div>
            <div class="kana-sub" id="quiz-kana" style="font-size:1.5rem; color:#666;"></div>
        </div>

        <div class="options-grid" id="options-area" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:20px;">
            </div>
        
        <div id="result-msg" style="text-align:center; margin-top:20px; font-weight:bold; height: 30px; font-size:1.2rem;"></div>
        
        <div style="text-align: center; margin-top:20px;">
            <button class="btn btn-primary" id="next-btn" style="display:none; padding:10px 25px; background:#839b72; color:white; border:none; border-radius:5px;" onclick="loadQuiz()">下一题</button>
        </div>
    </div>

    <script>
        let isAnswered = false;

        async function loadQuiz() {
            isAnswered = false;
            document.getElementById('result-msg').innerText = '';
            document.getElementById('next-btn').style.display = 'none';
            
            try {
                const res = await fetch('api.php?action=get_random_question');
                const data = await res.json();
                
                const q = data.question;
                document.getElementById('quiz-kanji').innerText = q.kanji || q.Kanji;
                document.getElementById('quiz-kana').innerText = q.kana || q.Kana;
                
                const optsDiv = document.getElementById('options-area');
                optsDiv.innerHTML = '';
                
                data.options.forEach(opt => {
                    let btn = document.createElement('div');
                    btn.style = "background:#fff; padding:15px; border:2px solid #eee; border-radius:10px; cursor:pointer; text-align:center; transition:0.2s;";
                    btn.innerText = opt.meaning || opt.Meaning;
                    btn.onmouseover = () => { if(!isAnswered) btn.style.borderColor = '#839b72'; };
                    btn.onmouseout = () => { if(!isAnswered) btn.style.borderColor = '#eee'; };
                    btn.onclick = () => checkAnswer(btn, opt.id, q.id);
                    optsDiv.appendChild(btn);
                });
            } catch (e) {
                console.error("加载题目失败", e);
            }
        }

        function checkAnswer(btn, selectedId, correctId) {
            if (isAnswered) return;
            isAnswered = true;
            
            if (selectedId == correctId) {
                btn.style.background = '#e8f5e9';
                btn.style.borderColor = '#4caf50';
                document.getElementById('result-msg').style.color = '#4caf50';
                document.getElementById('result-msg').innerText = '🎉 正确！';
            } else {
                btn.style.background = '#ffebee';
                btn.style.borderColor = '#f44336';
                document.getElementById('result-msg').style.color = '#f44336';
                document.getElementById('result-msg').innerText = '❌ 选错了哦';
            }
            document.getElementById('next-btn').style.display = 'inline-block';
        }

        window.onload = loadQuiz;
    </script>
</body>
</html>