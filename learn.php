<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>单词魔法书 - N3学习</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .stats-bar { background: #fff; padding: 12px; border-radius: 10px; margin: 15px auto; max-width: 600px; display: flex; justify-content: space-around; font-size: 0.9rem; border: 1px solid #eee; }
        .speaker-btn { cursor: pointer; font-size: 1.5rem; color: #839b72; margin-left: 10px; vertical-align: middle; transition: 0.2s; }
        .speaker-btn:hover { color: #d35400; }
    </style>
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px;">
        <a href="index.php" style="text-decoration:none; color:#839b72;">⬅ 返回大厅</a>
        <div style="font-weight:bold;">单词魔法书 📖</div>
        <div id="cur-pos" style="color:#999; font-size:0.8rem;">0 / 0</div>
    </header>

    <div class="container">
        <div class="stats-bar">
            <span>总数：<b id="stat-total">-</b></span>
            <span>已看：<b id="stat-learned">0</b></span>
            <span>剩余：<b id="stat-remain">-</b></span>
        </div>

        <div class="vocab-card" style="text-align:center; background:#fff; padding:40px; border-radius:20px;">
            <div style="color:#888;">音调: <span id="word-tone">-</span></div>
            <h1 style="font-size:4rem; margin:15px 0;">
                <span id="word-kanji">载入中...</span>
                <span class="speaker-btn" onclick="speak('word-kanji')">📢</span>
            </h1>
            <div style="font-size:1.5rem; color:#555; margin-bottom:20px;">
                <span id="word-kana"></span> 
                <span id="word-romaji" style="font-size:1rem; color:#999; margin-left:10px;"></span>
            </div>
            
            <div style="text-align:left; border-left:5px solid #839b72; padding:15px; background:#f9f9f9; margin:20px 0;">
                <span id="word-type" style="background:#839b72; color:white; padding:2px 6px; border-radius:4px; font-size:0.8rem;"></span>
                <span id="word-meaning" style="margin-left:10px;"></span>
            </div>

            <div style="text-align:left; background:#f4f4f4; padding:15px; border-radius:10px;">
                <strong>💡 拓展例句: <span class="speaker-btn" style="font-size:1.1rem;" onclick="speak('word-example-jp')">📢</span></strong><br>
                <span id="word-example-jp" style="color:#d35400; font-weight:bold;"></span><br>
                <span id="word-example-cn" style="font-size:0.9rem; color:#666;"></span>
            </div>
        </div>

        <div class="nav-controls" style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
            <button class="btn" onclick="jump('first')">首词</button>
            <button class="btn" id="btn-prev" onclick="jump('prev')">上一个</button>
            <button class="btn" id="btn-next" onclick="jump('next')">下一个</button>
            <button class="btn" onclick="jump('last')">尾词</button>
        </div>
    </div>

    <script>
        let wordList = [];
        let curIdx = 0;
        let total = 0;

        async function init() {
            const res = await fetch('api.php?action=get_study_set');
            wordList = await res.json();
            total = wordList.length;
            document.getElementById('stat-total').innerText = total;
            render();
        }

        function render() {
            const w = wordList[curIdx];
            document.getElementById('word-kanji').innerText = w.kanji;
            document.getElementById('word-kana').innerText = w.kana;
            document.getElementById('word-romaji').innerText = `[${w.romaji}]`;
            document.getElementById('word-tone').innerText = w.tone;
            document.getElementById('word-type').innerText = w.type;
            document.getElementById('word-meaning').innerText = w.meaning;
            document.getElementById('word-example-jp').innerText = w.example_sentence;
            document.getElementById('word-example-cn').innerText = w.example_meaning;

            document.getElementById('cur-pos').innerText = `${curIdx + 1} / ${total}`;
            document.getElementById('btn-prev').disabled = (curIdx === 0);
            document.getElementById('btn-next').disabled = (curIdx === total - 1);
            
            updateProgress(w.kanji);
        }

        function jump(dir) {
            if(dir === 'first') curIdx = 0;
            else if(dir === 'last') curIdx = total - 1;
            else if(dir === 'prev' && curIdx > 0) curIdx--;
            else if(dir === 'next' && curIdx < total - 1) curIdx++;
            render();
        }

        function updateProgress(k) {
            let learned = JSON.parse(localStorage.getItem('n3_read') || '[]');
            if(!learned.includes(k)) { learned.push(k); localStorage.setItem('n3_read', JSON.stringify(learned)); }
            document.getElementById('stat-learned').innerText = learned.length;
            document.getElementById('stat-remain').innerText = Math.max(0, total - learned.length);
        }

        function speak(id) {
            const text = document.getElementById(id).innerText;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.lang = 'ja-JP';
            window.speechSynthesis.speak(msg);
        }
        init();
    </script>
</body>
</html>