<?php
include '../koneksi.php';

if (isset($_GET['id_reservasi'])) {
    $id_reservasi = mysqli_real_escape_string($conn, $_GET['id_reservasi']);
    
    $query = mysqli_query($conn, "
               SELECT 
            dr.id_layanan,
            l.jenis_layanan,
            dr.jumlah,
            dr.harga,
            dr.subtotal
        FROM detail_reservasi dr
        JOIN layanan l ON dr.id_layanan = l.id_layanan
        WHERE dr.id_reservasi = '$id_reservasi'
    ");
    
    $details = [];
    if ($query && mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $details[] = $row;
        }
    }
    
    echo json_encode($details);
} else {
    echo json_encode([]);
}

mysqli_close($conn);
?>