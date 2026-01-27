<?php
ob_start(); // Tambahkan untuk mencegah masalah header
session_start();
include 'koneksi.php';

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Cek jika sudah login
if (isset($_SESSION['username'])) {
    header($_SESSION['role'] === 'admin' ? 'Location: dashboard_adm.php' : 'Location: dashboard_cust.php');
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf']) && $_POST['csrf'] === $_SESSION['csrf_token']) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT id_user, username, password, role FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            if (password_verify($password, $data['password'])) { // Perbandingan plain text
                $_SESSION['username'] = $data['username'];
                $_SESSION['role'] = $data['role'];
                $_SESSION['id_user'] = $data['id_user'];

                error_log("Login berhasil: username=$username, role=" . $data['role']); // Debugging
                header($data['role'] === 'admin' ? 'Location: dashboard_adm.php' : 'Location: dashboard_cust.php');
                exit;
            } else {
                $error = "Password salah!";
                error_log("Password salah untuk username=$username");
            }
        } else {
            $error = "Username tidak ditemukan!";
            error_log("Username tidak ditemukan: $username");
        }
        $stmt->close();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "CSRF token tidak valid!";
    error_log("CSRF token tidak valid: POST=" . ($_POST['csrf'] ?? 'tidak ada') . ", SESSION=" . $_SESSION['csrf_token']);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Login - Cars City</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #e6f0ff, #b3d9ff);
      color: #003366;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow: hidden;
    }

    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      width: 100%;
      max-width: 400px;
      z-index: 1;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 10px 20px rgba(0, 51, 102, 0.1);
      border: 2px solid #003366;
      width: 100%;
      text-align: center;
      animation: popIn 0.5s ease-out;
    }

    @keyframes popIn {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    .login-card h2 {
      font-family: 'Orbitron', sans-serif;
      font-size: 2.5rem;
      color: #003366;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
    }

    .icon {
      margin-bottom: 20px;
    }

    .icon img {
      width: 100px;
      height: 100px;
      border-radius: 10px;
      object-fit: cover;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.1);
    }

    .message {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 10px;
      font-size: 1rem;
      background: #f8d7da;
      color: #721c24;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #003366;
      border-radius: 10px;
      font-size: 1rem;
      color: #003366;
      background: #f5faff;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: #66ccff;
      box-shadow: 0 0 5px rgba(102, 204, 255, 0.5);
    }

    .btn-login {
      background: #003366;
      color: #ffffff;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 25px;
      font-family: 'Orbitron', sans-serif;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.5);
    }

    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 15px rgba(0, 51, 102, 0.7);
      background: #001f4d;
    }

    .extra-links {
      margin-top: 20px;
      font-size: 0.9rem;
      color: #003366;
    }

    .extra-links a {
      color: #66ccff;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .extra-links a:hover {
      color: #003366;
      text-shadow: 0 0 5px rgba(102, 204, 255, 0.5);
    }

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: linear-gradient(180deg, #b3d9ff, #e6f0ff);
      color: #003366;
      text-align: center;
      padding: 20px;
      font-size: 0.9rem;
      border-top: 2px solid #003366;
    }

    footer a {
      color: #66ccff;
      text-decoration: none;
      font-weight: 500;
    }

    footer a:hover {
      color: #003366;
    }

    @media (max-width: 768px) {
      .login-container {
        padding: 15px;
      }

      .login-card {
        padding: 20px;
      }

      .login-card h2 {
        font-size: 2rem;
      }

      .icon img {
        width: 80px;
        height: 80px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <h2>Login</h2>
      <div class="icon">
        <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?q=80&w=2070&auto=format&fit=crop" alt="Car Icon">
      </div>
      <?php if ($error): ?>
        <div class="message"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-4">
          <input type="text" name="username" class="form-control" placeholder="Email" required />
        </div>
        <div class="mb-4">
          <input type="password" name="password" class="form-control" placeholder="Password" required />
        </div>
        <button type="submit" class="btn-login">Masuk</button>
      </form>
      <div class="extra-links">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
      </div>
      <div class="extra-links">
        <a href="index.php">← Kembali ke Beranda</a>
      </div>
    </div>
  </div>
  <footer>
    © 2025 Cars City | <a href="mailto:info@carscity.com">Email Kami</a>
  </footer>
  <?php ob_end_flush(); ?>
</body>
</html>