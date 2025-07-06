<?php
require_once 'templates/header_asisten.php';
include '../config.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM mata_praktikum WHERE id = $id");
header("Location: daftar_praktikum.php");
exit();
