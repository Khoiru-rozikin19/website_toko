<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Akun - RZ Store</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            height: 100%;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
            overscroll-behavior: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FFFFFF;
            color: #1E293B;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        /* Background Video Container */
        .auth-video-container {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            background-color: #FFFFFF;
        }
        .auth-video-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 2;
        }
        .auth-video-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.25);
            z-index: 3;
        }

        /* Card Container matching Dashboard Theme */
        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 410px;
            animation: fadeInUp 0.35s ease-out;
            margin: auto;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            padding: 30px 26px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(226, 232, 240, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Brand Header */
        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 22px;
        }
        .auth-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4361EE, #6B8AFF);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.35);
        }
        .auth-logo-text {
            font-size: 19px;
            font-weight: 800;
            color: #4361EE;
            letter-spacing: -0.5px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }
        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            color: #94A3B8;
            display: flex;
            align-items: center;
            pointer-events: none;
        }
        .auth-input {
            width: 100%;
            background: #F8FAFC;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 11px 40px 11px 40px;
            color: #0F172A;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            transition: all 0.2s ease;
            outline: none;
        }
        .auth-input:focus {
            background: #FFFFFF;
            border-color: #4361EE;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }
        .auth-input::placeholder {
            color: #94A3B8;
            font-size: 13px;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            color: #94A3B8;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 4px;
            transition: color 0.2s;
        }
        .toggle-password:hover {
            color: #4361EE;
        }

        /* Checkbox & Links */
        .auth-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748B;
            cursor: pointer;
            user-select: none;
        }
        .checkbox-label input {
            accent-color: #4361EE;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }
        .auth-link {
            color: #4361EE;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .auth-link:hover {
            color: #2D41B0;
            text-decoration: underline;
        }

        /* Primary Button */
        .btn-submit {
            width: 100%;
            background: #4361EE;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(67, 97, 238, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background: #2D41B0;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 14px;
            font-weight: 500;
        }
        .alert-success {
            background: #D1FAE5;
            border: 1px solid #A7F3D0;
            color: #047857;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 14px;
            font-weight: 500;
        }

        /* Mobile specific styling */
        @media (max-width: 480px) {
            .auth-wrapper {
                max-width: 100%;
            }
            .auth-card {
                padding: 24px 20px;
                border-radius: 18px;
            }
        }
    </style>
</head>
<body>

    {{-- Background Video Container --}}
    <div class="auth-video-container">
        <video class="auth-video-bg" autoplay muted loop playsinline id="authVideo">
            <source src="{{ asset('videos/backgroudv.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/auth-bg.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/sonic.mp4') }}" type="video/mp4">
        </video>
        <div class="auth-video-overlay"></div>
    </div>

    {{-- Auth Card (Centered & Fixed) --}}
    <div class="auth-wrapper">
        <div class="auth-card">
            {{-- Brand Logo --}}
            <div class="auth-brand">
                <div class="auth-logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                </div>
                <span class="auth-logo-text">RZ STORE</span>
            </div>

            {{-- Alerts --}}
            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email / Nama Pengguna</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="email" name="email" id="email" class="auth-input" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" class="auth-input" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility()" title="Lihat kata sandi">
                            <svg id="eyeIcon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" checked>
                        <span>Ingat Saya</span>
                    </label>
                    <a href="{{ route('katalog') }}" class="auth-link">
                        Katalog Toko →
                    </a>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Masuk</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; font-size: 13px; color: #64748B;">
                Belum punya akun? <a href="{{ route('register') }}" class="auth-link" style="font-weight: 700;">Daftar Akun Baru</a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                `;
            } else {
                passInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }
    </script>
</body>
</html>
