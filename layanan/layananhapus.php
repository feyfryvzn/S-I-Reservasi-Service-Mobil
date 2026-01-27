<?php
// include database connection file
include '../koneksi.php';
 
// Get id from URL to delete that user
if (isset($_GET['id_layanan'])) {
    $id_layanan=$_GET['id_layanan'];
}
 
// Delete user row from table based on given id
$result = mysqli_query($conn, "DELETE FROM layanan WHERE id_layanan='$id_layanan'");
 
// After delete redirect to Home, so that latest user list will be displayed.
header("Location:layananlihat.php?success=delete");
?>