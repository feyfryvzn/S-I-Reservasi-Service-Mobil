<?php
ob_start();
session_start();
include './koneksi.php';
if (isset($_GET['logout']) && isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!$conn || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terinisialisasi"));
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check authentication
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['user', 'customer'])) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$username = $_SESSION['username'] ?? 'Unknown';

// Fetch id_user
$stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
if ($stmt === false) {
    $error = "Gagal menyiapkan statement id_user: " . $conn->error;
    error_log("Gagal menyiapkan statement id_user: " . $conn->error);
} else {
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        $error = "Gagal eksekusi query id_user: " . $stmt->error;
        error_log("Gagal eksekusi query id_user: " . $stmt->error);
    } else {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if (!$user) {
            session_destroy();
            header("Location: ../login.php");
            exit;
        }
        $id_user = $user['id_user'];
    }
    $stmt->close();
}

// Extract display name
$display_name = isset($username) ? explode('@', $username)[0] : 'Pengguna';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['csrf']) && $_POST['csrf'] === $_SESSION['csrf_token']) {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
    $nopolisi = trim($_POST['nopolisi'] ?? '');
    $no_mesin = trim($_POST['no_mesin'] ?? '');
    $merk = trim($_POST['merk'] ?? '');
    $tanggal_servis = $_POST['tanggal_servis'] ?? '';
    $waktu_servis = $_POST['waktu_servis'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');

    if (empty($nama_lengkap) || empty($nomor_telepon) || empty($nopolisi) || empty($no_mesin) || empty($merk) || empty($tanggal_servis) || empty($waktu_servis)) {
        $error = "Semua kolom wajib diisi kecuali catatan!";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $nomor_telepon)) {
        $error = "Nomor telepon tidak valid! Harus 10-15 digit angka.";
    } elseif (!preg_match("/^[A-Z]{1,2} [0-9]{1,4} [A-Z]{1,3}$/i", $nopolisi)) {
        $error = "Nomor polisi tidak valid! Contoh: B 1234 ABC.";
    } elseif (!preg_match("/^[A-Za-z0-9]{5,20}$/", $no_mesin)) {
        $error = "Nomor mesin tidak valid! Harus 5-20 karakter alfanumerik.";
    } elseif (strtotime($tanggal_servis) < strtotime(date('Y-m-d'))) {
        $error = "Tanggal servis tidak boleh di masa lalu!";
    } elseif (strtotime($waktu_servis) < strtotime('08:00') || strtotime($waktu_servis) > strtotime('17:00')) {
        $error = "Waktu servis harus antara 08:00 dan 17:00!";
    } else {
        $conn->begin_transaction();
        try {
            $max_attempts = 100;
            $attempt = 0;
            do {
                $id_reservasi = 'RES' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $stmt = $conn->prepare("SELECT id_reservasi FROM reservasi WHERE id_reservasi = ?");
                $stmt->bind_param("s", $id_reservasi);
                $stmt->execute();
                $result = $stmt->get_result();
                $exists = $result->fetch_assoc();
                $stmt->close();
                $attempt++;
                if ($attempt >= $max_attempts) {
                    throw new Exception("Gagal menghasilkan id_reservasi unik setelah $max_attempts percobaan.");
                }
            } while ($exists);

            $total_harga = 0;
            $status = 'Pending';
            $status_pembayaran = 'Belum Lunas';

            // 1. Masukkan dulu ke tabel reservasi
            $stmt = $conn->prepare("INSERT INTO reservasi (
                id_reservasi, id_user, merk, nopolisi, no_mesin, nama_lengkap, nomor_telepon,
                tanggal_servis, waktu_servis, catatan, status, status_pembayaran, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sissssssssss",
                $id_reservasi, $id_user, $merk, $nopolisi, $no_mesin,
                $nama_lengkap, $nomor_telepon, $tanggal_servis, $waktu_servis,
                $catatan, $status, $status_pembayaran
            );
            $stmt->execute();
            $stmt->close();

            // 2. Masukkan ke detail_reservasi
            if (!empty($_POST['layanan'])) {
                foreach ($_POST['layanan'] as $id_layanan) {
                    $id_layanan = mysqli_real_escape_string($conn, $id_layanan);
                    $q = mysqli_query($conn, "SELECT harga FROM layanan WHERE id_layanan = '$id_layanan'");
                    $data = mysqli_fetch_assoc($q);
                    $harga = $data['harga'];
                    $jumlah = isset($_POST['quantity'][$id_layanan]) ? intval($_POST['quantity'][$id_layanan]) : 1;
                    if ($jumlah < 1) $jumlah = 1;
                    $subtotal = $harga * $jumlah;
                    $total_harga += $subtotal;
                    $stmt = $conn->prepare("INSERT INTO detail_reservasi (id_reservasi, id_layanan, jumlah, harga, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssidd", $id_reservasi, $id_layanan, $jumlah, $harga, $subtotal);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // 3. Update reservasi dengan total_harga
            $stmt = $conn->prepare("UPDATE reservasi SET total_harga = ? WHERE id_reservasi = ?");
            $stmt->bind_param("ds", $total_harga, $id_reservasi);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $_SESSION['success_message'] = "Reservasi berhasil dibuat dengan ID: $id_reservasi!";
            echo "<script>
                alert('Reservasi berhasil dibuat dengan ID: $id_reservasi!');
                setTimeout(() => {
                    window.location.href = './dashboard_cust.php';
                }, 1000);
            </script>";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Gagal membuat reservasi: " . $e->getMessage();
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "CSRF token tidak valid!";
}

// Fetch reservations for display
$stmt = $conn->prepare("SELECT id_reservasi, tanggal_servis, waktu_servis, status, no_mesin FROM reservasi WHERE id_user = ? ORDER BY created_at DESC");
if ($stmt === false) {
    $error = "Gagal menyiapkan statement reservasi: " . $conn->error;
    error_log("Gagal menyiapkan statement reservasi: " . $conn->error);
} else {
    $stmt->bind_param("i", $id_user);
    if (!$stmt->execute()) {
        $error = "Gagal eksekusi query reservasi: " . $stmt->error;
        error_log("Gagal eksekusi query reservasi: " . $stmt->error);
    } else {
        $result = $stmt->get_result();
        $reservasi = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Reservasi - Cars City</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: #f4f7fc;
      color: #1a3557;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background: #ffffff;
      padding: 1rem 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    header h1 {
      color: #1a3557;
      font-size: 1.8rem;
      font-weight: 600;
    }

    nav {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    nav a {
      color: #1a3557;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 400;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #3b82f6;
    }

    .login-btn {
      background: #3b82f6;
      color: #ffffff;
      padding: 0.5rem 1.5rem;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    .login-btn:hover {
      background: #2563eb;
    }

    .container {
      max-width: 800px;
      margin: 100px auto 2rem;
      padding: 0 1.5rem;
      flex: 1;
    }

    h2 {
      font-size: 2rem;
      color: #1a3557;
      margin-bottom: 2rem;
      text-align: center;
      font-weight: 600;
    }

    .form-container {
      background: #ffffff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .form-section {
      margin-bottom: 1.5rem;
    }

    .form-section h3 {
      font-size: 1.5rem;
      color: #1a3557;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    .form-group label {
      display: block;
      font-size: 0.9rem;
      font-weight: 500;
      color: #1a3557;
      margin-bottom: 0.5rem;
    }

    .form-control {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #d1d5db;
      border-radius: 5px;
      font-size: 1rem;
      color: #1a3557;
      background: #f9fafb;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
      outline: none;
    }

    select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%231a3557' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'%3E%3C/path%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 0.8rem center;
      background-size: 12px;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 100px;
    }

    .btn-submit {
      background: #3b82f6;
      color: #ffffff;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
      width: 100%;
    }

    .btn-submit:hover {
      background: #2563eb;
      transform: translateY(-2px);
    }

    .message {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 5.elem5px;
      text-align: center;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .success {
      background: #dcfce7;
      color: #166534;
    }

    .error {
      background: #fee2e2;
      color: #991b1b;
    }

    footer {
      background: #1a3557;
      color: #ffffff;
      text-align: center;
      padding: 2rem 5%;
      margin-top: auto;
    }

    .social {
      margin: 1rem 0;
      display: flex;
      justify-content: center;
      gap: 1.5rem;
    }

    .social a {
      color: #ffffff;
      font-size: 1.5rem;
      transition: color 0.3s ease;
    }

    .social a:hover {
      color: #3b82f6;
    }

    .info p {
      margin: 0.5rem 0;
      font-size: 0.9rem;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 5%;
      }

      nav {
        flex-direction: column;
        gap: 1rem;
      }

      .container {
        margin-top: 120px;
        padding: 0 1rem;
      }

      .form-container {
        padding: 1.5rem;
      }

      h2 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Cars City</h1>
    <nav>
      <a href="../index.html#home">Beranda</a>
      <a href="../index.html#info">Tentang</a>
      <a href="../index.html#layanan">Layanan</a>
      <a href="../index.html#kontak">Kontak</a>
      <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </nav>
  </header>

  <div class="container">
    <h2>Selamat Datang, <?= htmlspecialchars($display_name) ?>! Buat Reservasi Servis</h2>
    <div class="form-container">
      <?php if ($success): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-section">
          <h3>Data Pribadi</h3>
          <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap Anda" required value="<?= htmlspecialchars($nama_lengkap ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="nomor_telepon">Nomor Telepon</label>
            <input type="number" id="nomor_telepon" name="nomor_telepon" class="form-control" placeholder="Masukkan nomor telepon (contoh: 081234567890)" required value="<?= htmlspecialchars($nomor_telepon ?? '') ?>">
          </div>
        </div>
        <div class="form-section">
          <h3>Data Kendaraan</h3>
          <div class="form-group">
            <label for="nopolisi">Nomor Polisi Kendaraan</label>
            <input type="text" id="nopolisi" name="nopolisi" class="form-control" placeholder="Masukkan nomor polisi (contoh: B 1234 ABC)" required value="<?= htmlspecialchars($nopolisi ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="no_mesin">Nomor Mesin</label>
            <input type="text" id="no_mesin" name="no_mesin" class="form-control" placeholder="Masukkan nomor mesin (contoh: A123456)" required value="<?= htmlspecialchars($no_mesin ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="merk">Merk Kendaraan</label>
            <input type="text" id="merk" name="merk" class="form-control" placeholder="Masukkan merk kendaraan (contoh: Toyota)" required value="<?= htmlspecialchars($merk ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="tanggal_servis">Tanggal Servis</label>
            <input type="date" id="tanggal_servis" name="tanggal_servis" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($tanggal_servis ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="waktu_servis">Waktu Servis</label>
            <input type="time" id="waktu_servis" name="waktu_servis" class="form-control" required min="08:00" max="17:00" value="<?= htmlspecialchars($waktu_servis ?? '') ?>">
          </div>
        </div>
        <div class="form-section">
          <h3>Jenis Layanan</h3>
            <div class="form-group">
                <label for="layanan">Pilih Layanan</label>
                <select id="layanan" name="layanan[]" class="form-control select2" multiple required>
                <?php
                include 'koneksi.php';
                $query = mysqli_query($conn, "SELECT * FROM layanan");
                while ($row = mysqli_fetch_assoc($query)) {
                    echo "<option value='{$row['id_layanan']}' data-harga='{$row['harga']}'>{$row['jenis_layanan']} - Rp " . number_format($row['harga'], 0, ',', '.') . "</option>";
                }
                ?>
                </select>
            </div>
            <div id="quantity-container"></div>
            <div class="form-group">
                <label for="catatan">Catatan (Opsional)</label>
                <textarea id="catatan" name="catatan" class="form-control" placeholder="Masukkan catatan tambahan"><?= htmlspecialchars($catatan ?? '') ?></textarea>
            </div>
        </div>

        <div id="totalHarga" style="margin-top: 10px; font-weight: bold;">
          Total Harga: Rp 0
        </div>

        <button type="submit" class="btn-submit">Buat Reservasi</button>
      </form>
    </div>
  </div>

  <footer>
    <div class="social">
      <a href="https://instagram.com/carscity" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://facebook.com/carscity" target="_blank"><i class="fab fa-facebook"></i></a>
    </div>
    <div class="info">
      <p><strong>Alamat:</strong> Jl. Danau Sunter Utara No.13 Blok F 20, RT.11/RW.12, Sunter Agung, Kec. Tj. Priok, Jkt Utara, Daerah Khusus Ibukota Jakarta 14350</p>
    </div>
    <p>© 2025 Cars City. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2({
        placeholder: "Pilih layanan",
        allowClear: true
      });

      $('.select2').on('change', function() {
        updateHarga();
      });
    });

    function updateQuantities() {
        let selectedOptions = $('#layanan').find('option:selected');
        let container = $('#quantity-container');
        container.empty();
        selectedOptions.each(function() {
            let serviceId = $(this).val();
            let serviceName = $(this).text();
            let inputHtml = `
                <div class="form-group">
                    <label for="quantity-${serviceId}">${serviceName}</label>
                    <input type="number" name="quantity[${serviceId}]" class="form-control quantity-input" data-service-id="${serviceId}" value="1" min="1">
                </div>
            `;
            container.append(inputHtml);
        });
        updateHarga();
    }

    function updateHarga() {
        let total = 0;
        $('#quantity-container input.quantity-input').each(function() {
            let serviceId = $(this).data('service-id');
            let quantity = parseInt($(this).val()) || 0;
            let price = parseInt($('#layanan option[value="' + serviceId + '"]').data('harga')) || 0;
            total += price * quantity;
        });
        $('#totalHarga').text('Total Harga: Rp ' + total.toLocaleString('id-ID'));
    }

    $('#layanan').on('change', updateQuantities);
    $('#quantity-container').on('change', 'input.quantity-input', updateHarga);
    updateQuantities(); // Panggil awal biar inisialisasi
  </script>
</body>
</html>