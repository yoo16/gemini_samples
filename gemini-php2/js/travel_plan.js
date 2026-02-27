const createPlan = async () => {
    // TODO: 条件を変える
    var posts = {
        "departure": "東京",
        "destination": "福岡",
        "departureDate": "2024/09/20",
        "arrivalDate": "2024/09/21",
        "budget": 30000,
        "keywords": "グルメ, ゆったり"
    }

    // Gemini AI生成アプリ(PHP)にアクセス
    const uri = 'http://localhost/gemini-php/api/ai_travel_plan.php'
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
            return response.json();
        })
        .then(planData => {
            console.log(planData)
            renderTravelPlan(planData);
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

const renderTravelPlan = (planData) => {
    const tripPlanDiv = document.getElementById('plan');

    // 基本情報を表示
    const basicInfo = `
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">基本情報</h2>
            <p><strong>出発地:</strong> ${planData.departure}</p>
            <p><strong>目的地:</strong> ${planData.destination}</p>
            <p><strong>出発日:</strong> ${planData.departureDate}</p>
            <p><strong>到着日:</strong> ${planData.arrivalDate}</p>
            <p><strong>予算:</strong> ¥${planData.budget}</p>
            <p><strong>キーワード:</strong> ${planData.keywords}</p>
        </div>
    `;
    tripPlanDiv.innerHTML += basicInfo;

    // 各日のプランを表示
    planData.planItems.forEach((dayPlan, index) => {
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('bg-white', 'shadow-md', 'rounded-lg', 'p-6');

        let dayContent = `<h2 class="text-xl font-bold text-gray-800 mb-4">日 ${index + 1}</h2>`;

        dayPlan.forEach(item => {
            dayContent += `
                <div class="mb-4">
                    <p><strong>日付:</strong> ${item.date}</p>
                    <p><strong>交通手段:</strong> ${item.transportation}</p>
                    <p><strong>場所:</strong> ${item.place}</p>
                    <p><strong>アクティビティ:</strong> ${item.activity}</p>
                    <p><strong>メモ:</strong> ${item.memo}</p>
                </div>
                <hr class="my-4">
            `;
        });

        dayDiv.innerHTML = dayContent;
        tripPlanDiv.appendChild(dayDiv);
    });
}
