<?php
include 'koneksi.php'; 
session_start();
date_default_timezone_set("Asia/Jakarta");
		
		$plat_nomer= "B 1234 EK";
		$query = mysqli_query($con, "SELECT hitung_jam_masuk,jenis FROM tb_daftar_parkir WHERE plat_nomer = '$plat_nomer'");
		$data = mysqli_fetch_array($query);

		$jam_keluar = date('H');
		$jam_masuk = $data['hitung_jam_masuk'];

		if ($jam_keluar == $jam_masuk) {
			$lama = 1;
		}else if ($jam_keluar > $jam_masuk){
			$lama = $jam_keluar - $jam_masuk;
		}else{
			$jam_keluar = $jam_keluar + 24;
			$lama = $jam_keluar - $jam_masuk;
		}

		echo $jam_masuk . "<br/>";
		echo $jam_keluar . "<br/>";
		echo $lama;
 ?>