const { GoogleGenerativeAI } = require("@google/generative-ai");
const fs = require("fs");
require('dotenv').config();

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

async function run() {
    const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });
    const result = await model.generateContent([
        "What is in this photo?",
        {
            inlineData: {
                data: Buffer.from(fs.readFileSync('./images/cappuccino.webp')).toString("base64"),
                mimeType: 'image/webp'
            }
        }]
    );
    console.log(result.response.text());
}
run();