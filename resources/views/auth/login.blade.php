<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ThreatPulse - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d1117 0%, #161b22 50%, #0d1117 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: #161b22;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.7);
            border: 1px solid #30363d;
        }
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-header .logo {
            font-size: 48px;
            color: #58a6ff;
            margin-bottom: 10px;
        }
        .login-header .logo img {
            max-width: 180px;
            height: auto;
            display: inline-block;
        }
        .login-header h1 {
            color: #f0f6fc;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
        }
        .login-header .subtitle {
            color: #8b949e;
            font-size: 14px;
            margin-top: 5px;
        }
        .form-control {
            background: #0d1117;
            border: 1px solid #30363d;
            color: #f0f6fc;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: #0d1117;
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.15);
            color: #f0f6fc;
        }
        .form-control::placeholder {
            color: #8b949e;
        }
        .form-label {
            color: #f0f6fc;
            font-weight: 500;
            font-size: 14px;
        }
        .btn-login {
            background: #238636;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #2ea043;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(35, 134, 54, 0.3);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .register-link {
            color: #8b949e;
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #58a6ff;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #0d1117;
            border: 1px solid #30363d;
            color: #8b949e;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .input-group .form-control:focus {
            border-left: none;
        }
        .form-check-label {
            color: #8b949e;
        }
        .form-check-input:checked {
            background-color: #238636;
            border-color: #238636;
        }
        .alert-danger {
            background: #da3633;
            color: white;
        }
        .alert-success {
            background: #238636;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="{{ asset('images/Picture1(1).png') }}" alt="ThreatPulse logo">
            </div>
            <h1></h1>
            <div></div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email Address
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter your password" required>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#" style="color: #58a6ff; text-decoration: none; font-size: 14px;">
                    Forgot password?
                </a>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>

        <div class="register-link">
            Don't have an account? <a href="{{ route('register') }}">Sign up now</a>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #8b949e; font-size: 12px;">
            <i class="fas fa-shield-alt me-1"></i> Secure &bull; Protected &bull; Encrypted
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>