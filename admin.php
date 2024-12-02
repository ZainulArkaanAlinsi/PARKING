<?php
include "config/koneksi.php";

date_default_timezone_set("Asia/Jakarta");
$waktu = date('H:i');
$tanggal = date('D, d M Y');


session_start();

// Check if user is already logged in, redirect to admin home
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
  header('Location: home_admin.php');
  exit();
}

if (isset($_POST['login_admin'])) {
  // Use prepared statement for better security
  $stmt = mysqli_prepare($con, "SELECT * FROM tb_login WHERE username = ? AND role = 'admin'");
  mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);

  // Verify password
  if ($user && password_verify($_POST['password'], $user['password'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = 'admin';

    // Redirect to admin home
    header('Location: home_admin.php');
    exit();
  } else {
    $error_message = 'Username atau Password Salah!';
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
  <title>E-Parking - Admin Login</title>

  <!-- CSS Files -->
  <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css" />
  <link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css" />
  <link href="asset/css/style.css" rel="stylesheet">
  <link rel="shortcut icon" href="asset/img/logo.png">
</head>

<body style="overflow-y: hidden;" class="bg-dark-red">
  <div class="panel container col-lg-4 col-md-6 col-sm-8 col-xs-12" style="position: relative; margin: auto; box-shadow: 0 7px 16px #691816, 0 4px 5px #93211f;">
    <div class="panel-body">
      <div style="float: left; margin-left:20px;">
        <img src="asset/img/logo.png" width="100px" class="animated fadeInDown">
      </div>
      <div style="float: left;">
        <h1 class="animated fadeInLeft" style="margin-left: 40px; font-size: 62pt"><?= $waktu ?></h1>
        <p class="animated fadeInRight" style="margin-left: 85px; font-size: 14pt;"><?= $tanggal ?></p>
      </div>
    </div>

    <div class="panel-heading bg-dark-red">
      <h4 style="color: white" class="animated zoomIn">Login Admin</h4>
    </div>

    <div class="col-md-12 panel-body" style="padding-bottom:400px;">
      <div class="col-md-11">
        <?php if (!empty($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form class="cmxform" method="POST">
          <div class="form-group form-animate-text" style="margin-top:50px !important;">
            <input type="text" class="form-text" id="validate_username" name="username" required>
            <span class="bar"></span>
            <label>Username Admin</label>
          </div>

          <div class="form-group form-animate-text" style="margin-top:10px !important;">
            <input type="password" class="form-text" id="validate_password" name="password" required>
            <span class="bar"></span>
            <label>Password</label>
          </div>

          <input class="submit btn btn-danger col-md-5 col-sm-5 col-xs-12" type="submit" value="Login Admin" name="login_admin" style="margin-top: 10px;margin-left: 10px; height: 40px;">
        </form>

        <form class="cmxform" method="POST" style="display: inline-block;">
          <input class="submit btn btn-default col-md-5 col-sm-5 col-xs-12" type="submit" value="Back" name="back" style="margin-top: 10px; margin-left: 10px; height: 40px;">
        </form>
      </div>
    </div>
  </div>
</body>

</html>