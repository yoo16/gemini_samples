// TODO: カスタム設定
const uri = 'http://localhost/gemini-php/api/travel/ai_create_plan.php';

const tripPlanDiv = document.getElementById('plan');
const tripForm = document.getElementById('tripForm');

// フォーム送信時にAIプランを作成
tripForm.addEventListener('submit', async (event) => {
    event.preventDefault();  // ページリロードを防ぐ
    const formData = new FormData(tripForm);

    // フォームからデータを取得
    const posts = {
        departure: formData.get('departure'),
        destination: formData.get('destination'),
        departureDate: formData.get('departureDate'),
        arrivalDate: formData.get('arrivalDate'),
        budget: parseInt(formData.get('budget'), 10),
        keywords: formData.get('keywords')
    };

    console.log('Request Payload:', posts);
    await createPlan(posts);
});

const createPlan = async (posts) => {
    // Gemini AI生成アプリ(PHP)にアクセス
    await fetch(uri, {
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
        .then(planData => {
            console.log(planData);
            renderTravelPlan(planData);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
};

const renderTravelPlan = (planData) => {
    tripPlanDiv.innerHTML = "";

    const plan = planData.plan;

    // 基本情報を表示
    const basicInfo = `
        <div class="bg-white shadow-md rounded-lg p-6 mb-4">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">基本情報</h2>
            <p><strong>出発地:</strong> ${plan.departure}</p>
            <p><strong>目的地:</strong> ${plan.destination}</p>
            <p><strong>出発日:</strong> ${plan.departureDate}</p>
            <p><strong>到着日:</strong> ${plan.arrivalDate}</p>
            <p><strong>予算:</strong> ¥${plan.budget}</p>
            <p><strong>キーワード:</strong> ${plan.keywords}</p>
        </div>
    `;
    tripPlanDiv.innerHTML += basicInfo;

    const groupedPlanItems = planData.plan_items.reduce((acc, item) => {
        if (!acc[item.date]) acc[item.date] = [];
        acc[item.date].push(item);
        return acc;
    }, {});

    let planItemsHtml = '';
    for (const [date, items] of Object.entries(groupedPlanItems)) {
        const dayHtml = `
            <div class="bg-gray-100 shadow-sm rounded-lg p-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-700">${date}</h3>
                ${items.map(item => `
                    <div class="border-b border-gray-300 py-2">
                        <p><strong>移動手段:</strong> ${item.transportation}</p>
                        <p><strong>場所:</strong> ${item.place}</p>
                        <p><strong>活動:</strong> ${item.activity}</p>
                        <p><strong>メモ:</strong> ${item.memo}</p>
                    </div>
                `).join('')}
            </div>
        `;
        planItemsHtml += dayHtml;
    }

    const planItemsContainer = `
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">旅行プラン</h2>
            ${planItemsHtml}
        </div>
    `;
    tripPlanDiv.innerHTML += planItemsContainer;
};
