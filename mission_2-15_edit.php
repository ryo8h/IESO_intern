<?php

//セッション開始
//session_startを何度も呼び出さないための処理
if(empty($_SESSION["setup"])){
	session_start();
	$_SESSION["setup"] = "true";
	//echo'$_SESSION["setup"] is '.$_SESSION["setup"];
}

?>



<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>なんでも掲示板</title>
</head>

<h1>なんでも掲示板</h1>



<?php

//MySQLの接続
$dsn = $_SESSION["dsn"];
$userDB = $_SESSION["userDB"];
$passwordDB = $_SESSION["passwordDB"];
$pdo = new PDO($dsn, $userDB, $passwordDB);
//文字化け対策
$stmt = $pdo->query('SET NAMES utf8');


//編集する番号
$editNum = $_POST["editNum"];
//編集モード確認用(編集番号を指定した時に、一緒にfalseが送られてきている)
$modeEdit = $_POST["modeEdit"];
//パスワード
$password = $_POST["password"];


//編集番号が存在するか
if(!empty($editNum)){
	
	//編集モード：オフ(該当レコードの名前とコメントを変更させる)(レコードの存在確認、パスワードの正誤)
	if($modeEdit == "false"){
		
		//編集したいidのレコードを探す
		$sql = $pdo -> prepare("SELECT * FROM tbKeiziban WHERE id = :id");
		$sql -> bindParam(':id', $editNum, PDO::PARAM_INT);
		$sql -> execute();
		$result = $sql -> rowCount();
		
		//編集したいidのレコードが無い時
		if(!$result){
			//エラー用メッセージ
			$_SESSION["message"] = "存在しない投稿番号です。";
			$_SESSION["typeM"] = "-1";
			// 掲示板ページへリダイレクト
			header("Location: ./mission_2-15.php");
			exit;
		}else{
			//連想配列の状態で受け取る
			$result = $sql -> fetch();
			
			//パスワードが間違っているとき
			if($result["password"] != $password){
				//エラー用メッセージ
				$_SESSION["message"] = "パスワードが間違っています。";
				$_SESSION["typeM"] = "-1";
				// 掲示板ページへリダイレクト
				header("Location: ./mission_2-15.php");
				exit;
			}else{
				//対象の名前、コメントデータを取得し、それぞれ$beforeName, $beforeCommに代入
				$beforeName = $result["name"];
				$beforeComm = $result["comment"];
				
				echo"投稿を変更します。"."<br /><br />";
				
				//編集フォーム
				echo '<form action="/mission_2-15_edit.php" method="post">';
				echo '名前　　:<input type="text" name="namae" value="'.$beforeName.'">';
				echo '<br />';
				echo 'コメント:<input type="text" name="comment" value="'.$beforeComm.'">';
				echo '<br />';
				echo '<input type="hidden" name="editNum" value="'.$editNum.'">';
				echo '<input type="hidden" name="modeEdit" value="true">';
				echo '<input type ="submit" value="送信">';
				echo '</form>';
			}
		}
	
	//編集モード：オン(データ更新)
	}else{
		$hiduke = date('Y-m-d H:i:s');
		$sql = $pdo -> prepare(
		"UPDATE tbKeiziban SET name = :name, comment = :comment, date = :date WHERE id = :id");
		$sql -> bindParam(':name', $_POST["namae"], PDO::PARAM_STR);
		$sql -> bindParam(':comment', $_POST["comment"], PDO::PARAM_STR);
		$sql -> bindParam(':date', $hiduke, PDO::PARAM_STR);
		$sql -> bindParam(':id', $editNum, PDO::PARAM_INT);
		$sql -> execute();
		$_SESSION["message"] = "編集が完了しました。";
		$_SESSION["typeM"] = "1";
		// 掲示板ページへリダイレクト
		header("Location: ./mission_2-15.php");
		exit;
	}
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "編集番号が入力されていません。";
	$_SESSION["typeM"] = "-1";
	// 掲示板ページへリダイレクト
	header("Location: ./mission_2-15.php");
}
?>