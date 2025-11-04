<?php
require 'db.php';
$tickets = $pdo->query("SELECT * FROM ticket_types ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Enrio Tickets</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        h1 { color: #2c3e50; }
        form, table { margin-top: 20px; }
        table { border-collapse: collapse; width: 50%; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; }
        button { background-color: #3498db; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #2980b9; }
    </style>
</head>
<body>

<h1>🎟️ Enrio Tickets</h1>

<h2>Kupi ulaznicu</h2>
<form method="POST" action="buy_ticket.php">
    <label>Ime i prezime:</label><br>
    <input type="text" name="name" required><br><br>
    
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    
    <label>Vrsta ulaznice:</label><br>
    <select name="ticket_type">
        <?php foreach ($tickets as $t): ?>
            <option value="<?= htmlspecialchars($t['name']) ?>"><?= htmlspecialchars($t['name']) ?> (<?= $t['price'] ?> EUR)</option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Kupi</button>
</form>

<hr>

<h2>Status ulaznica</h2>
<table>
<tr><th>Kategorija</th><th>Cijena</th><th>Preostalo</th></tr>
<?php foreach ($tickets as $t): 
    $remaining = $t['quota'] - $t['sold'];
    $status = $remaining > 0 ? $remaining : 'RASPRODANO';
?>
<tr>
    <td><?= htmlspecialchars($t['name']) ?></td>
    <td><?= $t['price'] ?> €</td>
    <td><?= $status ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>