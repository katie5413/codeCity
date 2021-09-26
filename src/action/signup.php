<?php
session_start();
include('../../pdoInc.php');

if (isset($_POST['reg_name']) && isset($_POST['reg_school']) && isset($_POST['reg_password']) && isset($_POST['reg_password2']) && isset($_POST['reg_email']) && $_POST['reg_name'] !== '' && $_POST['reg_school'] !== '' && $_POST['reg_password'] !== '' && $_POST['reg_password2'] !== '' && $_POST['reg_email'] !== '') {
	$name = htmlspecialchars($_POST['reg_name']);
	$email =  htmlspecialchars($_POST['reg_email']);
	$school = htmlspecialchars($_POST['reg_school']);
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


	if ($school !== '') {
		$sth = $dbh->prepare('SELECT id, name FROM school WHERE name = ?');
		$sth->execute(array($school));
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if ($sth->rowCount() >= 1) {
			$schoolID = $row['id'];
		} else {
			$schoolID = -1;
		}
	}

	if ($name !== '' && $password !== '' && $email !== '' && $schoolID !== '') {
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
				$addAccount = $dbh->prepare('INSERT INTO student (name, password, email, img, img_name, schoolID) VALUES (?, ?, ?, ?, ?, ?)');
			} else if ($identity === 'teacher') {
				$addAccount = $dbh->prepare('INSERT INTO teacher (name, password, email, img, img_name, schoolID) VALUES (?, ?, ?, ?, ?, ?)');
			}
			$addAccount->execute(array($name, $password, $email, $reg_file, $type, $schoolID));

			echo "<script>alert('success!')</script>";
			echo '<meta http-equiv="refresh" content="0; url=../../main.php">';
		}
	} else {
		echo "<script>alert('Fail QQ')</script>";
		echo '<meta http-equiv="refresh" content="0; url=../../signup.php">';
	}
} else {
	die('<meta http-equiv="refresh" content="0; url=../../index.php">');
}
