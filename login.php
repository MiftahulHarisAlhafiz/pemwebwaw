<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];
        $_SESSION['id'] = $user['id']; // ✅ Tambahkan baris ini!


    header("Location: index.php");
    exit;
  } else {
    $error = "Username atau password salah.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login - Lelangin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="col-md-5 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="text-center">Login</h4>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100" type="submit">Masuk</button>
        </form>
        <p class="text-center mt-2">Belum punya akun? <a href="register.php">Daftar</a></p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
