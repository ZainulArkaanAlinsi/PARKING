<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Parking</title>
	<link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="asset/css/plugins/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="asset/css/plugins/animate.min.css" />
	<link rel="stylesheet" type="text/css" href="asset/css/plugins/simple-line-icons.css" />

	<link href="asset/css/style.css" rel="stylesheet">

	<link rel="shortcut icon" href="asset/img/logo.png">

</head>

<?php
include 'config/koneksi.php';
session_start();
$username = $_GET['nama'];

$query = mysqli_query($con, "SELECT plat_nomor FROM tb_daftar_parkir WHERE plat_nomor='$_GET[plat_nomor]'");
$data = mysqli_fetch_array($query);

if (isset($_POST['btn_print'])) {
	echo "<script>document.location.href='home.php?nama=$username'</script>";
}
?>

<body>
	<div class="col-md-12">

		<!-- start: Content -->
		<center>
			<form method="post">
				<div class="page-404 animated flipInX">
					<div id="print-karcis" class="print-area" style="color : #2280c2;">
						<img src="asset/img/logo.png" style="width: 10%">
						<h2 style="font-weight:bold; color: #f36f5b ;margin-top: -4px;">Plat nomer anda : <?php echo $data['plat_nomor']; ?></h2>
						<h1 style="font-size: 70pt; font-weight: bold; color: #029688; margin-top: -10px;"><?= $data['plat_nomor'] ?></h1>
						<h3 style="margin-top: -10px;color: #029688"><?= $data['plat_nomor'] ?></h3>
						<button type="submit" onclick="javascript:printDiv('print-karcis');" class="btn btn-outline btn-success no-print" style="width: 13%;height: 1%; margin-top: 1%" name="btn_print">
							<div style="font-size: 14pt; font-weight: bold;">
								<span class="icons icon-printer"> </span>
								Print
							</div>
						</button>
					</div>
				</div>
			</form>
		</center>
		<!-- end: content -->
	</div>

	<textarea id="printing-css" style="display:none;">ini adalah code parkir nya kamu </textarea>
	<iframe id="printing-frame" name="print_frame" src="about:blank" style="display:none;"></iframe>
	<script>
		function printDiv(elementId) {
			var a = document.getElementById('printing-css').value;
			var b = document.getElementById(elementId).innerHTML;
			window.frames["print_frame"].document.title = document.title;
			window.frames["print_frame"].document.body.innerHTML = '<style>' + a + '</style>' + b;
			window.frames["print_frame"].window.focus();
			window.frames["print_frame"].window.print();
		}
	</script>
</body>

</html>