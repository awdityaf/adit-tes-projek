<table border="1" style="width: 100%;">
    <tr>
        <th>No.</th>
        <th>Kode Barang</th>
        <th>Nama Barang</th>
        <th colspan="3">Aksi</th>
    </tr>
    <?php foreach ($data as $key => $value) : ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= $value['code'] ?></td>
            <td><?= $value['name'] ?></td>
            <td>
                <a href="_form.php?act=edit&id=<?= $value['id'] ?>">Ubah</a>
            </td>
            <td>
                <a href="_form.php?act=delete&id=<?= $value['id'] ?>">Hapus</a>
            </td>
            <td>
                <a href="_form.php?act=process&id=<?= $value['id'] ?>">Proses</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>