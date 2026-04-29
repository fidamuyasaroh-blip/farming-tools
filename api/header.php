<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="api/login.php">TERRALEASE</a>
        
        <div class="collapse navbar-collapse show" id="navbarNav">
            <ul class="navbar-nav ms-auto d-flex flex-row">
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold px-3" href="api/login.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold px-3" href="api/daftar_alat.php">Katalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-bold px-3" href="api/Proses/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Tambahan agar bullet points (titik hitam) benar-benar hilang */
    ul.navbar-nav {
        list-style-type: none !important;
    }
</style>