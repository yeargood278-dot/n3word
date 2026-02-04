// 播放日语发音
function playAudio(text) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'ja-JP'; // 设置为日语
        utterance.rate = 0.9; // 语速稍慢，适合学习
        window.speechSynthesis.speak(utterance);
    } else {
        alert("您的浏览器不支持语音播放功能。");
    }
}

// 简单的洗牌算法
function shuffle(array) {
    let currentIndex = array.length,  randomIndex;
    while (currentIndex != 0) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex--;
        [array[currentIndex], array[randomIndex]] = [
            array[randomIndex], array[currentIndex]];
    }
    return array;
}