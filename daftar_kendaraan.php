<?php
include "config/koneksi.php";
session_start();

// Konfigurasi database
$host = 'localhost';     // Ganti sesuai host database Anda
$dbname = 'parkir';   // Nama database
$username = 'root';      // Username database
$password = '';          // Password database

try {
  // Membuat koneksi PDO
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

  // Set mode error PDO untuk menampilkan error
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Set default fetch mode
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Tangani error koneksi database
  die("Koneksi database gagal: " . $e->getMessage());
}


try {
  // Query untuk menghitung jumlah kendaraan berdasarkan jenis
  $vehicleCountQuery = "
        SELECT 
            SUM(CASE WHEN jenis = 'Motor' THEN 1 ELSE 0 END) as jumlah_motor,
            SUM(CASE WHEN jenis = 'Mobil' THEN 1 ELSE 0 END) as jumlah_mobil,
            SUM(CASE WHEN jenis = 'Truk/Bus/Lainnya' THEN 1 ELSE 0 END) as jumlah_truk_bus
        FROM kendaraan
    ";
  $countStmt = $pdo->query($vehicleCountQuery);
  $vehicleCounts = $countStmt->fetch(PDO::FETCH_ASSOC);

  // Query untuk kendaraan yang sedang terisi
  $currentVehiclesQuery = "
        SELECT 
            kode, 
            plat_nomor, 
            jenis, 
            merk, 
            jam_masuk 
        FROM kendaraan 
        WHERE status = 'Terisi'
        ORDER BY jam_masuk DESC
    ";
  $currentVehiclesStmt = $pdo->query($currentVehiclesQuery);
  $currentVehicles = $currentVehiclesStmt->fetchAll(PDO::FETCH_ASSOC);

  // Query untuk history kendaraan
  $vehicleHistoryQuery = "
        SELECT 
            kode, 
            plat_nomor, 
            jenis, 
            merk, 
            jam_masuk, 
            jam_keluar 
        FROM kendaraan 
        WHERE status = 'Selesai'
        ORDER BY jam_keluar DESC
        LIMIT 50
    ";
  $historyStmt = $pdo->query($vehicleHistoryQuery);
  $vehicleHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Tangani error database
  error_log('Database Error: ' . $e->getMessage());
  die('Terjadi kesalahan dalam mengambil data');
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Parking</title>

  <!-- start: Css -->
  <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
  <script type="text/javascript" src="asset/js/jquery.min.js"></script>
  <script type="text/javascript" src="asset/js/bootstrap.min.js"></script>

  <!-- plugins -->
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/nouislider.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/select2.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/ionrangeslider/ion.rangeSlider.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/ionrangeslider/ion.rangeSlider.skinFlat.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/bootstrap-material-datetimepicker.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/simple-line-icons.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/mediaelementplayer.css" />
  <link href="asset/css/style.css" rel="stylesheet">
  <!-- end: Css -->
  <link rel="shortcut icon" href="asset/img/logo.png">

</head>


<body>
  <div class="container">
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">E-Parking</a>
      <div class="navbar-nav">
        <a class="nav-item nav-link" href="home.php">Home</a>
        <a class="nav-item nav-link active" href="daftar_kendaraan.php">Daftar Kendaraan</a>
        <a class="nav-item nav-link" href="logout.php">Log Out</a>
      </div>
    </nav>

    <!-- Statistik Kendaraan -->
    <div class="vehicle-stats row">
      <div class="col-md-4">
        <div class="card">
          <h3>Jumlah Motor</h3>
          <p><?= htmlspecialchars($vehicleCounts['jumlah_motor'] ?? 0) ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <h3>Jumlah Mobil</h3>
          <p><?= htmlspecialchars($vehicleCounts['jumlah_mobil'] ?? 0) ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <h3>Jumlah Truk/Bus/Lainnya</h3>
          <p><?= htmlspecialchars($vehicleCounts['jumlah_truk_bus'] ?? 0) ?></p>
        </div>
      </div>
    </div>

    <!-- Daftar Kendaraan Terisi -->
    <div class="current-vehicles">
      <h2>Daftar Kendaraan Terisi</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Plat Nomor</th>
            <th>Jenis</th>
            <th>Merk</th>
            <th>Jam Masuk</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($currentVehicles as $vehicle): ?>
            <tr>
              <td><?= htmlspecialchars($vehicle['kode']) ?></td>
              <td><?= htmlspecialchars($vehicle['plat_nomor']) ?></td>
              <td><?= htmlspecialchars($vehicle['jenis']) ?></td>
              <td><?= htmlspecialchars($vehicle['merk']) ?></td>
              <td><?= htmlspecialchars($vehicle['jam_masuk']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- History Kendaraan -->
    <div class="vehicle-history">
      <h2>History Kendaraan</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Plat Nomor</th>
            <th>Jenis</th>
            <th>Merk</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vehicleHistory as $history): ?>
            <tr>
              <td><?= htmlspecialchars($history['kode']) ?></td>
              <td><?= htmlspecialchars($history['plat_nomor']) ?></td>
              <td><?= htmlspecialchars($history['jenis']) ?></td>
              <td><?= htmlspecialchars($history['merk']) ?></td>
              <td><?= htmlspecialchars($history['jam_masuk']) ?></td>
              <td><?= htmlspecialchars($history['jam_keluar']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>


</body>

</html>