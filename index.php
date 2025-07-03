<?php
session_start();
require_once 'config.php';

// cek kalau asisten login, langsung redirect dashboard asisten
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'asisten') {
    header("Location: asisten/dashboard.php");
    exit();
}

// ambil daftar praktikum
$result = $conn->query("SELECT * FROM praktikum");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-xl font-bold">SIMPRAK</div>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="px-4 py-2 bg-white text-blue-600 rounded hover:bg-gray-200">Login</a>
                    <a href="register.php" class="ml-2 px-4 py-2 bg-green-500 rounded hover:bg-green-600">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Daftar Mata Praktikum</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
            ?>
            <div class="bg-white p-4 rounded shadow hover:shadow-lg transition">
                <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($row['nama']); ?></h2>
                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                <?php
                    if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'mahasiswa') {
                        // mahasiswa login, bisa daftar langsung
                        echo '<a href="mahasiswa/daftar_praktikum.php?id='.$row['id'].'" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Daftar</a>';
                    } else {
                        // publik atau belum login
                        echo '<a href="login.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Login untuk Daftar</a>';
                    }
                ?>
            </div>
            <?php
                endwhile;
            else:
                echo "<p>Tidak ada praktikum tersedia.</p>";
            endif;
            ?>
        </div>
    </div>

</body>
</html>
