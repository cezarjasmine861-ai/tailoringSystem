<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $statement = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
    $statement->execute([$_SESSION['user_id']]);
    $user = $statement->fetch();

    if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
        $errors[] = 'Current password is incorrect.';
    }
    if (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match.';
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        $success = 'Password changed successfully.';
    }
}

$pageTitle = 'Change Password';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><h1>Change password</h1><p class="subtitle">Keep your administrator account secure.</p></div>
    <a class="button button-muted" href="index.php">&larr; Back</a>
</section>
<section class="panel form-panel">
    <?php if ($success): ?><div class="success-message"><?= e($success) ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required></div>
        <div class="form-grid password-grid">
            <div class="form-group"><label for="new_password">New password</label><input id="new_password" name="new_password" type="password" autocomplete="new-password" minlength="6" required></div>
            <div class="form-group"><label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" minlength="6" required></div>
        </div>
        <div class="form-actions"><button class="button" type="submit">Save password</button><a class="button button-muted" href="profile.php">Cancel</a></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>