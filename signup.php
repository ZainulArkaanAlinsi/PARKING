<?php
include "config/koneksi.php";
session_start();
date_default_timezone_set("Asia/Jakarta");
$waktu = date('H:i');
$tanggal = date('D, d M Y');

// Inisialisasi variabel
$username = '';
$error_message = '';

if (isset($_POST['signup'])) {
  // Bersihkan dan validasi input
  $username = mysqli_real_escape_string($con, $_POST['username']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Validasi input
  if (empty($username)) {
    $error_message = "Username tidak boleh kosong";
  } elseif (strlen($username) < 4) {
    $error_message = "Username minimal 4 karakter";
  } elseif (strlen($password) < 6) {
    $error_message = "Password minimal 6 karakter";
  } elseif ($password !== $confirm_password) {
    $error_message = "Konfirmasi password tidak sesuai";
  } else {
    // Cek apakah username sudah ada
    $check_query = mysqli_prepare($con, "SELECT * FROM tb_login WHERE username = ?");
    mysqli_stmt_bind_param($check_query, "s", $username);
    mysqli_stmt_execute($check_query);
    $result = mysqli_stmt_get_result($check_query);

    if (mysqli_num_rows($result) > 0) {
      $error_message = "Username sudah terdaftar";
    } else {
      // Hash password sebelum disimpan
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Gunakan prepared statement untuk insert
      $insert_query = mysqli_prepare($con, "INSERT INTO tb_login (username, password) VALUES (?, ?)");
      mysqli_stmt_bind_param($insert_query, "ss", $username, $hashed_password);

      if (mysqli_stmt_execute($insert_query)) {
        echo "<script>
                    alert('Registrasi berhasil');
                    window.location.href='index.php';
                </script>";
        exit();
      } else {
        $error_message = "Gagal menyimpan data. Silakan coba lagi.";
      }
    }
  }
}

if (isset($_POST['back'])) {
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parking - Sign Up</title>

  <!-- start: Css -->
  <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css" />
  <link href="asset/css/style.css" rel="stylesheet">
  <!-- end: Css -->
  <link rel="shortcut icon" href="asset/img/logo.png">
</head>

<body style="overflow-y: hidden;" class="bg-teal">
  <div class="panel container col-md-4 col-sm-8 col-xs-12" style="position: relative; margin: auto; box-shadow: 0 7px 16px #00655b, 0 4px 5px #006f64;">
    <div class="panel-body">
      <div style="float: left; margin-left:20px;">
        <img src="asset/img/logo.png" width="100px" class="animated fadeInDown">
      </div>
      <div style="float: left;">
        <h1 class="animated fadeInLeft" style="margin-left: 30px; font-size: 42pt"><?= $waktu ?></h1>
        <p class="animated fadeInRight" style="margin-left: 65px;"><?= $tanggal ?></p>
      </div>
    </div>
    <div class="panel-heading bg-teal">
      <h4 style="color: white" class="animated zoomIn">Sign Up</h4>
    </div>
    <div class="col-md-12 panel-body" style="padding-bottom:400px;">
      <div class="col-md-11">
        <?php if (!empty($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form class="cmxform" method="POST">
          <div class="form-group form-animate-text" style="margin-top:10px !important;">
            <input type="text" class="form-text" id="validate_username" name="username"
              value="<?= htmlspecialchars($username) ?>" required>
            <span class="bar"></span>
            <label>Username</label>
          </div>

          <div class="form-group form-animate-text" style="margin-top:10px !important;">
            <input type="password" class="form-text" id="validate_password"
              name="password" required>
            <span class="bar"></span>
            <label>Password</label>
          </div>

          <div class="form-group form-animate-text" style="margin-top:10px !important;">
            <input type="password" class="form-text" id="validate_confirm_password"
              name="confirm_password" required>
            <span class="bar"></span>
            <label>Confirm Password</label>
          </div>
          <input class="submit btn btn-info col-md-5 col-sm-5 col-xs-12"
            type="submit" value="Sign Up" name="signup" style="margin-top: 10px;">
        </form>

        <form class="cmxform" method="POST">
          <input class="submit btn btn-danger col-md-5 col-sm-5 col-xs-12"
            type="submit" value="Back" name="back" style="margin-top: 10px;">
        </form>
      </div>
    </div>
  </div>
</body>

</html>