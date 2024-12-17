<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manajemen Penjadwalan</title>

    <!-- Link ke Bootstrap CDN -->
     
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
        <!-- Konten halaman Anda di sini -->

    <!-- Script JavaScript Bootstrap dari CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Header -->
    <header class="bg-dark text-white text-center py-4">
        <h1>Dashboard Manajemen Penjadwalan</h1>
    </header>

    <!-- Navigasi Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container mt-5">
        <div class="row">
            <!-- Manajemen Dosen -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Dosen</h5>
                        <a href="dosen.php" class="btn btn-primary">Kelola Dosen</a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Kelas -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Kelas</h5>
                        <a href="kelas.php" class="btn btn-primary">Kelola Kelas</a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Ruangan -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Ruangan</h5>
                        <a href="ruangan.php" class="btn btn-primary">Kelola Ruangan</a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Mata Kuliah -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Matakuliah</h5>
                        <a href="matkul.php" class="btn btn-primary">Kelola Mata Kuliah</a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Program Studi -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Program Studi</h5>
                        <a href="prodi.php" class="btn btn-primary">Kelola Program Studi</a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Waktu -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manajemen Waktu</h5>
                        <a href="waktu.php" class="btn btn-primary">Kelola Waktu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- Manajemen perkuliahan -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Perkuliahan</h5>
                        <a href="perkuliahan.php" class="btn btn-primary">Perkuliahan </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>&copy; 2024 Sistem Penjadwalan. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-lZN37fIzp1Xxd8HtfpuT2Hde+4k3f4+44xzRi2jmOxs8wV0tXwXDBg5ne3J15fxt" crossorigin="anonymous"></script>

</body>
</html>
