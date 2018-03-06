<?php
//セッション開始
//session_startを何度も呼び出さないための処理
if(empty($_SESSION["setup"])){
	session_start();
	$_SESSION["setup"] = "true";
	//echo'$_SESSION["setup"] is '.$_SESSION["setup"];
}


$filename = 'm2_textList.txt' ;

$name = $_POST["namae"];
$comment = $_POST["comment"];
$password = $_POST["password"];
$hiduke = date('Y/m/d H:i:s');

//パスワードのチェック
if(!preg_match("/\A[a-z\d]{4,20}+\z/i", $password)){
	//エラー用メッセージ
	$_SESSION["message"] = "パスワードを半角英数字4～20文字以内にしてください。";
	$_SESSION["typeM"] = "-1";
	// 掲示板ページへリダイレクト
	header("Location: ./mission_2-6.php");
	exit;
}

if(!empty($name) and !empty($comment)){
	$fp = fopen($filename, 'a');
	$num = count(file($filename))+1;
	//<p>により、パスワードの判別
	//パスワードの後ろの<p>は、パスワードの部分の後ろに空白が入らないようにするため
	fwrite($fp, $num."<>".$name."<>".$comment."<>".$hiduke."<p>".$password."<p>\n");
	fclose($fp);
	//成功用メッセージ
	$_SESSION["message"] = "投稿しました。";
	$_SESSION["typeM"] = "1";
	// ステータスコード出力
	//http_response_code(301);
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "名前またはコメントが入力されていません。";
	$_SESSION["typeM"] = "-1";
}

// 掲示板ページへリダイレクト
header("Location: ./mission_2-6.php");

?>