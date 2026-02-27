document.addEventListener('DOMContentLoaded', () => {
    const startButton = document.getElementById('startButton');
    const resultElement = document.getElementById('result');

    // 音声認識が利用できるか確認
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        alert("このブラウザは音声認識に対応していません。Google Chromeなど最新のブラウザを使用してください。");
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = 'ja-JP'; // 日本語対応

    // 音声認識が開始されたとき
    recognition.onstart = () => {
        resultElement.textContent = "音声認識中...";
    };

    const fromLang = 'js-JP';
    const toLang = 'en-US';
    // 音声認識が終了したとき
    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript; // 音声認識結果
        resultElement.textContent = transcript; // 結果を表示

        // 翻訳
        translate(transcript, fromLang, toLang);
    };

    // エラーハンドリング
    recognition.onerror = (event) => {
        resultElement.textContent = `エラーが発生しました: ${event.error}`;
    };

    // ボタンがクリックされたとき、音声認識を開始
    startButton.addEventListener('click', () => {
        recognition.start();
    });
});

// URL
// AI生成アプリのURL
const uri = 'http://localhost/gemini-php/api/ai_translate.php';

const translate = async (transcript, fromLang, toLang) => {
    await fetch(uri, {
        method: 'POST',  // POSTリクエストを指定
        headers: {
            'Content-Type': 'application/json'  // JSON形式で送信するためのヘッダー
        },
        body: JSON.stringify({
            transcript: transcript,
            fromLang: fromLang,
            toLang: toLang
        })  // 送信するデータをJSON形式に変換
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();  // サーバーからのレスポンスをJSON形式で取得
        })
        .then(translationData => {
            console.log(translationData);
            // 翻訳結果を処理する関数を呼び出す (必要に応じて)
            renderTranslation(translationData);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
};

const renderTranslation = (translationData) => {
    addTranslationToHistory(translationData);
};

const addTranslationToHistory = (translationData) => {
    const listItem = document.createElement('li');
    listItem.classList.add('bg-gray-200', 'p-2', 'my-2', 'rounded');

    // 翻訳結果をHTMLに表示
    listItem.innerHTML = `
        <p><strong>Translated:</strong> ${translationData.translate}</p>
    `;

    const historyElement = document.getElementById('history');
    historyElement.appendChild(listItem);
};