<!DOCTYPE html>
<html lang="en">

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


</head>
<?php
include 'config/koneksi.php';
session_start();

$cek_sisa = 0;
$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Proses untuk masuk parkir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'masuk') {
  $plat_nomor = trim($_POST['plat_nomor']);
  $merk = trim($_POST['merk']);
  $jenis = trim($_POST['jenis']);  // jenis kendaraan
  // Validasi data parkir
  if (empty($plat_nomor) || empty($merk) || empty($jenis)) {
    echo "TEST";
    // echo json_encode(['status' => 'error', 'message' => 'Data parkir tidak lengkap.']);
  }
  // Simpan data parkir baru
  $query = "INSERT INTO tb_daftar_parkir (plat_nomor, merk, jenis, jam_masuk) VALUES (?, ?, ?, NOW())";
  $stmt = $con->prepare($query);
  $stmt->bind_param("sss", $plat_nomor, $merk, $jenis);
  $stmt->execute();
  if ($stmt->affected_rows > 0) {
    echo (['status' => 'success', 'message' => 'Kendaraan berhasil masuk parkir.']);
  } else {
    echo (['status' => 'error', 'message' => 'Gagal masuk
    ']);
  }
  // exit;
}
// Proses untuk keluar parkir berdasarkan plat nomor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plat_nomor_keluar'])) {
  $plat_nomor_keluar = trim($_POST['plat_nomor_keluar']);

  // Validasi plat nomor tidak boleh kosong
  if (empty($plat_nomor_keluar)) {
    echo (['status' => 'error', 'message' => 'Plat nomor tidak boleh kosong.']);
    exit;
  }

  // Ambil data kendaraan berdasarkan plat nomor
  $query = "SELECT plat_nomor, TIMESTAMPDIFF(MINUTE, jam_masuk, NOW()) AS durasi FROM parkir WHERE plat_nomor = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $plat_nomor_keluar);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $durasi = $data['durasi'];
    $total = $durasi * 1000; // Contoh: biaya per menit Rp 1000

    // Menghapus data kendaraan setelah keluar parkir
    $delete_query = "DELETE FROM parkir WHERE plat_nomor = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("s", $plat_nomor_keluar);
    $delete_stmt->execute();

    // Kembalikan data kendaraan dan total biaya
    echo (['status' => 'success', 'plat_nomor' => $data['plat_nomor'], 'total' => $total]);
  } else {
    echo (['status' => 'error', 'message' => 'Plat nomor tidak valid atau belum terdaftar.']);
  }
  exit;
}

?>




