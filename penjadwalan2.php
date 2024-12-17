<?php
// Koneksi ke database MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "penjadwalan2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Urutan hari yang diinginkan (dimulai dari Senin)
$urutan_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

// Mengambil data waktu (hari dan jam mulai-jam selesai)
$sql_waktu = "SELECT * FROM waktu ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'), jam_mulai";
$result_waktu = $conn->query($sql_waktu);

// Mengambil data ruangan
$sql_ruangan = "SELECT * FROM ruangan";
$result_ruangan = $conn->query($sql_ruangan);

// Mengambil data jadwal yang sudah ada
$sql_jadwal = "SELECT * FROM jadwal";
$result_jadwal = $conn->query($sql_jadwal);

// Membuat array untuk menyimpan jadwal dalam format matriks
$matriks_jadwal = [];

// Membaca data ruangan ke dalam array
$ruangan_arr = [];
while ($row = $result_ruangan->fetch_assoc()) {
    $ruangan_arr[] = $row['nama_ruangan'];
}

// Membaca data waktu (hari dan jam) ke dalam array
$waktu_arr = [];
while ($row = $result_waktu->fetch_assoc()) {
    $waktu_arr[] = [
        'hari' => $row['hari'],
        'jam_mulai' => $row['jam_mulai'],
        'jam_selesai' => $row['jam_selesai'],
    ];
}

// Menginisialisasi matriks berdasarkan waktu dan ruangan
foreach ($waktu_arr as $waktu) {
    foreach ($ruangan_arr as $ruangan) {
        // Inisialisasi dengan nilai kosong (kelas, dosen, matkul)
        $matriks_jadwal[$waktu['hari']][$waktu['jam_mulai']][$ruangan] = [
            'nama_matkul' => "",
            'nama_dosen' => "",
            'kelas' => ""
        ];
    }
}

// Mengisi matriks dengan data jadwal
while ($row = $result_jadwal->fetch_assoc()) {
    $hari = $row['hari'];
    $jam_mulai = $row['jam_mulai'];
    $nama_kelas = $row['nama_kelas'];
    $nama_dosen = $row['nama_dosen'];
    $nama_matkul = $row['nama_matkul'];
    $nama_ruangan = $row['nama_ruangan'];

    // Mengisi matriks sesuai dengan jadwal yang ada
    if (isset($matriks_jadwal[$hari][$jam_mulai][$nama_ruangan])) {
        $matriks_jadwal[$hari][$jam_mulai][$nama_ruangan] = [
            'nama_matkul' => $nama_matkul,
            'nama_dosen' => $nama_dosen,
            'kelas' => $nama_kelas
        ];
    }
}

// Menampilkan matriks jadwal dalam bentuk tabel HTML
echo "<table border='1'>";
echo "<tr><th>Hari/Jam</th>";

// Tampilkan nama-nama ruangan di kolom
foreach ($ruangan_arr as $ruangan) {
    echo "<th>$ruangan</th>";
}
echo "</tr>";

// Tampilkan baris-baris waktu dan jadwal berdasarkan urutan hari
foreach ($urutan_hari as $hari) {
    $hari_pertama = true; // Flag untuk mengecek jika hari pertama kali muncul

    foreach ($waktu_arr as $waktu) {
        if ($waktu['hari'] == $hari) {
            // Jika ini adalah baris pertama untuk hari ini, tampilkan nama hari
            echo "<tr>";

            // Tampilkan nama hari hanya pada baris pertama
            if ($hari_pertama) {
                echo "<td rowspan='3'>{$waktu['hari']}</td>"; // Menampilkan hari di kolom pertama untuk 3 waktu
                $hari_pertama = false; // Set flag untuk baris berikutnya
            }

            echo "<td>{$waktu['jam_mulai']} - {$waktu['jam_selesai']}</td>";

            // Tampilkan jadwal per ruangan
            foreach ($ruangan_arr as $ruangan) {
                $jadwal = $matriks_jadwal[$waktu['hari']][$waktu['jam_mulai']][$ruangan];

                // Menampilkan jadwal dengan nama kelas, nama dosen, dan nama matkul
                if ($jadwal['kelas'] != "") {
                    echo "<td>{$jadwal['kelas']}<br>Dosen: {$jadwal['nama_dosen']}<br>Matkul: {$jadwal['nama_matkul']}</td>";
                } else {
                    echo "<td>-</td>";
                }
            }

            echo "</tr>";
        }
    }
}

echo "</table>";
?>


<!-- Tombol untuk mencetak -->
<button onclick="window.print()">Cetak Jadwal</button>

<script type="text/javascript">
    // Menambahkan fungsionalitas pencetakan
    function printSchedule() {
        window.print();
    }
</script>

<?php
// Menutup koneksi
$conn->close();
?>
