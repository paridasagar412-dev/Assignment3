<?php
require 'auth.php'; $msg='';
function resizeImage($tmp,$dest,$mime){
 if(!extension_loaded('gd')) return move_uploaded_file($tmp,$dest);
 if($mime==='image/jpeg')$src=@imagecreatefromjpeg($tmp);elseif($mime==='image/png')$src=@imagecreatefrompng($tmp);elseif($mime==='image/gif')$src=@imagecreatefromgif($tmp);else return false;
 if(!$src)return false;$w=imagesx($src);$h=imagesy($src);$scale=min(1,600/max($w,$h));$nw=max(1,(int)($w*$scale));$nh=max(1,(int)($h*$scale));$out=imagecreatetruecolor($nw,$nh);
 if($mime==='image/png'||$mime==='image/gif'){imagealphablending($out,false);imagesavealpha($out,true);}
 imagecopyresampled($out,$src,0,0,0,0,$nw,$nh,$w,$h);
 $ok=$mime==='image/jpeg'?imagejpeg($out,$dest,85):($mime==='image/png'?imagepng($out,$dest):imagegif($out,$dest));imagedestroy($src);imagedestroy($out);return $ok;
}
if(isset($_GET['delete'])){$s=$pdo->prepare('DELETE FROM products WHERE id=?');$s->execute([(int)$_GET['delete']]);header('Location: products.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);$name=trim($_POST['name']??'');$cat=(int)($_POST['category_id']??0);$price=(float)($_POST['price']??0);$picture=null;
 if(isset($_FILES['picture']) && $_FILES['picture']['error']===UPLOAD_ERR_OK){
  $info=@getimagesize($_FILES['picture']['tmp_name']);$mime=$info['mime']??'';
  $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
  if(!$info||!isset($ext[$mime]))$msg='Only JPG, PNG or GIF images are allowed.';
  else{$picture=bin2hex(random_bytes(8)).'.'.$ext[$mime];if(!resizeImage($_FILES['picture']['tmp_name'],__DIR__.'/../uploads/'.$picture,$mime)){$picture=null;$msg='Image upload failed. Enable GD or check uploads folder.';}}
 }
 if(!$msg){if($id){if($picture){$s=$pdo->prepare('UPDATE products SET category_id=?,name=?,price=?,picture=? WHERE id=?');$s->execute([$cat,$name,$price,$picture,$id]);}else{$s=$pdo->prepare('UPDATE products SET category_id=?,name=?,price=? WHERE id=?');$s->execute([$cat,$name,$price,$id]);}$msg='Product updated.';}
 else{$s=$pdo->prepare('INSERT INTO products(category_id,name,price,picture) VALUES(?,?,?,?)');$s->execute([$cat,$name,$price,$picture]);$msg='Product added.';}}
}
$cats=$pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();$items=$pdo->query('SELECT p.*,c.name category FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC')->fetchAll();
?><h1>Manage Products</h1><p><?=h($msg)?></p><form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value=""><label>Name <input name="name" required></label><label>Category <select name="category_id" required><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=h($c['name'])?></option><?php endforeach?></select></label><label>Price <input type="number" name="price" min="0" step=".01" required></label><label>Picture <input type="file" name="picture" accept="image/png,image/jpeg,image/gif"></label><button>Add Product</button></form>
<p>To edit, use the Edit link to load the current product into a small form below.</p>
<table border="1" cellpadding="8"><tr><th>Picture</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr><?php foreach($items as $p):?><tr><td><?php if($p['picture']):?><img width="60" src="../uploads/<?=h($p['picture'])?>"><?php endif?></td><td><?=h($p['name'])?></td><td><?=h($p['category'])?></td><td><?=h($p['price'])?></td><td><a href="?delete=<?=$p['id']?>" onclick="return confirm('Delete product?')">Delete</a> | <a href="#edit<?=$p['id']?>">Edit</a></td></tr><?php endforeach?></table>
<h2>Edit product</h2><?php foreach($items as $p):?><details id="edit<?=$p['id']?>"><summary><?=h($p['name'])?></summary><form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="<?=$p['id']?>"><input name="name" value="<?=h($p['name'])?>" required><select name="category_id"><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=$c['id']==$p['category_id']?'selected':''?>><?=h($c['name'])?></option><?php endforeach?></select><input type="number" name="price" step=".01" min="0" value="<?=h($p['price'])?>" required><input type="file" name="picture" accept="image/*"><button>Save changes</button></form></details><?php endforeach?><p><a href="dashboard.php">Dashboard</a></p>