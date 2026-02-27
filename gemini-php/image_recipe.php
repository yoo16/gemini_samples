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
            <input id="imageInput" type="file" name="image">
        </div>

        <div class="flex justify-center mb-8">
            <button onclick="createImageRrecipe()" id="fetchRecipe" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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