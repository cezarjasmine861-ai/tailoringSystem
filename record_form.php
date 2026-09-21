<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
requireLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$isEdit = (bool) $id;
$errors = [];
$record = [
    'customer_name' => '', 'phone' => '', 'garment_type' => '', 'measurements' => '',
    'order_date' => date('Y-m-d'), 'due_date' => date('Y-m-d', strtotime('+7 days')),
    'price' => '', 'status' => 'Pending', 'priority' => 'Normal', 'notes' => '',
];

if ($isEdit) {
    $statement = $pdo->prepare('SELECT * FROM records WHERE id = ?');
    $statement->execute([$id]);
    $record = $statement->fetch() ?: $record;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($record) as $field) {
        if (isset($_POST[$field])) {
            $record[$field] = trim((string) $_POST[$field]);
        }
    }
    if ($record['customer_name'] === '') $errors[] = 'Customer name is required.';
    if ($record['phone'] === '') $errors[] = 'Phone number is required.';
    if ($record['garment_type'] === '') $errors[] = 'Garment type is required.';
    if ($record['measurements'] === '') $errors[] = 'Measurements are required.';
    if ($record['order_date'] === '' || $record['due_date'] === '') $errors[] = 'Date received and expected pickup date are required.';
    if (!is_numeric($record['price']) || (float) $record['price'] < 0) $errors[] = 'Price must be a valid positive number.';
    $allowedStatuses = ['Pending', 'Ongoing', 'Completed'];
    if (!in_array($record['status'], $allowedStatuses, true)) $errors[] = 'Please choose a valid status.';
    $allowedPriorities = ['High', 'Normal', 'Low'];
    if (!in_array($record['priority'], $allowedPriorities, true)) $errors[] = 'Please choose a valid priority.';

    if (!$errors) {
        $values = [$record['customer_name'], $record['phone'], $record['garment_type'], $record['measurements'], $record['order_date'], $record['due_date'], $record['price'], $record['status'], $record['priority'], $record['notes']];
        if ($isEdit) {
            $statement = $pdo->prepare('UPDATE records SET customer_name=?, phone=?, garment_type=?, measurements=?, order_date=?, due_date=?, price=?, status=?, priority=?, notes=? WHERE id=?');
            $statement->execute([...$values, $id]);
        } else {
            $statement = $pdo->prepare('INSERT INTO records (customer_name, phone, garment_type, measurements, order_date, due_date, price, status, priority, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $statement->execute($values);
        }
        header('Location: records.php?saved=1');
        exit;
    }
}

$pageTitle = $isEdit ? 'Edit Record' : 'New Record';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><h1><?= $isEdit ? 'Edit record' : 'New record' ?></h1><p class="subtitle">Enter the customer and garment details below.</p></div>
    <a class="button button-muted" href="records.php">&larr; Back</a>
</section>
<section class="panel form-panel">
    <?php if ($errors): ?><div class="alert"><?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="post">
        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
        <div class="form-grid">
            <div class="form-group"><label for="customer_name">Customer name *</label><input id="customer_name" name="customer_name" value="<?= e($record['customer_name']) ?>" required></div>
            <div class="form-group"><label for="phone">Phone number *</label><input id="phone" name="phone" value="<?= e($record['phone']) ?>" required></div>
            <div class="form-group"><label for="garment_type">Garment or alteration *</label><input id="garment_type" name="garment_type" placeholder="e.g. School uniform or trouser alteration" value="<?= e($record['garment_type']) ?>" required></div>
            <div class="form-group"><label for="price">Price (PHP) *</label><input id="price" name="price" type="number" min="0" step="0.01" value="<?= e((string) $record['price']) ?>" required></div>
            <div class="form-group"><label for="order_date">Date received *</label><input id="order_date" name="order_date" type="date" value="<?= e($record['order_date']) ?>" required></div>
            <div class="form-group"><label for="due_date">Expected pickup date *</label><input id="due_date" name="due_date" type="date" value="<?= e($record['due_date']) ?>" required></div>
            <div class="form-group"><label for="status">Order status *</label><select id="status" name="status"><?php foreach (['Pending', 'Ongoing', 'Completed'] as $status): ?><option <?= $record['status'] === $status ? 'selected' : '' ?>><?= $status ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="priority">Priority *</label><select id="priority" name="priority"><?php foreach (['High', 'Normal', 'Low'] as $priority): ?><option <?= $record['priority'] === $priority ? 'selected' : '' ?>><?= $priority ?></option><?php endforeach; ?></select></div>
            <div class="form-group full"><label for="measurements">Measurements *</label><textarea id="measurements" name="measurements" placeholder="Example: Chest: 34, Waist: 28, Length: 24" required><?= e($record['measurements']) ?></textarea></div>
            <div class="form-group full"><label for="notes">Notes</label><textarea id="notes" name="notes" placeholder="Fabric color, special requests, or fitting notes..."><?= e($record['notes']) ?></textarea></div>
        </div>
        <div class="form-actions"><button class="button" type="submit">Save record</button><a class="button button-muted" href="records.php">Cancel</a></div>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
