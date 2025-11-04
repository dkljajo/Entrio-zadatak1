<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'] ?: null;
    $quota = $_POST['quota'] ?: null;

    $fields = [];
    $params = [];

    if ($price !== null) {
        $fields[] = "price = ?";
        $params[] = $price;
    }

    if ($quota !== null) {
        $fields[] = "quota = ?";
        $params[] = $quota;
    }

    if (!empty($fields)) {
        $params[] = $name;
        $sql = "UPDATE ticket_types SET " . implode(", ", $fields) . " WHERE name = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo "Ažurirano uspješno!";
    } else {
        echo "Nema promjena.";
    }
}

$tickets = $pdo->query("SELECT * FROM ticket_types")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Enrio Tickets</title>
</head>
<body>
<h1>Admin panel</h1>
<form method="POST">
    <label>Kategorija:</label>
    <select name="name">
        <?php foreach ($tickets as $t): ?>
            <option value="<?= htmlspecialchars($t['name']) ?>"><?= htmlspecialchars($t['name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>
    <label>Nova cijena (EUR):</label>
    <input type="number" step="0.01" name="price"><br><br>
    <label>Nova kvota:</label>
    <input type="number" name="quota"><br><br>
    <button type="submit">Ažuriraj</button>
</form>
</body>
</html>