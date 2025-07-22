<?php include 'header.php'; include 'database.php'; ?>

<h3>Daftar Lelang Aktif</h3>

<?php
$stmt = $conn->query("
  SELECT l.id AS id_lelang, b.nama_barang, b.deskripsi, b.harga_awal
  FROM lelang l
  JOIN barang b ON l.id_barang = b.id
  WHERE l.status = 'dibuka'
");

foreach ($stmt as $row): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5><?= $row['nama_barang'] ?> - Rp<?= number_format($row['harga_awal']) ?></h5>
      <p><?= $row['deskripsi'] ?></p>
      <a href="penawaran.php?id=<?= $row['id_lelang'] ?>" class="btn btn-primary btn-sm">Tawar</a>
    </div>
  </div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
