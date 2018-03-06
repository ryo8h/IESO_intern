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
<title>プログラミングの勉強</title>
</head>

<h1>プログラミングの勉強</h1>



<?php


$filename = 'm2_textList.txt' ;
//編集する番号
$editNum = $_POST["editNum"];
//編集モード確認用(編集番号を指定した時に、一緒にfalseが送られてきている)
$modeEdit = $_POST["modeEdit"];
//パスワード
$password = $_POST["password"];


//編集番号が存在するか
if(!empty($editNum)){
	//テキストファイル内の投稿内容を配列に格納
	$array = file($filename);
	
	//編集番号が、正しい数字か(htmlの方で最小値は設定してあるので超過しか起こらない)
	if($editNum > count($array)){
		//エラー用メッセージ
		$_SESSION["message"] = "編集対象番号が投稿数を超えています。";
		$_SESSION["typeM"] = "-1";
	}else{
		
		//編集モード：オン(上書き)
		if($modeEdit == "true"){
			//ファイル開く
			$fp = fopen($filename, "w");
			//投稿の数だけループ
			foreach($array as $comList){
				//"<>"を区切りとして、各要素を配列に格納
				//$comm[0]=(投稿番号) $comm[1]=(名前)といった感じ
				$comm = explode("<>",$comList);
				//該当する投稿番号にあたったら差し替え
		  	if($editNum == $comm[0]){
		  		$comm[1] = $_POST["namae"];
		  		$comm[2] = $_POST["comment"];
		  	}
		  	$num++;
				fwrite($fp, $num."<>".$comm[1]."<>".$comm[2]."<>".$comm[3]);
			}
			//ファイル閉じる
			fclose($fp);
			//エラー用メッセージ
			$_SESSION["message"] = "変更が完了しました。";
			$_SESSION["typeM"] = "1";
			// 掲示板ページへリダイレクト
			header("Location: ./mission_2-6.php");
			exit;
		//編集モード:オフ(編集対象を取得し、フォームに埋め込む)
		}else{
			//投稿の数だけループ
			foreach($array as $comList){
				//"<>"を区切りとして、各要素を配列に格納
				//$comm[0]=(投稿番号) $comm[1]=(名前)といった感じ
				$comm = explode("<>",$comList);
		  	//該当する投稿番号の名前とコメントを取得(フォーム埋め込み用)
				if($editNum == $comm[0]){
					//パスワードが正しいかチェック
					//$comm2[0]=(日付) $comm2[1]=(パスワード)
					$comm2 = explode("<p>",$comm[3]);
					//パスワードが正しければ、編集対象の名前とコメントを取得
					if($comm2[1] == $password){
		  			$beforeName = $comm[1];
		  			$beforeComm = $comm[2];
		  		}else{
		  			//エラー用メッセージ
						$_SESSION["message"] = "パスワードが正しくありません。";
						$_SESSION["typeM"] = "-1";
						// 掲示板ページへリダイレクト
						header("Location: ./mission_2-6.php");
						exit;
		  		}
		  	}
		  }
		  echo"投稿を変更します。"."<br /><br />";
		  
		  //編集フォーム
			//編集番号が正しく入力されたときに表示したいので
			echo '<form action="/mission_2-6_edit.php" method="post">';
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
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "編集番号が入力されていません。";
	$_SESSION["typeM"] = "-1";
}
?>
