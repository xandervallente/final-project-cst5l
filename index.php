<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Already logged in, go straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /finalProj/views/dashboard.php");
    exit();
}

require_once __DIR__ . '/models/account.php';
require_once __DIR__ . '/controllers/account.php';
require_once __DIR__ . '/public/database.config.php';

$errors  = [];
$success = "";

$form = [
    "username" => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {

    // Sanitize inputs
    $form["username"] = trim($_POST["username"] ?? "");
    $password         = $_POST["password"] ?? "";

    // Validate: empty fields
    if (empty($form["username"])) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Only attempt login if no validation errors
    if (empty($errors)) {
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        if ($controller->login($form["username"], $password)) {
            header("Location: /finalProj/views/dashboard.php");
            exit();
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>

<?php require __DIR__ . '/views/partial/header.php'; ?>

<div class="auth-container flex-center">
    <div class="card auth-card">
        <h1>Login</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($form["username"]) ?>" placeholder="Enter your username">

            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter your password">

            <button type="submit" name="login">Login</button>
        </form>

        <p class="auth-switch">Don't have an account? <a href="/finalProj/views/register.php">Register here</a></p>
    </div>
</div>

<?php require __DIR__ . '/views/partial/footer.php'; ?>
