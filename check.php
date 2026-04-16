<?php
header('Content-Type: application/json');

$host = 'c3322-db';
$dbname = 'db3322';
$username = 'dummy';
$password = 'c3322b';

$db = new mysqli($host, $username, $password, $dbname);

if ($db->connect_error) {
    echo json_encode(['exists' => false, 'error' => 'Database connection failed']);
    exit();
}

$email = $_GET['email'] ?? '';

if ($email === '') {
    echo json_encode(['exists' => false, 'error' => 'No email provided']);
    exit();
}

// Check if email exists in database
$sql = "SELECT * FROM account WHERE useremail = '$email'";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}
?>