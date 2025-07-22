<?php include 'header.php'; include 'database.php'; ?>

<h3 class="mb-4">Dashboard Masyarakat</h3>
<p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>!</p>

<?php
// Proses penawaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_lelang'])) {
  $id_lelang = $_POST['id_lelang'];
  $harga_tawar = $_POST['harga_tawar'];
  $id_user = $_SESSION['id'];

  if (!is_numeric($harga_tawar) || $harga_tawar <= 0) {
    echo "<div class='alert alert-danger'>Harga tawar tidak valid!</div>";
  } else {
    $cek = $conn->prepare("SELECT MAX(harga_tawar) AS max_tawar FROM penawaran WHERE id_lelang = ?");
    $cek->execute([$id_lelang]);
    $max = $cek->fetch()['max_tawar'];

    if ($max && $harga_tawar <= $max) {
      echo "<div class='alert alert-warning'>Penawaran Anda harus lebih tinggi dari Rp " . number_format($max) . ".</div>";
    } else {
      $stmt = $conn->prepare("INSERT INTO penawaran (id_lelang, id_user, harga_tawar) VALUES (?, ?, ?)");
      $stmt->execute([$id_lelang, $id_user, $harga_tawar]);
      echo "<div class='alert alert-success'>Penawaran berhasil disimpan!</div>";
    }
  }
}

// Ambil lelang yang masih dibuka
$stmt = $conn->query("
  SELECT l.id AS id_lelang, b.nama_barang, b.deskripsi, b.harga_awal, b.tanggal_upload
  FROM lelang l
  JOIN barang b ON l.id_barang = b.id
  WHERE l.status = 'dibuka'
  ORDER BY l.id DESC
");
?>

<?php if ($stmt->rowCount() == 0): ?>
  <div class="alert alert-info mt-4">Belum ada lelang yang dibuka saat ini.</div>
<?php endif; ?>

<div class="row mt-4">
  <?php foreach ($stmt as $row): 
    $top = $conn->prepare("SELECT MAX(harga_tawar) AS tertinggi FROM penawaran WHERE id_lelang = ?");
    $top->execute([$row['id_lelang']]);
    $tertinggi = $top->fetch()['tertinggi'] ?? 0;
  ?>
    <div class="col-md-6">
      <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($row['nama_barang']) ?></h5>
          <h6 class="card-subtitle text-muted mb-2">Harga Awal: Rp<?= number_format($row['harga_awal']) ?></h6>
          <p><?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 100))) ?>...</p>
          <p class="text-muted small mb-2">Upload: <?= $row['tanggal_upload'] ?></p>
          <p><strong>Penawaran Tertinggi:</strong> Rp<?= number_format($tertinggi ?: $row['harga_awal']) ?></p>

          <form method="POST">
            <input type="hidden" name="id_lelang" value="<?= $row['id_lelang'] ?>">
            <div class="input-group">
              <input type="number" name="harga_tawar" class="form-control form-control-sm" required placeholder="Masukkan tawaran anda..." min="<?= $tertinggi + 1 ?>">
              <button class="btn btn-primary btn-sm">Tawar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<hr class="my-5">

<h4 class="mb-3">Hasil Lelang yang Anda Ikuti</h4>

<?php
$hasil = $conn->prepare("
  SELECT l.id, b.nama_barang, l.status, l.harga_akhir, u.nama AS pemenang
  FROM lelang l
  JOIN barang b ON l.id_barang = b.id
  LEFT JOIN users u ON l.id_pemenang = u.id
  WHERE l.status = 'ditutup' AND l.id IN (
    SELECT id_lelang FROM penawaran WHERE id_user = ?
  )
  ORDER BY l.id DESC
");
$hasil->execute([$_SESSION['id']]);
?>

<?php if ($hasil->rowCount() == 0): ?>
  <div class="alert alert-secondary">Belum ada hasil lelang yang diumumkan.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>Barang</th>
          <th>Status</th>
          <th>Harga Akhir</th>
          <th>Hasil</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($hasil as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><span class="badge bg-secondary">Ditutup</span></td>
            <td>Rp<?= number_format($row['harga_akhir']) ?></td>
            <td>
              <?php if ($row['pemenang'] == $_SESSION['nama']): ?>
                <span class="badge bg-success">Anda Menang</span>
              <?php else: ?>
                <span class="badge bg-danger">Anda Kalah</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
