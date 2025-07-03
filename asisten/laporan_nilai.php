<?php
session_start();
require_once '../config.php';

// pastikan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada id laporan
if (!isset($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}

$id = intval($_GET['id']);

// ambil data laporan
$stmt = $conn->prepare("SELECT laporan.*, 
        modul.judul AS modul_judul, 
        praktikum.nama AS praktikum_nama,
        users.nama AS mahasiswa_nama
    FROM laporan
    JOIN modul ON laporan.modul_id = modul.id
    JOIN praktikum ON modul.praktikum_id = praktikum.id
    JOIN pendaftaran ON laporan.pendaftaran_id = pendaftaran.id
    JOIN users ON pendaftaran.user_id = users.id
    WHERE laporan.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: laporan.php");
    exit();
}
$laporan = $result->fetch_assoc();
$stmt->close();

// cek nilai sebelumnya
$nilaiData = null;
$stmt = $conn->prepare("SELECT * FROM nilai WHERE laporan_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resNilai = $stmt->get_result();
if ($resNilai->num_rows > 0) {
    $nilaiData = $resNilai->fetch_assoc();
}
$stmt->close();

// proses simpan nilai
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nilai = intval($_POST['nilai']);
    $feedback = trim($_POST['feedback']);

    if ($nilai < 0 || $nilai > 100) {
        die("Nilai harus antara 0-100");
    }

    if ($nilaiData) {
        // update nilai
        $update = $conn->prepare("UPDATE nilai SET nilai=?, feedback=? WHERE laporan_id=?");
        $update->bind_param("isi", $nilai, $feedback, $id);
        $update->execute();
        $update->close();
    } else {
        // insert baru
        $insert = $conn->prepare("INSERT INTO nilai (laporan_id, nilai, feedback) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $id, $nilai, $feedback);
        $insert->execute();
        $insert->close();
    }

    // update status laporan jadi disetujui
    $conn->query("UPDATE laporan SET status='disetujui' WHERE id=$id");

    header("Location: laporan.php?status=scored");
    exit();
}

$pageTitle = "Nilai Laporan";
$activePage = "laporan";
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Nilai Laporan</h1>

    <div class="border p-4 rounded mb-4">
        <p><strong>Mahasiswa:</strong> <?php echo htmlspecialchars($laporan['mahasiswa_nama']); ?></p>
        <p><strong>Praktikum:</strong> <?php echo htmlspecialchars($laporan['praktikum_nama']); ?></p>
        <p><strong>Modul:</strong> <?php echo htmlspecialchars($laporan['modul_judul']); ?></p>
        <p><strong>File Laporan:</strong> 
            <a href="../uploads/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" class="text-blue-600 underline">Download</a>
        </p>
    </div>

    <form action="" method="post" class="space-y-4">
        <div>
            <label class="block mb-1">Nilai (0-100)</label>
            <input type="number" name="nilai" value="<?php echo $nilaiData ? $nilaiData['nilai'] : ''; ?>" class="border rounded p-2 w-full" required>
        </div>
        <div>
            <label class="block mb-1">Feedback</label>
            <textarea name="feedback" class="border rounded p-2 w-full" rows="4"><?php echo $nilaiData ? htmlspecialchars($nilaiData['feedback']) : ''; ?></textarea>
        </div>
        <div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Simpan Nilai</button>
            <a href="laporan.php" class="ml-2 text-gray-600 hover:underline">Kembali</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
