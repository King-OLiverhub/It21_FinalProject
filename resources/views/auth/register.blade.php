<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ThreatPulse - Register</title>
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
            padding: 20px;
        }
        .register-container {
            background: #161b22;
            border-radius: 20px;
            padding: 40px;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.7);
            border: 1px solid #30363d;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header .logo {
            font-size: 42px;
            color: #58a6ff;
            margin-bottom: 10px;
        }
        .register-header h1 {
            color: #f0f6fc;
            font-weight: 700;
            font-size: 26px;
            margin: 0;
        }
        .register-header .subtitle {
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
        .form-text {
            color: #8b949e;
            font-size: 12px;
        }
        .btn-register {
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
        .btn-register:hover {
            background: #2ea043;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(35, 134, 54, 0.3);
        }
        .btn-register:active {
            transform: translateY(0);
        }
        .login-link {
            color: #8b949e;
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #58a6ff;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
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
        .form-select {
            background: #0d1117;
            border: 1px solid #30363d;
            color: #f0f6fc;
            padding: 12px 16px;
            border-radius: 10px;
            cursor: pointer;
        }
        .form-select:focus {
            background: #0d1117;
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.15);
            color: #f0f6fc;
        }
        .form-select option {
            background: #161b22;
            color: #f0f6fc;
            padding: 10px;
        }
        .form-select option:hover {
            background: #0d1117;
        }
        .alert-danger {
            background: #da3633;
            color: white;
        }
        .alert-success {
            background: #238636;
            color: white;
        }
        .password-strength {
            margin-top: 5px;
        }
        .password-strength .progress {
            height: 4px;
            background: #0d1117;
            border-radius: 2px;
        }
        .password-strength .progress-bar {
            border-radius: 2px;
            transition: width 0.3s;
        }
        .role-card {
            background: #0d1117;
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 12px 15px;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 8px;
        }
        .role-card:hover {
            border-color: #30363d;
            background: #161b22;
        }
        .role-card.selected {
            border-color: #58a6ff;
            background: #161b22;
        }
        .role-card .role-name {
            color: #f0f6fc;
            font-weight: 600;
            font-size: 14px;
        }
        .role-card .role-desc {
            color: #8b949e;
            font-size: 12px;
            margin-top: 3px;
        }
        .role-card .role-icon {
            color: #58a6ff;
            font-size: 20px;
            margin-right: 12px;
        }
        .role-card .check-mark {
            color: #2ea043;
            font-size: 18px;
            display: none;
        }
        .role-card.selected .check-mark {
            display: block;
        }
        .role-card input[type="radio"] {
            display: none;
        }
        .role-badge-admin { border-left: 3px solid #da3633; }
        .role-badge-security { border-left: 3px solid #58a6ff; }
        .role-badge-system { border-left: 3px solid #d29922; }
        .role-badge-network { border-left: 3px solid #58a6ff; }
        .role-badge-it { border-left: 3px solid #8b949e; }
        .role-badge-officer { border-left: 3px solid #2ea043; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>Create Account</h1>
            <div class="subtitle">Join ThreatPulse Security Platform</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">
                    <i class="fas fa-user me-2"></i>Full Name
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Enter your full name" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email Address
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Create a password (min 8 characters)" required>
                </div>
                <div class="password-strength">
                    <div class="progress">
                        <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="form-text" id="passwordText">Enter a strong password</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-check-circle me-2"></i>Confirm Password
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                    <input type="password" class="form-control" id="password_confirmation" 
                           name="password_confirmation" placeholder="Confirm your password" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-user-tag me-2"></i>Account Role
                </label>
                
                <!-- Admin Role -->
                <div class="role-card role-badge-admin selected" id="card-admin" onclick="selectRole('admin')">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-cog role-icon"></i>
                            <div>
                                <div class="role-name">Administrator</div>
                                <div class="role-desc"></div>
                            </div>
                        </div>
                        <i class="fas fa-check-circle check-mark" id="check-admin" style="display: block;"></i>
                    </div>
                    <input type="radio" name="role" value="admin" id="role-admin" checked>
                </div>

                <!-- Network Administrator Role -->
                <div class="role-card role-badge-network" id="card-network_admin" onclick="selectRole('network_admin')">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-network-wired role-icon"></i>
                            <div>
                                <div class="role-name">Network Administrator</div>
                                <div class="role-desc"></div>
                            </div>
                        </div>
                        <i class="fas fa-check-circle check-mark" id="check-network_admin" style="display: none;"></i>
                    </div>
                    <input type="radio" name="role" value="network_admin" id="role-network_admin">
                </div>

                <!-- Analyst Role -->
                <div class="role-card role-badge-security" id="card-user" onclick="selectRole('security_analyst')">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt role-icon"></i>
                            <div>
                                <div class="role-name">User</div>
                                <div class="role-desc"></div>
                            </div>
                        </div>
                        <i class="fas fa-check-circle check-mark" id="check-user" style="display: none;"></i>
                    </div>
                    <input type="radio" name="role" value="security_analyst" id="role-user">
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #8b949e; font-size: 12px;">
            <i class="fas fa-shield-alt me-1"></i> Secure Registration &bull; End-to-End Encrypted
        </div>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('passwordText');
            
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[$@#&!]+/)) strength += 1;
            
            const percentage = (strength / 5) * 100;
            strengthBar.style.width = percentage + '%';
            
            if (percentage < 40) {
                text = 'Weak password';
                color = '#da3633';
            } else if (percentage < 70) {
                text = 'Medium strength';
                color = '#d29922';
            } else {
                text = 'Strong password!';
                color = '#2ea043';
            }
            
            strengthBar.style.background = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        });

        // Role selection
        function selectRole(role) {
            document.querySelectorAll('.role-card').forEach(card => card.classList.remove('selected'));
            document.querySelectorAll('.check-mark').forEach(chk => chk.style.display = 'none');
            
            const cardId = role === 'admin' ? 'card-admin'
                         : role === 'network_admin' ? 'card-network_admin'
                         : 'card-user';
            const checkId = role === 'admin' ? 'check-admin'
                          : role === 'network_admin' ? 'check-network_admin'
                          : 'check-user';
            const radioId = role === 'admin' ? 'role-admin'
                          : role === 'network_admin' ? 'role-network_admin'
                          : 'role-user';

            document.getElementById(cardId).classList.add('selected');
            document.getElementById(checkId).style.display = 'block';
            document.getElementById(radioId).checked = true;
        }

        // Set default selected role
        document.addEventListener('DOMContentLoaded', function() {
            selectRole('admin');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>