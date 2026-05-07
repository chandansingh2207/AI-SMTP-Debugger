<<<<<<< HEAD
# 🚀 AI Email Debugger

A clean, simple Laravel 12 utility tool for testing emails and analyzing them using AI providers like OpenAI, OpenRouter, and Google Gemini.

## 📋 Features

✨ **SMTP Testing**
- Dynamically configure SMTP settings
- Send test emails in real-time
- Capture SMTP success/error messages

🤖 **AI Analysis**
- Analyze emails for deliverability issues
- Multiple AI provider support
- Structured JSON responses with actionable recommendations

🎨 **Beautiful UI**
- Bootstrap 5 responsive design
- Real-time AJAX form submission
- Loading indicators and error handling
- Copy-to-clipboard functionality for improved content
- Pretty JSON display for debugging

## 🏗️ Project Structure

```
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── EmailController.php    # Main controller for form & analysis
│   └── Services/
│       └── AIService.php              # AI provider integrations
├── resources/
│   └── views/
│       └── email.blade.php            # Frontend UI
├── routes/
│   └── web.php                        # Route definitions
└── .env                               # Configuration & API keys
```

## ⚙️ Installation & Setup

### 1. Prerequisites
- PHP 8.2+
- Composer
- Laravel 12

### 2. Quick Start

The project is already created. Just configure your environment:

```bash
cd c:\xampp\htdocs\AI-email-debugger
```

### 3. Set API Keys

Edit `.env` file and add your API keys:

```env
# OpenAI (ChatGPT)
OPENAI_API_KEY=your_openai_api_key

# OpenRouter
OPENROUTER_API_KEY=your_openrouter_api_key

# Google Gemini
GEMINI_API_KEY=your_gemini_api_key
```

#### How to Get API Keys:

**OpenAI:**
1. Go to https://platform.openai.com/api-keys
2. Create a new API key
3. Copy and paste into `.env`

**OpenRouter:**
1. Go to https://openrouter.ai/
2. Sign up and get your API key
3. Copy and paste into `.env`

**Google Gemini:**
1. Go to https://makersuite.google.com/app/apikey
2. Create API key
3. Copy and paste into `.env`

### 4. Run the Application

```bash
# Start PHP development server
php artisan serve
```

Then open: **http://localhost:8000**

## 💡 How to Use

### Step 1: Configure SMTP
Fill in your SMTP settings:
- **SMTP Host**: e.g., `smtp.gmail.com`
- **SMTP Port**: e.g., `587`
- **SMTP Username**: Your email
- **SMTP Password**: Your password/app password
- **From Email**: Sender email address
- **To Email**: Recipient email address

### Step 2: Write Email
- **Subject**: Your email subject
- **Body**: Email content (plain text)

### Step 3: Choose AI Provider
Select one of:
- 🤖 OpenAI (ChatGPT)
- 🔀 OpenRouter
- 🎨 Google Gemini

### Step 4: Click "Test & Analyze Email"
The tool will:
1. Attempt to send the test email via SMTP
2. Capture success/error message
3. Send data to selected AI provider
4. Return analysis with recommendations

### Step 5: Review Results
You'll see:
- **SMTP Result**: Whether email sent successfully
- **AI Analysis**:
  - 🚨 Issues detected
  - ✅ Recommended fixes
  - 💌 Improved subject line
  - 📝 Improved body content
- **Copy Buttons**: Easily copy improved content

## 🧪 Testing

### Test with Gmail

```
SMTP Host: smtp.gmail.com
SMTP Port: 587
Username: your-email@gmail.com
Password: your-app-password (NOT your regular password)
```

**Note**: Enable 2FA on Gmail and generate an app-specific password for this to work.

### Test with Mailtrap (Free Service)

```
SMTP Host: send.mailtrap.io
SMTP Port: 2525
Username: Your Mailtrap username
Password: Your Mailtrap password
From Email: test@example.com
```

