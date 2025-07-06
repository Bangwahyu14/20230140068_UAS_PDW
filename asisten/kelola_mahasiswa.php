<?php
$pageTitle = 'Kelola Mahasiswa & Asisten';
$activePage = 'users';
require_once 'templates/header.php';
include '../config.php';

// Tambah User
if (isset($_POST['add'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    $stmt->execute();

    header("Location: kelola_mahasiswa.php");
    exit();
}

// Hapus User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // 1️⃣ Hapus semua laporan milik user ini
    $stmt = $conn->prepare("DELETE FROM laporan WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // 2️⃣ Hapus user-nya
    $conn->query("DELETE FROM users WHERE id=$id");

    header("Location: kelola_mahasiswa.php");
    exit();
}


// Mode Edit
$editMode = false;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editData = $conn->query("SELECT * FROM users WHERE id=$editId")->fetch_assoc();
    if ($editData) { $editMode = true; }
}

// Update User
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $email, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $email, $role, $id);
    }
    $stmt->execute();
    header("Location: kelola_mahasiswa.php");
    exit();
}

// Ambil semua user
$users = $conn->query("SELECT * FROM users ORDER BY role, created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Mahasiswa & Asisten</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Kelola Mahasiswa & Asisten</h1>

  <!-- Form Tambah / Edit -->
  <div class="bg-white p-4 rounded shadow mb-6 w-full md:w-2/3">
    <?php if ($editMode): ?>
      <h2 class="text-xl mb-2 font-semibold">Edit Pengguna</h2>
      <form method="POST">
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">

        <label>Nama:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($editData['nama']) ?>" required class="border w-full p-2 mb-2">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($editData['email']) ?>" required class="border w-full p-2 mb-2">

        <label>Password (Biarkan kosong jika tidak diubah):</label>
        <input type="password" name="password" class="border w-full p-2 mb-2">

        <label>Role:</label>
        <select name="role" required class="border w-full p-2 mb-2">
          <option value="mahasiswa" <?= $editData['role']=='mahasiswa' ? 'selected':'' ?>>Mahasiswa</option>
          <option value="asisten" <?= $editData['role']=='asisten' ? 'selected':'' ?>>Asisten</option>
        </select>

        <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
        <a href="kelola_mahasiswa.php" class="ml-2 text-red-500">Batal</a>
      </form>
    <?php else: ?>
      <h2 class="text-xl mb-2 font-semibold">Tambah Pengguna Baru</h2>
      <form method="POST">
        <label>Nama:</label>
        <input type="text" name="nama" required class="border w-full p-2 mb-2">

        <label>Email:</label>
        <input type="email" name="email" required class="border w-full p-2 mb-2">

        <label>Password:</label>
        <input type="password" name="password" required class="border w-full p-2 mb-2">

        <label>Role:</label>
        <select name="role" required class="border w-full p-2 mb-2">
          <option value="mahasiswa">Mahasiswa</option>
          <option value="asisten">Asisten</option>
        </select>

        <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Tambah Pengguna</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Tabel Pengguna -->
  <table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-200">
      <tr>
        <th class="p-2">Nama</th>
        <th class="p-2">Email</th>
        <th class="p-2">Role</th>
        <th class="p-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $row): ?>
        <tr>
          <td class="border p-2"><?= htmlspecialchars($row['nama']) ?></td>
          <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
          <td class="border p-2"><?= htmlspecialchars($row['role']) ?></td>
          <td class="border p-2">
            <a href="kelola_mahasiswa.php?edit=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
            <a href="kelola_mahasiswa.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus user ini?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
