# 🤖 AITOS — AI Technical OS & Context Compiler

AITOS is an AI-powered software architecture platform that transforms raw project ideas into structured, AI-ready development workspaces. Instead of generating code directly, it creates a complete project blueprint—including requirements, database schema, API design, UI blueprint, development planning, documentation, and AI context packages—so developers and AI coding assistants can build software from a consistent architectural foundation.

AITOS addresses one of the biggest challenges in AI-assisted development: context drift. By acting as a project's single source of truth, it preserves architectural decisions and generates portable workspaces that can be shared across AI tools such as Cursor, Claude, Gemini, and other coding assistants, enabling reliable, scalable, and collaborative software development.

✨ Key Features
🧠 AI-powered requirement analysis
🏗️ Multi-stage architecture generation pipeline
🗄️ Database schema generation
🔌 REST API design generation
🎨 UI blueprint generation
📋 Project planning & task generation
📚 Documentation generation
🤖 AI-ready prompt & context packs
📦 Portable ZIP workspace export/import
📄 Interactive HTML reports
📑 PDF documentation export
📊 Mermaid ER diagram generation
🔄 Workspace restoration from exported packages
🚀 Vision


 ## 🛠️ Features
* **Project Wizard:** Define your project metadata, target objectives, and choose frameworks, databases, and frontend stacks.
* **Requirements Analyzer:** AI compiles user roles, modules, functional requirements, user stories, risks, and assumptions.
* **Architecture Blueprinter:** Instantly drafts database schemas (migration structure), API endpoints, and UI blueprints.
* **Virtual Development Workspace:** Generates markdown context logs and prompts customized for AI code editors like Cursor, Claude, or Copilot.
* **Export Center:** Download an offline repository package (`.zip`) or generate a printable **Project Brief** (ZIP including interactive HTML, clean PDF, and Mermaid ER diagram sources).



AITOS is designed to become an AI Software Development Operating System—a platform that bridges software architecture and AI-assisted coding by providing structured, reusable, and portable project intelligence for developers and AI agents.



---

## 📋 Requirements
Before running the application, make sure you have installed:
* **PHP 8.2 or higher**
* **Composer**
* **Node.js & NPM**
* **SQLite** (or MySQL/PostgreSQL)

---

## 🚀 Getting Started & Local Installation

Follow these steps to run AITOS locally on your machine:

### 1. Clone the repository
```bash
git clone https://github.com/yashpadaliya08/AITOS.git
cd AITOS
```

### 2. Install PHP & Node dependencies
```bash
composer install
npm install
```

### 3. Setup Configuration & Database
Copy the environment example file:
```bash
cp .env.example .env
```

Generate the application encryption key:
```bash
php artisan key:generate
```

Initialize your SQLite database (default configuration):
```bash
# Create empty database file (Windows PowerShell)
New-Item -ItemType File -Path database/database.sqlite -Force

# Run migrations to build tables
php artisan migrate
```

### 4. Build frontend assets
```bash
npm run build
```

---

## 🔑 Configuring Your AI API Keys

AITOS supports two convenient ways to configure your API keys (OpenAI, Gemini, Anthropic, or OpenRouter):

### Option A: Via the `.env` File (Recommended for Local Dev)
Open the `.env` file at the root of your project and paste your keys directly:

```env
# Default provider settings ("gemini", "openai", "anthropic")
AITOS_DEFAULT_PROVIDER=openai

# Your API Keys (Fill in what you plan to use)
GEMINI_API_KEY=your_gemini_key_here
OPENAI_API_KEY=your_openai_or_openrouter_key_here
ANTHROPIC_API_KEY=your_anthropic_key_here
```

> [!TIP]  
> If you are using **OpenRouter**, paste your `sk-or-v1-...` key into the `OPENAI_API_KEY` slot. The system automatically detects OpenRouter keys and adjusts endpoints accordingly.****
**> You can get key from openrouter for free and then tell the AI to put with NVIDIA Nemotron 3 Ultra(free) version make sure you specifies the model other wise it choose the paid one ******
---

### Option B: Via the Settings UI Dashboard (No file changes needed)
If you don't want to edit configuration files:
1. Start the application.
2. Navigate to the **System Settings** page from the sidebar menu.
3. Paste your keys under **AI Engine Configuration**.
4. Click **Save Preferences**.

*Note: Keys entered in the UI are securely saved strictly in your local browser storage (`localStorage`) and are only sent to the local server during active compilation requests.*

---

## 💻 Running the App
Start the local Laravel development server:
```bash
php artisan serve
```

Open your browser and navigate to:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**


if anythings not working mail mm email- yashpadaliya2@gmail.com
