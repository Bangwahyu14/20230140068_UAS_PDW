<?php
session_start();
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php';
include '../config.php';

// Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil praktikum pertama yang diikuti
$praktikum = $conn->query("
  SELECT mp.* FROM mata_praktikum mp
  JOIN pendaftaran p ON mp.id = p.praktikum_id
  WHERE p.user_id = $user_id
  LIMIT 1
")->fetch_assoc();
?>



<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600">3</div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500">8</div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500">4</div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
  <!-- Kotak Praktikum Diikuti, Tugas Selesai, dll -->
</div>

<!-- Tambahkan tombol shortcut di sini -->
<div class="text-center mb-8">
  <a href="modul_saya.php"
     class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
    Lihat Semua Modul
  </a>
</div>


    
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        
        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">🔔</span>
            <div>
                Nilai untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 1: HTML & CSS</a> telah diberikan.
            </div>
        </li>

        <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
            <span class="text-xl mr-4">⏳</span>
            <div>
                Batas waktu pengumpulan laporan untuk <a href="#" class="font-semibold text-blue-600 hover:underline">Modul 2: PHP Native</a> adalah besok!
            </div>
        </li>

        <li class="flex items-start p-3">
            <span class="text-xl mr-4">✅</span>
            <div>
                Anda berhasil mendaftar pada mata praktikum <a href="#" class="font-semibold text-blue-600 hover:underline">Jaringan Komputer</a>.
            </div>
        </li>
        
    </ul>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>