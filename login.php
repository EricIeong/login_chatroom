<?php
session_start();
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

// Set charset to UTF-8
$db->set_charset("utf8");

$register_error = '';
$show_register = false;

if (isset($_GET['action']) && $_GET['action'] == 'signout') {#GET /login.php?action=signout
    # logout
    session_destroy();
    header('Location: login.php');
    exit();
} else if (isset($_SESSION["user"])) {
    # check if session is valid and show chat room
    header('Location: chat.php');
    exit();
} else if (isset($_POST["type"]) && $_POST["type"] == 'login') { # POST /login.php
    # check if login info is correct in check.php and if correct, set session and show chat room
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM account WHERE useremail = '$email' AND password = '$password'";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION["user"] = $email;
        $_SESSION['login_time'] = time();
        header('Location: chat.php');
        exit();
    } else {
        $login_error = "Invalid email or password";
    }
} else if (isset($_POST['type']) && $_POST['type'] == 'register') { # POST /login.php with type=register
    # handle registration
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    if (!str_ends_with($email, '@connect.hku.hk')) {
        # show error message
        $register_error = "Email must end with @connect.hku.hk";
        $show_register = true;
    } else if ($confirm != $password) {
        # show error message
        $register_error = "Passwords do not match";
        $show_register = true;
    } else {
        // Check if email already exists in database
        $check_sql = "SELECT * FROM account WHERE useremail = '$email'";
        $result = $db->query($check_sql);
        if ($result->num_rows > 0) {
            # show error message
            $register_error = "Email already registered";
            $show_register = true;
        } else {
            # check if email already exists in database, if not, insert new user into database and show login form
            $sql = "INSERT INTO account (id, useremail, password) VALUES (NULL, '$email', '$password')";
            if ($db->query($sql) === TRUE) { #for testing delete later
                $show_register = false;
            }
        }
    }
}
?>
<?php if (isset($_GET['timeout'])): ?>
    <div style="color: orange; margin-bottom: 10px;">
        You were logged out due to 2 minutes of inactivity.
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3322 Chatroom - Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <h1>3322 Chatroom</h1>
    <!-- Login Form -->
    <div id="loginForm" style="<?php echo empty($register_error) ? '' : 'display: none;'; ?>">
        <h2>Login to Chatroom</h2>
        <form method="POST">
            <input type="hidden" name="type" value="login">
            <!-- login fields here -->
            <label for="loginEmail">Email:</label><br>
            <input type="email" id="loginEmail" name="email" required><br>
            <label for="loginPassword">Password:</label><br>
            <input type="password" id="loginPassword" name="password" required><br><br>
            <input type="submit" value="login">
            <div id="loginError" class="error-message" style="color: red; display: none;"></div>
        </form>
        <?php if (!empty($login_error)): ?>
            <div style="color: red; margin-bottom: 10px;">
                <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>
        <span>Click <a href="#" id="showRegisterLink">here</a> to register an account</span>
    </div>

    <!-- Registration Form -->
    <div id="registerForm" style="<?php echo $show_register ? '' : 'display: none;'; ?>">
        <h2>Register an Account</h2>
        <?php if (!empty($register_error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                <?php echo htmlspecialchars($register_error); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="type" value="register">
            <!-- registration fields here -->
            <label for="regEmail">Email:</label><br>
            <input type="email" id="regEmail" name="email" value=""><br>
            <label for="regPassword">Password:</label><br>
            <input type="password" id="regPassword" name="password" value=""><br>
            <label for="regConfirm">Confirm:</label><br>
            <input type="password" id="regConfirm" name="confirm" value=""><br><br>
            <input type="submit" value="Register">
            <div id="registerError" class="error-message" style="color: red; display: none;"></div>
        </form>
        <span>Click <a href="#" id="showLoginLink">here</a> to login</span>
    </div>
    <script src="login.js"></script>
</body>

</html>