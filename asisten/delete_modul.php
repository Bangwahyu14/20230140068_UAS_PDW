<?php
$pageTitle = 'Hapus Modul';
$activePage = 'modul';
require_once 'templates/header.php';
include '../config.php';

// Pastikan ada ID
if (!isset($_GET['id'])) {
    die('ID modul tidak valid.');
}

$id = intval($_GET['id']);

// Eksekusi delete
$stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect balik
header("Location: manajemen_modul.php?status=deleted");
exit();
