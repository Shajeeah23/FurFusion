<?php
session_start();

/* ---------- DATABASE CONNECTION ---------- */
$conn = mysqli_connect("localhost", "root", "", "furfusion");

if (!$conn) {
    die("Database connection failed");
}

/* ---------- REGISTRATION ---------- */
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        $message = "Username already exists!";
    } else {
        mysqli_query($conn,
            "INSERT INTO users (username, password) VALUES ('$username', '$password')"
        );
        $message = "Registration successful. Please login.";
    }
}

/* ---------- LOGIN ---------- */
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = $username;
    } else {
        $message = "Invalid login credentials";
    }
}

/* ---------- LOGOUT ---------- */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: furfusion.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FurFusion – CSE370 Project</title>
    <style>
        body { font-family: Arial; background:#f4f6f8; padding:20px; }
        .box { background:white; padding:20px; margin-bottom:15px; border-radius:5px; }
        input { padding:8px; width:100%; margin:5px 0; }
        button { padding:8px 15px; background:#4f46e5; color:white; border:none; }
        .logout { background:#dc2626; }
    </style>
</head>
<body>

<h2>FurFusion – Cat Adoption System</h2>

<?php if (!isset($_SESSION['user'])) { ?>

<!-- ---------- REGISTRATION ---------- -->
<div class="box">
    <h3>First Time User – Register</h3>
    <form method="post">
        <input type="text" name="username" placeholder="Create Username" required>
        <input type="password" name="password" placeholder="Create Password" required>
        <button type="submit" name="register">Register</button>
    </form>
</div>

<!-- ---------- LOGIN ---------- -->
<div class="box">
    <h3>Login</h3>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<p style="color:red;">
    <?php if (isset($message)) echo $message; ?>
</p>

<?php } else { ?>

<!-- ---------- DASHBOARD ---------- -->
<div class="box">
    <p>Welcome, <b><?php echo $_SESSION['user']; ?></b></p>
    <a href="?logout=true">
        <button class="logout">Logout</button>
    </a>
</div>

<!-- ---------- FEATURE: VIEW CATS ---------- -->
<div class="box">
    <h3>Cat List</h3>

    <?php
    $cats = mysqli_query($conn, "SELECT * FROM cats");

    while ($cat = mysqli_fetch_assoc($cats)) {
        echo "<p>
            <b>{$cat['name']}</b><br>
            Breed: {$cat['breed']}<br>
            Age: {$cat['age']}<br>
            Gender: {$cat['gender']}<br>
            Status: {$cat['status']}
        </p><hr>";
    }
    ?>
</div>

<?php } ?>

</body>
</html>
