<?php
session_start();

// Already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /finalProj/views/dashboard.php");
    exit();
}

require_once __DIR__ . '/../models/account.php';
require_once __DIR__ . '/../controllers/account.php';
require_once __DIR__ . '/../public/database.config.php';

$errors  = [];
$success = "";

$form = [
    "username" => "",
    "password" => "",
    "confirm"  => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {

    // Sanitize inputs
    $form["username"] = trim($_POST["username"] ?? "");
    $form["password"] = $_POST["password"] ?? "";
    $form["confirm"]  = $_POST["confirm"] ?? "";

    // Validate username
    if (empty($form["username"])) {
        $errors[] = "Username is required.";
    } elseif (strlen($form["username"]) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } elseif (strlen($form["username"]) > 100) {
        $errors[] = "Username must not exceed 100 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $form["username"])) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    // Validate password
    if (empty($form["password"])) {
        $errors[] = "Password is required.";
    } elseif (strlen($form["password"]) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Validate confirm password
    if (empty($form["confirm"])) {
        $errors[] = "Please confirm your password.";
    } elseif ($form["password"] !== $form["confirm"]) {
        $errors[] = "Passwords do not match.";
    }

    // Only proceed if no errors
    if (empty($errors)) {
        $controller = new AccountController($SERVER_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        if ($controller->register($form["username"], $form["password"])) {
            $success = "Account created successfully! You can now log in.";
            $form = ["username" => "", "password" => "", "confirm" => ""];
        } else {
            $errors[] = "Username is already taken. Please choose another.";
        }
    }
}
?>

<?php require __DIR__ . '/partial/header.php'; ?>

<div class="auth-container flex-center">
    <div class="card auth-card">
        <h1>Register</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($success) ?></p>
            </div>
            <a href="/finalProj/index.php" class="btn btn-primary" style="width:100%; text-align:center; display:block;">Go to Login</a>
        <?php else: ?>
            <form method="POST" novalidate>
                <label>Username: <span class="required">*</span></label>
                <input type="text" name="username" value="<?= htmlspecialchars($form["username"]) ?>" placeholder="e.g. john_doe" maxlength="100">

                <label>Password: <span class="required">*</span></label>
                <input type="password" name="password" placeholder="Minimum 6 characters">

                <label>Confirm Password: <span class="required">*</span></label>
                <input type="password" name="confirm" placeholder="Re-enter your password">

                <p class="required-note"><span class="required">*</span> All fields are required.</p>

                <button type="submit" name="register">Create Account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="/finalProj/index.php">Login here</a></p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/partial/footer.php'; ?>
