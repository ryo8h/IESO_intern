<!--セッション開始(コメント投稿、削除、編集などのメッセージ表示に)-->
<?php
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
<title>プログラミングの勉強</title>
</head>

<h1>プログラミングの勉強</h1>

<!--投稿フォーム-->
<form action="/mission_2-6_post.php" method="post">
名前　　　:<input type="text" name="namae">
<br />
コメント　:<input type="text" name="comment">
<br />
パスワード:<input type="text" name="password">
<input type ="submit" value="送信">
</form><br />

<!--削除フォーム-->
<form action="/mission_2-6_del.php" method="post">
削除対象番号:<input type="number" name="delNum" min="1">
<br />
パスワード　:<input type="text" name="password">
<input type ="submit" value="送信">
</form><br />

<!--編集フォーム-->
<form action="/mission_2-6_edit.php" method="post">
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
?>


<br />
<p>－－－みんなの投稿(新着順)－－－</p>
<?php

$filename = 'm2_textList.txt' ;
	
//投稿記事が格納されるテキストファイルがあるかどうか調べる
if(file_exists($filename)){
	$array = file($filename, FILE_IGNORE_NEW_LINES);
	//投稿を降順にする
	$arrayR = array_reverse($array);

	//パスワードの部分を含めないで投稿を出力
	foreach($arrayR as $textList){
		$text = explode("<p>",$textList);
		//$text[1]...パスワード
		echo $text[0]."<hr>";
	}
}else{
	echo "--投稿はありません。--";
}

?>

</html>