<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "penjadwalan2";

$conn = new mysqli($host, $user, $pass, $db);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil prodi_id dari parameter
$prodi_id = isset($_GET['prodi_id']) ? $conn->real_escape_string($_GET['prodi_id']) : '';

if ($prodi_id) {
    // Ambil data jurusan berdasarkan prodi_id
    $query = "SELECT jurusan FROM prodi WHERE prodi_id = '$prodi_id'";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $jurusanList = explode(",", $row['jurusan']); // Misal data jurusan disimpan dalam format CSV
        echo json_encode($jurusanList);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();
?>
