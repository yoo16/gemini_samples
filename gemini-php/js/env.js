// 現在のホストとプロトコルを取得
const HOST = `${window.location.protocol}//${window.location.hostname}`;

// TRANSLATION_URI と CHAT_URI を設定
const TRANSLATION_URI = `${HOST}/gemini-php/api/translate/ai_translate.php`;
const CHAT_URI = `${HOST}:3000`;

console.log("TRANSLATION_URI: ", TRANSLATION_URI)
console.log("CHAT_URI: ", CHAT_URI)