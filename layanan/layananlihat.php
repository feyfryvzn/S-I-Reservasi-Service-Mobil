<?php
session_start();

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tangani logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    if (isset($_GET['csrf']) && $_GET['csrf'] === $_SESSION['csrf_token']) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    } else {
        die("Token CSRF tidak valid!");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
            color: #000;
            margin: 0;
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #0b1e33, #0d2744);
            color: #fff;
            padding: 20px 15px;
            position: fixed;
            width: 250px;
            top: 0;
            left: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            transition: width 0.3s ease;
            z-index: 1000;
        }

        .sidebar h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 1px;
            color: #74b9ff;
        }

        .sidebar a {
            color: #fff;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin: 5px 0;
            text-decoration: none;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background-color: rgba(0, 123, 255, 0.3);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
        }

        .main {
            margin-left: 260px;
            padding: 30px;
            transition: margin-left: 0.3s ease;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffca2c, #ffda6a);
            border: none;
            color: #000;
            font-weight: 600;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #ffda6a, #ffca2c);
            color: #000;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #38c172);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #38c172, #28a745);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #ef5753);
            border: none;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ef5753, #dc3545);
        }

        .table-responsive {
            background-color: #f8f9fa;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: linear-gradient(180deg, #0b1e33, #0d2744);
            color: #fff;
            border: none;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .alert {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 20px 5px;
            }

            .sidebar h4 {
                display: none;
            }

            .sidebar a {
                justify-content: center;
                padding: 10px;
            }

            .sidebar a i {
                margin-right: 0;
            }

            .sidebar a span {
                display: none;
            }

            .main {
                margin-left: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>Panel Admin</h4>
        <a href="../dashboard_adm.php" ><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="../reservasi/reservasilihat.php" ><i class="fas fa-users"></i><span>Reservasi</span></a>
        <a href="../layanan/layananlihat.php"class="active"><i class="fas fa-box"></i><span>Layanan</span></a>
        <a href="?logout=true&csrf=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="login-btn" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <div class="main">
        <div class="dashboard-header">
            <div>
                <h2 class="text-dark font-weight-bold mb-0">Data Layanan</h2>
                <small class="text-muted"><?= date('H:i A \W\I\B, d F Y') ?></small>
            </div>
      <img src="../images/logo.png" alt="Logo" height="70">
        </div>

        <div style="text-align: end; margin-bottom: 20px;">
            <a href="layanantambah.php" class="btn btn-warning" type="button">+ TAMBAHKAN</a>
        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered" style="width:100%; text-align: center;">
                <thead class="table-primary">
                    <tr>
                        <th>ID Layanan</th>
                        <th>Jenis Layanan</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include '../koneksi.php';
                    if (!$conn) {
                        echo "<tr><td colspan='4'>Gagal terhubung ke database</td></tr>";
                    } else {
                        $query = mysqli_query($conn, "SELECT * FROM layanan");
                        if ($query) {
                            if (mysqli_num_rows($query) == 0) {
                                echo "<tr><td colspan='4'>Tidak ada data Layanan</td></tr>";
                            } else {
                                while ($data = mysqli_fetch_array($query)) {
                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($data['id_layanan']); ?></td>
                                        <td style="text-align: start;"><?php echo htmlspecialchars($data['jenis_layanan']); ?></td>
                                        <td><?php echo htmlspecialchars($data['harga']); ?></td>
                                        <td><?php echo htmlspecialchars($data['deskripsi']); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-success" href="layananubah.php?id_layanan=<?php echo urlencode($data['id_layanan']); ?>" data-toggle="tooltip" title="Ubah"><i class="fa fa-pencil"></i></a>
                                                <a class="btn btn-sm btn-danger btn-delete"
                                                    href="layananhapus.php?id_layanan=<?php echo urlencode($data['id_layanan']); ?>"
                                                    data-id="<?php echo htmlspecialchars($data['id_layanan']); ?>"
                                                    data-toggle="tooltip" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                    <?php
                                }
                            }
                            mysqli_free_result($query);
                        } else {
                            echo "<tr><td colspan='4'>Gagal menjalankan query</td></tr>";
                            error_log("Query Layanan gagal: " . mysqli_error($conn));
                        }
                        mysqli_close($conn);
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            // Enable tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Tampilkan SweetAlert2 berdasarkan parameter success
            <?php if (isset($_GET['success'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '<?php 
                        switch ($_GET['success']) {
                            case 'add':
                                echo 'Data Layanan berhasil ditambahkan!';
                                break;
                            case 'update':
                                echo 'Data Layanan berhasil diubah!';
                                break;
                            case 'delete':
                                echo 'Data Layanan berhasil dihapus!';
                                break;
                            default:
                                echo 'Data Layanan berhasil ditambahkan!';
                        }
                    ?>',
                    showConfirmButton: false,
                    timer: 3000
                });
            <?php endif; ?>
        });
        // Konfirmasi hapus dengan SweetAlert2
        $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var id = $(this).data('id');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data layanan dengan ID " + id + " akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
            window.location.href = href;
            }
        });
        });
    </script>
</body>
</html>