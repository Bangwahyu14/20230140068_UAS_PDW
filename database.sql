CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel daftar mata praktikum
CREATE TABLE `mata_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_praktikum` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel relasi pendaftaran praktikum oleh mahasiswa
CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `praktikum_id` int(11) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`praktikum_id`) REFERENCES `mata_praktikum`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel modul materi untuk setiap praktikum
CREATE TABLE `modul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praktikum_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `file_materi` varchar(255),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`praktikum_id`) REFERENCES `mata_praktikum`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel laporan yang diunggah mahasiswa
CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modul_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_laporan` varchar(255),
  `nilai` int(11) DEFAULT NULL,
  `feedback` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`modul_id`) REFERENCES `modul`(`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pendaftaran` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `praktikum_id` INT(11) NOT NULL,
  `tanggal_daftar` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`praktikum_id`) REFERENCES `mata_praktikum`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT mp.* FROM mata_praktikum mp
JOIN pendaftaran p ON mp.id = p.praktikum_id
WHERE p.user_id = ?

CREATE TABLE `modul` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `praktikum_id` INT,
  `judul` VARCHAR(255),
  `file_materi` VARCHAR(255),
  FOREIGN KEY (`praktikum_id`) REFERENCES mata_praktikum(id)
);

CREATE TABLE `laporan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `modul_id` INT,
  `user_id` INT,
  `file_laporan` VARCHAR(255),
  `nilai` DECIMAL(5,2),
  `feedback` TEXT,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `laporan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `modul_id` INT,
  `user_id` INT,
  `file_laporan` VARCHAR(255),
  `nilai` DECIMAL(5,2),
  `feedback` TEXT,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `pendaftaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `praktikum_id` INT
);