<body style="overflow-x: hidden;" class="dashboard topnav">
  start: Header -->
  <nav class="navbar navbar-default header navbar-fixed-top bg-teal">
    <div class="col-md-12 nav-wrapper">
      <div class="navbar-header" style="width:100%;">
        <div class="navbar-brand" style="margin-left: -10px;" name="home_logo">
          <img src="asset/img/logo.png" class="img-circle" alt="logo" style="float: left;margin-top: -10px;" width="45px" />
          <b style="float: left;margin-left: 4px;">Parking</b>
        </div>

        <ul class="nav navbar-nav search-nav" style="margin-left: 7%">
          <li class="active"><a style="font-size: 18pt">Home</a></li>
          <li><a href="daftar_kendaraan.php?nama=<?= $username ?>"><span style="font-size: 18pt">Daftar Kendaraan</a></span></li>
          <?php if ($role === 'user'): ?>
            <li><a href="home_admin.php"><span style="font-size: 18pt">Admin Panel</span></a></li>
          <?php endif; ?>
        </ul>

        <ul class="nav navbar-nav navbar-right user-nav">
          <li class="user-name"><span><?php echo $username ?></span></li>
          <li class="dropdown avatar-dropdown">
            <img src="asset/img/petugas.png" class="img-circle avatar" alt="username" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="cursor: pointer;" />
            <ul class="dropdown-menu user-dropdown">
              <li>
                <ul>
                  <a href="?nama=<?= $username ?>&logout">
                    <li style="float: left;"><span class="fa fa-power-off "></span></li>
                    <li style="color: black; float: left;margin-left: 10px">Log Out</li>
                  </a>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  end: Header

  <!-- Content -->
  <div id="content">
    <!-- Masuk Parkir -->
    <div class="col-md-7" style="margin-top: 30px;">
      <div class="col-md-10 panel">
        <div class="col-md-12 panel-heading bg-teal">
          <h4 style="color: white;font-size: 20pt;">Masuk Parkir <span class="right" style="color : #f6c700; font-weight: bold; text-align: right; padding-right: 10px;">Empty : <?= $cek_sisa ?></span></h4>
        </div>
        <div class="col-md-12 panel-body" style="padding-bottom:25px;">
          <div class="col-md-12">
            <form class="cmxform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <div class="col-md-6">
                <div class="form-group form-animate-text" style="margin-top:15px !important;">
                  <input type="text" class="form-text" name="plat_nomor" id="plat_nomor" required>
                  <span class="bar"></span>
                  <label>Plat Nomor</label>
                </div>

                <div class="form-group form-animate-text" style="margin-top:10px !important;">
                  <input type="text" class="form-text" name="merk" id="merk" required>
                  <span class="bar"></span>
                  <label>Merk Kendaraan</label>
                </div>
              </div>

              <div class="col-md-6" style="padding-top: 10px">
                <label>
                  <h4>Jenis Kendaraan</h4>
                </label>
              </div>

              <div class="col-md-6" style="padding:5px 20px 0 25px" name="jenis_kendaraan">

                <div class="form-animate-radio">
                  <label class="radio">
                    <input id="radio1" type="radio" name="jenis" value="Motor" required />
                    <span class="outer">
                      <span class="inner"></span>
                    </span> Motor
                  </label>
                </div>

                <div class="form-animate-radio">
                  <label class="radio">
                    <input id="radio2" type="radio" name="jenis" value="Mobil" required />
                    <span class="outer">
                      <span class="inner"></span>
                    </span> Mobil
                  </label>
                </div>

                <div class="form-animate-radio">
                  <label class="radio">
                    <input id="radio3" type="radio" name="jenis" value="Truk/Bus/Lainnya" required />
                    <span class="outer">
                      <span class="inner"></span>
                    </span> Truk / Bus / Lainnya
                  </label>
                </div>
              </div>
              <input type="hidden" name="action" value="masuk">
              <input class="submit btn btn-primary col-md-12" type="submit" value="Submit" style="height: 40px" name="btn_masuk">
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- end:Masuk Parkir -->

    <!-- Add this section after the "Daftar Kendaraan Yang Parkir" section -->


    <!-- Keluar Parkir -->
    <div class="col-md-5" style="margin-top: 30px">
      <div class="col-md-10 panel">
        <div class="col-md-12 panel-heading bg-teal">
          <h4 style="color: white;font-size: 20pt;">Keluar Parkir</h4>
        </div>
        <div class="col-md-12 panel-body" style="padding-bottom:25px;">
          <div class="col-md-12">
            <form class="cmxform" method="POST">
              <div class="col-md-12">
                <div class="form-group form-animate-text" style="margin-top:25px !important;">
                  <input type="text" class="form-text" name="plat_nomor" id="plat_nomor" required>
                  <span class="bar"></span>
                  <label>Masukan Plat Nomor Kendaraan anda</label>
                </div>
              </div>
              <input class="btn btn-primary col-md-12" type="button" value="Go" style="height: 40px" id="btnKeluar" style="height: 40px">
              <!-- Modal -->
              <div class="col-md-12">
                <div class="modal fade modal-v1" id="myModal">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h2 class="modal-title">
                          <i class="icon-logout icons"> </i>
                          Keluar Parkir
                        </h2>
                      </div>
                      <div class="modal-body" style="padding-bottom: 10px;">
                        <h3 id="plat"></h3>
                        <div class="form-group"><label class="col-sm-2 control-label text-right" style="font-size:14pt">Total</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control android" name="total" id="total" readonly>
                          </div>
                        </div>
                        <div class="form-group" style="margin-top: 14%;">
                          <label class="col-sm-2 control-label text-right" style="font-size:14pt">Bayar</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control android" name="bayar" id="bayar">
                          </div>
                        </div>
                        <input class="btn btn-primary" type="button" value="Hintung" name="btn_hitung" id="hitung" style="margin: 20px 17px 20px 0; width: 180px; height: 40px; font-weight: bold; ">
                        <div class="form-group"><label class="col-sm-2 control-label text-right" style="font-size:14pt">Kembali</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control android" name="kembali" id="kembali" readonly>
                          </div>
                        </div>
                        <input class="btn btn-primary" type="submit" value="Go" name="btn_keluar" style="margin: 20px 17px 0 0; height: 40px; font-weight: bold;">
                      </div>
                      <div class="modal-footer">
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
              </div>
              <!-- end:Modal -->
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- end:Keluar Parkir -->

    <!-- Daftar Kendaraan Yang Parkir -->
    <!-- Data Parkir Section -->
    <div class="col-md-12 col-sm-12 col-x-12" style="margin-top: 20px;">
      <div class="col-md-12 panel">
        <div class="col-md-12 panel-heading bg-teal">
          <h4 style="color: white;font-size: 20pt;">Data Parkir Keseluruhan</h4>
        </div>
        <div class="panel-body">
          <div class="table-responsive col-md-12 col-sm-12 col-xs-12">
            <table class="table table-hover col-md-12 col-sm-12 col-xs-12" width="100%" cellspacing="0">
              <thead>
                <tr style="font-size: 13pt">
                  <th>Plat Nomor</th>
                  <th>Jenis</th>
                  <th>Merk</th>
                  <th>Jam Masuk</th>
                  <th>Jam Keluar</th>
                  <th>Total Biaya</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $mysql = "SELECT * FROM tb_daftar_parkir ORDER BY jam_masuk DESC";
                $query = mysqli_query($con, $mysql);
                while ($data = mysqli_fetch_array($query)) { ?>
                  <tr style="font-size: 11pt">
                    <td><?php echo $data['plat_nomor'] ?></td>
                    <td><?php echo $data['jenis'] ?></td>
                    <td><?php echo $data['merk'] ?></td>
                    <td><?php echo $data['jam_masuk'] . " WIB" ?></td>
                    <td><?php echo ($data['jam_keluar'] ? $data['jam_keluar'] . " WIB" : "Belum Keluar") ?></td>
                    <td><?php echo ($data['status'] == '1' ? 'Sedang Parkir' : 'Selesai') ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!-- End: Data Parkir Section -->



</body>

</html> -->