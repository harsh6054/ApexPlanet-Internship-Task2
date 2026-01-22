<?php
$conn = new mysqli("localhost", "root", "", "blog");
if ($conn->connect_error) {
    die("Database Connection Failed");
}

session_start();

$register_error = "";
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 3 || strlen($password) < 3) {
        $register_error = "Username & Password must be at least 3 characters!";
    } else {
        $check = $conn->query("SELECT id FROM users WHERE username='$username'");
        if ($check->num_rows > 0) {
            $register_error = "Username already exists!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (username, password) VALUES ('$username','$passwordHash')");
            $register_error = "Registration successful! Please login.";

            // Automatically switch to login form
            echo "<script>window.onload = function(){ showLogin(); }</script>";
        }
    }
}

$login_error = "";
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];

        header("Location: post.php");
        exit();
    } else {
        $login_error = "Invalid username or password!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Authentication</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php if (!isset($_SESSION['user'])) { ?>

<div class="container">

    <div id="loginForm">
        <h2>Login</h2>

        <?php if ($login_error) echo "<p class='error'>$login_error</p>"; ?>

        <form method="post">
            <div class="input-group">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <button name="login">Login</button>
        </form>

        <div class="switch">
            Not registered? <a onclick="showRegister()">Create account</a>
        </div>
    </div>

    <div id="registerForm">
        <h2>Register</h2>

        <?php if ($register_error) echo "<p class='error'>$register_error</p>"; ?>

        <form method="post">
            <div class="input-group">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>

            <button name="register">Register</button>
        </form>

        <div class="switch">
            Already have an account? <a onclick="showLogin()">Login</a>
        </div>
    </div>

</div>

<?php } else { ?>

<div class="container">
    <div class="card">
        <h2>Welcome, <?php echo $_SESSION['user']; ?></h2>
        <a href="?logout=1">Logout</a>
    </div>
</div>

<?php } ?>

<script>
function showRegister() {
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("registerForm").style.display = "block";
}

function showLogin() {
    document.getElementById("registerForm").style.display = "none";
    document.getElementById("loginForm").style.display = "block";
}
</script>

</body>
</html>
