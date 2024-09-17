<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>旅行プラン表示</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto mt-10">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">旅行プラン</h1>
        <div class="flex justify-center mb-8">
            <button onclick="createPlan()" id="fetchRecipe" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                AIプラン作成
            </button>
        </div>
        <div id="plan" class="space-y-8">
            <!-- 旅行プランがここに表示されます -->
        </div>
    </div>

    <script src="js/travel_plan.js" defer></script>
</body>
</html>
