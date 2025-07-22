<?php
include 'database.php';
session_start();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas') {
  header("Location: index.php");
  exit;
}

$id_barang = $_GET['id_barang'];
$id_petugas = $_SESSION['id'];

// Cek apakah sudah ada lelang
$cek = $conn->prepare("SELECT * FROM lelang WHERE id_barang = ?");
$cek->execute([$id_barang]);

if ($cek->rowCount() > 0) {
  echo "<script>alert('Barang ini sudah pernah dilelang!');window.history.back();</script>";
  exit;
}

// Simpan lelang
$conn->prepare("INSERT INTO lelang (id_barang, id_petugas) VALUES (?, ?)")
     ->execute([$id_barang, $id_petugas]);

// Update status barang
$conn->prepare("UPDATE barang SET status = 'dilelang' WHERE id = ?")
     ->execute([$id_barang]);

echo "<script>alert('Lelang berhasil dibuka!');window.location='dashboard_{$_SESSION['role']}.php';</script>";
