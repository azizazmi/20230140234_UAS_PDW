<?php
session_start();
require_once '../config.php';

// pastikan mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// pastikan ada parameter id praktikum
if (!isset($_GET['id'])) {
    header("Location: my_courses.php");
    exit();
}

$praktikum_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// cek apakah mahasiswa memang sudah terdaftar
$cek = $conn->prepare("SELECT id FROM pendaftaran WHERE praktikum_id = ? AND user_id = ?");
$cek->bind_param("ii", $praktikum_id, $user_id);
$cek->execute();
$cek->store_result();
if ($cek->num_rows == 0) {
    header("Location: my_courses.php?status=unauthorized");
    exit();
}
$cek->close();

// ambil nama praktikum
$praktikum = $conn->prepare("SELECT nama FROM praktikum WHERE id = ?");
$praktikum->bind_param("i", $praktikum_id);
$praktikum->execute();
$praktikum->bind_result($nama_praktikum);
$praktikum->fetch();
$praktikum->close();

// ambil modul
$modul = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ?");
$modul->bind_param("i", $praktikum_id);
$modul->execute();
$result_modul = $modul->get_result();

$pageTitle = "Detail Praktikum";
$activePage = "my_courses";

require_once 'templates/header_mahasiswa.php';
?>

<div class="bg-white p-6 rounded-xl shadow-lg mb-6">
    <h1 class="text-2xl font-bold mb-4">Detail Praktikum: <?php echo htmlspecialchars($nama_praktikum); ?></h1>

    <?php
    if ($result_modul->num_rows > 0) {
        while ($row = $result_modul->fetch_assoc()) {
            // cek jika mahasiswa sudah upload laporan
            $cekLaporan = $conn->prepare("SELECT id, file_laporan FROM laporan WHERE pendaftaran_id = (
                SELECT id FROM pendaftaran WHERE user_id = ? AND praktikum_id = ?
            ) AND modul_id = ?");
            $cekLaporan->bind_param("iii", $user_id, $praktikum_id, $row['id']);
            $cekLaporan->execute();
            $resLap = $cekLaporan->get_result();
            $laporanData = $resLap->fetch_assoc();
            $cekLaporan->close();

            // ambil nilai jika sudah dinilai
            $nilai = null;
            $feedback = null;
            if ($laporanData) {
                $id_laporan = $laporanData['id'];
                $nilaiQ = $conn->prepare("SELECT nilai, feedback FROM nilai WHERE laporan_id = ?");
                $nilaiQ->bind_param("i", $id_laporan);
                $nilaiQ->execute();
                $nilaiQ->bind_result($nilai, $feedback);
                $nilaiQ->fetch();
                $nilaiQ->close();
            }
    ?>
    <div class="border rounded p-4 mb-4">
        <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($row['judul']); ?></h2>
        <a href="../uploads/<?php echo htmlspecialchars($row['file_materi']); ?>" class="text-blue-500 underline mb-2 inline-block">Download Materi</a>

        <?php if ($laporanData): ?>
            <p class="text-green-600 mt-2">Laporan sudah dikumpulkan:
                <a href="../uploads/<?php echo htmlspecialchars($laporanData['file_laporan']); ?>" class="underline">Lihat File</a>
            </p>
            <?php if ($nilai !== null): ?>
                <div class="mt-2 bg-green-100 p-2 rounded">
                    <strong>Nilai:</strong> <?php echo $nilai; ?><br>
                    <strong>Feedback:</strong> <?php echo htmlspecialchars($feedback); ?>
                </div>
            <?php else: ?>
                <div class="mt-2 bg-yellow-100 p-2 rounded">
                    Menunggu penilaian dari asisten.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <form class="mt-4" action="upload_laporan.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="modul_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="praktikum_id" value="<?php echo $praktikum_id; ?>">
                <label class="block mb-2">Upload Laporan (PDF/DOCX):</label>
                <input type="file" name="file" required class="border p-2 rounded mb-2 w-full">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Kirim</button>
            </form>
        <?php endif; ?>
    </div>
    <?php
        }
    } else {
        echo "<p class='text-gray-500'>Belum ada modul pada praktikum ini.</p>";
    }
    ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>
