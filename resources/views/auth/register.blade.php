<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun Baru - RZ Store</title>
    
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

        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
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
            padding: 28px 24px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(226, 232, 240, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .auth-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #10B981, #059669);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .auth-logo-text {
            font-size: 19px;
            font-weight: 800;
            color: #10B981;
            letter-spacing: -0.5px;
        }

        .form-group {
            margin-bottom: 12px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
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
            padding: 10px 14px 10px 40px;
            color: #0F172A;
            font-size: 13.5px;
            font-family: inherit;
            transition: all 0.2s ease;
            outline: none;
        }
        .auth-input:focus {
            background: #FFFFFF;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .auth-input::placeholder {
            color: #94A3B8;
            font-size: 13px;
        }

        .btn-submit {
            width: 100%;
            background: #10B981;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            padding: 11px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }

        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 12px;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .auth-wrapper {
                max-width: 100%;
            }
            .auth-card {
                padding: 22px 18px;
                border-radius: 18px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-video-container">
        <video class="auth-video-bg" autoplay muted loop playsinline id="authVideo">
            <source src="{{ asset('videos/backgroudv.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/auth-bg.mp4') }}" type="video/mp4">
            <source src="{{ asset('videos/sonic.mp4') }}" type="video/mp4">
        </video>
        <div class="auth-video-overlay"></div>
    </div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-logo-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7.5" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <span class="auth-logo-text">RZ STORE</span>
            </div>

            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text" name="name" id="name" class="auth-input" value="{{ old('name') }}" placeholder="Nama Anda" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" name="email" id="email" class="auth-input" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" name="password" id="password" class="auth-input" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Ulangi Kata Sandi</label>
                    <div class="input-box">
                        <span class="input-icon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="auth-input" placeholder="Ulangi kata sandi" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Daftar Sekarang</span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <div style="text-align: center; margin-top: 18px; font-size: 13px; color: #64748B;">
                Sudah memiliki akun? <a href="{{ route('login') }}" style="color: #10B981; font-weight: 700; text-decoration: none;">Masuk di Sini →</a>
            </div>
        </div>
    </div>
</body>
</html>
