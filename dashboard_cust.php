<?php
ob_start();
session_start();
include './koneksi.php';

// Cek jika role bukan 'user', logout dan redirect ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Cek jika URL mengandung ?logout, maka logout
if (isset($_GET['logout']) && isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM layanan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Customer - Cars City</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      overflow-x: hidden;
      line-height: 1.6;
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 70px;
      background: #003366;
      color: #ffffff;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
      transition: width 0.3s ease;
      z-index: 1001;
    }

    .sidebar:hover {
      width: 200px;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 15px 0;
      text-decoration: none;
      color: #ffffff;
      font-size: 1.2rem;
      transition: background 0.3s ease, color 0.3s ease;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a span {
      display: none;
      font-weight: 500;
    }

    .sidebar:hover a span {
      display: inline;
    }

    .sidebar a:hover {
      background: #66ccff;
      color: #003366;
    }

    .content {
      margin-left: 70px;
      width: 100%;
      padding: 120px 5% 20px;
      transition: margin-left 0.3s ease, width 0.3s ease;
    }

    .sidebar:hover ~ .content {
      margin-left: 200px;
      width: calc(100% - 200px);
    }

    header {
      background: linear-gradient(90deg, #ffffff, #b3d9ff);
      padding: 15px 5%;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      position: fixed;
      top: 0;
      width: calc(100% - 70px);
      right: 0;
      z-index: 1000;
      box-shadow: 0 4px 15px rgba(0, 51, 102, 0.1);
      border-bottom: 2px solid #003366;
      transition: width 0.3s ease;
    }

    .sidebar:hover ~ .content header {
      width: calc(100% - 200px);
    }

    header h1 {
      position: fixed;
      left: 70px; /* Matches sidebar's default width */
      top: 15px; /* Aligns with header padding */
      margin: 0;
      padding: 0 10px;
      z-index: 100; /* Above sidebar and header */
      transition: left 0.3s ease; /* Smooth transition for left position */
    }

    .sidebar:hover ~ .content h1 {
      left: 200px; /* Matches sidebar's hover width */
    }

    header span {
      margin-right: 20px;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .login-btn {
      cursor: pointer;
      color: #ffffff;
      font-weight: 700;
      font-family: 'Orbitron', sans-serif;
      padding: 10px 25px;
      border-radius: 25px;
      background: #003366;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.5);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 15px rgba(0, 51, 102, 0.7);
    }

    .hero {
      height: 100vh; /* Full viewport height */
      width: 100vw; /* Full width minus sidebar */
      display: flex;
      align-items: center;
      padding: 0 5%;
      background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
      position: relative;
      left: -70px; /* Offset to avoid sidebar overlap */
      overflow: hidden;
      transition: width 0.3s ease, left 0.3s ease; /* Smooth transition for sidebar hover */
    }

    .sidebar:hover ~ .content .hero {
      width: calc(100% - 1px); /* Adjust width when sidebar expands */
      left: 200px; /* Adjust position when sidebar expands */
    }

    

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(230, 240, 255, 0.7), rgba(179, 217, 255, 0.4));
      z-index: 1;
      animation: fadeGlow 5s infinite alternate;
    }

    @keyframes fadeGlow {
      0% { opacity: 0.7; }
      100% { opacity: 0.9; }
    }

    .hero-content {
      z-index: 2;
      text-align: center;
      animation: slideIn 1.5s ease-out;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(50px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .hero-content h2 {
      font-family: 'Orbitron', sans-serif;
      font-size: 4.5rem;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 20px;
      color: #003366;
      text-shadow: 0 0 10px rgba(0, 51, 102, 0.3);
    }

    .hero-content p {
      font-size: 1.4rem;
      font-weight: 300;
      margin-bottom: 40px;
      color: #003366;
    }

    .hero-buttons a {
      padding: 12px 35px;
      text-decoration: none;
      border-radius: 25px;
      font-weight: 600;
      font-size: 1.2rem;
      transition: all 0.3s ease;
      display: inline-block;
      margin: 0 15px;
    }

    .hero-buttons .primary {
      background: #003366;
      color: #ffffff;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.5);
    }

    .hero-buttons .primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 15px rgba(0, 51, 102, 0.7);
    }

    .hero-buttons .secondary {
      background: transparent;
      color: #003366;
      border: 2px solid #003366;
    }

    .hero-buttons .secondary:hover {
      background: #003366;
      color: #ffffff;
      transform: translateY(-3px);
    }

    .special-offer {
      padding: 80px 5%;
      text-align: center;
      margin: 80px 5%;
      border-radius: 20px;
      background: linear-gradient(135deg, #e6f0ff, #ffffff);
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 20px rgba(0, 51, 102, 0.1);
      border: 2px solid #003366;
    }

    .special-offer::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(102, 204, 255, 0.2), transparent 70%);
      z-index: 0;
      animation: rotatePulse 10s infinite linear;
    }

    @keyframes rotatePulse {
      0% { transform: rotate(0deg) scale(1); }
      50% { transform: rotate(180deg) scale(1.1); }
      100% { transform: rotate(360deg) scale(1); }
    }

    .special-offer h3 {
      font-family: 'Orbitron', sans-serif;
      font-size: 3rem;
      color: #003366;
      margin-bottom: 20px;
      font-weight: 700;
      text-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
      position: relative;
      z-index: 1;
    }

    .special-offer p {
      font-size: 1.5rem;
      color: #003366;
      margin-bottom: 30px;
      position: relative;
      z-index: 1;
    }

    .btn-special {
      display: inline-block;
      background: #003366;
      color: #ffffff;
      padding: 12px 40px;
      text-decoration: none;
      font-weight: 600;
      border-radius: 25px;
      transition: all 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.5);
      position: relative;
      z-index: 1;
    }

    .btn-special:hover {
      transform: translateY(-3px);
      box-shadow: 0 0 15px rgba(0, 51, 102, 0.7);
    }

    section {
      padding: 100px 5%;
      text-align: center;
      position: relative;
    }

    section h3 {
      font-family: 'Orbitron', sans-serif;
      font-size: 3.5rem;
      font-weight: 700;
      color: #003366;
      margin-bottom: 40px;
      position: relative;
      text-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
    }

    section h3::after {
      content: '';
      width: 80px;
      height: 4px;
      background: #66ccff;
      position: absolute;
      bottom: -15px;
      left: 50%;
      transform: translateX(-50%);
      animation: glowLine 2s infinite alternate;
    }

    @keyframes glowLine {
      0% { box-shadow: none; }
      100% { box-shadow: 0 0 10px #66ccff; }
    }

    .info {
      background: linear-gradient(180deg, #ffffff, #e6f0ff);
    }

    .services {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin-top: 60px;
    }

    .layanan {
      background: linear-gradient(180deg, #e6f0ff, #ffffff);
    }

    .service-card {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 6px 20px rgba(0, 51, 102, 0.1);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      position: relative;
      border: 1px solid #003366;
    }

    .service-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, #003366, #66ccff);
      z-index: 1;
    }

    .service-card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .service-card:hover img {
      transform: scale(1.1);
    }

    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 10px 25px rgba(0, 51, 102, 0.2);
    }

    .service-content {
      padding: 20px;
      text-align: left;
      position: relative;
      z-index: 2;
    }

    .service-content h4 {
      font-family: 'Orbitron', sans-serif;
      font-size: 1.7rem;
      font-weight: 600;
      color: #003366;
      margin-bottom: 15px;
      text-shadow: 0 0 3px rgba(0, 51, 102, 0.2);
    }

    .service-content p {
      font-size: 1rem;
      color: #003366;
      margin-bottom: 20px;
    }

    .service-content a {
      padding: 10px 25px;
      background: #003366;
      color: #ffffff;
      text-decoration: none;
      border-radius: 20px;
      font-weight: 600;
      transition: background 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.3);
    }

    .service-content a:hover {
      background: #001f4d;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(179, 217, 255, 0.95);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      max-width: 600px;
      width: 90%;
      padding: 40px;
      text-align: center;
      position: relative;
      animation: popIn 0.5s ease-out;
      box-shadow: 0 10px 20px rgba(0, 51, 102, 0.1);
      border: 2px solid #003366;
    }

    @keyframes popIn {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    .modal-content img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.1);
    }

    .modal-content h4 {
      font-family: 'Orbitron', sans-serif;
      font-size: 2rem;
      color: #003366;
      margin-bottom: 15px;
      font-weight: 600;
      text-shadow: 0 0 3px rgba(0, 51, 102, 0.2);
    }

    .modal-content p {
      font-size: 1.1rem;
      color: #003366;
      margin-bottom: 20px;
    }

    .modal-content .btn-close {
      position: absolute;
      top: 15px;
      right: 15px;
      background: #ff6666;
      color: #ffffff;
      border: none;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      font-size: 1.2rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .modal-content .btn-close:hover {
      background: #cc3333;
    }

    .modal-content .btn-contact {
      background: #003366;
      color: #ffffff;
      padding: 10px 35px;
      text-decoration: none;
      border-radius: 20px;
      font-weight: 600;
      transition: background 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.3);
    }

    .modal-content .btn-contact:hover {
      background: #001f4d;
    }

    .contact {
      background: linear-gradient(180deg, #e6f0ff, #ffffff);
      padding: 80px 5%;
      margin-top: 80px;
      position: relative;
      overflow: hidden;
    }

    .contact::before {
      content: '';
      position: absolute;
      top: -30%;
      right: -10%;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(102, 204, 255, 0.2), transparent 70%);
      z-index: 0;
      animation: pulseGlow 8s infinite alternate;
    }

    @keyframes pulseGlow {
      0% { transform: scale(1); opacity: 0.5; }
      100% { transform: scale(1.1); opacity: 0.8; }
    }

    .contact-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      margin-top: 60px;
      position: relative;
      z-index: 1;
    }

    .contact-map iframe {
      width: 100%;
      height: 400px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0, 51, 102, 0.1);
    }

    .contact-details h4 {
      font-family: 'Orbitron', sans-serif;
      font-size: 2.5rem;
      font-weight: 600;
      color: #003366;
      margin-bottom: 25px;
      text-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
    }

    .contact-details p, .contact-details a {
      font-size: 1.2rem;
      color: #003366;
      margin-bottom: 25px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 15px;
      transition: color 0.3s ease;
    }

    .contact-details a:hover {
      color: #66ccff;
      text-shadow: 0 0 5px rgba(102, 204, 255, 0.5);
    }

    footer {
      background: linear-gradient(180deg, #b3d9ff, #e6f0ff);
      color: #003366;
      text-align: center;
      padding: 60px 5%;
      font-size: 1rem;
      border-top: 2px solid #003366;
    }

    footer .social {
      margin: 30px 0;
      display: flex;
      justify-content: center;
      gap: 30px;
    }

    footer .social a {
      color: #003366;
      font-size: 2rem;
      transition: all 0.3s ease;
    }

    footer .social a:hover {
      color: #66ccff;
      transform: scale(1.2);
      text-shadow: 0 0 5px rgba(102, 204, 255, 0.5);
    }

    footer .info {
      margin-top: 20px;
    }

    .floating-reservation {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
    }

    .floating-reservation a {
      display: flex;
      align-items: center;
      padding: 10px 20px;
      background: #003366;
      color: #ffffff;
      text-decoration: none;
      font-weight: 600;
      border-radius: 20px;
      box-shadow: 0 0 10px rgba(0, 51, 102, 0.5);
      transition: all 0.3s ease;
    }

    .floating-reservation a:hover {
      transform: scale(1.1);
      box-shadow: 0 0 15px rgba(0, 51, 102, 0.7);
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 60px;
      }

      .sidebar:hover {
        width: 180px;
      }

      .sidebar:hover ~ .content {
        margin-left: 180px;
        width: calc(100% - 180px);
      }

      .sidebar:hover ~ .content header {
        width: calc(100% - 180px);
      }

      .hero {
        min-height: 70vh;
        padding: 80px 5%;
      }

      .hero-content h2 {
        font-size: 3rem;
      }

      .hero-content p {
        font-size: 1.1rem;
      }

      .contact-container {
        grid-template-columns: 1fr;
      }

      .contact-map iframe {
        height: 300px;
      }

      .modal-content {
        padding: 30px;
      }

      .services {
        grid-template-columns: 1fr;
      }

      .floating-reservation a {
        padding: 8px 15px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <a href="#home"><i class="fas fa-home"></i><span>Beranda</span></a>
    <a href="#info"><i class="fas fa-info"></i><span>Tentang</span></a>
    <a href="#layanan"><i class="fas fa-wrench"></i><span>Layanan</span></a>
    <a href="#kontak"><i class="fas fa-phone"></i><span>Kontak</span></a>
    <a href="./riwayat_service/riwayat_reservasi.php"><i class="fas fa-calendar-check"></i><span>Riwayat</span></a>
  </div>

  <div class="content">
    <header>
    <h1 style="text-align: left;">Cars City</h1>
      <span>👋 <?= htmlspecialchars($_SESSION['username']) ?></span>
      <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </header>

    <section class="hero" id="home">
      <div class="hero-content">
        <h2>Selamat Datang, <?= htmlspecialchars(explode('@', $_SESSION['username'])[0]) ?>!</h2>
        <p>Atur reservasi Anda dan nikmati layanan premium dari Cars City.</p>
        <div class="hero-buttons">
          <a href="reservasi.php" class="primary">Buat Reservasi</a>
          <a href="#layanan" class="secondary">Lihat Layanan</a>
        </div>
      </div>
    </section>

    <section class="special-offer">
      <h3>Program Loyalitas Cerdas</h3>
      <p>Kumpulkan poin pintar untuk servis dan tukar dengan inovasi terbaru atau diskon eksklusif.</p>
      <a href="https://wa.me/6285710010194" class="btn-special" target="_blank">Bergabung Sekarang</a>
    </section>

    <section class="info" id="info">
      <h3>Tentang Kami</h3>
      <p>Cars City menggabungkan AI canggih dan teknisi ahli untuk memberikan solusi kendaraan masa depan.</p>
    </section>

    <section class="layanan" id="layanan">
      <h3>Layanan Kami</h3>
      <div class="services">

        <?php while ($layanan = mysqli_fetch_assoc($query)) : ?>
          <div class="service-card">
            <!-- Jika tidak pakai gambar, bisa ganti dengan ikon atau div kosong -->
            <div style="height: 180px; background-color: #eaeaea; border-radius: 8px;"></div>
            <div class="service-content">
              <h4><?= htmlspecialchars($layanan['jenis_layanan']) ?></h4>
              <p><?= htmlspecialchars($layanan['deskripsi']) ?></p>
              <a href="#" class="detail-btn" data-item='<?= json_encode([
                "title" => $layanan['jenis_layanan'],
                "image" => "", // Kosong karena tidak pakai img
                "description" => $layanan['deskripsi'],
                "price" => "Rp " . number_format($layanan['harga'], 0, ',', '.')
              ]) ?>'>Lihat Detail</a>
            </div>
          </div>
        <?php endwhile; ?>

      </div>
    </section>

    <section class="contact" id="kontak">
      <h3>Hubungi Kami</h3>
      <div class="contact-container">
        <div class="contact-map">
          <iframe src="https://maps.app.goo.gl/tq1JuLzy6K8dqqSt8" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="contact-details">
          <h4>Informasi Kontak</h4>
          <p><i class="fas fa-map-marker-alt"></i> Jl. Danau Sunter Utara No.13 Blok F 20, RT.11/RW.12, Sunter Agung, Kec. Tj. Priok, Jkt Utara, Daerah Khusus Ibukota Jakarta 14350</p>
          <a href="mailto:info@carscity.com"><i class="fas fa-envelope"></i> info@carscity.com</a>
          <a href="https://instagram.com/carscity" target="_blank"><i class="fab fa-instagram"></i> @carscity</a>
          <a href="https://facebook.com/carscity" target="_blank"><i class="fab fa-facebook"></i> Cars City</a>
        </div>
      </div>
    </section>

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

    <div class="modal" id="detailModal">
      <div class="modal-content">
        <button class="btn-close">×</button>
        <img id="modalImage" src="" alt="Layanan">
        <h4 id="modalTitle"></h4>
        <p id="modalDescription"></p>
        <p><strong>Harga:</strong> <span id="modalPrice"></span></p>
        <a href="https://wa.me/6285710010194" class="btn-contact" target="_blank">Hubungi Sekarang</a>
      </div>
    </div>

    <div class="floating-reservation">
      <a href="reservasi.php"><i class="fas fa-calendar-check"></i> Reservasi</a>
    </div>
  </div>

  <script>
    const modal = document.getElementById('detailModal');
    const detailButtons = document.querySelectorAll('.detail-btn');
    const closeButton = document.querySelector('.btn-close');

    detailButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        e.preventDefault();
        const item = JSON.parse(button.getAttribute('data-item'));
        document.getElementById('modalImage').src = item.image;
        document.getElementById('modalTitle').textContent = item.title;
        document.getElementById('modalDescription').textContent = item.description;
        document.getElementById('modalPrice').textContent = item.price;
        modal.style.display = 'flex';
      });
    });

    closeButton.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    // Parallax effect
    window.addEventListener('scroll', () => {
      const hero = document.querySelector('.hero');
      const specialOffer = document.querySelector('.special-offer');
      const scrollPosition = window.pageYOffset;
      hero.style.backgroundPositionY = `${scrollPosition * 0.2}px`;
      specialOffer.style.backgroundPositionY = `${scrollPosition * 0.15}px`;
    });
  </script>
</body>
</html>
<?php ob_end_flush(); ?>