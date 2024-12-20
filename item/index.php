<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/koneksi.php';

    $query = "SELECT * FROM items";
    $getItems = $mysqli->query($query);
    if( !$getItems ) die("Query gagal : ". $mysqli->error);

    $data = $getItems->fetch_all(MYSQLI_ASSOC);

    mysqli_close($mysqli);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Produksi | Barang </title>
</head>
<body>
    <div style="width: 50vw;">
        <h1>Daftar Barang</h1>

        <div style="margin-bottom: 10px; text-align: right;"> 
            <a href="_form.php?act=create">Tambah Barang</a>
        </div>

        <?php include '_list.php'; ?>
    </div>
</body>
</html>