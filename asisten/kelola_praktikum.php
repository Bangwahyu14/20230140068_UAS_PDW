<?php
require_once '../config.php';


// === TAMBAH PRAKTIKUM ===
if (isset($_POST['add'])) {
    $nama = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = $conn->prepare("INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    $stmt->execute();

    header("Location: kelola_praktikum.php");
    exit();
}

// === UPDATE PRAKTIKUM ===
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama_praktikum']);
    $deskripsi = trim($_POST['deskripsi']);

    $stmt = $conn->prepare("UPDATE mata_praktikum SET nama_praktikum=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    $stmt->execute();

    header("Location: kelola_praktikum.php");
    exit();
}

// === HAPUS PRAKTIKUM ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM mata_praktikum WHERE id=$id");
    header("Location: kelola_praktikum.php");
    exit();
}

// === AMBIL SEMUA PRAKTIKUM ===
$praktikum = $conn->query("SELECT * FROM mata_praktikum");

// === JIKA MODE EDIT ===
$editMode = false;
$praktikumEdit = null;

if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $praktikumEdit = $conn->query("SELECT * FROM mata_praktikum WHERE id=$editId")->fetch_assoc();
    if ($praktikumEdit) {
        $editMode = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Mata Praktikum</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Kelola Mata Praktikum</h1>

  <!-- FORM TAMBAH / EDIT -->
  <div class="bg-white p-4 rounded shadow mb-6 w-full md:w-1/2">
    <?php if ($editMode): ?>
      <h2 class="text-xl mb-2 font-semibold">Edit Praktikum</h2>
      <form method="POST">
        <input type="hidden" name="id" value="<?= $praktikumEdit['id'] ?>">
        <label>Nama Praktikum:</label>
        <input type="text" name="nama_praktikum" value="<?= htmlspecialchars($praktikumEdit['nama_praktikum']) ?>" required class="border w-full p-2 mb-2">

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="3" class="border w-full p-2 mb-2"><?= htmlspecialchars($praktikumEdit['deskripsi']) ?></textarea>

        <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
        <a href="kelola_praktikum.php" class="ml-2 text-red-500">Batal</a>
      </form>
    <?php else: ?>
      <h2 class="text-xl mb-2 font-semibold">Tambah Praktikum Baru</h2>
      <form method="POST">
        <label>Nama Praktikum:</label>
        <input type="text" name="nama_praktikum" required class="border w-full p-2 mb-2">

        <label>Deskripsi:</label>
        <textarea name="deskripsi" rows="3" class="border w-full p-2 mb-2"></textarea>

        <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Tambah</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- TABEL PRAKTIKUM -->
  <table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-200">
      <tr>
        <th class="p-2">Nama Praktikum</th>
        <th class="p-2">Deskripsi</th>
        <th class="p-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $praktikum->fetch_assoc()): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
          <td class="border p-2"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
          <td class="border p-2">
            <a href="kelola_praktikum.php?edit=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
            <a href="kelola_praktikum.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
