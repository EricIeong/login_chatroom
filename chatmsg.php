<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
header('Content-Type: application/json');
$host = 'c3322-db';
$dbname = 'db3322';
$username = 'dummy';
$password = 'c3322b';
// Create connection
$db = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}
// Get last message time
$stmt = $db->prepare("SELECT MAX(time) as last_time FROM message WHERE person = ?");
$person = explode('@', $_SESSION['user'])[0];
$stmt->bind_param("s", $person);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Use last message time, or session start time if no messages
if ($row && $row['last_time']) {
    $lastActivity = max($row['last_time'], $_SESSION['login_time'] ?? 0);
} else {
    // No messages sent yet - use session start time
    $lastActivity = $_SESSION['login_time'] ?? time();
}

if (time() - $lastActivity > 120) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['error' => 'Session expired']);
    exit();
}
// Handle POST request (send message)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? '';
    $time = time();
    $person = explode('@', $_SESSION['user'])[0];

    $stmt = $db->prepare("INSERT INTO message (time, message, person) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $time, $message, $person);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $db->error]);
    }
    exit();
}

// Handle GET request (fetch messages)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Delete messages older than 1 hour
    $db->query("DELETE FROM message WHERE time < " . (time() - 3600));
    // Fetch messages from the last hour, ordered by time, should be redundant where statement
    $result = $db->query("SELECT * FROM message WHERE time > UNIX_TIMESTAMP() - 3600 ORDER BY time ASC");
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);


    exit();
}
?>