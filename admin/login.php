<?php
require '../config.php'; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $s=$pdo->prepare('SELECT * FROM admins WHERE username=?'); $s->execute([trim($_POST['username']??'')]); $a=$s->fetch();
 if($a && password_verify($_POST['password']??'', $a['password'])) { $_SESSION['admin_id']=$a['id']; header('Location: dashboard.php'); exit; }
 $error='Invalid username or password';
}
?><h2>Admin Login</h2><p style="color:red"><?=h($error)?></p><form method="post"><label>Username <input name="username" required></label><br><label>Password <input type="password" name="password" required></label><br><button>Login</button></form><p>Demo: admin / password</p><a href="../index.php">Home</a>