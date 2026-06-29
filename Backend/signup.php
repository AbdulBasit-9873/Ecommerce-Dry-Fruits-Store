<?php
require_once __DIR__ . '/db.php';

$errors = [];
$old = ['username' => '', 'email' => ''];

// Ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid form submission.';
    }

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $old['username'] = $username;
    $old['email'] = $email;

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'All fields are required.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if ($password !== '' && strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $pdo = getDB();

        // Check for existing email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?,?,?)');
            $insert->execute([$username, $email, $hash]);
            // Registration successful — regenerate CSRF and redirect
            unset($_SESSION['csrf_token']);
            header('Location: success.php');
            exit;
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>Create an Account</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">

        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?php echo e($old['username']); ?>" required>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?php echo e($old['email']); ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required minlength="8">

        <label for="password_confirm">Confirm Password</label>
        <input id="password_confirm" name="password_confirm" type="password" required minlength="8">

        <button type="submit">Sign Up</button>
    </form>

    <p class="muted">Already have an account? <a href="login.php">Login</a></p>
</div>

<script>
// Simple client-side check for matching passwords
const pwd = document.getElementById('password');
const pwd2 = document.getElementById('password_confirm');
const form = document.querySelector('form');
form.addEventListener('submit', function (e) {
    if (pwd.value !== pwd2.value) {
        e.preventDefault();
        alert('Passwords do not match.');
        pwd2.focus();
    }
});
</script>
</body>
</html>
