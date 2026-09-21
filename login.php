<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $statement = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1');
    $statement->execute([$username]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password.';
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="panel form-panel auth-panel">
        <div class="auth-heading"><span class="brand-mark">TR</span><h1>Welcome back</h1><p class="subtitle">Log in to manage tailoring records.</p></div>
        <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group"><label for="username">Username</label><input id="username" name="username" autocomplete="username" required autofocus></div>
            <div class="form-group"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
            <button class="button auth-button" type="submit">Log in</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>