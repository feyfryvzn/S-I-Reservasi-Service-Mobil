<?php
// include database connection file
include '../koneksi.php';
 
// Get id from URL to delete that user
if (isset($_GET['id_reservasi'])) {
    $id_reservasi=$_GET['id_reservasi'];
}
 
// Delete user row from table based on given id
$result = mysqli_query($conn, "DELETE FROM reservasi WHERE id_reservasi='$id_reservasi'");
 
header("Location: reservasilihat.php?success=delete");
exit();

?>