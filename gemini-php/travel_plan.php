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
        
        <!-- 旅行プラン作成フォーム -->
        <form id="tripForm" class="space-y-4 max-w-lg mx-auto">
            <div>
                <label for="departure" class="block text-gray-700 font-semibold mb-2">出発地:</label>
                <input type="text" id="departure" name="departure" class="w-full p-2 border border-gray-300 rounded" value="東京" required>
            </div>
            <div>
                <label for="destination" class="block text-gray-700 font-semibold mb-2">目的地:</label>
                <input type="text" id="destination" name="destination" class="w-full p-2 border border-gray-300 rounded" value="大阪" required>
            </div>
            <div>
                <label for="departureDate" class="block text-gray-700 font-semibold mb-2">出発日:</label>
                <input type="date" id="departureDate" name="departureDate" class="w-full p-2 border border-gray-300 rounded" value="2024-10-01" required>
            </div>
            <div>
                <label for="arrivalDate" class="block text-gray-700 font-semibold mb-2">到着日:</label>
                <input type="date" id="arrivalDate" name="arrivalDate" class="w-full p-2 border border-gray-300 rounded" value="2024-10-02" required>
            </div>
            <div>
                <label for="budget" class="block text-gray-700 font-semibold mb-2">予算 (円):</label>
                <input type="number" id="budget" name="budget" class="w-full p-2 border border-gray-300 rounded" value="50000" required>
            </div>
            <div>
                <label for="keywords" class="block text-gray-700 font-semibold mb-2">キーワード:</label>
                <input type="text" id="keywords" name="keywords" class="w-full p-2 border border-gray-300 rounded" value="レジャー,グルメ">
            </div>
            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    AIプラン作成
                </button>
            </div>
        </form>

        <!-- プラン表示領域 -->
        <div id="plan" class="space-y-8 mt-8">
            <!-- 旅行プランがここに表示されます -->
        </div>
    </div>

    <script src="js/travel_plan.js" defer></script>
</body>
</html>
