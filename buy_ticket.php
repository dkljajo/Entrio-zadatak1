<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['name']);
    $customerEmail = trim($_POST['email']);
    $ticketType = $_POST['ticket_type'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT * FROM ticket_types WHERE name = ? FOR UPDATE");
        $stmt->execute([$ticketType]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            throw new Exception("Nepostojeća kategorija.");
        }

        if ($ticket['sold'] >= $ticket['quota']) {
            throw new Exception("Kategorija '{$ticket['name']}' je rasprodana.");
        }

        $stmt = $pdo->prepare("UPDATE ticket_types SET sold = sold + 1 WHERE id = ?");
        $stmt->execute([$ticket['id']]);

        $stmt = $pdo->prepare("INSERT INTO orders (ticket_type_id, customer_name, customer_email) VALUES (?, ?, ?)");
        $stmt->execute([$ticket['id'], $customerName, $customerEmail]);

        $pdo->commit();

        echo "<h2>Kupnja uspjesna!</h2>";
        echo "<p>Hvala, {$customerName}. Kupili ste ulaznicu za <b>{$ticket['name']}</b> po cijeni od <b>{$ticket['price']} EUR</b>.</p>";
        echo "<a href='index.php'>⬅ Povratak</a>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<h2>Greska!</h2>";
        echo "<p>{$e->getMessage()}</p>";
        echo "<a href='index.php'>⬅ Povratak</a>";
    }
}
?>