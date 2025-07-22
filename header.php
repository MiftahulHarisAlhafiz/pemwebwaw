
<?php include 'auth.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Lelangin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Lelangin</a>
    <span class="text-white"><?= $_SESSION['nama'] ?> (<?= $_SESSION['role'] ?>)</span>
    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>
<div class="container">