Visit https://mailtrap.io for free testing account.

## 🔧 Backend Logic

### Controller: `EmailController.php`
- **Validates** all form inputs
- **Dynamically configures** Laravel Mail with provided SMTP credentials
- **Sends test email** using `Mail::raw()`
- **Catches exceptions** for error handling
- **Returns JSON** response with SMTP result and AI analysis

### Service: `AIService.php`
- **Analyzes emails** based on selected provider
- **Calls appropriate API** (OpenAI, OpenRouter, or Gemini)
- **Parses JSON responses** and handles errors
- **Validates** required fields in AI response
- **Removes markdown** code blocks if present in response

### Common AI Prompt:
```
You are an email deliverability expert.
Analyze this email and SMTP result.

Check:
- Why email may go to spam
- Issues in subject line
- Content quality
- Missing best practices
- If SMTP error exists, explain it clearly

Return ONLY JSON:
{
  "issue": "...",
  "fix": "...",
  "improved_subject": "...",
  "improved_body": "..."
}
```

## 🎨 Frontend Features

- **Beautiful Bootstrap 5 UI** with gradient backgrounds
- **Responsive design** - works on mobile, tablet, desktop
- **AJAX form submission** - no page reload
- **Real-time validation** via HTML5
- **Loading spinner** during processing
- **Error alerts** with clear messages
- **Copy buttons** for quick use of improvements
- **Pretty JSON display** for debugging

## 📝 Environment Variables

```env
# Mail Configuration (overridden dynamically by form)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# AI Provider API Keys
OPENAI_API_KEY=your_key_here
OPENROUTER_API_KEY=your_key_here
GEMINI_API_KEY=your_key_here
```

## ⚠️ Important Notes

- ❌ **No database** - This is a utility tool, not a SaaS product
- ❌ **No authentication** - Use this in private/local environments
- ✅ **Error handling** - All API errors are caught and displayed
- ✅ **Input validation** - Form inputs are validated on both client & server
- ✅ **CORS friendly** - Works with external APIs

## 🛠️ Troubleshooting

### "Connection refused" Error
- Make sure your SMTP server is running
- Verify SMTP credentials are correct
- Check firewall isn't blocking the SMTP port

### "API key not set" Error
- Ensure `.env` file has your API keys
- Run `php artisan config:clear` to refresh config cache

### "Invalid JSON response" Error
- Some AI providers might include markdown formatting
- The app automatically strips markdown code blocks
- Check raw JSON response in expandable section

### SMTP Port Issues
- Port 587: TLS encryption (most common)
- Port 465: SSL encryption
- Port 25: Unencrypted (rarely used)

## 📚 Code Organization

### Request Flow:
```
Form Submit (AJAX)
    ↓
EmailController::analyzeEmail()
    ↓
Validate inputs
    ↓
Configure Laravel Mail dynamically
    ↓
Send test email (try/catch)
    ↓
Call AIService::analyzeEmail()
    ↓
Select AI Provider (OpenAI/OpenRouter/Gemini)
    ↓
Call API & parse response
    ↓
Return JSON response
    ↓
Display results in UI
```

## 🚀 Future Enhancements

Possible improvements (not included):
- Email template builder
- SMTP connection testing before sending
- Multiple SMTP profiles saved
- Email header analysis
- SPF/DKIM/DMARC checking
- Batch email testing
- Usage analytics

## 📄 License

This project is open source and available under the MIT License.

## 💬 Support

This is a developer utility tool. For issues or questions:
1. Check the troubleshooting section
2. Verify all API keys are set correctly
3. Check API provider status pages
4. Ensure SMTP credentials are valid

---

**Built with ❤️ for email developers**


Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
=======
# AI-SMTP-Debugger
Universal SMTP Testing &amp; Email Authentication Tool
>>>>>>> 470adb010e290ec0e155452cd87f342ef6844aa9
