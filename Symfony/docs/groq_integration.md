# 🤖 GROQ Integration

## Prerequisites

Project is integrated with GROQ and allow ask question.

## Configuration

For configuring GROQ ai developer should change `.env.local` file

```.env
GROQ_API_KEY="!ChangeThis!"
GROQ_API_MODEL="!ChangeThis!"
```

> 💡 You can obtain these values from your [GROQ](https://console.groq.com/home) dashboard or service provider documentation.

## Ask

If user wants to ask question, then api call **POST** [/ask](https://localhost/docs#/TextGeneration/api_ask_post) should be made.

> For asking questions user should be [authorized](./authorization.md).
