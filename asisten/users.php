<?php
require_once 'templates/header_asisten.php';
include '../config.php';

// === TAMBAH USER ===
if (isset($_POST['add'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    $stmt->execute();
    header("Location: users.php");
    exit;
}

// === HAPUS USER ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit;
}

// === AMBIL DATA USER ===
$users = $conn->query("SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Manajemen Akun Pengguna</h1>

    <!-- Form Tambah User -->
    <div class="bg-white p-4 rounded shadow mb-6">
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
    </div>

    <!-- Tabel User -->
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
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['email']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['role']) ?></td>
                    <td class="border p-2">
                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                        <a href="users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
