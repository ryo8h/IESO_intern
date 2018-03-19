<?php
//セッション開始(コメント投稿、削除、編集などのメッセージ表示に)
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

<!--投稿フォーム-->
<form action="/mission_2-15_post.php" method="post">
名前　　　　:<input type="text" name="namae">
<br />
コメント　　:<input type="text" name="comment">
<br />
パスワード　:<input type="text" name="password">
<input type ="submit" value="送信">
</form><br />

<!--削除フォーム-->
<form action="/mission_2-15_del.php" method="post">
削除対象番号:<input type="number" name="delNum" min="1">
<br />
パスワード　:<input type="text" name="password">
<input type ="submit" value="送信">
</form><br />

<!--編集フォーム-->
<form action="/mission_2-15_edit.php" method="post">
編集対象番号:<input type="number" name="editNum" min="1">
<br />
パスワード　:<input type="text" name="password">
<!--編集モード用隠しパラメータ(編集モード：オフを送信)-->
<input type="hidden" name="modeEdit" value="false">
<input type ="submit" value="送信">
</form><br />


<!--メッセージ通知(投稿、編集、削除)-->
<!--typeM・・・1(成功) -1(エラー)-->
<?php
if($_SESSION["typeM"] == "1"){
	echo '<font color="GREEN">'.$_SESSION["message"].'</font><br />';
}elseif($_SESSION["typeM"] == "-1"){
	echo '<font color="RED">'.$_SESSION["message"].'</font><br />';
}
//サイト更新後、メッセージをずっと出さないようにするため
$_SESSION["typeM"] = 0;
?>


<br />
<p>－－－みんなの投稿(新着順)－－－</p>


<?php


//MySQLの接続
//gitに公開するため、アカウント情報は一度htmlパラメータから受け取るようにしてあります。
//以降はセッションで管理しておくようになっています。
if(!empty($_SESSION["dsn"])){
	$dsn = $_SESSION["dsn"];
	$userDB = $_SESSION["userDB"];
	$passwordDB = $_SESSION["passwordDB"];
}else{
	$_SESSION["dsn"] = ($dsn = 'mysql:host=localhost;dbname='.$_GET["dbname"]);
	$_SESSION["userDB"] = ($userDB = $_GET["user"]);
	$_SESSION["passwordDB"] = ($passwordDB = $_GET["password"]);
}
try{
	$pdo = new PDO($dsn, $userDB, $passwordDB);
	//文字化け対策
	$stmt = $pdo->query('SET NAMES utf8');
}catch(PDOException $e){
	//echo $e->getMessage()."<br>";
	echo '<font color="RED">'.'データベース接続に必要なアカウント情報(dbname,user,password)を、htmlパラメータにつけて下さい'.'</font><br>';
	exit;
}

//テーブル作成
$sql = "CREATE TABLE tbKeiziban"
." ("
."id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
."name varchar(255),"
."comment TEXT,"
."date DATETIME,"
."password varchar(255)"
.");";
$stmt = $pdo->query($sql);


//レコードがひとつでもあるか探す
$sql = $pdo -> prepare("SELECT COUNT(*) FROM tbKeiziban LIMIT 1");
$sql -> execute();
$result = $sql -> rowCount();
//レコードが無い時
if(!$result){
	echo "・・・投稿はありません・・・";
//idが1のレコードがある時
}else{
	//テーブルtbtestの中のデータをselectで表示
	$sql = 'SELECT * FROM tbKeiziban ORDER BY id DESC';
	$results = $pdo -> query($sql);
	foreach ($results as $row){
	  //$rowの中にはテーブルのカラム名が入る
	  echo $row['id'].', ';
	  echo $row['name'].', ';
	  echo $row['comment'].', ';
	  echo $row['date'].'<hr>';
	}
}

?>

</html>