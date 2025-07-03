<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Tambah Pengguna";
$activePage = "users";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // validasi sederhana
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } elseif (!in_array($role, ['mahasiswa', 'asisten'])) {
        $message = "Peran tidak valid!";
    } else {
        // cek email sudah terdaftar
        $cek = $conn->prepare("SELECT id FROM users WHERE email=?");
        $cek->bind_param("s", $email);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $message = "Email sudah terdaftar!";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $nama, $email, $hash, $role);
            if ($stmt->execute()) {
                header("Location: users.php?status=added");
                exit();
            } else {
                $message = "Gagal menambahkan user.";
            }
            $stmt->close();
        }
        $cek->close();
    }
}

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Tambah Pengguna</h1>

    <?php if (!empty($message)) : ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form action="" method="post" class="space-y-4">
        <div>
            <label class="block mb-1">Nama Lengkap</label>
            <input type="text" name="nama" class="border p-2 rounded w-full" required>
        </div>
        <div>
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="border p-2 rounded w-full" required>
        </div>
        <div>
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="border p-2 rounded w-full" required>
        </div>
        <div>
            <label class="block mb-1">Role</label>
            <select name="role" class="border p-2 rounded w-full" required>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="asisten">Asisten</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan</button>
            <a href="users.php" class="ml-2 text-gray-600 hover:underline">Kembali</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
