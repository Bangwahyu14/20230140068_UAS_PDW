<?php
$pageTitle = 'Edit Modul';
$activePage = 'modul';
require_once 'templates/header.php';
include '../config.php';

// Cek ID
if (!isset($_GET['id'])) {
    die('ID modul tidak valid.');
}

$id = intval($_GET['id']);

// Ambil data modul
$modul = $conn->query("SELECT * FROM modul WHERE id=$id")->fetch_assoc();
if (!$modul) {
    die('Modul tidak ditemukan.');
}

// Ambil daftar praktikum
$praktikum = $conn->query("SELECT * FROM mata_praktikum");

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $praktikum_id = $_POST['praktikum_id'];
    $judul = trim($_POST['judul']);

    // Upload file jika ada file baru
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../uploads/" . $file_name);

        $stmt = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=?, file_materi=? WHERE id=?");
        $stmt->bind_param("issi", $praktikum_id, $judul, $file_name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=? WHERE id=?");
        $stmt->bind_param("isi", $praktikum_id, $judul, $id);
    }

    $stmt->execute();
    header("Location: manajemen_modul.php?status=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Modul</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Edit Modul</h1>

  <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-full md:w-2/3">
    <label>Mata Praktikum:</label>
    <select name="praktikum_id" required class="border w-full p-2 mb-2">
      <?php foreach ($praktikum as $row): ?>
        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $modul['praktikum_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['nama_praktikum']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Judul Modul:</label>
    <input type="text" name="judul" value="<?= htmlspecialchars($modul['judul']) ?>" required class="border w-full p-2 mb-2">

    <label>File Materi (Opsional):</label>
    <input type="file" name="file_materi" accept=".pdf,.docx" class="border w-full p-2 mb-2">

    <?php if ($modul['file_materi']): ?>
      <p class="text-sm">File saat ini: 
        <a href="../uploads/<?= htmlspecialchars($modul['file_materi']) ?>" target="_blank" class="text-blue-600 underline">
          <?= htmlspecialchars($modul['file_materi']) ?>
        </a>
      </p>
    <?php endif; ?>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
    <a href="manajemen_modul.php" class="ml-4 text-gray-600 underline">Batal</a>
  </form>
</body>
</html>
