<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_reservasi = $_POST['id_reservasi'];
    $status = $_POST['status'];
    $status_pembayaran = $_POST['status_pembayaran'];

    $stmt = $conn->prepare("UPDATE reservasi SET status = ?, status_pembayaran = ? WHERE id_reservasi = ?");
    $stmt->bind_param("sss", $status, $status_pembayaran, $id_reservasi);

    if ($stmt->execute()) {
        header("Location: reservasilihat.php?success=update");
        exit();
    } else {
        echo "Gagal update status.";
    }
    $stmt->close();
}
?>
