<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - TERRALEASE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { width: 350px; border: none; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="Proses/prosesRegister.php" method="POST" class="card p-4 shadow">
            <h2 class="text-success text-center fw-bold mb-4">Daftar Akun</h2>
            
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="yuli" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="***" required>
            </div>

            <button type="submit" class="btn btn-success w-100 fw-bold mt-2">Daftar Sekarang</button>
            
            <p class="text-center mt-3 small">
                Sudah punya akun? 
                <a href="api/login.php" class="text-success fw-bold text-decoration-none">Login di sini</a>
            </p>
        </form>
    </div>
</body>
</html>