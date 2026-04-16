<?php
session_start();

// Check if logged in
if (!isset($_SESSION["user"])) {
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Chat Room</title>
    <link rel="stylesheet" type="text/css" href="chat.css">
</head>

<body>
    <h1>3322 Chatroom</h1>
    <div class="chat-container">
        <div class="user-info">
            Logged in as: <?php echo htmlspecialchars(explode('@', $_SESSION['user'])[0]); ?>
        </div>
        <a href="login.php?action=signout"><button>LogOut</button></a>
        <div id="chat-box" class="chat-body">
            <!-- Messages will appear here -->
        </div>
        <div class="chat-footer">
            <input type="hidden" id="currentUser" value="<?php echo htmlspecialchars($_SESSION['user']); ?>">
            <input type="hidden" id="username"
                value="<?php echo htmlspecialchars(explode('@', $_SESSION['user'])[0]); ?>">
            <input type="text" id="user-input" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
</body>
<script src="chat.js"></script>

</html>