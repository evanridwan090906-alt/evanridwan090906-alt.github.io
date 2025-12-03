<?php
// Mulai sesi
session_start();

// Sertakan file koneksi database
include('config/conn.php');

// --- Logika Penentuan Base URL ---
// Tentukan protokol (http atau https)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";

// Ambil host (domain/IP)
$host = $_SERVER['HTTP_HOST'];

// Ambil path direktori saat ini
$path = str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

// Gabungkan untuk mendapatkan base URL
$base_url = "{$protocol}://{$host}{$path}";
// --- Akhir Logika Penentuan Base URL ---

// --- Logika Login ---
if (isset($_POST['cek_login'])) {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $error = ''; // Inisialisasi variabel error

    if (empty($username) || empty($password)) {
        // Cek jika salah satu atau keduanya kosong
        $error = 'Harap isi username dan password!';
    } else {
        // --- PENTING: Gunakan Prepared Statement untuk mencegah SQL Injection ---
        // Query menggunakan placeholder (?) untuk nilai yang dimasukkan user
        $query = "SELECT id_user, username, nama_user, role_user FROM tb_user WHERE username = ? AND password = ?";

        // Siapkan statement
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            // Bind parameter ke placeholder: 'ss' berarti 2 string
            mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

            // Eksekusi statement
            mysqli_stmt_execute($stmt);

            // Ambil hasil
            $result = mysqli_stmt_get_result($stmt);

            // Cek jumlah baris yang ditemukan
            if (mysqli_num_rows($result) === 1) {
                // Login berhasil
                $data = mysqli_fetch_assoc($result);

                // Set session
                $_SESSION['id'] = $data['id_user'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['name'] = $data['nama_user'];
                $_SESSION['role'] = $data['role_user'];

                // Alihkan ke halaman utama
                header("Location: " . $base_url);
                exit; // Penting untuk menghentikan eksekusi kode setelah header()
            } else {
                // Username atau password salah
                $error = 'Username atau password salah!';
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        } else {
            // Penanganan jika prepare statement gagal (misal: kesalahan sintaks SQL)
            // Dalam lingkungan produksi, sebaiknya gunakan log error, bukan menampilkan error langsung ke user.
            $error = 'Terjadi kesalahan sistem saat memproses login. Silakan coba lagi.';
        }
    }

    // Set pesan error ke session jika ada
    if (!empty($error)) {
        $_SESSION['error'] = $error;
        // Alihkan kembali ke halaman ini agar pesan error muncul saat refresh
        // Jika kode ini disimpan sebagai index.php, pengalihan ini akan me-refresh halaman login.
        // Jika perlu, tambahkan logika pengalihan kembali ke halaman login.
    }
}
// --- Akhir Logika Login ---

// Pastikan koneksi ditutup di akhir script jika belum ditutup di 'config/conn.php'
// if (isset($con)) {
//     mysqli_close($con);
// }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Manajemen Inventory">
    <meta name="author" content="Maknum Munib">

    <title>SISTEM MANAJEMEN INVENTORY</title>

    <link href="<?= $base_url; ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="<?= $base_url; ?>assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Gaya tambahan untuk penempatan form di tengah */
        body {
            background-color: #f8f9fc; /* Warna latar belakang body, sesuai SB Admin 2 */
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px; /* Batasi lebar maksimal card */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 0.35rem; /* Sesuai SB Admin 2 */
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="card login-card p-5">
            <div class="text-center">
                <i class="fa fa-book fa-4x text-primary mb-3"></i>
                <h4 class="h5 text-gray-900 mb-4">Selamat Datang di **SISTEM MANAJEMEN INVENTORY**</h4>
            </div>

            <form method="post" action="" class="user">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="flash-data-berhasil" data-berhasil="<?= $_SESSION['success']; ?>"></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="flash-data-gagal" data-gagal="<?= $_SESSION['error']; ?>"></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="form-group">
                    <input required name="username" id="username" class="form-control form-control-user" type="text" placeholder="Masukkan Username..." autocomplete="off" />
                </div>
                
                <div class="form-group">
                    <input required name="password" id="password" class="form-control form-control-user" type="password" placeholder="Password" autocomplete="off" />
                </div>
                
                <button type="submit" class="btn btn-primary btn-user btn-block mt-4" name="cek_login">
                    **Login**
                </button>
            </form>
            
            <hr>

            <footer class="text-center mt-3 small text-muted">
                Dibuat oleh <a href="#" title="T.P">Kelompok</a>
            </footer>
        </div>
    </div>

    <script src="<?= $base_url; ?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?= $base_url; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="<?= $base_url; ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= $base_url; ?>assets/vendor/sweet-alert/sweetalert2.all.min.js"></script>

    <script src="<?= $base_url; ?>assets/js/sb-admin-2.min.js"></script>
    <script src="<?= $base_url; ?>assets/js/demo/sweet-alert.js"></script>
</body>
</html>