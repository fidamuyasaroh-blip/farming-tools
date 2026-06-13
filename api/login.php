<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            /* PERBAIKAN: Mengganti teks placeholder dengan gambar sawah asli */
            background-image: url('https://images.unsplash.com/photo-1516253593875-bd7ba052fbc5?q=80&w=1920'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .login-container {
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
            /* Efek kaca transparan (Glassmorphism) agar gambar latar belakang sedikit tembus */
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
        }
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
        }
    </style>
</head>
<body>

    <div class="login-container px-3">
        <form action="Proses/prosesLogin.php" method="POST" class="card p-4 p-md-5 shadow-lg">
            
            <div class="text-center mb-4">
                <h2 class="text-success fw-bold mb-1">TERRALEASE</h2>
                <p class="text-muted small">Silakan login untuk melanjutkan</p>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Username</label>
                <input type="text" name="username" class="form-control form-control-lg" placeholder="Masukkan username" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary small">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="***" required>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold mb-3">Masuk</button>
            
            <p class="text-center mt-2 mb-0 small text-secondary">
                Belum punya akun? 
                <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar di sini</a>
            </p>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-muted small text-decoration-none">← Kembali ke Beranda</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>