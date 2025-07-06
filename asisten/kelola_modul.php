<?php
$pageTitle = 'Kelola Modul';
$activePage = 'modul';
require_once 'templates/header.php';
include '../config.php';

// ➕ TAMBAH MODUL
if (isset($_POST['add'])) {
    $praktikum_id = $_POST['praktikum_id'];
    $judul = trim($_POST['judul']);
    $file_materi = null;

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === 0) {
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../uploads/" . $file_name);
        $file_materi = $file_name;
    }

    $stmt = $conn->prepare("INSERT INTO modul (praktikum_id, judul, file_materi) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $praktikum_id, $judul, $file_materi);
    $stmt->execute();

    header("Location: kelola_modul.php");
    exit();
}

// ✏️ UPDATE MODUL
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $praktikum_id = $_POST['praktikum_id'];
    $judul = trim($_POST['judul']);

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] === 0) {
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../uploads/" . $file_name);
        $stmt = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=?, file_materi=? WHERE id=?");
        $stmt->bind_param("issi", $praktikum_id, $judul, $file_name, $id);
    } else {
        $stmt = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=? WHERE id=?");
        $stmt->bind_param("isi", $praktikum_id, $judul, $id);
    }
    $stmt->execute();
    header("Location: kelola_modul.php");
    exit();
}

// ❌ HAPUS MODUL
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM modul WHERE id=$id");
    header("Location: kelola_modul.php");
    exit();
}

// AMBIL DATA
$praktikum = $conn->query("SELECT * FROM mata_praktikum");
$modul = $conn->query("
  SELECT m.*, mp.nama_praktikum 
  FROM modul m 
  JOIN mata_praktikum mp ON m.praktikum_id = mp.id
");

// MODE EDIT
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editData = $conn->query("SELECT * FROM modul WHERE id=$editId")->fetch_assoc();
    if ($editData) $editMode = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Modul</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Kelola Modul</h1>

  <!-- FORM TAMBAH / EDIT -->
  <div class="bg-white p-4 rounded shadow mb-6 w-full md:w-2/3">
    <?php if ($editMode): ?>
      <h2 class="text-xl mb-2 font-semibold">Edit Modul</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        
        <label>Mata Praktikum:</label>
        <select name="praktikum_id" required class="border w-full p-2 mb-2">
          <?php foreach ($praktikum as $row): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id']==$editData['praktikum_id']) ? 'selected':'' ?>>
              <?= htmlspecialchars($row['nama_praktikum']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Judul Modul:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($editData['judul']) ?>" required class="border w-full p-2 mb-2">

        <label>File Materi (Opsional):</label>
        <input type="file" name="file_materi" accept=".pdf,.docx" class="border w-full p-2 mb-2">
        <?php if ($editData['file_materi']): ?>
          <p class="text-sm">File saat ini: <a href="../uploads/<?= htmlspecialchars($editData['file_materi']) ?>" target="_blank" class="text-blue-600 underline"><?= htmlspecialchars($editData['file_materi']) ?></a></p>
        <?php endif; ?>

        <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
        <a href="kelola_modul.php" class="ml-2 text-red-500">Batal</a>
      </form>
    <?php else: ?>
      <h2 class="text-xl mb-2 font-semibold">Tambah Modul Baru</h2>
      <form method="POST" enctype="multipart/form-data">
        <label>Mata Praktikum:</label>
        <select name="praktikum_id" required class="border w-full p-2 mb-2">
          <?php foreach ($praktikum as $row): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_praktikum']) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Judul Modul:</label>
        <input type="text" name="judul" required class="border w-full p-2 mb-2">

        <label>File Materi:</label>
        <input type="file" name="file_materi" accept=".pdf,.docx" class="border w-full p-2 mb-2">

        <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Tambah Modul</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- TABEL MODUL -->
  <table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-200">
      <tr>
        <th class="p-2">Praktikum</th>
        <th class="p-2">Judul Modul</th>
        <th class="p-2">File Materi</th>
        <th class="p-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($modul as $row): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
          <td class="border p-2"><?= htmlspecialchars($row['judul']) ?></td>
          <td class="border p-2">
            <?php if ($row['file_materi']): ?>
              <a href="../uploads/<?= htmlspecialchars($row['file_materi']) ?>" target="_blank" class="text-blue-600 underline">Download</a>
            <?php endif; ?>
          </td>
          <td class="border p-2">
            <a href="kelola_modul.php?edit=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
            <a href="kelola_modul.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus modul ini?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
