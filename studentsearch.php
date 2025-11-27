<?php
$conn = new mysqli("localhost", "root", "", "cw2_students", 3307);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM student WHERE studentid = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "✅ Found: " . $row['firstname'];
        }
    } else {
        echo "❌ No match.";
    }
}
?>
<form method="GET">
    Enter Student ID: <input type="text" name="id">
    <input type="submit" value="Search">
</form>