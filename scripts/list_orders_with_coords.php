<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=fruitshop;charset=utf8mb4","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT id,status,driver_latitude,driver_longitude,driver_updated_at,latitude,longitude FROM orders WHERE driver_latitude IS NOT NULL AND driver_longitude IS NOT NULL ORDER BY id DESC LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage();
}
