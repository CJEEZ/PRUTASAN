<?php
// Simple DB query without Laravel
$host = 'localhost';
$db = 'fruit2web';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $stmt = $pdo->query("SELECT id, name, email, email_verified_at FROM users WHERE role = 'seller'");
    $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sellers)) {
        echo "No sellers found.\n";
    } else {
        echo "ID | Name | Email | Status\n";
        echo str_repeat("-", 80) . "\n";
        foreach ($sellers as $seller) {
            $status = $seller['email_verified_at'] ? 'APPROVED' : 'PENDING';
            printf("%d | %s | %s | %s\n", $seller['id'], $seller['name'], $seller['email'], $status);
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
