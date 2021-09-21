<?php
session_start();
include('../../pdoInc.php');

//-------登入-------------
if (isset($_POST['email']) && isset($_POST['password']) && $_POST['email'] !== '' && $_POST['password'] !== '') {
	$email = htmlspecialchars($_POST['email']);
	$password = preg_replace("/[^A-Za-z0-9]/", '', $_POST['password']);

	if ($email !== '' && $password !== '') {
		$password = hash('sha256', $password); //加密
		$sth = $dbh->prepare('SELECT name, password,email, img, img_name FROM student WHERE email = ? AND password = ?');
		$sth->execute(array($email, $password));
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if ($sth->rowCount() >= 1) { //有帳密
			$_SESSION['user'] = [
				'name' => $row['name'],
				'email' => $email,
				'img' => $row['img'],
				'identity' => 'student',
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

	//----------註冊----------
} elseif (isset($_POST['reg_name']) && isset($_POST['reg_password']) && isset($_POST['reg_password2']) && isset($_POST['reg_email']) && $_POST['reg_name'] !== '' && $_POST['reg_password'] !== '' && $_POST['reg_password2'] !== '' && $_POST['reg_email'] !== '') {
	$name = htmlspecialchars($_POST['reg_name']);
	$email =  htmlspecialchars($_POST['reg_email']);
	$password = preg_replace("/[^A-Za-z0-9]/", '', $_POST['reg_password']);
	$password2 = preg_replace("/[^A-Za-z0-9]/", '', $_POST['reg_password2']);
	$identity = $_POST['reg_identity'];

	if ($password !== $password2) { //密碼驗證錯誤
		echo '<script>alert(\'Verify Fail QQ\')</script>';
		die('<meta http-equiv="refresh" content="0; url=../../signup.php">');
	}

	if (isset($_FILES["reg_file"]["name"])) {	//如果上傳頭貼
		if ($_FILES["reg_file"]["size"] / 1024 > 5 * 1024) {
			echo '<script>alert(\'Too Big! No more than 5MB\')</script>';
			die('<meta http-equiv="refresh" content="0; url=../../signup.php">');
		}
		$type = explode(".", $_FILES["reg_file"]["name"]);
		$type =  strtolower(end($type));
		if (in_array($type, array('jpeg', 'jpg', 'png', 'svg'))) {
			$data = file_get_contents($_FILES["reg_file"]["tmp_name"]); // 把整个文件读入一个字符串
			$reg_file = 'data:image/' . $type . ';base64,' . base64_encode($data);
		} else {
			$reg_file = 1;
		}
	} else {
		$reg_file = 1;
	}


	if ($name !== '' && $password !== '' && $email !== '') {
		$password = hash('sha256', $password); //加密
		$sth = $dbh->prepare('SELECT email FROM student WHERE email = ? UNION SELECT email FROM teacher WHERE email = ?');
		$sth->execute(array($email, $email));
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if ($sth->rowCount() >= 1) { //email 重複
			echo '<script>alert(\'Duplicate Email\')</script>';
			die('<meta http-equiv="refresh" content="0; url=../../signup.php">');
		} else {
			$_SESSION['user'] = [
				'name' => $name,
				'email' => $email,
				'identity' => $identity,
				'img' => $reg_file,
			];

			if ($identity === 'student') {
				$sth = $dbh->prepare('INSERT INTO student (name, password, email, img, img_name) VALUES (?, ?, ?, ?, ?)');
			} else if ($identity === 'teacher') {
				$sth = $dbh->prepare('INSERT INTO teacher (name, password, email, img, img_name) VALUES (?, ?, ?, ?, ?)');
			}
			$sth->execute(array($name, $password, $email, $reg_file, $type));

			echo "<script>alert('success!')</script>";
			echo '<meta http-equiv="refresh" content="0; url=../../main.php">';
		}
	}else{
		echo "<script>alert('Fail QQ')</script>";
		echo '<meta http-equiv="refresh" content="0; url=../../signup.php">';
	}

	//------------忘記密碼---------------------
} else {
	die('<meta http-equiv="refresh" content="0; url=../../signup.php">');
}
