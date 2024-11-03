<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "acara 8 pgweb";

// Membuat koneksi ke MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa apakah koneksi berhasil
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil nama kecamatan dari form
$kecamatan = $conn->real_escape_string($_POST['kecamatan']);

// Menulis query SQL untuk menghapus data
$sql = "DELETE FROM penduduk WHERE Kecamatan='$kecamatan'";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil dihapus";
} else {
    echo "Error: " . $conn->error;
}

// Menutup koneksi setelah selesai
$conn->close();
?>
