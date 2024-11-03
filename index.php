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
        $markers[] = [
            "kecamatan" => $row["Kecamatan"],
            "longitude" => (float)$row["Longitude"],
            "latitude" => (float)$row["Latitude"],
            "luas" => $row["Luas"],
            "jumlah_penduduk" => $row["Jumlah_Penduduk"]
        ];
    }
} else {
    echo "Tidak ada data ditemukan.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta Leaflet dan Data Penduduk</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Warna latar belakang abu-abu */
            color: #333; /* Warna teks abu-abu gelap */
        }

        #map {
            width: 100%;
            height: 600px;
        }

        .floating-table {
            position: absolute;
            top: 90px;
            right: 10px;
            width: 300px;
            background-color: rgba(255, 255, 255, 0.95); /* Warna latar belakang putih dengan transparansi */
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 1000;
        }

        .floating-table.hidden {
            display: none;
        }

        .toggle-button {
            position: absolute;
            top: 20px;
            right: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1001;
            transition: background-color 0.3s;
        }

        .toggle-button:hover {
            background-color: #0056b3; /* Ubah warna saat hover */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0; /* Menghilangkan margin */
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #e9ecef; /* Warna latar belakang abu-abu muda */
            font-weight: bold;
            color: #495057; /* Warna teks lebih gelap untuk kontras */
        }

        td {
            background-color: #ffffff; /* Latar belakang putih untuk sel */
        }

        td a {
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
        }

        .table-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            margin: 20px 0;
            color: #333; /* Warna teks judul */
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 50px; 
            height: auto; 
        }
    </style>
</head>

<body>

    <img src="logo.png" alt="Logo" class="logo"> <!-- Ganti dengan path logo Anda -->

    <h1>WEB GIS: KABUPATEN SLEMAN</h1>

    <!-- Tombol Buka-Tutup Tabel -->
    <button class="toggle-button" onclick="toggleTable()">Tampilkan Tabel</button>

    <!-- Peta -->
    <div id="map"></div>

    <!-- Tabel Mengambang -->
    <div class="floating-table hidden" id="table-container">
        <table>
            <thead>
                <tr>
                    <th>Kecamatan</th>
                    <th>Luas (km²)</th>
                    <th>Jumlah Penduduk</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php foreach ($markers as $marker): ?>
                    <tr>
                        <td>
                            <a onclick="focusOnMarker(<?php echo $marker['latitude']; ?>, <?php echo $marker['longitude']; ?>)">
                                <?php echo htmlspecialchars($marker['kecamatan']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($marker['luas']); ?></td>
                        <td><?php echo htmlspecialchars($marker['jumlah_penduduk']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Inisialisasi peta
        var map = L.map('map');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Data marker dari PHP
        var markers = <?php echo json_encode($markers); ?>;

        // Membuat bounds peta
        var bounds = L.latLngBounds();

        // Menambahkan marker ke peta dan memperluas bounds
        markers.forEach(function(marker) {
            var latLng = [marker.latitude, marker.longitude];
            L.marker(latLng)
                .addTo(map)
                .bindPopup("<b>" + marker.kecamatan + "</b><br>Luas: " + marker.luas + " km²<br>Jumlah Penduduk: " + marker.jumlah_penduduk);
            bounds.extend(latLng);
        });

        // Menyesuaikan peta agar sesuai dengan semua marker
        map.fitBounds(bounds);

        // Fungsi Buka-Tutup Tabel
        function toggleTable() {
            var tableContainer = document.getElementById('table-container');
            tableContainer.classList.toggle('hidden');
        }

        // Fungsi Fokus pada Marker
        function focusOnMarker(lat, lng) {
            map.setView([lat, lng], 15); // Zoom ke level 15
        }
    </script>

</body>

</html>
