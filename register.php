<?php
include 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  $cek = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $cek->execute([$username]);

  if ($cek->rowCount() > 0) {
    $error = "Username sudah dipakai.";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $username, $password, $role]);
    $success = "Akun berhasil dibuat. Silakan login.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register - Lelangin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="col-md-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="text-center mb-3">Registrasi</h4>
        <?php
        if (isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        if (isset($success)) echo "<div class='alert alert-success'>$success</div>";
        ?>
        <form method="POST">
          <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required class="form-control">
          </div>
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" required class="form-control">
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" required class="form-control">
          </div>
          <div class="mb-3">
            <label>Daftar Sebagai</label>
            <select name="role" class="form-control" required>
              <option value="masyarakat">Masyarakat</option>
              <option value="petugas">Petugas</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button class="btn btn-success w-100">Daftar</button>
        </form>
        <p class="text-center mt-2">Sudah punya akun? <a href="login.php">Login</a></p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
