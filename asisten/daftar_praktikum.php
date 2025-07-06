<?php
require_once 'templates/header_asisten.php';
include '../config.php';

// Ambil semua data praktikum
$result = $conn->query("SELECT * FROM mata_praktikum");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Mata Praktikum</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Daftar Mata Praktikum</h1>

  <!-- Tombol tambah praktikum -->
  <a href="tambah_praktikum.php" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block">Tambah Praktikum</a>

  <!-- Tabel daftar -->
  <table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-200">
      <tr>
        <th class="p-2">Nama Praktikum</th>
        <th class="p-2">Deskripsi</th>
        <th class="p-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
          <td class="border p-2"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
          <td class="border p-2">
            <a href="edit_praktikum.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
            <a href="delete_praktikum.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
