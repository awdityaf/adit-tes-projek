<?php
	$env = parse_ini_file('.env');
	$host = $env['DB_HOST'];
	$username = $env['DB_USERNAME'];
	$password = $env['DB_PASSWORD'];
	$database = $env['DB_NAME'];

	$mysqli = mysqli_connect($host, $username, $password, $database);
	if (mysqli_connect_errno()) {
	    die("Koneksi gagal: ". mysqli_connect_error());
	}
?>
