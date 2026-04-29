<?php
include 'koneksi.php';

// Proteksi Halaman: Cek apakah cookie role ada dan bernilai admin
if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

$query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC");
?>
<!-- Konten HTML Kelola User kamu di sini -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="fw-bold">Daftar Pengguna Sistem</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
        </div>
        <hr>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query)) : 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge <?= $row['role'] == 'admin' ? 'bg-primary' : 'bg-info'; ?>">
                                    <?= $row['role']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="Proses/hapusUser.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>