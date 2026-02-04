<?php
/**
 * N3 词汇乐园 - 核心全能 API
 * 支持功能：统计、全词库读取、随机出题（50题模式）、高随机性干扰项生成
 */

header('Content-Type: application/json; charset=utf-8');

// 1. 数据库配置：指定 SQLite 数据库路径
$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'n3_learning';

try {
    // 建立连接
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo json_encode(['error' => '数据库连接失败，请确认是否运行了 install.php。详情: ' . $e->getMessage()]);
    exit;
}

// 2. 数据清洗助手函数
// 作用：将数据库中带空格或大小写的键名（如 "Example Sentence"）统一转为小写下划线（"example_sentence"）
function cleanRow($row) {
    if (!$row) return null;
    $clean = [];
    foreach ($row as $key => $value) {
        // 将键名转为小写并把空格换成下划线
        $newKey = strtolower(str_replace(' ', '_', $key));
        $clean[$newKey] = $value;
    }
    // 兼容性映射：确保例句和翻译在前端有固定键名
    if (isset($row['Example Sentence'])) $clean['example_sentence'] = $row['Example Sentence'];
    if (isset($row['Example Translation'])) $clean['example_meaning'] = $row['Example Translation'];
    return $clean;
}

// 3. 处理前端请求
$action = $_GET['action'] ?? '';

switch ($action) {
    
    // 接口 A: 获取统计信息（用于显示总词数）
    case 'get_stats':
        $total = $db->query('SELECT COUNT(*) FROM vocab')->fetchColumn();
        echo json_encode(['total' => (int)$total]);
        break;

    // 接口 B: 获取全量数据集（用于学习页的上一页/下一页导航）
    case 'get_study_set':
        $stmt = $db->query('SELECT * FROM vocab ORDER BY id ASC');
        $rows = $stmt->fetchAll();
        $results = array_map('cleanRow', $rows);
        echo json_encode($results);
        break;

    // 接口 C: 获取随机题目（用于练习、听力、以及50题测试模式）
    case 'get_random_question':
        // 随机抽取一个单词作为题目
        $q = $db->query('SELECT * FROM vocab ORDER BY RANDOM() LIMIT 1')->fetch();
        if (!$q) {
            echo json_encode(['error' => '词库为空，请先导入数据']);
            exit;
        }
        $q = cleanRow($q);

        // 随机抽取3个干扰项（错误答案），确保不与正确答案重复
        $stmt = $db->prepare('SELECT id, meaning FROM vocab WHERE id != ? ORDER BY RANDOM() LIMIT 3');
        $stmt->execute([$q['id']]);
        $options = $stmt->fetchAll();
        
        // 清洗干扰项字段
        $options = array_map('cleanRow', $options);
        
        // 加入正确选项并随机打乱
        $options[] = ['id' => $q['id'], 'meaning' => $q['meaning']];
        shuffle($options);

        echo json_encode([
            'question' => $q,
            'options' => $options
        ]);
        break;

    default:
        echo json_encode(['error' => '请求的接口动作不存在']);
        break;
}