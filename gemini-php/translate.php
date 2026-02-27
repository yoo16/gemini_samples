<?php
require_once 'lib/Lang.php';
$langs = Lang::$languages;

$defaultFromLang = 'ja-JP';
$defaultToLang = 'en-US';

function selected($value, $selected)
{
    return ($value == $selected) ? 'selected' : '';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Translate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">AI Translate</h1>

        <div class="flex flex-col items-center">
            <div class="bg-white my-2 shadow-md rounded-lg px-6 py-3 w-full max-w-lg">
                <div class="flex">
                    <div>
                        <label for="fromLang" class="text-gray-800 font-semibold">翻訳前の言語</label>
                        <select id="fromLang" class="bg-white border border-gray-300 rounded-md p-2">
                            <?php foreach ($langs as $lang): ?>
                                <option value="<?= $lang['code'] ?>" <?= selected($defaultFromLang, $lang['code']) ?>><?= $lang['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="ml-5">
                        <label for="toLang" class="text-gray-800 font-semibold">翻訳後の言語</label>
                        <select id="toLang" class="bg-white border border-gray-300 rounded-md p-2">
                            <?php foreach ($langs as $value => $lang): ?>
                                <option value="<?= $lang['code'] ?>" <?= selected($defaultToLang, $lang['code']) ?>><?= $lang['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <button id="startButton" class="bg-teal-500 hover:bg-teal-700 text-sm text-white font-bold py-1 px-2 rounded my-2" onclick="startSpeech()">
                    音声入力（Ctrl + I）
                </button>
                <input id="result" class="my-1 p-2 w-full rounded text-gray-700 border">
                <button id="startButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-2" onclick="handleTranslate()">
                    テキスト翻訳
                </button>
                <p id="status" class="text-red-500"></p>
            </div>

            <!-- チャット形式の翻訳履歴の表示部分 -->
            <div class="mt-3 bg-white shadow-md rounded-lg p-6 w-full max-w-lg">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">会話</h2>
                <button id="startButton" class="bg-blue-500 hover:bg-blue-700 text-sm text-white font-bold py-1 px-2 rounded mb-4" onclick="saveHistory()">
                    保存
                </button>

                <div id="chatHistory" class="flex flex-col space-y-4">
                    <!-- チャットの吹き出しがここに追加されます -->
                </div>
            </div>


            <div class="my-4">
                <button onclick="speakTranslation('Hello')" class="p-2 border border-gray-500 rounded">Test Voice</button>
            </div>
        </div>
    </div>

    <script src="js/translate.js" defer></script>
</body>

</html>