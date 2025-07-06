<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $praktikum_id = intval($_POST['praktikum_id']);
    $user_id = $_SESSION['user_id'];

    // Cek jika sudah terdaftar, jangan double insert
    $cek = $conn->prepare("SELECT * FROM pendaftaran WHERE user_id=? AND praktikum_id=?");
    $cek->bind_param("ii", $user_id, $praktikum_id);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO pendaftaran (user_id, praktikum_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $praktikum_id);
        $stmt->execute();
    }

    header("Location: katalog.php");
    exit();
} else {
    die('Permintaan tidak valid.');
}
?>
