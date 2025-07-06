<?php
session_start();
include '../config.php';

// Cek apakah user login dan role asisten
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'asisten') {
    die('Akses ditolak. Halaman ini hanya untuk Asisten/Admin.');
}

// Tambah Praktikum
if (isset($_POST['add'])) {
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("INSERT INTO mata_praktikum (nama_praktikum, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    $stmt->execute();
    header("Location: mata_praktikum.php");
    exit;
}

// Update Praktikum
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("UPDATE mata_praktikum SET nama_praktikum=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    $stmt->execute();
    header("Location: mata_praktikum.php");
    exit;
}

// Hapus Praktikum
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: mata_praktikum.php");
    exit;
}


// Ambil semua data
$result = $conn->query("SELECT * FROM mata_praktikum");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Manajemen Mata Praktikum</h1>

        <!-- Form Tambah Praktikum -->
        <div class="mb-8 p-4 bg-white rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Tambah Praktikum Baru</h2>
            <form method="POST">
                <input type="text" name="nama_praktikum" placeholder="Nama Praktikum" required class="border px-2 py-1 w-full mb-2">
                <textarea name="deskripsi" placeholder="Deskripsi" class="border px-2 py-1 w-full mb-2"></textarea>
                <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah</button>
            </form>
        </div>

        <!-- Tabel Data Praktikum -->
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Nama Praktikum</th>
                    <th class="border px-4 py-2">Deskripsi</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['id']; ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="border px-4 py-2"><?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                    <td class="border px-4 py-2">
                        <!-- Tombol Edit -->
                        <button onclick="document.getElementById('edit-<?php echo $row['id']; ?>').classList.toggle('hidden')" class="bg-yellow-400 text-white px-2 py-1 rounded">Edit</button>

                        <!-- Tombol Hapus -->
                        <a href="mata_praktikum.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>

                        <!-- Form Edit (Hidden) -->
                        <div id="edit-<?php echo $row['id']; ?>" class="hidden mt-2">
                            <form method="POST" class="bg-gray-100 p-2 rounded">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="nama_praktikum" value="<?php echo htmlspecialchars($row['nama_praktikum']); ?>" class="border px-2 py-1 w-full mb-2">
                                <textarea name="deskripsi" class="border px-2 py-1 w-full mb-2"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
                                <button type="submit" name="update" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
