<?php

$host = 'localhost';
$db   = 'cw2_students';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$port = 3307;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

require_once __DIR__ . '/vendor/autoload.php';
require_once '_includes/dbconnect.inc';

$faker = Faker\Factory::create();

try {
    $stmt = $pdo->prepare("INSERT INTO student 
        (studentid, password, dob, firstname, lastname, house, town, county, country, postcode, image) 
        VALUES 
        (:studentid, :password, :dob, :firstname, :lastname, :house, :town, :county, :country, :postcode, :image)");

    for ($i = 0; $i < 5; $i++) {
        $stmt->execute([
            ':studentid' => strtoupper($faker->bothify('??#####')), // e.g. AB12345
            ':password' => password_hash('password123', PASSWORD_DEFAULT),
            ':dob' => $faker->date('Y-m-d', '2005-01-01'),
            ':firstname' => $faker->firstName,
            ':lastname' => $faker->lastName,
            ':house' => $faker->streetAddress,
            ':town' => $faker->city,
            ':county' => $faker->state,
            ':country' => $faker->country,
            ':postcode' => $faker->postcode,
            ':image' => 'default.jpg' // You can use $faker->imageUrl() if needed
        ]);
    }

    echo "✅ 5 student records inserted successfully.";
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}