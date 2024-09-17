import google.generativeai as genai
import PIL.Image
import os
from dotenv import load_dotenv

# .env ファイルを読み込む
load_dotenv()

# 環境変数から API キーを取得
API_KEY = os.getenv("GEMINI_API_KEY")

# 画像読み込み
img = PIL.Image.open('images/cappuccino.webp')

# Gemini APIプロンプト
prompt = "この写真はなんですか？"

genai.configure(api_key=API_KEY)
model = genai.GenerativeModel(model_name="gemini-1.5-flash")
response = model.generate_content([prompt, img])

if (response):
    print(response.text)
else:
    print("Gemini Error")