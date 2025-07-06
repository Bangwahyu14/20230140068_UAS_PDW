<?php
require_once 'templates/header_asisten.php';
include '../config.php';

// === TAMBAH ===
if (isset($_POST['add'])) {
    $nama = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = $conn->prepare("INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    $stmt->execute();

    header("Location: mata_praktikum.php");
    exit();
}

// === HAPUS ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM mata_praktikum WHERE id=$id");
    header("Location: mata_praktikum.php");
    exit();
}

// === DATA ===
$result = $conn->query("SELECT * FROM mata_praktikum");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Manajemen Mata Praktikum</h1>

    <!-- Tambah -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="POST">
            <label>Nama Praktikum:</label>
            <input type="text" name="nama_praktikum" required class="border w-full p-2 mb-2">

            <label>Deskripsi:</label>
            <textarea name="deskripsi" rows="3" class="border w-full p-2 mb-2"></textarea>

            <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Tambah Praktikum</button>
        </form>
    </div>

    <!-- Tabel -->
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
                        <a href="mata_praktikum.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
