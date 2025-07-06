<?php
session_start();
include 'config.php';

// Pastikan hanya mahasiswa yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'mahasiswa') {
    die('Akses ditolak. Anda harus login sebagai mahasiswa.');
}

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modul_id = intval($_POST['modul_id']);

    // Cek apakah sudah pernah upload laporan untuk modul ini
    $stmt = $conn->prepare("SELECT id FROM laporan WHERE user_id = ? AND modul_id = ?");
    $stmt->bind_param("ii", $user_id, $modul_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Anda sudah mengumpulkan laporan untuk modul ini!'); window.history.back();</script>";
        exit;
    }

    // Validasi file
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file_laporan']['tmp_name'];
        $file_name = basename($_FILES['file_laporan']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'docx'];

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Format file harus PDF atau DOCX'); window.history.back();</script>";
            exit;
        }

        $upload_dir = 'uploads/';
        $unique_name = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $file_name);
        $target_file = $upload_dir . $unique_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            // Simpan ke DB
            $stmt = $conn->prepare("INSERT INTO laporan (modul_id, user_id, file_laporan) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $modul_id, $user_id, $unique_name);

            if ($stmt->execute()) {
                echo "<script>alert('Laporan berhasil diunggah!'); window.history.back();</script>";
                exit;
            } else {
                echo "Gagal menyimpan laporan: " . $conn->error;
            }
        } else {
            echo "<script>alert('Gagal mengunggah file!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Tidak ada file yang dipilih!'); window.history.back();</script>";
    }
} else {
    echo "Metode tidak diizinkan.";
}
?>
