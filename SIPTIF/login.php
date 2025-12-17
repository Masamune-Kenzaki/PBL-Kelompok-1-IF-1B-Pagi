<?php
// login.php
session_start();
// include 'db_config.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Logika Login Sederhana
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin.php');
        exit();
    } else {
        $error = 'Nama pengguna atau kata sandi salah!';
    }
}

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIPTIF Polibatam</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-navy: #1e3a8a;
            --polibatam-blue: #2563eb;
            --bg-deep: #0f172a;
            --glass-white: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-deep);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* --- Background Animation --- */
        .ocean-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, transparent 70%);
            filter: blur(80px);
            border-radius: 50%;
            animation: float 20s infinite alternate;
        }

        .blob-1 { top: -150px; right: -100px; }
        .blob-2 { bottom: -150px; left: -100px; animation-delay: -5s; }

        @keyframes float {
            from { transform: translate(0, 0); }
            to { transform: translate(80px, 40px); }
        }

        /* --- Card Masuk --- */
        .login-card {
            background: var(--glass-white);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            width: 90%;
            max-width: 400px;
            padding: 45px 35px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.5);
            z-index: 10;
            animation: cardEntrance 0.7s ease-out;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        /* Mengembalikan Logo Buku */
        .icon-box {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--polibatam-blue), var(--primary-navy));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 30px;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .header h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary-navy);
            letter-spacing: -1px;
        }

        .header p {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- Form --- */
        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--polibatam-blue);
            margin-bottom: 8px;
            margin-left: 4px;
        }

        .input-box {
            position: relative;
        }

        .input-box i.field-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 52px;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 16px;
            font-size: 15px;
            color: #1e293b;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--polibatam-blue);
            background: white;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            padding: 5px;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--polibatam-blue), var(--primary-navy));
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
        }

        .error-alert {
            background: #fff1f2;
            color: #be123c;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #ffe4e6;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }

        .badge-info {
            display: inline-block;
            background: #eff6ff;
            color: var(--polibatam-blue);
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .copyright {
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="ocean-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="login-card">
        <div class="header">
            <div class="icon-box">
                <i class="fas fa-book-open"></i>
            </div>
            <h1>SIPTIF</h1>
            <p>Buku Tamu & Administrasi Digital</p>
        </div>

        <?php if ($error): ?>
            <div class="error-alert">
                <i class="fas fa-circle-exclamation"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">NAMA PENGGUNA</label>
                <div class="input-box">
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan Nama Pengguna" required autocomplete="off">
                    <i class="fas fa-user field-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">KATA SANDI</label>
                <div class="input-box">
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Masukkan Kata Sandi" required>
                    <i class="fas fa-lock field-icon"></i>
                    <i class="fas fa-eye toggle-password" id="togglePass"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>MASUK</span>
                <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div class="footer">
            <div class="badge-info">
                Akses: <b>Administrator</b>
            </div>
            <p class="copyright">© 2025 Politeknik Negeri Batam</p>
        </div>
    </div>

    <script>
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('password');

        togglePass.addEventListener('click', function() {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>