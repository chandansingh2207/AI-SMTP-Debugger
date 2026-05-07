# 🚀 Quick Setup Guide - AI Email Debugger

## ✅ What's Already Set Up

Your Laravel 12 application is **fully installed** and ready to use:

- ✅ Laravel 12 framework
- ✅ All dependencies installed
- ✅ `.env` file configured
- ✅ Application key generated
- ✅ Database initialized (SQLite)
- ✅ EmailController created
- ✅ AIService created
- ✅ Blade template (email.blade.php) created
- ✅ Routes configured

## 🎯 Next Steps (3 Easy Steps)

### Step 1: Add API Keys to `.env`

Open the `.env` file in the project root and add your API keys:

```env
# OpenAI - Get from https://platform.openai.com/api-keys
OPENAI_API_KEY=sk-your-key-here

# OpenRouter - Get from https://openrouter.ai/
OPENROUTER_API_KEY=sk-your-key-here

# Google Gemini - Get from https://makersuite.google.com/app/apikey
GEMINI_API_KEY=your-key-here
```

### Step 2: Start the Development Server

Run this command:

```bash
cd c:\xampp\htdocs\AI-email-debugger
php artisan serve
```

You should see output like:
```
   INFO  Server running on [http://127.0.0.1:8000].
```

### Step 3: Open in Browser

Go to: **http://localhost:8000**

You'll see the beautiful AI Email Debugger interface!

---

## 📧 Test It Out

### Quick Test Setup:

1. **Use a Free SMTP Service** (no API keys required):
   - **Mailtrap**: https://mailtrap.io (Free tier available)
   
   ```
   SMTP Host: send.mailtrap.io
   SMTP Port: 2525
   Username: [Your Mailtrap username]
   Password: [Your Mailtrap password]
   From Email: test@example.com
   To Email: your-email@example.com
   ```

2. **Or Use Gmail**:
   ```
   SMTP Host: smtp.gmail.com
   SMTP Port: 587
   Username: your-email@gmail.com
   Password: your-app-password*
   ```
   
   *Generate app password: https://myaccount.google.com/apppasswords

3. **Write a test email**:
   - Subject: "Great Deal! Don't Miss Out!!!"
   - Body: "Click here now! LIMITED TIME OFFER!!!"

4. **Select an AI Provider** (requires API key):
   - OpenAI
   - OpenRouter
   - Google Gemini

5. **Click "Test & Analyze Email"**

You'll see:
- ✅ SMTP result (success or error)
- 🧠 AI analysis with improvements
- 💌 Better subject line
- 📝 Improved body content
- 📋 Copy buttons for easy use

---

## 🔑 Getting API Keys

### 🤖 OpenAI (ChatGPT)
1. Go to https://platform.openai.com/api-keys
2. Sign up or log in
3. Click "Create new secret key"
4. Copy the key (shows only once!)
5. Paste into `.env` as `OPENAI_API_KEY`

### 🔀 OpenRouter
1. Go to https://openrouter.ai/
2. Click "Sign in" (top right)
3. Connect with Google/GitHub or create account
4. Go to Keys section
5. Create new key
6. Paste into `.env` as `OPENROUTER_API_KEY`

### 🎨 Google Gemini
1. Go to https://makersuite.google.com/app/apikey
2. Click "Create new API key"
3. Copy the key
4. Paste into `.env` as `GEMINI_API_KEY`

---

## 📝 File Locations

All your files are here:

```
c:\xampp\htdocs\AI-email-debugger\
├── app\Http\Controllers\EmailController.php    ← Main controller
├── app\Services\AIService.php                  ← AI integrations
├── resources\views\email.blade.php             ← Frontend UI
├── routes\web.php                              ← Routes
├── .env                                        ← Configuration
└── README.md                                   ← Full documentation
```

---

## 🧪 Test Different Scenarios

### Scenario 1: Test SMTP Connection
- Fill in SMTP credentials
- Keep Subject and Body simple
- Choose any AI provider
- If SMTP works, you'll see "Email sent successfully!"

### Scenario 2: Test AI Analysis
- Use this sample email:
  ```
  Subject: CLAIM YOUR FREE iPHONE NOW!!!
  Body: Click here now!!!!! Limited time offer for TODAY ONLY!!! 
  You won't believe this! ACT NOW!!!!
  ```
- The AI will identify spam triggers

### Scenario 3: Test Error Handling
- Intentionally use wrong SMTP credentials
- Submit the form
- See how the error is caught and displayed

---

## 🛠️ Troubleshooting

### Issue: "Port 8000 already in use"
**Solution**: Use different port
```bash
php artisan serve --port=8001
```

### Issue: "API key not set" error
**Solution**: 
1. Check you added the key to `.env`
2. Run: `php artisan config:clear`
3. Restart the server

### Issue: "SMTP Connection failed"
**Solution**: 
1. Verify SMTP credentials are correct
2. Check if port is correct (usually 587 or 465)
3. Try with Mailtrap first to test setup

### Issue: "Invalid JSON from AI"
**Solution**: 
1. Check API key is valid and has credits
2. Try with different AI provider
3. Check raw JSON response in the expandable section

---

## 🔒 Security Notes

⚠️ **This is a LOCAL UTILITY TOOL**
- Do NOT deploy to production
- Do NOT share `.env` file with API keys
- Use only in private/local environments
- No authentication - anyone with URL access can use it

---

## 📞 Support

If you encounter issues:

1. **Check .env file** - Ensure API keys are added
2. **Verify internet connection** - Needed for API calls
3. **Check API provider status** - Visit their status pages
4. **Review error message** - Click expandable JSON section for details
5. **Test SMTP separately** - Use an email client first

---

## 🎉 You're Ready!

Your **AI Email Debugger** is ready to use!

**Start the server and visit http://localhost:8000**

Good luck! 🚀

---

*Built with Laravel 12 • Powered by AI • For Email Developers*
