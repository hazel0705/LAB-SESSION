<?php
session_start();

if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] !== "user") {
    header("Location: home2.php");
    exit;
}

if (isset($_GET["logout"])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100">


<div class="container py-5" style="max-width: 600px;margin-top:200px;">
    <div class="card border rounded-3 bg-white p-4">
        <h2 class="h5 fw-bold mb-1">Welcome, <?= $username ?>!</h2>
        <p class="text-secondary small mb-4">You are logged in as a regular user.</p>

        <a href="home.php?logout=1" class="btn btn-outline-danger btn-sm align-self-start">Sign out</a>
    </div>
</div>

</body>
</html>