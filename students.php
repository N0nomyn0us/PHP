<?php
$host = 'localhost';
$db   = 'cw2_students';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$port = 3307;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;port=$port;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected'])) {
    $selectedIds = $_POST['selected'];
    $in = str_repeat('?,', count($selectedIds) - 1) . '?';
    $stmt = $pdo->prepare("DELETE FROM student WHERE studentid IN ($in)");
    $stmt->execute($selectedIds);
}

// Fetch students
$stmt = $pdo->query("SELECT * FROM student");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        img.thumb {
            max-height: 100px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Student Records</h2>

        <form method="POST">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="selected[]" value="<?= htmlspecialchars($row['studentid']) ?>"></td>
                            <td><?= htmlspecialchars($row['studentid']) ?></td>
                            <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                            <td><?= htmlspecialchars($row['dob']) ?></td>
                            <td>
                                <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                                    <img src="<?= htmlspecialchars($row['image']) ?>" class="thumb" alt="Student Image">
                                <?php else: ?>
                                    No image
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-danger">Delete Selected</button>
        </form>
    </div>

    <script>
        // Select/Deselect all checkboxes
        document.getElementById('select-all').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[name="selected[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>