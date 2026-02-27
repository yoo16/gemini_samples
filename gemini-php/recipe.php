<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レシピ表示</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div id="loading" class="hidden fixed inset-0 flex justify-center items-center bg-gray-700 bg-opacity-50">
        <div class="loader">Loading...</div>
    </div>

    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">AI Generated Recipe</h1>

        <!-- 料理ジャンル、時間帯、キーワードのフォーム -->
        <div class="flex justify-center mb-8 space-x-4">
            <!-- 料理ジャンル -->
            <div>
                <label for="genre" class="block mb-2 text-gray-800 font-bold">料理ジャンル</label>
                <select id="genre" class="bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded">
                    <option value="和食">和食</option>
                    <option value="中華">中華</option>
                    <option value="イタリアン">イタリアン</option>
                    <option value="フレンチ">フレンチ</option>
                    <option value="インド">インド</option>
                </select>
            </div>

            <!-- 時間帯 -->
            <div>
                <label for="time" class="block mb-2 text-gray-800 font-bold">時間帯</label>
                <select id="time" class="bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded">
                    <option value="なし">なし</option>
                    <option value="朝食">朝食</option>
                    <option value="昼食">昼食</option>
                    <option value="夕食">夕食</option>
                    <option value="軽食">軽食</option>
                </select>
            </div>

            <!-- キーワード -->
            <div>
                <label for="keywords" class="block mb-2 text-gray-800 font-bold">キーワード</label>
                <div id="keywordInputContainer" class="flex flex-wrap bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded">
                    <input id="keywordsInput" type="text" class="outline-none flex-grow" placeholder="Enterで追加">
                </div>
            </div>
        </div>

        <div class="flex justify-center mb-8">
            <button onclick="createRecipe()" id="fetchRecipe" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                AIレシピ作成
            </button>
        </div>

        <div id="recipe" class="bg-white shadow-lg rounded-lg p-6">
            <!-- レシピがここに表示されます -->
        </div>

        <div class="flex justify-center m-5">
            <button onclick="saveRecipe()" id="fetchRecipe" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                レシピ保存
            </button>
        </div>
    </div>

    <script src="js/recipe.js" defer></script>
</body>

</html>