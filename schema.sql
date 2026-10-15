CREATE DATABASE db_absensi;
USE db_absensi;

CREATE TABLE tb_absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpa') NOT NULL,
    tanggal DATE NOT NULL
);