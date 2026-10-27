<?php
require '../config.php';header('Content-Type: application/json; charset=utf-8');
$rows=$pdo->query('SELECT p.id,p.name,p.price,p.picture,c.name AS category FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
echo json_encode(['success'=>true,'data'=>$rows],JSON_PRETTY_PRINT);
?>