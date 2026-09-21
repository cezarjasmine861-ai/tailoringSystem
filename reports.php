<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$pageTitle = 'Reports';
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');
$filters = [];
$parameters = [];

if ($dateFrom !== '') {
    $filters[] = 'order_date >= :date_from';
    $parameters['date_from'] = $dateFrom;
}
if ($dateTo !== '') {
    $filters[] = 'order_date <= :date_to';
    $parameters['date_to'] = $dateTo;
}
$where = $filters ? ' WHERE ' . implode(' AND ', $filters) : '';

$summaryStatement = $pdo->prepare("SELECT COUNT(*) AS total_orders, COALESCE(SUM(price), 0) AS total_sales, SUM(status = 'Pending') AS pending_orders, SUM(status = 'Ongoing') AS ongoing_orders, SUM(status = 'Completed') AS completed_orders FROM records{$where}");
$summaryStatement->execute($parameters);
$summary = $summaryStatement->fetch();

$statusStatement = $pdo->prepare("SELECT status, COUNT(*) AS total FROM records{$where} GROUP BY status ORDER BY FIELD(status, 'Pending', 'Ongoing', 'Completed')");
$statusStatement->execute($parameters);
$statusCounts = $statusStatement->fetchAll();

$priorityStatement = $pdo->prepare("SELECT priority, COUNT(*) AS total FROM records{$where} GROUP BY priority ORDER BY FIELD(priority, 'High', 'Normal', 'Low')");
$priorityStatement->execute($parameters);
$priorityCounts = $priorityStatement->fetchAll();

$recordsStatement = $pdo->prepare("SELECT * FROM records{$where} ORDER BY order_date DESC, id DESC");
$recordsStatement->execute($parameters);
$records = $recordsStatement->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><h1>Reports</h1><p class="subtitle">Review orders and sales for a selected date range.</p></div>
    <a class="button button-muted" href="index.php">&larr; Back</a>
</section>

<section class="panel report-filter">
    <form class="filter-form" method="get">
        <div class="form-group"><label for="date_from">Date received from</label><input id="date_from" name="date_from" type="date" value="<?= e($dateFrom) ?>"></div>
        <div class="form-group"><label for="date_to">Date received to</label><input id="date_to" name="date_to" type="date" value="<?= e($dateTo) ?>"></div>
        <button class="button" type="submit">Generate report</button>
        <?php if ($dateFrom !== '' || $dateTo !== ''): ?><a class="button button-muted" href="reports.php">Clear</a><?php endif; ?>
    </form>
</section>

<section class="stats report-stats">
    <div class="stat"><div class="stat-label">Total orders</div><div class="stat-value"><?= (int) $summary['total_orders'] ?></div></div>
    <div class="stat"><div class="stat-label">Total sales</div><div class="stat-value report-money">PHP <?= number_format((float) $summary['total_sales'], 2) ?></div></div>
    <div class="stat"><div class="stat-label">Pending</div><div class="stat-value"><?= (int) $summary['pending_orders'] ?></div></div>
    <div class="stat"><div class="stat-label">Completed</div><div class="stat-value"><?= (int) $summary['completed_orders'] ?></div></div>
</section>

<div class="report-columns">
    <section class="panel"><div class="panel-heading"><h2>By status</h2></div><div class="report-list">
        <?php foreach ($statusCounts as $item): ?><div class="report-row"><span class="status <?= orderStatusClass($item['status']) ?>"><?= e($item['status']) ?></span><strong><?= (int) $item['total'] ?></strong></div><?php endforeach; ?>
        <?php if (!$statusCounts): ?><div class="empty">No orders in this period.</div><?php endif; ?>
    </div></section>
    <section class="panel"><div class="panel-heading"><h2>By priority</h2></div><div class="report-list">
        <?php foreach ($priorityCounts as $item): ?><div class="report-row"><span class="priority priority-<?= strtolower(e($item['priority'])) ?>"><?= e($item['priority']) ?></span><strong><?= (int) $item['total'] ?></strong></div><?php endforeach; ?>
        <?php if (!$priorityCounts): ?><div class="empty">No orders in this period.</div><?php endif; ?>
    </div></section>
</div>

<section class="panel report-table"><div class="panel-heading"><h2>Order details</h2><span class="hint"><?= count($records) ?> record(s)</span></div>
    <?php if (!$records): ?><div class="empty">No orders found for the selected dates.</div><?php else: ?>
    <div class="table-wrap"><table><thead><tr><th>Date received</th><th>Customer</th><th>Garment</th><th>Status</th><th>Priority</th><th>Price</th></tr></thead><tbody>
        <?php foreach ($records as $record): ?><tr><td><?= formatDate($record['order_date']) ?></td><td><div class="customer"><?= e($record['customer_name']) ?></div><div class="secondary"><?= e($record['phone']) ?></div></td><td><?= e($record['garment_type']) ?></td><td><span class="status <?= orderStatusClass($record['status']) ?>"><?= e($record['status']) ?></span></td><td><span class="priority priority-<?= strtolower(e($record['priority'])) ?>"><?= e($record['priority']) ?></span></td><td>PHP <?= number_format((float) $record['price'], 2) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>