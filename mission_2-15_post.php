<?php
//セッション開始
//session_startを何度も呼び出さないための処理
if(empty($_SESSION["setup"])){
	session_start();
	$_SESSION["setup"] = "true";
	//echo'$_SESSION["setup"] is '.$_SESSION["setup"];
}

//MySQLの接続
$dsn = $_SESSION["dsn"];
$userDB = $_SESSION["userDB"];
$passwordDB = $_SESSION["passwordDB"];
$pdo = new PDO($dsn, $userDB, $passwordDB);
//文字化け対策
$stmt = $pdo->query('SET NAMES utf8');


$name = $_POST["namae"];
$comment = $_POST["comment"];
$password = $_POST["password"];
$hiduke = date('Y-m-d H:i:s');

//パスワードのチェック
if(!preg_match("/\A[a-z\d]{4,20}+\z/i", $password)){
	//エラー用メッセージ
	$_SESSION["message"] = "パスワードを半角英数字4～20文字以内にしてください。";
	$_SESSION["typeM"] = "-1";
	// 掲示板ページへリダイレクト
	header("Location: ./mission_2-15.php");
	exit;
}


if(!empty($name) and !empty($comment)){
	
	
	//投稿データのインサート
	//投稿番号は自動補完されるようにしているのでidはインサートしない
	$sql = $pdo -> prepare
	("INSERT INTO tbKeiziban(name, comment, date, password) VALUES(:name, :comment, :date, :password)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $hiduke, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password, PDO::PARAM_STR);
	$sql -> execute();
	//成功用メッセージ
	$_SESSION["message"] = "投稿しました。";
	$_SESSION["typeM"] = "1";
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "名前またはコメントが入力されていません。";
	$_SESSION["typeM"] = "-1";
}


// 掲示板ページへリダイレクト
header("Location: ./mission_2-15.php");

?>