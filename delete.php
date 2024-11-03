<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Data Penduduk</title>
</head>
<body>
    <h2>Form Hapus Data Penduduk</h2>
    <form action="delete_action.php" method="post">
        <label for="kecamatan">Kecamatan yang ingin dihapus:</label>
        <input type="text" id="kecamatan" name="kecamatan" required>
        <input type="submit" value="Hapus">
    </form>
</body>
</html>
