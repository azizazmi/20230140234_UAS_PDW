<?php
session_start();
require_once '../config.php';

// pastikan user asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada id praktikum
if (!isset($_GET['id'])) {
    header("Location: praktikum.php");
    exit();
}

$id = intval($_GET['id']);

// ambil data praktikum
$stmt = $conn->prepare("SELECT * FROM praktikum WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: praktikum.php");
    exit();
}
$praktikum = $result->fetch_assoc();
$stmt->close();

// proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $error = "Nama praktikum tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("UPDATE praktikum SET nama = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama, $deskripsi, $id);
        if ($stmt->execute()) {
            header("Location: praktikum.php?status=updated");
            exit();
        } else {
            $error = "Gagal mengupdate praktikum.";
        }
        $stmt->close();
    }
}

$pageTitle = "Edit Mata Praktikum";
$activePage = "praktikum";
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit Mata Praktikum</h1>

    <?php if (!empty($error)) { echo "<div class='text-red-600 mb-4'>$error</div>"; } ?>

    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1">Nama Praktikum</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($praktikum['nama']); ?>" class="border rounded p-2 w-full" required>
        </div>
        <div>
            <label class="block mb-1">Deskripsi</label>
            <textarea name="deskripsi" class="border rounded p-2 w-full" rows="4"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></textarea>
        </div>
        <div>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
            <a href="praktikum.php" class="ml-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
