<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$pageTitle = 'Dashboard';
$total = (int) $pdo->query('SELECT COUNT(*) FROM records')->fetchColumn();
$pending = (int) $pdo->query("SELECT COUNT(*) FROM records WHERE status = 'Pending'")->fetchColumn();
$ongoing = (int) $pdo->query("SELECT COUNT(*) FROM records WHERE status = 'Ongoing'")->fetchColumn();
$completed = (int) $pdo->query("SELECT COUNT(*) FROM records WHERE status = 'Completed'")->fetchColumn();
$recentRecords = $pdo->query('SELECT * FROM records ORDER BY created_at DESC, id DESC LIMIT 6')->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Good day, tailor.</h1>
        <p class="subtitle">Keep track of customer orders and fitting details in one place.</p>
    </div>
    <div class="heading-actions">
        <a class="button button-muted" href="records.php">&larr; Back</a>
        <a class="button" href="record_form.php">+ Add customer record</a>
    </div>
</section>

<section class="stats">
    <div class="stat"><div class="stat-label">Total records</div><div class="stat-value"><?= $total ?></div></div>
    <div class="stat"><div class="stat-label">Pending</div><div class="stat-value"><?= $pending ?></div></div>
    <div class="stat"><div class="stat-label">Ongoing</div><div class="stat-value"><?= $ongoing ?></div></div>
    <div class="stat"><div class="stat-label">Completed</div><div class="stat-value"><?= $completed ?></div></div>
</section>

<section class="panel">
    <div class="panel-heading"><h2>Recent records</h2><a class="hint" href="records.php">View all records &rarr;</a></div>
    <?php if (!$recentRecords): ?>
        <div class="empty">No records yet. Add your first customer record to get started.</div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Customer</th><th>Garment</th><th>Pickup date</th><th>Status</th><th>Priority</th><th>Price</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recentRecords as $record): ?>
                <tr>
                    <td><div class="customer"><?= e($record['customer_name']) ?></div><div class="secondary"><?= e($record['phone']) ?></div></td>
                    <td><?= e($record['garment_type']) ?></td>
                    <td><?= formatDate($record['due_date']) ?></td>
                    <td><span class="status <?= orderStatusClass($record['status']) ?>"><?= e($record['status']) ?></span></td>
                    <td><span class="priority priority-<?= strtolower(e($record['priority'])) ?>"><?= e($record['priority']) ?></span></td>
                    <td>₱<?= number_format((float) $record['price'], 2) ?></td>
                    <td class="actions"><a href="record_form.php?id=<?= $record['id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
