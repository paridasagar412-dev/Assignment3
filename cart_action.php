<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');
$id=(int)($_POST['product_id']??0);
$s=$pdo->prepare('SELECT id,name,price FROM products WHERE id=?');$s->execute([$id]);$product=$s->fetch();
if(!$product){http_response_code(404);echo json_encode(['message'=>'Product not found.']);exit;}
if(!isset($_SESSION['cart']))$_SESSION['cart']=[];
$_SESSION['cart'][$id]=($_SESSION['cart'][$id]??0)+1;
echo json_encode(['message'=>$product['name'].' added to cart.']);
?>