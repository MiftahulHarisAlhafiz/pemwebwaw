<?php
include 'header.php';
include 'database.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas') {
  echo "<div class='alert alert-danger'>Akses ditolak!</div>";
  include 'footer.php';
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_barang = $_POST['nama_barang'];
  $deskripsi = $_POST['deskripsi'];
  $harga_awal = $_POST['harga_awal'];
  $id_user = $_SESSION['id']; // user yang input

  $stmt = $conn->prepare("INSERT INTO barang (id_user, nama_barang, deskripsi, harga_awal) VALUES (?, ?, ?, ?)");
  $stmt->execute([$id_user, $nama_barang, $deskripsi, $harga_awal]);

  echo "<div class='alert alert-success'>Barang berhasil ditambahkan.</div>";
}
?>

<h3>Tambah Barang Lelang</h3>
<form method="POST">
  <div class="mb-3">
    <label>Nama Barang</label>
    <input type="text" name="nama_barang" required class="form-control">
  </div>
  <div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
  </div>
  <div class="mb-3">
    <label>Harga Awal</label>
    <input type="number" name="harga_awal" required class="form-control">
  </div>
  <button class="btn btn-primary">Simpan</button>
</form>

<?php include 'footer.php'; ?>
