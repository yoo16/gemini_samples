document.addEventListener('DOMContentLoaded', function () {
    // ボタンがクリックされたときにレシピを取得
    document.getElementById('fetchRecipe').addEventListener('click', createRecipe);
});

const createRecipe = async () => {
    // TODO: 条件を変える
    var posts = {
        "genre": "イタリアン",
        "time": "昼食",
        "keywords": "さっぱり,簡単,パスタ"
    }

    // Gemini AI生成アプリ(PHP)にアクセス
    const uri = 'http://localhost/gemini-php/api/ai_recipe.php';
    await fetch(uri, {
        method: 'POST',  // POSTリクエストを指定
        headers: {
            'Content-Type': 'application/json'  // JSON形式で送信するためのヘッダー
        },
        body: JSON.stringify(posts)  // 送信するデータをJSON形式に変換
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();  // JSONを返す
        })
        .then(recipeData => {
            console.log(recipeData);
            renderRecipe(recipeData);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

const renderRecipe = (recipeData) => {
    // レシピ表示用のDOM要素
    const recipeDiv = document.getElementById('recipe');

    // タイトル、説明、ジャンル、キーワードを表示
    const title = `<h2 class="text-3xl font-bold text-gray-800 mb-4">${recipeData.title}</h2>`;
    const description = `<p class="text-gray-700 mb-4">${recipeData.description}</p>`;
    const genre = `<p class="text-gray-600 mb-4"><strong>ジャンル:</strong> ${recipeData.genre}</p>`;
    const keywords = `<p class="text-gray-600 mb-6"><strong>キーワード:</strong> ${recipeData.keywords}</p>`;

    recipeDiv.innerHTML += title + description + genre + keywords;

    // 材料リストを生成
    const ingredientsTitle = `<h3 class="text-2xl font-bold text-gray-800 mb-2">材料</h3>`;
    recipeDiv.innerHTML += ingredientsTitle;

    const ingredientsList = document.createElement('ul');
    ingredientsList.classList.add('list-disc', 'pl-5', 'mb-6');

    recipeData.ingredients.forEach(ingredient => {
        const li = document.createElement('li');
        li.innerHTML = `<strong>${ingredient.name}</strong>: ${ingredient.quantity}`;
        ingredientsList.appendChild(li);
    });

    recipeDiv.appendChild(ingredientsList);

    // 作り方リストを生成
    const stepsTitle = `<h3 class="text-2xl font-bold text-gray-800 mb-2">作り方</h3>`;
    recipeDiv.innerHTML += stepsTitle;

    const stepsList = document.createElement('ol');
    stepsList.classList.add('list-decimal', 'pl-5', 'mb-6');

    recipeData.steps.forEach(step => {
        const li = document.createElement('li');
        li.innerHTML = `ステップ ${step.stepNumber}: ${step.instruction}`;
        stepsList.appendChild(li);
    });

    recipeDiv.appendChild(stepsList);
}