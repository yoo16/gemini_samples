const TRANSLATION_URI = 'http://localhost/gemini-php/api/translate/ai_translate.php';

const startButton = document.getElementById('startButton');
const resultElement = document.getElementById('result');
const statusElement = document.getElementById('status');
const fromLangSelect = document.getElementById('fromLang');
const toLangSelect = document.getElementById('toLang');
const chatHistoryElement = document.getElementById('chatHistory');

var historyList = [];
SpeechRecognition = webkitSpeechRecognition || SpeechRecognition;
const recognition = new SpeechRecognition();
recognition.interimResults = false;

recognition.onstart = () => {
    statusElement.textContent = "音声認識中...";
};

recognition.onresult = (event) => {
    console.log('onresult')
    var text = event.results[0][0].transcript;
    resultElement.value = text;
    addOrigin(text);
    translate(text, fromLangSelect.value, toLangSelect.value);
}

recognition.onend = () => {
    console.log('音声認識が終了しました');
    statusElement.textContent = "";
};

recognition.onerror = (event) => {
    statusElement.textContent = `エラーが発生しました: ${event.error}`;
};

// ボタンがクリックされたとき、音声認識を開始
const startSpeech = () => {
    console.log("Lang: ", fromLangSelect.value);
    recognition.lang = fromLangSelect.value;
    recognition.start(); // 音声認識を開始
}

/**
 * 翻訳イベント
 */
const handleTranslate = () => {
    var text = resultElement.value;
    if (!text) return;

    addOrigin(text);
    // addTranslation(text);
    translate(text, fromLangSelect.value, toLangSelect.value);
}

/**
 * 翻訳
 */
const translate = async (text, fromLang, toLang) => {
    statusElement.textContent = "翻訳中...";

    await fetch(TRANSLATION_URI, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            origin: text,
            fromLang: fromLang,
            toLang: toLang
        })
    })
        .then(response => {
            statusElement.textContent = "";
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data)
            renderTranslation(data);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
};

// 翻訳結果を表示
const renderTranslation = (translationData) => {
    addTranslation(translationData);
    speakTranslation(translationData.translate); // 翻訳結果を読み上げる
};

const addOrigin = (text, lang) => {
    // 翻訳前の吹き出しを作成（左側）
    const originalMessageDiv = document.createElement('div');
    originalMessageDiv.classList.add('flex', 'justify-start');

    const originalBubble = document.createElement('div');
    originalBubble.classList.add('bg-teal-500', 'text-white', 'rounded-lg', 'p-3', 'max-w-xs', 'text-left');
    originalBubble.innerHTML = text;

    originalMessageDiv.appendChild(originalBubble);
    chatHistoryElement.appendChild(originalMessageDiv);
}

// 翻訳履歴を追加
const addTranslation = (result) => {
    // 翻訳後の吹き出しを作成（右側）
    const translationMessageDiv = document.createElement('div');
    translationMessageDiv.classList.add('flex', 'justify-end');

    const translationBubble = document.createElement('div');
    translationBubble.classList.add('bg-gray-300', 'text-gray-800', 'rounded-lg', 'p-3', 'max-w-xs', 'text-left');
    translationBubble.innerHTML = result.translate ? result.translate : "Translation error.";

    translationMessageDiv.appendChild(translationBubble);

    // チャット履歴に追加
    chatHistoryElement.appendChild(translationMessageDiv);
};

const playText = () => {
    if (lastTranslation) {
        speakTranslation(lastTranslation); // 最後の翻訳結果を読み上げ
    } else {
        console.log('再生する翻訳結果がありません');
    }
}

// TODO: Chromeで利用できない
// 翻訳結果を音声で読み上げ
const speakTranslation = (text) => {
    console.log('speakTranslation', text, toLangSelect.value)
    const synth = window.speechSynthesis;
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = toLangSelect.value;
    synth.speak(utterance);

    synth.addEventListener('voiceschanged', () => {
        console.log('voice changed')
        const voice = speechSynthesis.getVoices();
        console.log(voice);
    });
};

const swapLanguages = () => {
    const fromLang = fromLangSelect.value;
    const toLang = toLangSelect.value;

    // 入れ替える
    fromLangSelect.value = toLang;
    toLangSelect.value = fromLang;
};

/**
 * saveHistory
 */
const saveHistory = () => {
    alert('会話を保存しました');
}


/**
 * キーボード操作
 */
document.addEventListener('keydown', (event) => {
    // 音声入力
    if (event.ctrlKey && event.code === 'KeyI') {
        event.preventDefault();
        startSpeech();
    }
    // 言語を入れ替える
    if (event.ctrlKey && event.code === 'KeyL') {
        event.preventDefault();
        swapLanguages();
    }
});