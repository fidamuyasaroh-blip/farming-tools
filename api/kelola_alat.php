<?php
include 'koneksi.php';

// CEK LOGIN MENGGUNAKAN COOKIE (Bukan Session)
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
    // Jika tidak ada cookie admin, lempar ke login
    header("Location: login.php"); 
    exit();
}

// Proses Tambah Data
if (isset($_POST['tambah'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_gambar   = $_FILES['gambar']['name'];
    $tmp_name      = $_FILES['gambar']['tmp_name'];
    $lokasi_simpan = 'img/' . $nama_gambar;

    if (!is_dir('img')) { mkdir('img', 0777, true); }

    if (move_uploaded_file($tmp_name, $lokasi_simpan)) {
        $query = "INSERT INTO alat (nama_alat, harga, stok, deskripsi, gambar) 
                  VALUES ('$nama', '$harga', '$stok', '$deskripsi', '$nama_gambar')";
        mysqli_query($koneksi, $query);
        header("Location: kelola_alat.php");
        exit();
    }
}

// Proses Edit Data
if (isset($_POST['edit'])) {
    $id        = $_POST['id'];
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_alat']);
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (!empty($_FILES['gambar']['name'])) {
        $nama_gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'img/' . $nama_gambar);
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi', gambar='$nama_gambar' WHERE id='$id'";
    } else {
        $query = "UPDATE alat SET nama_alat='$nama', harga='$harga', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'";
    }

    mysqli_query($koneksi, $query);
    header("Location: kelola_alat.php");
    exit();
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM alat WHERE id='$id'");
    header("Location: kelola_alat.php");
    exit();
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($koneksi, "SELECT * FROM alat WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($q);
}

$query_tampil = mysqli_query($koneksi, "SELECT * FROM alat");
?>