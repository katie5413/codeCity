<?php
session_start();
include('../../pdoInc.php');

//-------登入-------------
if (isset($_POST['email']) && isset($_POST['password']) && $_POST['email'] !== '' && $_POST['password'] !== '') {
	$email = htmlspecialchars($_POST['email']);
	$password = preg_replace("/[^A-Za-z0-9]/", '', $_POST['password']);

	if ($email !== '' && $password !== '') {
		$password = hash('sha256', $password); //加密
		$sth = $dbh->prepare('SELECT name, password,email, img, img_name, coins FROM student WHERE email = ? AND password = ?');
		$sth->execute(array($email, $password));
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if ($sth->rowCount() >= 1) { //有帳密
			$_SESSION['user'] = [
				'name' => $row['name'],
				'email' => $email,
				'img' => $row['img'],
				'identity' => 'student',
				'coins' => $row['coins'],
			];
			// 學生進入
			die('<meta http-equiv="refresh" content="0; url=../../main.php">');
		} else {
			$sth = $dbh->prepare('SELECT name, password,email,img,img_name FROM teacher WHERE email = ? AND password = ?');
			$sth->execute(array($email, $password));
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			if ($sth->rowCount() >= 1) { //有帳密
				$_SESSION['user'] = [
					'name' => $row['name'],
					'email' => $email,
					'img' => $row['img'],
					'identity' => 'teacher',
				];
				// 老師進入
				die('<meta http-equiv="refresh" content="0; url=../../main.php">');
			} else {
				// 登入失敗
				die('<meta http-equiv="refresh" content="0; url=../../index.php">');
			}
		}
	} else {
		echo '<script>alert(\'Fail QQ\')</script>';
		die('<meta http-equiv="refresh" content="0; url=../../index.php">');
	}
} else {
	die('<meta http-equiv="refresh" content="0; url=../../signup.php">');
}
