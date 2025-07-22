<?php include 'header.php'; include 'database.php';

// Proses hapus barang
if (isset($_GET['hapus'])) {
  $conn->prepare("DELETE FROM barang WHERE id = ?")->execute([$_GET['hapus']]);
  echo "<div class='alert alert-success'>Barang berhasil dihapus.</div>";
}

// Proses tutup lelang langsung
if (isset($_GET['tutup'])) {
  $id_barang = $_GET['tutup'];

  // Ambil ID lelang
  $stmt = $conn->prepare("SELECT id FROM lelang WHERE id_barang = ?");
  $stmt->execute([$id_barang]);
  $id_lelang = $stmt->fetchColumn();

  // Ambil penawaran tertinggi
  $stmt = $conn->prepare("
    SELECT id_user, harga_tawar 
    FROM penawaran 
    WHERE id_lelang = ? 
    ORDER BY harga_tawar DESC LIMIT 1
  ");
  $stmt->execute([$id_lelang]);
  $data = $stmt->fetch();

  if ($data) {
    $conn->prepare("UPDATE lelang SET status = 'ditutup', id_pemenang = ?, harga_akhir = ? WHERE id = ?")
         ->execute([$data['id_user'], $data['harga_tawar'], $id_lelang]);
  } else {
    $conn->prepare("UPDATE lelang SET status = 'ditutup' WHERE id = ?")->execute([$id_lelang]);
  }

  $conn->prepare("UPDATE barang SET status = 'terjual' WHERE id = ?")->execute([$id_barang]);

  echo "<script>alert('Lelang telah ditutup.'); location.href='dashboard_{$_SESSION['role']}.php';</script>";
  exit;
}
?>

<h3>Dashboard <?= ucfirst($_SESSION['role']) ?></h3>
<p>Selamat datang, <strong><?= $_SESSION['nama'] ?></strong>!</p>

<a href="barang_tambah.php" class="btn btn-success mb-3">+ Tambah Barang</a>

<?php
// Ambil data barang dan petugas input
$stmt = $conn->query("
  SELECT b.*, u.nama 
  FROM barang b 
  JOIN users u ON b.id_user = u.id 
  ORDER BY b.id DESC
");
?>

<table class="table table-bordered">
  <thead class="table-light">
    <tr>
      <th>No</th>
      <th>Barang</th>
      <th>Harga Awal</th>
      <th>Status</th>
      <th>Petugas</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; foreach ($stmt as $row): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['nama_barang'] ?></td>
        <td>Rp<?= number_format($row['harga_awal']) ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= $row['nama'] ?></td>
        <td>
          <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus barang ini?')" class="btn btn-danger btn-sm">Hapus</a>
          
          <?php if ($row['status'] === 'tersedia'): ?>
            <a href="lelang_buka.php?id_barang=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Buka Lelang</a>
          
          <?php elseif ($row['status'] === 'dilelang'): ?>
            <a href="?tutup=<?= $row['id'] ?>" onclick="return confirm('Tutup lelang ini?')" class="btn btn-warning btn-sm">Tutup Lelang</a>
          
          <?php elseif ($row['status'] === 'terjual'): 
            $q = $conn->prepare("
              SELECT l.harga_akhir, u.nama 
              FROM lelang l 
              LEFT JOIN users u ON l.id_pemenang = u.id 
              WHERE l.id_barang = ?
            ");
            $q->execute([$row['id']]);
            $pemenang = $q->fetch();
          ?>
            <div class="text-success small mt-2">
              <strong>Pemenang:</strong> <?= $pemenang['nama'] ?? 'Tidak ada' ?><br>
              <strong>Harga Akhir:</strong> Rp<?= number_format($pemenang['harga_akhir']) ?>
            </div>
          <?php endif; ?>
        </td>
      </tr>

      <?php
      // Tampilkan penawaran jika dilelang
      if ($row['status'] === 'dilelang'):
        $q = $conn->prepare("SELECT id FROM lelang WHERE id_barang = ?");
        $q->execute([$row['id']]);
        $id_lelang = $q->fetchColumn();

        $p = $conn->prepare("
          SELECT u.nama, pn.harga_tawar, pn.waktu_penawaran
          FROM penawaran pn
          JOIN users u ON pn.id_user = u.id
          WHERE pn.id_lelang = ?
          ORDER BY pn.harga_tawar DESC
        ");
        $p->execute([$id_lelang]);
      ?>
        <tr>
          <td colspan="6">
            <strong>Penawaran Masyarakat:</strong>
            <?php if ($p->rowCount() == 0): ?>
              <div class="text-muted small">Belum ada penawaran.</div>
            <?php else: ?>
              <table class="table table-sm table-bordered mt-2">
                <thead class="table-light">
                  <tr>
                    <th>Nama</th>
                    <th>Harga Tawar</th>
                    <th>Waktu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($p as $pen): ?>
                    <tr>
                      <td><?= $pen['nama'] ?></td>
                      <td>Rp<?= number_format($pen['harga_tawar']) ?></td>
                      <td><?= $pen['waktu_penawaran'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include 'footer.php'; ?>
