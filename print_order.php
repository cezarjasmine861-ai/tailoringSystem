<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$statement = $pdo->prepare('SELECT * FROM records WHERE id = ?');
$statement->execute([$id]);
$record = $statement->fetch();

if (!$record) {
    header('Location: records.php');
    exit;
}

$pageTitle = 'Print Order Record';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading print-toolbar">
    <div><h1>Order slip</h1><p class="subtitle">Print this order record as a customer reference.</p></div>
    <div class="heading-actions">
        <a class="button button-muted" href="records.php">&larr; Back</a>
        <button class="button" type="button" onclick="window.print()">Print order</button>
    </div>
</section>

<section class="order-slip">
    <div class="slip-header">
        <div><div class="slip-brand">TR</div><h2>Tailoring Records</h2></div>
        <div class="slip-number">Order #<?= e((string) $record['id']) ?><br><span><?= formatDate($record['order_date']) ?></span></div>
    </div>
    <div class="slip-rule"></div>
    <div class="slip-grid">
        <div><span class="slip-label">Customer</span><strong><?= e($record['customer_name']) ?></strong></div>
        <div><span class="slip-label">Phone</span><strong><?= e($record['phone']) ?></strong></div>
        <div><span class="slip-label">Garment / alteration</span><strong><?= e($record['garment_type']) ?></strong></div>
        <div><span class="slip-label">Expected pickup</span><strong><?= formatDate($record['due_date']) ?></strong></div>
        <div><span class="slip-label">Status</span><span class="status <?= orderStatusClass($record['status']) ?>"><?= e($record['status']) ?></span></div>
        <div><span class="slip-label">Priority</span><span class="priority priority-<?= strtolower(e($record['priority'])) ?>"><?= e($record['priority']) ?></span></div>
        <div class="slip-wide"><span class="slip-label">Measurements</span><div class="slip-text"><?= nl2br(e($record['measurements'])) ?></div></div>
        <div class="slip-wide"><span class="slip-label">Notes</span><div class="slip-text"><?= $record['notes'] !== '' ? nl2br(e($record['notes'])) : 'None' ?></div></div>
    </div>
    <div class="slip-total"><span>Total price</span><strong>PHP <?= number_format((float) $record['price'], 2) ?></strong></div>
    <p class="slip-footer">Thank you for choosing our tailoring service.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>