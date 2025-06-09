# 🚀 Getting Started

## Prerequisites

- [Docker + Docker Compose](https://docs.docker.com/engine/install/)
- Make

## Installation

```bash
make setup
```

## Generate JWT keys

```bash
make jwt-keys
```

## Mailpit

To capture outgoing emails during development, this project uses Mailpit.

Add the following to your .env.local:

```env
MAILER_DSN=smtp://mailpit:1025
```

## 🤖 GROQ API Integration

This project integrates with the GROQ API for natural language or AI-based processing (e.g., text generation, analysis, etc.).

### 🔧 Required Environment Variables

To configure the GROQ API, set the following variables in your .env or .env.local:

```env
GROQ_API_KEY=your-secret-api-key
GROQ_API_MODEL=your-model-name
```

* GROQ_API_KEY: Your authentication key for GROQ API requests.
* GROQ_API_MODEL: The model you wish to use, e.g. llama3-8b-8192, mixtral-8x7b-32768, or any other available model ID from GROQ.

> 💡 You can obtain these values from your [GROQ](https://console.groq.com/home) dashboard or service provider documentation.
