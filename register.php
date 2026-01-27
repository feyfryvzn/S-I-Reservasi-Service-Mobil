<?php
session_start();
include 'koneksi.php';

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf']) && $_POST['csrf'] === $_SESSION['csrf_token']) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];

    // Validasi input
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $error = "Username harus berupa email yang valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password harus minimal 6 karakter!";
    } elseif ($password !== $konfirmasi) {
        $error = "Password dan konfirmasi tidak cocok!";
    } else {
        // Cek apakah username sudah digunakan
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header("Location: dashboard_cust.php");
                exit;
            } else {
                $error = "Terjadi kesalahan saat registrasi: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Daftar - Cars City</title>
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

    .message {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 10px;
      font-size: 1rem;
    }

    .message.success {
      background: #d4f1d4;
      color: #2e7d32;
    }

    .message.error {
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

    .btn-daftar {
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

    .btn-daftar:hover {
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
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <h2>Daftar Akun</h2>
      <?php if ($success): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-4">
          <label>Username / Email</label>
          <input type="text" name="username" class="form-control" required placeholder="Masukkan email" />
        </div>
        <div class="mb-4">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" />
        </div>
        <div class="mb-4">
          <label>Konfirmasi Password</label>
          <input type="password" name="konfirmasi" class="form-control" required placeholder="Ulangi password" />
        </div>
        <button type="submit" class="btn-daftar">Daftar</button>
      </form>
      <div class="extra-links">
        Sudah punya akun? <a href="login.php">Login di sini</a>
      </div>
      <div class="extra-links">
        <a href="index.php">← Kembali ke Beranda</a>
      </div>
    </div>
  </div>
  <footer>
    © 2025 Cars City | <a href="mailto:info@carscity.com">Email Kami</a>
  </footer>
</body>
</html>