// TODO: URLカスタマイズ
const aiCreateUri = 'http://localhost/gemini-php/api/recipe/ai_create.php';
const aiImageCreateUri = 'http://localhost/gemini-php/api/recipe/ai_image_create.php';
const saveUri = 'http://localhost/gemini-php/api/recipe/save.php';

var keywordList = [];
var recipe = {};

// ローディング表示を制御する関数
const showLoading = () => {
    document.getElementById('loading').classList.remove('hidden');
};

const hideLoading = () => {
    document.getElementById('loading').classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', function () {
    const keywordInputContainer = document.getElementById('keywordInputContainer');
    const keywordsInput = document.getElementById('keywordsInput');

    // キーワードを追加する関数
    function addKeyword(keyword) {
        if (keyword.trim() === "") return;

        // キーワードが重複していないか確認
        if (!keywordList.includes(keyword)) {
            keywordList.push(keyword);
            const keywordElement = document.createElement('span');
            keywordElement.classList.add('bg-green-500', 'text-white', 'py-1', 'px-2', 'rounded', 'mr-2', 'mb-2', 'flex', 'items-center');
            keywordElement.innerHTML = `
                ${keyword}
                <button class="ml-2 text-white focus:outline-none remove-keyword">&times;</button>
            `;
            keywordInputContainer.insertBefore(keywordElement, keywordsInput);

            // 削除ボタンのイベントリスナーを追加
            keywordElement.querySelector('.remove-keyword').addEventListener('click', function () {
                keywordInputContainer.removeChild(keywordElement);
                keywordList = keywordList.filter(k => k !== keyword);
            });
        }
        keywordsInput.value = '';
    }

    // Enterキーでキーワードを追加
    keywordsInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            console.log('on enter');
            e.preventDefault();
            addKeyword(keywordsInput.value);
        }
    });
});


const createRecipe = async () => {
    showLoading();

    const genre = document.getElementById('genre').value;
    const time = document.getElementById('time').value;
    const keywords = keywordList.join(',');

    var posts = {
        "genre": genre,
        "time": time,
        "keywords": keywords
    };
    console.log(posts);

    await fetch(aiCreateUri, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(posts)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            renderRecipe(data);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        })
        .finally(() => {
            hideLoading();
        });
};

const createImageRrecipe = async () => {
    const imageInput = document.getElementById('imageInput');
    const file = imageInput.files[0];

    if (!file) {
        alert("画像を選択してください");
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    showLoading();
    await fetch(aiImageCreateUri, {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            renderRecipe(data);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        })
        .finally(() => {
            hideLoading();
        });
}

const renderRecipe = (data) => {
    // レシピデータ一時保存
    recipe = data;

    const recipeDiv = document.getElementById('recipe');
    recipeDiv.innerHTML = '';

    // タイトル、説明、ジャンル、キーワードを表示
    const title = `<h2 class="text-3xl font-bold text-gray-800 mb-4">${recipe.title}</h2>`;
    const description = `<p class="text-gray-700 mb-4">${recipe.description}</p>`;
    const genre = `<p class="text-gray-600 mb-4"><strong>ジャンル:</strong> ${recipe.genre}</p>`;
    const keywords = `<p class="text-gray-600 mb-6"><strong>キーワード:</strong> ${recipe.keywords}</p>`;

    recipeDiv.innerHTML += title + description + genre + keywords;

    // 材料リストを生成
    const ingredientsTitle = `<h3 class="text-2xl font-bold text-gray-800 mb-2">材料</h3>`;
    recipeDiv.innerHTML += ingredientsTitle;

    const ingredientsList = document.createElement('ul');
    ingredientsList.classList.add('list-disc', 'pl-5', 'mb-6');

    recipe.ingredients.forEach(ingredient => {
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

    recipe.steps.forEach(step => {
        const li = document.createElement('li');
        li.innerHTML = `ステップ ${step.stepNumber}: ${step.instruction}`;
        stepsList.appendChild(li);
    });

    recipeDiv.appendChild(stepsList);
}

const saveRecipe = async () => {
    if (!recipe.title) {
        console.error('保存するレシピがありません');
        return;
    }
    console.log('send data:', recipe);

    showLoading();

    // レシピデータを保存用APIに送信
    await fetch(saveUri, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(recipe)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Recipe saved successfully:', data);
        })
        .catch(error => {
            console.error('Save error:', error);
        })
        .finally(() => {
            hideLoading();  // ローディング表示終了
        });
}