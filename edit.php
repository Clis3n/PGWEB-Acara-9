<?php
// Koneksi ke MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "acara 8 pgweb";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel 'penduduk'
$sql = "SELECT Kecamatan, Longitude, Latitude, Luas, Jumlah_Penduduk FROM penduduk";
$result = $conn->query($sql);

// Array untuk menyimpan data marker
$markers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $markers[] = $row; // Simpan semua data dalam array
    }
} else {
    echo "Tidak ada data ditemukan.";
}

// Memproses form update jika ada data yang di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $kecamatan = $_POST['kecamatan'];
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];
    $luas = $_POST['luas'];
    $jumlah_penduduk = $_POST['jumlah_penduduk'];

    // Query untuk memperbarui data
    $sql = "UPDATE penduduk SET Longitude = ?, Latitude = ?, Luas = ?, Jumlah_Penduduk = ? WHERE Kecamatan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddiss", $longitude, $latitude, $luas, $jumlah_penduduk, $kecamatan);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui.');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Data Penduduk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .edit-button {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <h1>Edit Data Penduduk</h1>

    <table>
        <thead>
            <tr>
                <th>Kecamatan</th>
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Luas (km²)</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($markers as $marker): ?>
                <tr>
                    <form method="POST" action="">
                        <td><?php echo htmlspecialchars($marker['Kecamatan']); ?></td>
                        <td><input type="number" step="any" name="longitude" value="<?php echo htmlspecialchars($marker['Longitude']); ?>" required></td>
                        <td><input type="number" step="any" name="latitude" value="<?php echo htmlspecialchars($marker['Latitude']); ?>" required></td>
                        <td><input type="number" step="any" name="luas" value="<?php echo htmlspecialchars($marker['Luas']); ?>" required></td>
                        <td><input type="number" name="jumlah_penduduk" value="<?php echo htmlspecialchars($marker['Jumlah_Penduduk']); ?>" required></td>
                        <td>
                            <input type="hidden" name="kecamatan" value="<?php echo htmlspecialchars($marker['Kecamatan']); ?>">
                            <button type="submit" name="update" class="edit-button">Edit</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
