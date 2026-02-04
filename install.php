<?php
header("Content-type: text/html; charset=utf-8");
$csvFile = 'n3_vocab.csv';
$dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'n3_learning';

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 创建 SQLite 表结构
    $db->exec("DROP TABLE IF EXISTS vocab");
    $db->exec("CREATE TABLE vocab (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        kanji TEXT, kana TEXT, romaji TEXT, type TEXT, 
        meaning TEXT, tone TEXT, example_sentence TEXT, example_meaning TEXT
    )");

    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $db->beginTransaction();
        fgetcsv($handle); // 跳过表头
        $stmt = $db->prepare("INSERT INTO vocab (kanji, kana, romaji, type, meaning, tone, example_sentence, example_meaning) VALUES (?,?,?,?,?,?,?,?)");
        
        $row = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) < 8) continue;
            $stmt->execute([$data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]]);
            $row++;
        }
        $db->commit();
        fclose($handle);
        echo "✅ 成功导入 {$row} 个单词！";
    }
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ 错误: " . $e->getMessage();
}