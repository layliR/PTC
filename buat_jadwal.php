<?php
// Menjalankan skrip Python untuk melakukan penjadwalan
$output = shell_exec("python penjadwalan2.py");

// Mengecek hasil dari skrip Python (opsional)
if ($output === null) {
    // Menampilkan pesan error jika skrip Python gagal dijalankan
    echo "Terjadi kesalahan saat menjalankan skrip Python.";
} else {
    // Anda dapat menampilkan output di sini jika diperlukan
    // echo "<pre>$output</pre>";
    // Misalnya, jika ingin memeriksa output dari skrip Python
    // echo "Output dari Python: " . $output;
}

// Mengalihkan pengguna ke halaman penjadwalan2.php setelah skrip selesai
header('Location: penjadwalan2.php');
exit;
?>
