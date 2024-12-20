<?php
    define('__ROOT__', dirname(dirname(__FILE__)));
    require_once __ROOT__.'/koneksi.php';

    if( !isset($_GET['act']) ) {
        echo "
            <script>
                alert('Terdapat kesalahan pada parameter Anda.'); 
                window.location.href = '/item/index.php';
            </script>
        ";
    }

    $action = $_GET['act'];

    if( $action == 'edit' ) {
        // action edit

        $itemID = $_GET['id'];

        $query = "SELECT * FROM items WHERE id = $itemID";
        $getItem = $mysqli->query($query);
        $dataItem = $getItem->fetch_assoc();

        if( isset($_POST['submit']) ) {
            $itemID = $_POST['item_id'];
            $itemCode = $_POST['item_code'];
            $itemName = $_POST['item_name'];

            $updateItem = $mysqli->query("UPDATE items SET code = '$itemCode', name = '$itemName' WHERE id = $itemID");
            if ($updateItem) {
                echo "
                    <script>
                        alert('Data berhasil diubah'); 
                        window.location.href = '/produksi/item';
                    </script>
                ";
            } else {
                echo "
                    <script>
                        alert('Data gagal diubah');
                        window.location.href = '/produksi/item';
                    </script>
                ";
            }
        }
    } else if( $action == 'create' ) {
        // action create
        if( isset($_POST['submit']) ) {
            $itemCode = $_POST['item_code'];
            $itemName = $_POST['item_name'];

            $createItem = $mysqli->query("INSERT INTO items VALUES (null, '$itemCode', '$itemName')");
            if ($createItem) {
                echo "
                    <script>
                        alert('Data produk berhasil ditambahkan'); 
                        window.location.href = '/produksi/item';
                    </script>
                ";
            } else {
                echo "
                    <script>
                        alert('Data produk gagal ditambahkan');
                        window.location.href = '/produksi/item';
                    </script>
                ";
            }
        }
    } else if( $action == 'delete' ) {
        // action delete
        $itemID = $_GET['id'];

        $query = "DELETE FROM items WHERE id = $itemID";
        $deleteItem = $mysqli->query($query);

        if ($deleteItem) {
            echo "
                <script>
                    alert('Data berhasil dihapus'); 
                    window.location.href = '/produksi/item';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Data gagal dihapus');
                    window.location.href = '/produksi/item';
                </script>
            ";
        }
    } else if( $action == 'process' ) {
        // action process
        $STEP_PENGADAAN = 1;
        $STEP_PRODUKSI = 2;
        $STEP_PENGECEKAN_KUALITAS = 3;
        $STEP_PENGEMASAN = 4;

        $itemID = $_GET['id'];

        $queryGetProcess = "SELECT * FROM process WHERE item_id = $itemID AND step_id = $STEP_PENGADAAN";
        $getProcess = $mysqli->query($queryGetProcess);
        $itemProcess = $getProcess->fetch_assoc();

        if( $itemProcess ) {
            $processID = $itemProcess['id'];
            $qty = 1 + $itemProcess['qty'];
            $query = "UPDATE process SET qty = $qty WHERE id = $processID";
        } else {
            $query = "INSERT INTO process VALUES (null, 1, $itemID, 1, null)";
        }
        
        $processItem = $mysqli->query($query);

        if ($processItem) {
            echo "
                <script>
                    alert('Data berhasil diproses');
                    window.location.href = '/produksi/process/index.php';
                </script>
            ";
        } else {
            echo "
                <script>
                    alert('Data gagal diproses');
                    window.location.href = '/item/index.php';
                </script>
            ";
        }
    } else {
        echo "
            <script>
                alert('Halaman tidak ditemukan.'); 
                window.location.href = '/item/index.php';
            </script>
        ";
    }
?>

<h1><?= $action == 'edit' ? 'Ubah Barang' : 'Tambah Barang'?></h1>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Produksi | Form Barang </title>
</head>
<body>
    <form method="POST">
        <input 
            type="hidden" 
            name="item_id" 
            value="<?= isset($_GET['act']) && $_GET['act'] == 'edit' ? $dataItem['id'] : null ?>"
        >
        <input 
            type="text" 
            name="item_code" 
            value="<?= isset($_GET['act']) && $_GET['act'] == 'edit' ? $dataItem['code'] : '' ?>" 
            placeholder="Kode Barang"
        >
        <input 
            type="text" 
            name="item_name" 
            value="<?= isset($_GET['act']) && $_GET['act'] == 'edit' ? $dataItem['name'] : '' ?>" 
            placeholder="Nama Barang"
        >

        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>