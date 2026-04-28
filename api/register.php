<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card { 
            width: 100%;
            max-width: 400px; 
            border: none; 
            border-radius: 15px; 
        }
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
        }
    </style>
</head>
<body>
    <div class="register-container px-3">
        <!-- Action tetap ke Proses/prosesRegister.php karena folder Proses ada di dalam folder api -->
        <form action="Proses/prosesRegister.php" method="POST" class="card p-4 p-md-5 shadow-lg">
            
            <div class="text-center mb-4">
                <h2 class="text-success fw-bold mb-1">Daftar Akun</h2>
                <p class="text-muted small">Gabung bersama TERRALEASE hari ini</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary small">Password</label>
                <input type="password" name="password" class="form-control" placeholder="***" required>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold mb-3">Daftar Sekarang</button>
            
            <p class="text-center mt-2 mb-0 small text-secondary">
                Sudah punya akun? 
                <!-- PERBAIKAN: Hapus 'api/' karena login.php berada di folder yang sama -->
                <a href="login.php" class="text-success fw-bold text-decoration-none">Login di sini</a>
            </p>
            <div class="text-center mt-3">
                <a href="../index.html" class="text-muted small text-decoration-none">← Kembali ke Beranda</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>