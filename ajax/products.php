<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');
$cat=max(0,(int)($_GET['category']??0)); $page=max(1,(int)($_GET['page']??1)); $per=6;
$where=$cat ? ' WHERE category_id = ?' : '';
$count=$pdo->prepare('SELECT COUNT(*) FROM products'.$where); $cat?$count->execute([$cat]):$count->execute();
$total=(int)$count->fetchColumn(); $pages=max(1,(int)ceil($total/$per)); $page=min($page,$pages); $offset=($page-1)*$per;
$sql='SELECT id,name,price,picture FROM products'.$where.' ORDER BY id DESC LIMIT '.$per.' OFFSET '.$offset;
$stmt=$pdo->prepare($sql); $cat?$stmt->execute([$cat]):$stmt->execute();
echo json_encode(['items'=>$stmt->fetchAll(),'page'=>$page,'pages'=>$pages]);
?>