<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cars City - Solusi Servis Mobil Canggih</title>
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
      overflow-x: hidden;
      line-height: 1.6;
    }

    header {
      background: linear-gradient(90deg, #ffffff, #b3d9ff);
      padding: 15px 5%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 4px 15px rgba(0, 51, 102, 0.1);
      border-bottom: 2px solid #003366;
    }

    header h1 {
      color: #003366;
      font-family: 'Orbitron', sans-serif;
      font-size: 2.8rem;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      text-shadow: 0 0 5px rgba(0, 51, 102, 0.3);
    }

    nav {
      display: flex;
      align-items: center;
      gap: 25px;
    }

    nav a {
      color: #003366;
      text-decoration: none;
      font-weight: 500;
      font-size: 1.1rem;
      position: relative;
      transition: all 0.3s ease;
    }

    nav a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -5px;
      left: 0;
      background: #66ccff;
      transition: width 0.3s ease;
    }

    nav a:hover::after {
      width: 100%;
    }

    nav a:hover {
      color: #66ccff;
      text-shadow: 0 0 5px rgba(102, 204, 255, 0.5);
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
      min-height: 90vh;
      display: flex;
      align-items: center;
      padding: 120px 5%;
      background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
      position: relative;
      overflow: hidden;
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
      max-width: 800px;
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

    @media (max-width: 768px) {
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

      header {
        flex-direction: column;
        gap: 20px;
        padding: 15px 5%;
      }

      nav {
        flex-direction: column;
        gap: 15px;
      }

      .modal-content {
        padding: 30px;
      }

      .services {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Cars City</h1>
    <nav>
      <a href="#home">Beranda</a>
      <a href="#info">Tentang</a>
      <a href="#layanan">Layanan</a>
      <a href="#kontak">Kontak</a>
      <a href="login.php" class="login-btn">Login</a>
    </nav>
  </header>

  <section class="hero" id="home">
    <div class="hero-content">
      <h2>Selamat Datang Di Cars City</h2>
      <p>Atur reservasi Anda dan nikmati layanan premium dari Cars City.</p>
      <div class="hero-buttons">
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
    <h3>Layanan Inovatif</h3>
    <div class="services">
      <div class="service-card">
        <img src="https://images.unsplash.com/photo-1634828358957-eb0a99ef6b9e?q=80&w=2070&auto=format&fit=crop" alt="Servis Otomatis">
        <div class="service-content">
          <h4>Servis Otomatis</h4>
          <p>Perawatan cerdas berbasis AI untuk performa maksimal.</p>
          <a href="#" class="detail-btn" data-item='{
            "title": "Servis Otomatis",
            "image": "https://images.unsplash.com/photo-1634828358957-eb0a99ef6b9e?q=80&w=2070&auto=format&fit=crop",
            "description": "Servis otomatis dengan AI untuk analisis dan perawatan presisi tinggi.",
            "price": "Rp 600.000"
          }'>Lihat Detail</a>
        </div>
      </div>
      <div class="service-card">
        <img src="https://images.unsplash.com/photo-1616407588054-3b5177e6b107?q=80&w=2070&auto=format&fit=crop" alt="Upgrade Komponen">
        <div class="service-content">
          <h4>Upgrade Komponen</h4>
          <p>Penggantian komponen canggih untuk performa unggul.</p>
          <a href="#" class="detail-btn" data-item='{
            "title": "Upgrade Komponen",
            "image": "https://images.unsplash.com/photo-1616407588054-3b5177e6b107?q=80&w=2070&auto=format&fit=crop",
            "description": "Komponen terbaru dipasang dengan teknologi presisi tinggi.",
            "price": "Mulai Rp 250.000"
          }'>Lihat Detail</a>
        </div>
      </div>
      <div class="service-card">
        <img src="https://images.unsplash.com/photo-1616578737155-811d7fc59b55?q=80&w=2070&auto=format&fit=crop" alt="Diagnosa Digital">
        <div class="service-content">
          <h4>Diagnosa Digital</h4>
          <p>Perbaikan mesin dengan teknologi digital canggih.</p>
          <a href="#" class="detail-btn" data-item='{
            "title": "Diagnosa Digital",
            "image": "https://images.unsplash.com/photo-1616578737155-811d7fc59b55?q=80&w=2070&auto=format&fit=crop",
            "description": "Diagnosa mesin digital untuk solusi cepat dan akurat.",
            "price": "Mulai Rp 1.200.000"
          }'>Lihat Detail</a>
        </div>
      </div>
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
      <a href="https://wa.me/6285710010194" class="btn-contact" target="_blank">Hubungi via WhatsApp</a>
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