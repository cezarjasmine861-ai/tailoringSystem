<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$pageTitle = 'All Records';
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $statement = $pdo->prepare('SELECT * FROM records WHERE customer_name LIKE :search OR phone LIKE :search OR garment_type LIKE :search OR priority LIKE :search ORDER BY FIELD(priority, "High", "Normal", "Low"), due_date ASC, id DESC');
    $statement->execute(['search' => '%' . $search . '%']);
    $records = $statement->fetchAll();
} else {
    $records = $pdo->query('SELECT * FROM records ORDER BY FIELD(priority, "High", "Normal", "Low"), due_date ASC, id DESC')->fetchAll();
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><h1>All records</h1><p class="subtitle">Search and manage every tailoring order.</p></div>
    <div class="heading-actions">
        <a class="button button-muted" href="index.php">&larr; Back</a>
        <a class="button" href="record_form.php">+ New record</a>
    </div>
</section>
<?php if (($_GET['saved'] ?? '') === '1'): ?><div class="alert">Record saved successfully.</div><?php endif; ?>
<?php if (($_GET['deleted'] ?? '') === '1'): ?><div class="alert">Record deleted.</div><?php endif; ?>
<section class="panel">
    <form class="searchbar" method="get">
        <input type="search" name="search" value="<?= e($search) ?>" placeholder="Search customer, phone, or garment...">
        <button class="button" type="submit">Search</button>
        <?php if ($search !== ''): ?><a class="button button-muted" href="records.php">Clear</a><?php endif; ?>
    </form>
    <?php if (!$records): ?>
        <div class="empty">No matching records found.</div>
    <?php else: ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Customer</th><th>Garment</th><th>Date received</th><th>Pickup date</th><th>Status</th><th>Priority</th><th>Price</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><div class="customer"><?= e($record['customer_name']) ?></div><div class="secondary"><?= e($record['phone']) ?></div></td>
                    <td><?= e($record['garment_type']) ?></td>
                    <td><?= formatDate($record['order_date']) ?></td>
                    <td><?= formatDate($record['due_date']) ?></td>
                    <td><span class="status <?= orderStatusClass($record['status']) ?>"><?= e($record['status']) ?></span></td>
                    <td><span class="priority priority-<?= strtolower(e($record['priority'])) ?>"><?= e($record['priority']) ?></span></td>
                    <td>₱<?= number_format((float) $record['price'], 2) ?></td>
                    <td class="actions"><a href="record_form.php?id=<?= $record['id'] ?>">Edit</a><a href="print_order.php?id=<?= $record['id'] ?>">Print</a><a href="delete.php?id=<?= $record['id'] ?>" onclick="return confirm('Delete this record?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
