const loanForm = document.getElementById("loanForm");

if (loanForm) {
    loanForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const nama = document.getElementById("nama").value;
        const alat = document.getElementById("alat").value;
        const tanggal = document.getElementById("tanggal").value;
        const pesan = document.getElementById("pesan");

        if (nama && alat && tanggal) {
            pesan.className = "alert alert-success mt-3"; // Menggunakan class Bootstrap
            pesan.textContent = `Terima kasih ${nama}, permohonan peminjaman ${alat} pada ${tanggal} berhasil dikirim!`;
            this.reset();
        } else {
            pesan.className = "alert alert-danger mt-3";
            pesan.textContent = "Mohon lengkapi semua data!";
        }
    });
}

// LOGIN
function login() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    if (!email || !password) {
        alert("Email dan Password harus diisi!");
    } else {
        alert("Login berhasil! Selamat datang di TERRALEASE.");
        window.location.href = "dashboard_user.php";
    }
}

const urlParams = new URLSearchParams(window.location.search);
const idAlat = urlParams.get('alat');
// DATA ALAT
const DATA_ALAT = {
    'traktor': {
        nama: "Traktor Modern",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/896.jpg",
        deskripsi: "Alat mekanis serbaguna yang digunakan untuk mengolah tanah secara efisien sebelum masa tanam.",
        stok: "Tersedia 3 Unit",
        harga: "Rp 500.000"
    },
    'seeder': {
        nama: "Seeder (Mesin Tanam)",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/seeder.jpg",
        deskripsi: "Mesin presisi untuk menanam benih padi dengan jarak yang teratur dan kedalaman yang konsisten.",
        stok: "Tersedia 4 Unit",
        harga: "Rp 700.000"
    },
    'combine': {
        nama: "Combine Harvester",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/Mesin-pertanian-modern.jpg",
        deskripsi: "Mesin pemanen multifungsi yang menggabungkan proses menuai, merontokkan, dan membersihkan biji-bijian.",
        stok: "Tersedia 5 Unit",
        harga: "Rp 700.000"
    },
    'cultivator': {
        nama: "Cultivator",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/Cultivator.jpg",
        deskripsi: "Sangat efektif untuk mengolah lahan kering, menghancurkan gumpalan tanah, dan membasmi gulma.",
        stok: "Tersedia 5 Unit",
        harga: "Rp 300.000"
    },
    'sensor': {
        nama: "Smart Sensor Tanah",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/6062.jpg",
        deskripsi: "Teknologi IoT untuk memantau kelembapan, suhu, dan kadar nutrisi tanah secara real-time.",
        stok: "Tersedia 6 Unit",
        harga: "Rp 800.000"
    },
    'fertilizer': {
        nama: "Fertilizer Spreader",
        gambar: "https://pennyu.co.id/wp-content/uploads/2025/01/Fertilizer-Spreader.jpg",
        deskripsi: "Memastikan penyebaran pupuk kimia maupun organik dilakukan secara merata ke seluruh permukaan lahan.",
        stok: "Tersedia 5 Unit",
        harga: "Rp 600.000"
    }
};

//HALAMAN ALAT
if (idAlat && DATA_ALAT[idAlat]) {
    const item = DATA_ALAT[idAlat];
    
    document.getElementById('namaAlat').innerText = item.nama;
    document.getElementById('gambarAlat').src = item.gambar;
    document.getElementById('deskripsi').innerText = item.deskripsi;
    document.getElementById('stok').innerText = item.stok;
    document.getElementById('harga').innerText = item.harga;
} else {
    // Jika tidak ada data, munculkan peringatan atau teks kosong
    document.getElementById("namaAlat").innerText = "Alat Tidak Ditemukan";
}
