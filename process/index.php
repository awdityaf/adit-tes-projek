<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once __ROOT__ . '/koneksi.php';
 
function handleAction($mysqli, $action, $process_id, $step_id, $item_id)
{
    if ($action == 'move_next') {
        $next_step_id = $step_id + 1;
 
        // Pengecekan jika langkah saat ini adalah pengemasan
        $queryGetStep = "SELECT * FROM steps WHERE id = $next_step_id";
        $getStep = $mysqli->query($queryGetStep);
        $step = $getStep->fetch_assoc();
 
        if ($step['name'] == 'pengemasan') {
            // Query untuk mendapatkan data item dan quantity
            $queryGetItem = "SELECT items.name, process.qty FROM process JOIN items ON process.item_id = items.id WHERE process.id = $process_id";
            $getItem = $mysqli->query($queryGetItem);
            $itemData = $getItem->fetch_assoc();
 
            // Insert data ke tabel stock
            $itemName = $itemData['name'];
            $itemQty = $itemData['qty'];
 
            $queryInsertStock = "INSERT INTO stok (name, qty) VALUES ('$itemName', $itemQty)";
            $mysqli->query($queryInsertStock);
        }
 
        $queryUpdateStep = "UPDATE process SET step_id = $next_step_id WHERE id = $process_id";
        $mysqli->query($queryUpdateStep);
    } elseif ($action == 'move_prev') {
        $prev_step_id = $step_id - 1;
        $queryUpdateStep = "UPDATE process SET step_id = $prev_step_id WHERE id = $process_id";
        $mysqli->query($queryUpdateStep);
    } elseif ($action == 'delete') {
        $queryDeleteItem = "DELETE FROM process WHERE id = $process_id";
        $mysqli->query($queryDeleteItem);
    }
}
 
if (isset($_GET['action']) && isset($_GET['process_id']) && isset($_GET['step_id']) && isset($_GET['item_id'])) {
    $action = $_GET['action'];
    $process_id = $_GET['process_id'];
    $step_id = $_GET['step_id'];
    $item_id = $_GET['item_id'];
 
    handleAction($mysqli, $action, $process_id, $step_id, $item_id);
 
    // Redirect untuk menghindari refresh ulang yang memicu aksi yang sama
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
 
// Query untuk mendapatkan semua proses
$queryGetProcess = "
    SELECT
        items.id as item_id, 
        items.name as item_name, 
        steps.id as step_id, 
        steps.index as step_index, 
        steps.name as step_name,
        process.id as process_id,
        code,
        qty,
        updated_at
    FROM process 
    JOIN items ON items.id = process.item_id 
    JOIN steps ON steps.id = process.step_id
";
$getProcess = $mysqli->query($queryGetProcess);
$itemProcess = $getProcess->fetch_all(MYSQLI_ASSOC);
 
// Filter proses berdasarkan step
if (isset($_GET['filter_step'])) {
    $filterStep = $_GET['filter_step'];
    $itemProcess = array_filter($itemProcess, function ($item) use ($filterStep) {
        return $item['step_id'] == $filterStep;
    });
}
// Filter proses berdasarkan item
if (isset($_GET['filter_item'])) {
    $filterItem = $_GET['filter_item'];
    $itemProcess = array_filter($itemProcess, function ($item) use ($filterItem) {
        return $item['item_id'] == $filterItem;
    });
}
// Filter proses berdasarkan pencarian
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $itemProcess = array_filter($itemProcess, function ($item) use ($searchQuery) {
        return stripos($item['item_name'], $searchQuery) !== false;
    });
}
?>
 
<!DOCTYPE html>
<html>
 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Produksi | Proses </title>
</head>
 
<body>
    <div style="width: 50vw;">
        <h1>Proses Produksi</h1>
        <br />
 
        <a href="/Produksi/item"><- Daftar Barang</a>
 
        <div style="margin: 10px 0; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; flex-direction: column;">
                <label for="filter_step" style="margin-bottom: 5px;">Proses Barang</label>
                <select name="select_item" id="select_item" onchange="location = this.value;">
                    <option selected disabled>Pilih barang</option>
                    <?php
                    $queryGetItems = "SELECT * FROM items";
                    $getItems = $mysqli->query($queryGetItems);
                    $items = $getItems->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <?php foreach ($items as $key => $value): ?>
                        <option value="/item/_form.php?act=process&id=<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
 
            <div style="display: flex; flex-direction: column;">
                <label for="search" style="margin-bottom: 5px;">Cari Barang</label>
                <input type="text" name="search" id="search" placeholder="Cari barang"
                    value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                <button onclick="location = '?search=' + document.getElementById('search').value">Cari</button>
            </div>
 
            <div style="display: flex; flex-direction: column;">
                <label for="filter_step" style="margin-bottom: 5px;">Tampilkan</label>
                <select name="filter_step" id="filter_step" onchange="location = this.value;">
                    <option value="?filter_step=showall" selected>Semua</option>
                    <?php
                    $queryGetSteps = "SELECT * FROM steps";
                    $getSteps = $mysqli->query($queryGetSteps);
                    $steps = $getSteps->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <?php foreach ($steps as $key => $value): ?>
                        <option value="?filter_step=<?= $value['id'] ?>"><?= $value['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
 
        <table border="1" width="100%">
            <tr>
                <th>No.</th>
                <th>Nama Barang</th>
                <th>Quantity</th>
                <th>Status</th>
                <th colspan="3" style="width: 10%;">Aksi</th>
            </tr>
            <?php foreach ($itemProcess as $key => $value): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $value['item_name'] ?></td>
                    <td><?= $value['qty'] ?></td>
                    <td><?= $value['step_name'] ?></td>
                    <td>
                        <?php if ($value['step_id'] > 1): ?>
                            <a
                                href="?action=move_prev&process_id=<?= $value['process_id'] ?>&step_id=<?= $value['step_id'] ?>&item_id=<?= $value['item_id'] ?>">
                                < </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $queryGetMaxStep = "SELECT MAX(id) as max_step_id FROM steps";
                        $getMaxStep = $mysqli->query($queryGetMaxStep);
                        $maxStep = $getMaxStep->fetch_assoc();
                        if ($value['step_id'] < $maxStep['max_step_id']):
                            ?>
                            <a
                                href="?action=move_next&process_id=<?= $value['process_id'] ?>&step_id=<?= $value['step_id'] ?>&item_id=<?= $value['item_id'] ?>">
                                > </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);"
                            onclick="confirmDelete('?action=delete&process_id=<?= $value['process_id'] ?>&step_id=0&item_id=0')">
                            X </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
<script>
        function confirmDelete(url) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                window.location.href = url;
            }
        }
</script>
 
</html>
