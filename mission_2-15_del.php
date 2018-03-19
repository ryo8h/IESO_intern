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


//削除する番号
$delNum = $_POST["delNum"];
//パスワード
$password = $_POST["password"];


if(!empty($delNum)){
	
	//削除したいidのレコードを探す
	$sql = $pdo -> prepare("SELECT * FROM tbKeiziban WHERE id = :id");
	$sql -> bindParam(':id', $delNum, PDO::PARAM_INT);
	$sql -> execute();
	$result = $sql -> rowCount();
	
	//削除したいidのレコードが無い時
	if(!$result){
		//エラー用メッセージ
		$_SESSION["message"] = "存在しない投稿番号です。";
		$_SESSION["typeM"] = "-1";
	}else{
		//パスワードのチェック
		$sql= $pdo -> prepare("SELECT * FROM tbKeiziban WHERE id = :id");
		$sql -> bindParam(':id', $delNum, PDO::PARAM_INT);
		$sql -> execute();
		$result = $sql -> fetch();
		
		//パスワードが間違っているとき
		if($result["password"] != $password){
			//エラー用メッセージ
			$_SESSION["message"] = "パスワードが間違っています。";
			$_SESSION["typeM"] = "-1";
		}else{
			//削除する
			$sql = $pdo -> prepare("DELETE FROM tbKeiziban WHERE id = :id");
			$sql -> bindParam(':id', $delNum, PDO::PARAM_INT);
			$sql -> execute();
			$_SESSION["message"] = "削除が完了しました。";
			$_SESSION["typeM"] = "1";
		}
	}
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "削除番号が入力されていません。";
	$_SESSION["typeM"] = "-1";
}


// 掲示板ページへリダイレクト
header("Location: ./mission_2-15.php");

?>