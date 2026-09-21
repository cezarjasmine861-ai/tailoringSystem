<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$statement = $pdo->prepare('SELECT username, created_at FROM users WHERE id = ? LIMIT 1');
$statement->execute([$_SESSION['user_id']]);
$user = $statement->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

$pageTitle = 'My Profile';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><h1>My profile</h1><p class="subtitle">View your administrator account details.</p></div>
    <a class="button button-muted" href="index.php">&larr; Back</a>
</section>
<section class="panel form-panel profile-card">
    <div class="profile-avatar"><?= e(strtoupper(substr($user['username'], 0, 1))) ?></div>
    <h2><?= e($user['username']) ?></h2>
    <p class="subtitle">Administrator account</p>
    <div class="profile-detail"><span>Username</span><strong><?= e($user['username']) ?></strong></div>
    <div class="profile-detail"><span>Member since</span><strong><?= formatDate($user['created_at']) ?></strong></div>
    <div class="form-actions"><a class="button" href="change_password.php">Change password</a><a class="button button-muted" href="index.php">Cancel</a></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>