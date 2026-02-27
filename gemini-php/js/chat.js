// HTML Element
const startButton = document.getElementById('startButton');
const inputTextElement = document.getElementById('inputText');
const statusElement = document.getElementById('status');
const langSelect = document.getElementById('lang');
const chatHistoryElement = document.getElementById('chatHistory');

// Socket IO開始
const socket = io(CHAT_URI);

// 音声認識
var recognition;
if ('webkitSpeechRecognition' in window) {
    recognition = new webkitSpeechRecognition();
} else if ('SpeechRecognition' in window) {
    recognition = new SpeechRecognition();
} else {
    alert("このブラウザは音声認識をサポートしていません");
}

recognition.interimResults = false;

recognition.onstart = () => {
    statusElement.textContent = "音声認識中...";
};

recognition.onresult = (event) => {
    const text = event.results[0][0].transcript;
    console.log("text:", text)
    if (text) {
        // 入力欄のテキストを取得
        const lang = langSelect.value;
        sendMessage(text, lang);
    }
};

recognition.onend = () => {
    statusElement.textContent = "";
};

recognition.onerror = (event) => {
    statusElement.textContent = `エラー: ${event.error}`;
};

// 翻訳前のテキストを表示
const addOrigin = (text, isOwnMessage = false) => {
    const originalMessageDiv = document.createElement('div');
    originalMessageDiv.classList.add('flex', isOwnMessage ? 'justify-start' : 'justify-end');

    const originalBubble = document.createElement('div');
    originalBubble.classList.add(
        isOwnMessage ? 'bg-teal-500' : 'bg-blue-500',
        'text-white', 'rounded-lg', 'p-3', 'max-w-xs', 'text-left', 'mb-1'
    );

    // 会話作成
    originalBubble.innerHTML = text;
    originalMessageDiv.appendChild(originalBubble);

    // 会話履歴に追加
    chatHistoryElement.appendChild(originalMessageDiv);
};

// 翻訳後のテキストを表示
const addTranslation = (text, isOwnMessage = false) => {
    const translationMessageDiv = document.createElement('div');
    translationMessageDiv.classList.add('flex', isOwnMessage ? 'justify-start' : 'justify-end');

    const translationBubble = document.createElement('div');
    translationBubble.classList.add(
        isOwnMessage ? 'bg-gray-300' : 'bg-gray-400',
        'text-gray-800', 'rounded-lg', 'p-3', 'max-w-xs', 'text-left'
    );

    // 会話作成
    translationBubble.innerHTML = text;
    translationMessageDiv.appendChild(translationBubble);

    // 会話履歴に追加
    chatHistoryElement.appendChild(translationMessageDiv);
};

// lang の変更を反映
function updateLang() {
    recognition.lang = langSelect.value;
    console.log("音声認識の言語が変更されました:", langSelect.value);
}

async function onSendMessage() {
    const message = inputTextElement.value;
    const lang = langSelect.value;
    console.log(message, lang)

    statusElement.textContent = "";
    if (!message) {
        statusElement.textContent = "メッセージを入力してください";
        return;
    }
    await sendMessage(message, lang);
}

// メッセージをサーバーに送信する関数
async function sendMessage(message, lang) {
    if (!message) return;

    // チャットに翻訳前のテキストを追加
    addOrigin(message, true);

    // チャットメッセージ
    socket.emit('message', { original: message, lang: lang });
}

const translateMessage = (data) => {
    console.log(data)
    if (data) {
        // 他のユーザーからのメッセージを表示
        addOrigin(data.origin, false);
        // 翻訳後のメッセージ表示
        addTranslation(data.translate, false);
        // 音声読み上げ
        speakTranslation(data.translate, data.toLang);
    } else {
        statusElement.textContent = "翻訳できませんでした";
    }
}

/**
 * 翻訳
 */
const translate = async (text, fromLang, toLang) => {
    try {
        statusElement.textContent = "翻訳中...";

        const response = await fetch(TRANSLATION_URI, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                origin: text,
                fromLang: fromLang,
                toLang: toLang
            })
        });

        statusElement.textContent = "";
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        console.log(data);
        translateMessage(data);
    } catch (error) {
        console.error('Fetch error:', error);
        return null; // エラー時も値を返す
    }
};


// TODO: Chromeで利用できない
// 翻訳結果を音声で読み上げ
const speakTranslation = (text, lang) => {
    console.log('speakTranslation', text, lang)
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    synth.speak(utterance);
};

// サーバーからのメッセージを受信
socket.on('message', async (msg) => {
    console.log(msg)
    const toLang = langSelect.value;
    if (!msg.original) {
        statusElement.textContent = "送信元のメッセージがありません";
        return;
    }
    if (!msg.lang) {
        statusElement.textContent = "送信元の言語がありません";
        return;
    }
    if (!toLang) {
        statusElement.textContent = "翻訳する言語が選択されていません";
        return;
    }
    // 翻訳
    await translate(msg.original, msg.lang, toLang);
});


// 音声認識の開始
const startSpeech = () => {
    recognition.lang = langSelect.value;
    recognition.start();
};
