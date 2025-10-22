<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                overflow-x: hidden;
            }

            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                z-index: 1;
            }

            .form-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 20px;
                padding: 40px;
                max-width: 450px;
                width: 100%;
                box-shadow:
                    0 20px 40px rgba(0, 0, 0, 0.1),
                    0 15px 12px rgba(0, 0, 0, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 0.6);
                position: relative;
                z-index: 2;
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-header {
                text-align: center;
                margin-bottom: 35px;
                position: relative;
            }

            .icon-container {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-5px); }
            }

            .icon-container svg {
                width: 35px;
                height: 35px;
                color: white;
            }

            .form-header h1 {
                font-size: 28px;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 8px;
                letter-spacing: -0.5px;
            }

            .form-header p {
                font-size: 16px;
                color: #718096;
                font-weight: 400;
                line-height: 1.5;
            }

            .form-group {
                margin-bottom: 25px;
                position: relative;
            }

            .form-group label {
                display: block;
                font-size: 15px;
                font-weight: 600;
                color: #4a5568;
                margin-bottom: 8px;
                letter-spacing: -0.2px;
            }

            .input-wrapper {
                position: relative;
            }

            .form-group input {
                width: 100%;
                padding: 16px 20px;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                font-size: 16px;
                color: #2d3748;
                background: #ffffff;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-weight: 500;
                letter-spacing: 0.5px;
            }

            .form-group input:focus {
                border-color: #667eea;
                outline: none;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                transform: translateY(-1px);
            }

            .form-group input::placeholder {
                color: #a0aec0;
                font-weight: 400;
            }

            .form-group .help-text {
                font-size: 13px;
                color: #718096;
                margin-top: 6px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .help-text::before {
                content: 'ℹ';
                color: #667eea;
                font-weight: 600;
            }

            .form-button {
                width: 100%;
                padding: 16px 24px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                letter-spacing: 0.3px;
                position: relative;
                overflow: hidden;
            }

            .form-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s;
            }

            .form-button:hover::before {
                left: 100%;
            }

            .form-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            }

            .form-button:active {
                transform: translateY(0);
            }

            .security-info {
                text-align: center;
                margin-top: 30px;
                padding: 20px;
                background: rgba(72, 187, 120, 0.1);
                border-radius: 12px;
                border: 1px solid rgba(72, 187, 120, 0.2);
            }

            .security-info svg {
                vertical-align: middle;
                margin-right: 8px;
                color: #48bb78;
                width: 18px;
                height: 18px;
            }

            .security-info span {
                font-size: 14px;
                color: #38a169;
                font-weight: 500;
            }

            @media (max-width: 480px) {
                .form-container {
                    padding: 30px 25px;
                    margin: 20px;
                }

                .form-header h1 {
                    font-size: 24px;
                }

                .icon-container {
                    width: 70px;
                    height: 70px;
                }

                .icon-container svg {
                    width: 30px;
                    height: 30px;
                }
            }

            /* Loading animation */
            .form-button.loading {
                pointer-events: none;
                position: relative;
            }

            .form-button.loading::after {
                content: '';
                position: absolute;
                width: 20px;
                height: 20px;
                top: 50%;
                left: 50%;
                margin-left: -10px;
                margin-top: -10px;
                border: 2px solid transparent;
                border-top: 2px solid #ffffff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

    </head>
    <body>
        <div class="form-container">
            <div class="form-header">
                <div class="icon-container">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0V4a2 2 0 014 0v2"></path>
                    </svg>
                </div>
                <h1>Form Input NIP/nip</h1>
            </div>
            <form method="POST" action="{{ route('atur_bawahan') }}" onsubmit="showLoading()">
                @csrf
                <div class="form-group">
                    <label for="nip">NIP (Nomor Induk Pegawai)</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            maxlength="20"
                            required
                            placeholder="Contoh: 1234567890123456"
                        >
                    </div>
                    <div class="help-text">NIP maksimal 20 digit angka</div>
                </div>
                <button type="submit" class="form-button" id="submitBtn">
                    <span id="btnText">Lihat Pegawai</span>
                </button>
            </form>
            <div class="security-info">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>Data Anda dienkripsi dan terlindungi dengan aman</span>
            </div>

            <script>
                function formatnip(input) {
                    // Add visual feedback for nip length
                    const length = input.value.length;
                    if (length === 16) {
                        input.style.borderColor = '#48bb78';
                        input.style.boxShadow = '0 0 0 3px rgba(72, 187, 120, 0.1)';
                    } else if (length > 0 && length < 16) {
                        input.style.borderColor = '#ed8936';
                        input.style.boxShadow = '0 0 0 3px rgba(237, 137, 54, 0.1)';
                    } else {
                        input.style.borderColor = '#e2e8f0';
                        input.style.boxShadow = 'none';
                    }
                }

                function showLoading() {
                    const btn = document.getElementById('submitBtn');
                    const btnText = document.getElementById('btnText');
                    btn.classList.add('loading');
                    btnText.textContent = 'Memverifikasi...';
                }

                // Add enter key support
                document.getElementById('nip').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (this.value.length === 16) {
                            this.form.submit();
                        }
                    }
                });
            </script>
        </div>
    </body>
</html>
