<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "penjadwalan2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data jadwal dari database
$sql = "SELECT * FROM jadwal ORDER BY hari, jam_mulai";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian KRS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Pembelian Kartu Rencana Studi (KRS)</h1>

        <form action="proses_pembelian.php" method="POST">
            <div class="form-group">
                <label for="npm">NPM Mahasiswa:</label>
                <input type="text" id="npm" name="npm" required>
            </div>

            <div class="form-group">
                <label for="jadwal">Pilih Mata Kuliah:</label>
                <select name="jadwal_id" id="jadwal" required>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo $row['nama_matkul']; ?> 
                            (<?php echo $row['sks']; ?> SKS) - 
                            Dosen: <?php echo $row['nama_dosen']; ?> - 
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">Beli KRS</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
