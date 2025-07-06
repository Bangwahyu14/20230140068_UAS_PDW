<?php
require_once 'templates/header_asisten.php';
include '../config.php';

// Tambah Modul
if (isset($_POST['add'])) {
    $praktikum_id = $_POST['praktikum_id'];
    $judul = $_POST['judul'];

    // Upload file materi
    $file_materi = null;
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../uploads/" . $file_name);
        $file_materi = $file_name;
    }

    $stmt = $conn->prepare("INSERT INTO modul (praktikum_id, judul, file_materi) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $praktikum_id, $judul, $file_materi);
    $stmt->execute();
    header("Location: modul.php");
    exit;
}

// Hapus Modul
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM modul WHERE id=$id");
    header("Location: modul.php");
    exit;
}

// Ambil semua praktikum
$praktikum = $conn->query("SELECT * FROM mata_praktikum");

// Ambil semua modul
$modul = $conn->query("SELECT m.*, mp.nama_praktikum FROM modul m JOIN mata_praktikum mp ON m.praktikum_id = mp.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Modul</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Manajemen Modul</h1>

    <!-- Tambah Modul -->
    <div class="bg-white p-4 mb-6 rounded shadow">
        <form method="POST" enctype="multipart/form-data">
            <label>Praktikum:</label>
            <select name="praktikum_id" required class="border w-full p-2 mb-2">
                <?php while ($row = $praktikum->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_praktikum']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Judul Modul:</label>
            <input type="text" name="judul" required class="border w-full p-2 mb-2">

            <label>File Materi:</label>
            <input type="file" name="file_materi" accept=".pdf,.docx" class="border w-full p-2 mb-2">

            <button type="submit" name="add" class="bg-green-500 text-white px-4 py-2 rounded">Tambah</button>
        </form>
    </div>

    <!-- Tabel Modul -->
    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Praktikum</th>
                <th class="p-2">Judul</th>
                <th class="p-2">File Materi</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $modul->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['judul']) ?></td>
                    <td class="border p-2">
                        <?php if ($row['file_materi']): ?>
                            <a href="../uploads/<?= urlencode($row['file_materi']) ?>" target="_blank" class="text-blue-600 underline">Download</a>
                        <?php endif; ?>
                    </td>
                    <td class="border p-2">
                        <a href="modul.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
