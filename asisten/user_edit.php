<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// cek id user
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = intval($_GET['id']);

// ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    header("Location: users.php");
    exit();
}
$user = $res->fetch_assoc();
$stmt->close();

$pageTitle = "Edit Pengguna";
$activePage = "users";
$message = "";

// proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    if (empty($nama) || empty($email) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } elseif (!in_array($role, ['mahasiswa', 'asisten'])) {
        $message = "Peran tidak valid!";
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $email, $role, $id);
        if ($stmt->execute()) {
            header("Location: users.php?status=updated");
            exit();
        } else {
            $message = "Gagal update user.";
        }
        $stmt->close();
    }
}

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit Pengguna</h1>

    <?php if (!empty($message)) : ?>
        <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <form action="" method="post" class="space-y-4">
        <div>
            <label class="block mb-1">Nama Lengkap</label>
            <input type="text" name="nama" class="border p-2 rounded w-full" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
        </div>
        <div>
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="border p-2 rounded w-full" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div>
            <label class="block mb-1">Role</label>
            <select name="role" class="border p-2 rounded w-full" required>
                <option value="mahasiswa" <?php if ($user['role']=='mahasiswa') echo 'selected'; ?>>Mahasiswa</option>
                <option value="asisten" <?php if ($user['role']=='asisten') echo 'selected'; ?>>Asisten</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
            <a href="users.php" class="ml-2 text-gray-600 hover:underline">Kembali</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
