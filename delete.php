<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $statement = $pdo->prepare('DELETE FROM records WHERE id = ?');
    $statement->execute([$id]);
}
header('Location: records.php?deleted=1');
exit;
