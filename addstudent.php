<?php

$host = 'localhost';
$db   = 'cw2_students';  // Change if you used a different DB name
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';
$port = 3307;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;port=$port;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}

// Initialize error messages
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $studentid = $_POST['studentid'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $house = $_POST['house'] ?? '';
    $town = $_POST['town'] ?? '';
    $county = $_POST['county'] ?? '';
    $country = $_POST['country'] ?? '';
    $postcode = $_POST['postcode'] ?? '';

    // Validate student ID uniqueness
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM student WHERE studentid = ?");
    $stmt->execute([$studentid]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "❌ Student ID already exists. Please use a different one.";
    }

    // Password validation: Minimum 1 uppercase, 1 number
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{6,}$/', $password)) {
        $errors[] = "❌ Password must be at least 6 characters, with 1 uppercase letter and 1 number.";
    }

    // Check password confirmation
    if ($password !== $confirm_password) {
        $errors[] = "❌ Passwords do not match.";
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // File upload handling
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate file type (only allow images)
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "❌ Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imagePath = $targetFilePath;
            } else {
                $errors[] = "❌ Error uploading the image.";
            }
        }
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO student (studentid, password, dob, firstname, lastname, house, town, county, country, postcode, image)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$studentid, $hashed_password, $dob, $firstname, $lastname, $house, $town, $county, $country, $postcode, $imagePath])) {
            $success = "✅ Student added successfully!";
        } else {
            $errors[] = "❌ Failed to add student.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<style>
    body {
        padding-bottom: 80px; /* Pushes content up from bottom */
    }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Add a New Student</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) { echo "<p>$error</p>"; } ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Student ID:</label>
            <input type="text" name="studentid" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password:</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Date of Birth:</label>
            <input type="date" name="dob" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">First Name:</label>
            <input type="text" name="firstname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name:</label>
            <input type="text" name="lastname" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">House:</label>
            <input type="text" name="house" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Town:</label>
            <input type="text" name="town" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">County:</label>
            <input type="text" name="county" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Country:</label>
            <input type="text" name="country" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Postcode:</label>
            <input type="text" name="postcode" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Image:</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>
</div>
</body>
</html>