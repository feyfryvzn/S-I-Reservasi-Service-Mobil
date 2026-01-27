<?php
    include '../koneksi.php';

    if (!isset($_GET['id_reservasi'])) {
        die("ID reservasi tidak ditemukan.");
    }

    $id_reservasi = $_GET['id_reservasi'];

    // Ambil data reservasi utama
    $query_reservasi = mysqli_query($conn, "SELECT * FROM reservasi WHERE id_reservasi = '$id_reservasi'");
    $reservasi = mysqli_fetch_assoc($query_reservasi);

    $sql_detail = "
        SELECT 
            dr.id_layanan,
            l.jenis_layanan,
            dr.jumlah,
            dr.harga,
            dr.subtotal
        FROM detail_reservasi dr
        JOIN layanan l ON dr.id_layanan = l.id_layanan
        WHERE dr.id_reservasi = ?
    ";
    $stmt_detail = $conn->prepare($sql_detail);
    $stmt_detail->bind_param("s", $id_reservasi);
    $stmt_detail->execute();
    $result_detail = $stmt_detail->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CC Cars City - Invoice</title>
    <style>
        @page {
            size: 16cm 10.5cm;
            margin: 0.3cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            width: 15.4cm;
            height: 9.9cm;
            padding: 0.2cm;
            background: #fff;
            overflow: hidden;
        }
        
        .invoice-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #1e3a8a;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            flex: 1;
        }
        
        .logo {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            border: 1px solid #1e3a8a;
            background: #f8f9fa;
        }
        
        .logo img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        
        .company-name .cars {
            color: #1e3a8a;
        }
        
        .company-name .city {
            color: #ff8c00;
        }
        
        .tagline {
            font-size: 7px;
            color: #666;
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        .address-section {
            text-align: right;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            max-width: 160px;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .customer-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 225px;
            margin-bottom: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        
        .left-info, .right-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            font-size: 9px;
        }
        
        .info-label {
            font-weight: bold;
            color: #1e3a8a;
            min-width: 80px;
            margin-right: 8px;
        }
        
        .info-value {
            color: #333;
            font-weight: 500;
        }
        
        .service-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .service-table th {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            border: 1px solid rgb(0, 0, 0);
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .service-table td {
            border: 1px solid rgb(0, 0, 0);
            padding: 5px;
            vertical-align: middle;
            background: #fff;
            font-size: 9px;
        }
        
        .service-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .service-table .jenis-layanan {
            width: 40%;
            text-align: left;
            font-weight: 500;
        }
        
        .service-table .jumlah {
            width: 15%;
            text-align: center;
            font-weight: bold;
        }
        
        .service-table .harga {
            width: 22.5%;
            text-align: right;
            font-family: monospace;
            font-size: 8px;
        }
        
        .service-table .subtotal {
            width: 22.5%;
            text-align: right;
            font-family: monospace;
            font-weight: 500;
            font-size: 8px;
        }
        
        .total-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .total-section td {
            border: 1px solid rgb(0, 0, 0);
            padding: 6px;
            font-size: 10px;
            font-weight: bold;
            font-family: monospace;
            text-align: center;
            background: #fff;
            color: #000;
        }

        .total-label {
            width: 77.5%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fff;
        }

        .total-amount {
            width: 22.5%;
        }

        
        .empty-row {
            height: 25px;
        }
        
        .footer {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        
        @media print {
            .invoice-container {
                box-shadow: none;
                page-break-inside: avoid;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .service-table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice-container">
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <img src="../images/logo.png" alt="Logo">
                </div>
                <div class="company-info">
                    <div class="company-name">
                        <span class="cars">CARS</span> <span class="city">CITY</span>
                    </div>
                    <div class="tagline">ENGINE • SERVICE AC • ACCESSORIES</div>
                </div>
            </div>
            <div class="address-section">
                Jl. Danau Sunter Utara Blok F 20 /<br>
                No.12A Jakarta Utara 14350<br>
                Telp./Fax: (021) 6530 3630
            </div>
        </div>
        
        <div class="invoice-title">Invoice Service</div>
        
        <div class="customer-info">
            <div class="left-info">
                <div class="info-item">
                    <span class="info-label">No Reservasi:</span>
                    <span class="info-value" style="font-weight:bold;"><?= $reservasi['id_reservasi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?= $reservasi['nama_lengkap']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No Polisi:</span>
                    <span class="info-value"><?= $reservasi['nopolisi']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Merk:</span>
                    <span class="info-value"><?= $reservasi['merk']; ?></span>
                </div>
            </div>
            <div class="right-info">
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value"><?= date('d-m-Y', strtotime($reservasi['tanggal_servis'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Waktu Servis:</span>
                    <span class="info-value"><?= $reservasi['waktu_servis']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">No Telepon:</span>
                    <span class="info-value"><?= $reservasi['nomor_telepon']; ?></span>
                </div>
            </div>
        </div>
        
        <div class="service-section">
            <table class="service-table">
                <thead>
                    <tr>
                        <th class="jenis-layanan">Jenis Layanan</th>
                        <th class="jumlah">Jumlah</th>
                        <th class="harga">Harga</th>
                        <th class="subtotal">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $row_count = 0;
                    while ($detail = mysqli_fetch_assoc($result_detail)) {
                        $subtotal = $detail['jumlah'] * $detail['harga'];
                        $total += $subtotal;
                        $row_count++;
                        echo "<tr>
                                <td class='jenis-layanan'>{$detail['jenis_layanan']}</td>
                                <td class='jumlah'>{$detail['jumlah']}</td>
                                <td class='harga'>Rp. " . number_format($detail['harga'], 0, ',', '.') . "</td>
                                <td class='subtotal'>Rp. " . number_format($subtotal, 0, ',', '.') . "</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <table class="total-section">
                <tr>
                    <td class="total-label">Total Harga</td>
                    <td class="total-amount">Rp. <?= number_format($total, 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            Terima kasih atas kepercayaan Anda menggunakan layanan Cars City
        </div>
    </div>
</body>
</html>