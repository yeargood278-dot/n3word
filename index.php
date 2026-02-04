<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N3词汇乐园 - 首页</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .menu-card {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-bottom: 5px solid #839b72;
        }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .menu-icon { font-size: 3rem; display: block; margin-bottom: 10px; }
        .menu-title { font-size: 1.2rem; font-weight: bold; display: block; }
        .menu-desc { font-size: 0.85rem; color: #888; margin-top: 8px; display: block; }
    </style>
</head>
<body>
    <header><a href="index.php" class="logo">🇯🇵 N3 Adventure</a></header>
    <div class="container">
        <h1 style="text-align:center; margin: 40px 0;">选择你的冒险模式</h1>
        <div class="menu-grid">
            <a href="learn.php" class="menu-card">
                <span class="menu-icon">📖</span>
                <span class="menu-title">单词魔法书</span>
                <span class="menu-desc">带有例句与全音频学习</span>
            </a>
            <a href="practice.php" class="menu-card">
                <span class="menu-icon">⚔️</span>
                <span class="menu-title">记忆修炼场</span>
                <span class="menu-desc">汉字选意快速反应</span>
            </a>
            <a href="listening.php" class="menu-card">
                <span class="menu-icon">👂</span>
                <span class="menu-title">大力磨耳朵</span>
                <span class="menu-desc">纯听力辨析训练</span>
            </a>
            <a href="test.php" class="menu-card" style="border-bottom-color: #d35400;">
                <span class="menu-icon">👹</span>
                <span class="menu-title">BOSS挑战关</span>
                <span class="menu-desc">50题随机综合大测验</span>
            </a>
        </div>
    </div>
</body>
</html>