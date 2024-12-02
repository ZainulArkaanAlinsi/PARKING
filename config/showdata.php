<?php

include 'koneksi.php'; // Pastikan koneksi ke database sudah benar
date_default_timezone_set("Asia/Jakarta");

// Validasi dan sanitasi input
if (isset($_POST['plat_nomer'])) {
	$plat_nomer = mysqli_real_escape_string($con, $_POST['plat_nomer']); // Sanitasi input

	// Query untuk mengambil data jam masuk dan jenis kendaraan
	$query = mysqli_query($con, "SELECT hitung_jam_masuk, jenis FROM tb_daftar_parkir WHERE plat_nomer = '$plat_nomer'");

	if ($data = mysqli_fetch_array($query)) {
		$jam_masuk = $data['hitung_jam_masuk'];
		$jam_keluar = date('H');

		// Hitung lama parkir
		if ($jam_keluar == $jam_masuk) {
			$lama = 1; // Jika jam keluar sama dengan jam masuk
		} elseif ($jam_keluar > $jam_masuk) {
			$lama = $jam_keluar - $jam_masuk; // Hitung selisih jam
		} else {
			$jam_keluar += 24; // Tambah 24 jam jika jam keluar kurang dari jam masuk
			$lama = $jam_keluar - $jam_masuk;
		}

		// Hitung biaya berdasarkan jenis kendaraan
		if ($data['jenis'] == "Motor") {
			$hasil = 1500 * $lama;
		} elseif ($data['jenis'] == "Mobil") {
			$hasil = 2500 * $lama;
		} elseif ($data['jenis'] == "Truk/Bus/Lainnya") {
			$hasil = 5000 * $lama;
		} else {
			$hasil = 0; // Jika jenis tidak dikenali
		}

		// Tampilkan hasil
		echo json_encode(['plat_nomer' => $plat_nomer, 'biaya' => $hasil]);
	} else {
		echo json_encode(['error' => 'Plat nomor tidak ditemukan.']);
	}
} else {
	echo json_encode(['error' => 'Plat nomor tidak disediakan.']);
}
