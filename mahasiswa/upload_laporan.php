<?php
session_start();
include '../config.php';

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pastikan ada modul_id
if (!isset($_POST['modul_id'])) {
    die('Modul tidak valid.');
}

$modul_id = intval($_POST['modul_id']);

// Validasi file upload
if (!isset($_FILES['file_laporan']) || $_FILES['file_laporan']['error'] !== 0) {
    die('Upload file gagal.');
}

// Rename file unik
$file_name = time() . '_' . basename($_FILES['file_laporan']['name']);
$target_dir = "../uploads/";
$target_file = $target_dir . $file_name;

// Pindahkan file ke folder uploads/
if (move_uploaded_file($_FILES['file_laporan']['tmp_name'], $target_file)) {
    // Cek jika laporan sudah ada untuk modul ini
    $cek = $conn->prepare("SELECT * FROM laporan WHERE modul_id=? AND user_id=?");
    $cek->bind_param("ii", $modul_id, $user_id);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        // Jika sudah ada ➜ update file
        $stmt = $conn->prepare("UPDATE laporan SET file_laporan=?, submitted_at=NOW() WHERE modul_id=? AND user_id=?");
        $stmt->bind_param("sii", $file_name, $modul_id, $user_id);
    } else {
        // Jika belum ada ➜ insert baru
        $stmt = $conn->prepare("INSERT INTO laporan (modul_id, user_id, file_laporan) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $modul_id, $user_id, $file_name);
    }
    $stmt->execute();

    // Redirect kembali ke modul_saya.php
    $praktikum = $conn->query("SELECT praktikum_id FROM modul WHERE id=$modul_id")->fetch_assoc();
    $praktikum_id = $praktikum['praktikum_id'];

    header("Location: modul_saya.php?praktikum_id=$praktikum_id");
    exit();
} else {
    die('Gagal memindahkan file.');
}
?>
