<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Kelola Pengguna";
$activePage = "users";

// ambil semua user
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Manajemen Akun Pengguna</h1>
    <a href="user_tambah.php" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block hover:bg-green-600">+ Tambah Pengguna</a>

    <table class="w-full border mt-4 text-sm">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Role</th>
                <th class="p-2 border">Dibuat</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td class="p-2 border"><?php echo htmlspecialchars($row['nama']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['email']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['role']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td class="p-2 border">
                    <a href="user_edit.php?id=<?php echo $row['id']; ?>" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Edit</a>
                    <a href="user_hapus.php?id=<?php echo $row['id']; ?>" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php
                endwhile;
            else:
                echo "<tr><td colspan='5' class='text-center p-4'>Belum ada pengguna.</td></tr>";
            endif;
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>
