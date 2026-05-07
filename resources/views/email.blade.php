<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Email Debugger - Universal SMTP Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-analyze {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 40px;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-analyze:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-analyze:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-test {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(54, 209, 220, 0.4);
            color: white;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-border {
            color: #667eea;
        }

        .result-section {
            display: none;
            margin-top: 30px;
            animation: slideIn 0.3s ease-in;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
        }

        .result-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-header.success { color: #28a745; }
        .result-header.error { color: #dc3545; }
        .result-header.pending { color: #ffc107; }

        .result-header i {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .result-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .smtp-result, .auth-result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .ai-analysis {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #764ba2;
        }

        .analysis-item {
            margin-bottom: 20px;
        }

        .analysis-item:last-child {
            margin-bottom: 0;
        }

        .analysis-item h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .analysis-item p, .analysis-item pre {
            margin: 0;
            color: #333;
            font-size: 0.95rem;
            line-height: 1.6;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .copy-button {
            background: #667eea;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .copy-button:hover {
            background: #764ba2;
            transform: scale(1.05);
        }

        .copy-button.copied {
            background: #28a745;
        }

        .error-alert {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin-bottom: 20px;
        }

        .json-pretty {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            overflow-x: auto;
            font-size: 0.85rem;
            font-family: 'Courier New', monospace;
            color: #333;
        }

        .smtp-log {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }

        .smtp-log .log-line {
            padding: 2px 0;
        }

        .smtp-log .log-line.error {
            color: #f44747;
        }

        .smtp-log .log-line.success {
            color: #6a9955;
        }

        .auth-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .auth-status.found {
            background: #d4edda;
            color: #155724;
        }

        .auth-status.missing {
            background: #f8d7da;
            color: #721c24;
        }

        .auth-status.invalid {
            background: #fff3cd;
            color: #856404;
        }

        .nav-tabs .nav-link {
            color: #667eea;
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            color: #764ba2;
            border-bottom: 3px solid #764ba2;
        }

        .tab-content {
            padding-top: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚀 AI Email Debugger</h1>
            <p>Universal SMTP Testing & Email Authentication Tool</p>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp" type="button" role="tab">📧 SMTP Test</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="auth-tab" data-bs-toggle="tab" data-bs-target="#auth" type="button" role="tab">🔐 Auth Check</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- SMTP Test Tab -->
            <div class="tab-pane fade show active" id="smtp" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>📧 SMTP Configuration & Email Test</h5>
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <form id="emailForm">
                            @csrf

                            <!-- SMTP Configuration Section -->
                            <div class="form-row full">
                                <h6 style="color: #667eea; font-weight: 700; margin-bottom: 15px;">SMTP Settings</h6>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label for="smtp_host" class="form-label">SMTP Host</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                        placeholder="e.g., smtp.gmail.com" required>
                                </div>
                                <div>
                                    <label for="smtp_port" class="form-label">SMTP Port</label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                        placeholder="e.g., 587" value="587" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label for="smtp_encryption" class="form-label">Encryption</label>
                                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                        <option value="tls" selected>TLS (Recommended)</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None (Plain)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="smtp_timeout" class="form-label">Timeout (seconds)</label>
                                    <input type="number" class="form-control" id="smtp_timeout" name="smtp_timeout" 
                                        placeholder="10" value="10" min="5" max="60">
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label for="smtp_username" class="form-label">SMTP Username</label>
                                    <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                        placeholder="Your email" required>
                                </div>
                                <div>
                                    <label for="smtp_password" class="form-label">SMTP Password</label>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                        placeholder="Your password" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <button type="button" class="btn-test" id="testSmtpBtn" style="width: 100%;">
                                        🔌 Test Connection Only
                                    </button>
                                </div>
                                <div>
                                    <small class="text-muted">Tests connection without sending email</small>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">

                            <!-- Email Content Section -->
                            <div class="form-row full">
                                <h6 style="color: #667eea; font-weight: 700; margin-bottom: 15px;">Email Content</h6>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label for="from_email" class="form-label">From Email</label>
                                    <input type="email" class="form-control" id="from_email" name="from_email" 
                                        placeholder="sender@example.com" required>
                                </div>
                                <div>
                                    <label for="to_email" class="form-label">To Email</label>
                                    <input type="email" class="form-control" id="to_email" name="to_email" 
                                        placeholder="recipient@example.com" required>
                                </div>
                            </div>

                            <div class="form-row full">
                                <div>
                                    <label for="subject" class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject" 
                                        placeholder="Enter email subject" required>
                                </div>
                            </div>

                            <div class="form-row full">
                                <div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_html" name="is_html" value="1">
                                        <label class="form-check-label" for="is_html">
                                            Send as HTML email
                                        </label>
                                    </div>
                                    <label for="body" class="form-label">Email Body</label>
                                    <textarea class="form-control" id="body" name="body" 
                                        placeholder="Enter email body..." required></textarea>
                                </div>
                            </div>

                            <!-- AI Provider Section -->
                            <div class="form-row full">
                                <h6 style="color: #667eea; font-weight: 700; margin-bottom: 15px; margin-top: 20px;">AI Analysis</h6>
                            </div>

                            <div class="form-row full">
                                <div>
                                    <label for="ai_provider" class="form-label">Select AI Provider</label>
                                    <select class="form-select" id="ai_provider" name="ai_provider" required>
                                        <option value="">-- Choose a Provider --</option>
                                        <option value="openai">🤖 OpenAI (ChatGPT)</option>
                                        <option value="openrouter">🔀 OpenRouter</option>
                                        <option value="gemini">🎨 Google Gemini</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-row full" style="margin-top: 30px;">
                                <button type="submit" class="btn-analyze" id="submitBtn">
                                    ⚡ Test & Analyze Email
                                </button>
                            </div>

                            <!-- Loading Indicator -->
                            <div class="loading" id="loading">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p style="margin-top: 10px; color: #667eea; font-weight: 600;">Processing your request...</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Auth Check Tab -->
            <div class="tab-pane fade" id="auth" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>🔐 Email Authentication Check (SPF/DKIM/DMARC/MX)</h5>
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <form id="authForm">
                            @csrf
                            <div class="form-row">
                                <div>
                                    <label for="domain" class="form-label">Domain to Check</label>
                                    <input type="text" class="form-control" id="domain" name="domain" 
                                        placeholder="example.com" required>
                                </div>
                                <div>
                                    <label for="dkim_selector" class="form-label">DKIM Selector (Optional)</label>
                                    <input type="text" class="form-control" id="dkim_selector" name="dkim_selector" 
                                        placeholder="default">
                                </div>
                            </div>

                            <div class="form-row full" style="margin-top: 20px;">
                                <button type="submit" class="btn-test" id="checkAuthBtn" style="width: 200px;">
                                    🔍 Check Authentication
                                </button>
                            </div>

                            <div class="loading" id="authLoading">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p style="margin-top: 10px; color: #667eea; font-weight: 600;">Checking DNS records...</p>
                            </div>
                        </form>

                        <!-- Auth Results -->
                        <div id="authResults" style="display: none; margin-top: 30px;">
                            <div class="result-card">
                                <div class="result-header">
                                    <i class="fas fa-shield-alt" style="color: #764ba2;"></i>
                                    <h5 style="color: #764ba2;">Authentication Results</h5>
                                </div>

                                <!-- SPF Result -->
                                <div class="auth-result" id="spfResult">
                                    <h6>SPF Record <span class="auth-status" id="spfStatus"></span></h6>
                                    <p id="spfMessage"></p>
                                    <div id="spfDetails" style="display: none; margin-top: 10px;">
                                        <strong>Record:</strong>
                                        <pre id="spfRecord" style="background: white; padding: 10px; border-radius: 6px; margin-top: 5px;"></pre>
                                    </div>
                                </div>

                                <!-- DKIM Result -->
                                <div class="auth-result" id="dkimResult">
                                    <h6>DKIM Record <span class="auth-status" id="dkimStatus"></span></h6>
                                    <p id="dkimMessage"></p>
                                    <div id="dkimDetails" style="display: none; margin-top: 10px;">
                                        <strong>Record:</strong>
                                        <pre id="dkimRecord" style="background: white; padding: 10px; border-radius: 6px; margin-top: 5px;"></pre>
                                    </div>
                                </div>

                                <!-- DMARC Result -->
                                <div class="auth-result" id="dmarcResult">
                                    <h6>DMARC Record <span class="auth-status" id="dmarcStatus"></span></h6>
                                    <p id="dmarcMessage"></p>
                                    <div id="dmarcDetails" style="display: none; margin-top: 10px;">
                                        <strong>Record:</strong>
                                        <pre id="dmarcRecord" style="background: white; padding: 10px; border-radius: 6px; margin-top: 5px;"></pre>
                                        <div id="dmarcPolicy" style="margin-top: 10px;"></div>
                                    </div>
                                </div>

                                <!-- MX Result -->
                                <div class="auth-result" id="mxResult">
                                    <h6>MX Records <span class="auth-status" id="mxStatus"></span></h6>
                                    <p id="mxMessage"></p>
                                    <div id="mxDetails" style="display: none; margin-top: 10px;">
                                        <ul id="mxList" style="margin: 0; padding-left: 20px;"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="result-section" id="resultSection">
            <!-- Error Alert -->
            <div id="errorAlert" style="display: none;">
                <div class="error-alert">
                    <p class="error-message" id="errorMessage"></p>
                </div>
            </div>

            <!-- SMTP Result Card -->
            <div class="result-card" id="smtpCard" style="display: none;">
                <div class="result-header" id="smtpHeader">
                    <i id="smtpIcon" class="fas fa-paper-plane"></i>
                    <h5 id="smtpTitle">SMTP Result</h5>
                </div>
                <div class="smtp-result">
                    <strong>Status:</strong> <span id="smtpStatus"></span>
                    <br>
                    <strong>Message:</strong> <span id="smtpMessage"></span>
                </div>
                <!-- SMTP Log -->
                <div id="smtpLogSection" style="display: none;">
                    <h6 style="color: #667eea; font-weight: 700;">SMTP Connection Log</h6>
                    <div class="smtp-log" id="smtpLog"></div>
                </div>
                <!-- Server Info -->
                <div id="serverInfoSection" style="display: none; margin-top: 15px;">
                    <h6 style="color: #667eea; font-weight: 700;">Server Information</h6>
                    <div id="serverInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px;"></div>
                </div>
            </div>

            <!-- AI Analysis Card -->
            <div class="result-card" id="aiCard" style="display: none;">
                <div class="result-header">
                    <i class="fas fa-brain" style="color: #764ba2;"></i>
                    <h5 style="color: #764ba2;">AI Analysis</h5>
                </div>

                <div class="ai-analysis">
                    <!-- Issue -->
                    <div class="analysis-item">
                        <h6>🚨 Issue Detected</h6>
                        <p id="aiIssue"></p>
                    </div>

                    <!-- Fix -->
                    <div class="analysis-item">
                        <h6>✅ Recommended Fix</h6>
                        <p id="aiFix"></p>
                    </div>

                    <!-- Improved Subject -->
                    <div class="analysis-item">
                        <h6>💌 Improved Subject Line</h6>
                        <p id="aiImprovedSubject" style="background: white; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"></p>
                        <button class="copy-button" onclick="copyToClipboard('aiImprovedSubject', this)">📋 Copy</button>
                    </div>

                    <!-- Improved Body -->
                    <div class="analysis-item">
                        <h6>📝 Improved Body</h6>
                        <pre id="aiImprovedBody" style="background: white; padding: 10px; border-radius: 6px; border: 1px solid #ddd; max-height: 200px; overflow-y: auto;"></pre>
                        <button class="copy-button" onclick="copyToClipboard('aiImprovedBody', this)">📋 Copy</button>
                    </div>
                </div>
            </div>

            <!-- Raw JSON Response (for debugging) -->
            <details style="margin-top: 20px;">
                <summary style="cursor: pointer; color: #667eea; font-weight: 600; padding: 10px; background: white; border-radius: 8px; border: 1px solid #ddd;">
                    📊 View Raw JSON Response
                </summary>
                <div class="json-pretty" id="rawJson" style="margin-top: 10px;"></div>
            </details>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SMTP Test Form
        const form = document.getElementById('emailForm');
        const submitBtn = document.getElementById('submitBtn');
        const loading = document.getElementById('loading');
        const resultSection = document.getElementById('resultSection');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const smtpCard = document.getElementById('smtpCard');
        const aiCard = document.getElementById('aiCard');
        const testSmtpBtn = document.getElementById('testSmtpBtn');

        // Auth Form
        const authForm = document.getElementById('authForm');
        const authLoading = document.getElementById('authLoading');
        const authResults = document.getElementById('authResults');

        // SMTP Test Form Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            resetUI();
            showLoading(true);

            try {
                const formData = new FormData(form);
                const response = await fetch('{{ route("analyze-email") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Unknown error occurred');
                }

                showResults(data);
            } catch (error) {
                showError(error.message);
            } finally {
                showLoading(false);
            }
        });

        // Test SMTP Connection Only
        testSmtpBtn.addEventListener('click', async () => {
            resetUI();
            showLoading(true);

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('smtp_host', document.getElementById('smtp_host').value);
                formData.append('smtp_port', document.getElementById('smtp_port').value);
                formData.append('smtp_encryption', document.getElementById('smtp_encryption').value);
                formData.append('smtp_timeout', document.getElementById('smtp_timeout').value);
                formData.append('smtp_username', document.getElementById('smtp_username').value);
                formData.append('smtp_password', document.getElementById('smtp_password').value);

                const response = await fetch('{{ route("test-smtp") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();
                showSmtpTestResult(data);
            } catch (error) {
                showError(error.message);
            } finally {
                showLoading(false);
            }
        });

        // Auth Form Submit
        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authLoading.style.display = 'block';
            authResults.style.display = 'none';

            try {
                const formData = new FormData(authForm);
                const response = await fetch('{{ route("check-auth") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();
                showAuthResults(data);
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                authLoading.style.display = 'none';
            }
        });

        function resetUI() {
            errorAlert.style.display = 'none';
            smtpCard.style.display = 'none';
            aiCard.style.display = 'none';
            resultSection.style.display = 'none';
            document.getElementById('smtpLogSection').style.display = 'none';
            document.getElementById('serverInfoSection').style.display = 'none';
        }

        function showLoading(show) {
            loading.style.display = show ? 'block' : 'none';
            submitBtn.disabled = show;
        }

        function showError(message) {
            resultSection.style.display = 'block';
            errorAlert.style.display = 'block';
            errorMessage.textContent = message;
        }

        function showResults(data) {
            resultSection.style.display = 'block';
            displayResults(data);
        }

        function showSmtpTestResult(data) {
            resultSection.style.display = 'block';
            smtpCard.style.display = 'block';

            const smtpStatus = document.getElementById('smtpStatus');
            const smtpMessage = document.getElementById('smtpMessage');
            const smtpHeader = document.getElementById('smtpHeader');
            const smtpIcon = document.getElementById('smtpIcon');

            if (data.success) {
                smtpStatus.textContent = 'SUCCESS';
                smtpMessage.textContent = data.message;
                smtpHeader.className = 'result-header success';
                smtpIcon.className = 'fas fa-check-circle';
            } else {
                smtpStatus.textContent = 'ERROR';
                smtpMessage.textContent = data.message;
                smtpHeader.className = 'result-header error';
                smtpIcon.className = 'fas fa-exclamation-circle';
            }

            // Show SMTP log if available
            if (data.log && data.log.length > 0) {
                const smtpLog = document.getElementById('smtpLog');
                smtpLog.innerHTML = '';
                data.log.forEach(line => {
                    const div = document.createElement('div');
                    div.className = 'log-line';
                    if (line.includes('ERROR')) div.classList.add('error');
                    if (line.includes('successfully') || line.includes('SUCCESS')) div.classList.add('success');
                    div.textContent = line;
                    smtpLog.appendChild(div);
                });
                document.getElementById('smtpLogSection').style.display = 'block';
            }

            // Show server info if available
            if (data.server_info && Object.keys(data.server_info).length > 0) {
                const serverInfo = document.getElementById('serverInfo');
                let html = '';
                if (data.server_info.capabilities) {
                    html += '<strong>Capabilities:</strong> ' + data.server_info.capabilities.join(', ') + '<br>';
                }
                if (data.server_info.max_size) {
                    html += '<strong>Max Message Size:</strong> ' + formatBytes(data.server_info.max_size);
                }
                serverInfo.innerHTML = html;
                document.getElementById('serverInfoSection').style.display = 'block';
            }
        }

        function displayResults(data) {
            // Display SMTP Result
            smtpCard.style.display = 'block';
            const smtpHeader = document.getElementById('smtpHeader');
            const smtpStatus = document.getElementById('smtpStatus');
            const smtpMessage = document.getElementById('smtpMessage');

            smtpStatus.textContent = data.smtp.status.toUpperCase();
            smtpMessage.textContent = data.smtp.message;

            smtpHeader.className = 'result-header ' + data.smtp.status;
            const icon = smtpHeader.querySelector('i');
            if (data.smtp.status === 'success') {
                icon.className = 'fas fa-check-circle';
            } else if (data.smtp.status === 'error') {
                icon.className = 'fas fa-exclamation-circle';
            } else {
                icon.className = 'fas fa-hourglass-half';
            }

            // Display AI Analysis
            if (data.ai) {
                aiCard.style.display = 'block';
                document.getElementById('aiIssue').textContent = data.ai.issue || 'No issue detected';
                document.getElementById('aiFix').textContent = data.ai.fix || 'No fix available';
                document.getElementById('aiImprovedSubject').textContent = data.ai.improved_subject || '';
                document.getElementById('aiImprovedBody').textContent = data.ai.improved_body || '';
            }

            // Display raw JSON
            document.getElementById('rawJson').textContent = JSON.stringify(data, null, 2);
        }

        function showAuthResults(data) {
            if (!data.success) {
                alert('Error: ' + (data.error || 'Unknown error'));
                return;
            }

            authResults.style.display = 'block';
            const results = data.results;

            // SPF
            displayAuthItem('spf', results.spf);
            // DKIM
            displayAuthItem('dkim', results.dkim);
            // DMARC
            displayAuthItem('dmarc', results.dmarc);
            // MX
            displayAuthItem('mx', results.mx);
        }

        function displayAuthItem(type, result) {
            const statusEl = document.getElementById(type + 'Status');
            const messageEl = document.getElementById(type + 'Message');
            const detailsEl = document.getElementById(type + 'Details');

            statusEl.textContent = result.status.toUpperCase();
            statusEl.className = 'auth-status ' + result.status;
            messageEl.textContent = result.message;

            if (result.record) {
                document.getElementById(type + 'Record').textContent = result.record;
                detailsEl.style.display = 'block';
            }

            // Special handling for DMARC policy
            if (type === 'dmarc' && result.policy) {
                let policyHtml = '<strong>Policy:</strong> ' + (result.policy.p || 'Not set') + '<br>';
                policyHtml += '<strong>Subdomain Policy:</strong> ' + (result.policy.sp || 'Not set') + '<br>';
                policyHtml += '<strong>Percentage:</strong> ' + (result.policy.pct || 100) + '%<br>';
                document.getElementById('dmarcPolicy').innerHTML = policyHtml;
            }

            // Special handling for MX records
            if (type === 'mx' && result.records) {
                const mxList = document.getElementById('mxList');
                mxList.innerHTML = '';
                result.records.forEach(record => {
                    const li = document.createElement('li');
                    li.textContent = record.target + ' (priority: ' + record.priority + ')';
                    mxList.appendChild(li);
                });
            }
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function copyToClipboard(elementId, button) {
            const element = document.getElementById(elementId);
            const text = element.textContent || element.innerText;

            navigator.clipboard.writeText(text).then(() => {
                button.textContent = '✓ Copied!';
                button.classList.add('copied');
                setTimeout(() => {
                    button.textContent = '📋 Copy';
                    button.classList.remove('copied');
                }, 2000);
            }).catch(() => {
                alert('Failed to copy');
            });
        }
    </script>
</body>
</html>