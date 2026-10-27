<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');
echo json_encode($pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll());
?>