<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada id modul
if (!isset($_GET['id'])) {
    header("Location: modul.php");
    exit();
}

$id = intval($_GET['id']);

// ambil data modul
$stmt = $conn->prepare("SELECT * FROM modul WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: modul.php");
    exit();
}
$modul = $result->fetch_assoc();
$stmt->close();

// ambil daftar praktikum untuk dropdown
$praktikumList = $conn->query("SELECT id, nama FROM praktikum");

// proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $praktikum_id = intval($_POST['praktikum_id']);
    $judul = trim($_POST['judul']);

    if (!empty($_FILES["file"]["name"])) {
        $fileName = $_FILES["file"]["name"];
        $fileTmp = $_FILES["file"]["tmp_name"];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ["pdf", "docx"];
        if (!in_array($fileExt, $allowed)) {
            die("File harus PDF atau DOCX");
        }
        $newName = uniqid() . "_" . basename($fileName);
        $target = "../uploads/" . $newName;

        if (move_uploaded_file($fileTmp, $target)) {
            $update = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=?, file_materi=? WHERE id=?");
            $update->bind_param("issi", $praktikum_id, $judul, $newName, $id);
            $update->execute();
            $update->close();
            header("Location: modul.php?status=updated");
            exit();
        } else {
            echo "Gagal upload file.";
        }
    } else {
        $update = $conn->prepare("UPDATE modul SET praktikum_id=?, judul=? WHERE id=?");
        $update->bind_param("isi", $praktikum_id, $judul, $id);
        $update->execute();
        $update->close();
        header("Location: modul.php?status=updated");
        exit();
    }
}

$pageTitle = "Edit Modul";
$activePage = "modul";
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit Modul</h1>

    <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block mb-1">Pilih Praktikum</label>
            <select name="praktikum_id" class="border rounded p-2 w-full" required>
                <?php while($row = $praktikumList->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $modul['praktikum_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block mb-1">Judul Modul</label>
            <input type="text" name="judul" value="<?php echo htmlspecialchars($modul['judul']); ?>" class="border rounded p-2 w-full" required>
        </div>
        <div>
            <label class="block mb-1">Ganti File Materi (opsional)</label>
            <input type="file" name="file" class="border p-2 w-full">
            <p class="text-gray-500 text-sm mt-1">Kosongkan jika tidak ingin mengganti file.</p>
        </div>
        <div>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
            <a href="modul.php" class="ml-2 text-gray-600 hover:underline">Kembali</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
