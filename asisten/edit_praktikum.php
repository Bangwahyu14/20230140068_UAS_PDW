<?php
require_once 'templates/header_asisten.php';
include '../config.php';

// Ambil data praktikum yang akan diedit
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM mata_praktikum WHERE id = $id");
$praktikum = $result->fetch_assoc();

if (!$praktikum) {
    die("Data praktikum tidak ditemukan!");
}

// Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = $conn->prepare("UPDATE mata_praktikum SET nama_praktikum=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    $stmt->execute();

    header("Location: daftar_praktikum.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Praktikum</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Edit Praktikum</h1>
  <form method="POST" class="bg-white p-4 rounded shadow w-full md:w-1/2">
    <label>Nama Praktikum:</label>
    <input type="text" name="nama_praktikum" value="<?= htmlspecialchars($praktikum['nama_praktikum']) ?>" required class="border w-full p-2 mb-2">

    <label>Deskripsi:</label>
    <textarea name="deskripsi" rows="3" class="border w-full p-2 mb-2"><?= htmlspecialchars($praktikum['deskripsi']) ?></textarea>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
  </form>
</body>
</html>
