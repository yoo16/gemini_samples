<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>音声入力デモ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">音声入力デモ</h1>

        <div class="flex flex-col items-center">
            <button id="startButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                音声入力を開始
            </button>

            <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-lg">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">音声入力結果</h2>
                <p id="result" class="text-gray-700">ここに音声入力の結果が表示されます...</p>
            </div>

            <div>
                <ul id="history">
                </ul>
            </div>
        </div>
    </div>

    <script src="js/translate.js" defer></script>
</body>

</html>