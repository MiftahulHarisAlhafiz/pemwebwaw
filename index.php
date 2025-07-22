<?php
session_start();
if (isset($_SESSION['role'])) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: dashboard_admin.php");
  } elseif ($_SESSION['role'] === 'petugas') {
    header("Location: dashboard_petugas.php");
  } else {
    header("Location: dashboard_masyarakat.php");
  }
} else {
  header("Location: login.php");
}
?>
