<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// ambil daftar praktikum untuk dropdown
$praktikumList = $conn->query("SELECT id, nama FROM praktikum");

$pageTitle = "Tambah Modul";
$activePage = "modul";

require_once 'templates/header.php';

// proses tambah
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $praktikum_id = intval($_POST['praktikum_id']);
    $judul = trim($_POST['judul']);

    // file materi
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
        $stmt = $conn->prepare("INSERT INTO modul (praktikum_id, judul, file_materi) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $praktikum_id, $judul, $newName);
        if ($stmt->execute()) {
            header("Location: modul.php?status=added");
            exit();
        } else {
            echo "Gagal simpan ke database.";
        }
        $stmt->close();
    } else {
        echo "Gagal upload file.";
    }
}

?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Tambah Modul</h1>

    <form action="" method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block mb-1">Pilih Praktikum</label>
            <select name="praktikum_id" class="border rounded p-2 w-full" required>
                <?php while($row = $praktikumList->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block mb-1">Judul Modul</label>
            <input type="text" name="judul" class="border rounded p-2 w-full" required>
        </div>
        <div>
            <label class="block mb-1">File Materi (PDF/DOCX)</label>
            <input type="file" name="file" required class="border p-2 w-full">
        </div>
        <div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan</button>
            <a href="modul.php" class="ml-2 text-gray-600 hover:underline">Kembali</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
