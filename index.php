<?php 
	$request = $_SERVER['REQUEST_URI'];

	switch ($request) {
		case '':
		case '/':
			require_once __DIR__.'/item/index.php';
			break;
		case '/process':
			require_once __DIR__.'/process/index.php';
			break;
		default:
			http_response_code(404);
			echo "Halaman tidak ditemukan.";
			break;
	}
?>