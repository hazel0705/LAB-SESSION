<?php
session_start();
require_once "db.php";

// Redirect if already authenticated
if (isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: " . ($_SESSION["role"] === "admin" ? "home2.php" : "home.php"));
    exit;
}

$error   = "";
$success = "";
$page    = $_GET["page"] ?? "login";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    // ----------------------------------------
    // REGISTRATION
    // ----------------------------------------
    if ($action === "register") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm  = $_POST["confirm_password"] ?? "";
        $page     = "register";

        if ($username === "" || $password === "" || $confirm === "") {
            $error = "All fields are required.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            $error = "Username must be 3–50 characters (letters, numbers, underscores).";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Username is already taken.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                $stmt->bind_param("ss", $username, $hashedPassword);

                if ($stmt->execute()) {
                    $success = "Account created. You can now log in.";
                    $page    = "login";
                } else {
                    $error = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
            $check->close();
        }

    // ----------------------------------------
    // LOGIN
    // ----------------------------------------
    } elseif ($action === "login") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $page     = "login";

        if ($username === "" || $password === "") {
            $error = "Please fill in all fields.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"]     = $user["role"];

                header("Location: " . ($user["role"] === "admin" ? "home2.php" : "home.php"));
                exit;
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page === 'register' ? 'Register' : 'Sign In' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-4">

<div class="container" style="max-width: 380px;">
    <div class="card border rounded-3 bg-white p-4">
        
        <div class="mb-4">
            <h1 class="h5 fw-bold mb-1"><?= $page === 'register' ? 'Create an account' : 'Sign in' ?></h1>
            <p class="text-secondary small mb-0">
                <?= $page === 'register' ? 'Enter your details to get started.' : 'Enter your credentials to access your account.' ?>
            </p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div>
        <?php endif; ?>

        <?php if ($success !== ""): ?>
            <div class="alert alert-success py-2 small mb-3"><?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?></div>
        <?php endif; ?>

        <?php if ($page === "register"): ?>
            <form method="POST" action="index.php?page=register">
                <input type="hidden" name="action" value="register">

                <div class="mb-3">
                    <label class="form-label small fw-medium">Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" minlength="3" maxlength="50" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium">Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" minlength="8" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-sm" minlength="8" required>
                </div>

                <button type="submit" class="btn btn-dark btn-sm w-100 py-2 mt-2">Register</button>
            </form>

            <div class="text-center mt-3 pt-3 border-top small text-secondary">
                Already registered? <a href="index.php" class="text-dark fw-medium text-decoration-none">Sign in</a>
            </div>

        <?php else: ?>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="login">

                <div class="mb-3">
                    <label class="form-label small fw-medium">Username</label>
                    <input type="text" name="username" class="form-control form-control-sm" maxlength="50" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium">Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" required>
                </div>

                <button type="submit" class="btn btn-dark btn-sm w-100 py-2 mt-2">Sign in</button>
            </form>

            <div class="text-center mt-3 pt-3 border-top small text-secondary">
                Don't have an account? <a href="index.php?page=register" class="text-dark fw-medium text-decoration-none">Register</a>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>