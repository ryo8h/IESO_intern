<?php

//セッション開始
//session_startを何度も呼び出さないための処理
if(empty($_SESSION["setup"])){
	session_start();
	$_SESSION["setup"] = "true";
	//echo'$_SESSION["setup"] is '.$_SESSION["setup"];
}




$filename = 'm2_textList.txt' ;
//削除する番号
$delNum = $_POST["delNum"];
//投稿番号の更新用
$num = 0;
//パスワード
$password = $_POST["password"];


if(!empty($delNum)){
	//テキストファイル内の投稿内容を配列に格納
	$array = file($filename);
	
	if($delNum > count($array)){
		//エラー用メッセージ
		$_SESSION["message"] = "削除対象番号が投稿数を超えています。";
		$_SESSION["typeM"] = "-1";
	}else{
	  //ファイルを開く
		$fp = fopen($filename, 'w');
		//投稿の数だけループ
		foreach($array as $comList){
			//"<>"を区切りとして、各要素を配列に格納
			//$comm[0]=(投稿番号) $comm[1]=(名前)といった感じ
			$comm = explode("<>",$comList);
		
			//該当しない番号を上書き保存
			if($delNum != $comm[0]){
				$num++;
				fwrite($fp, $num."<>".$comm[1]."<>".$comm[2]."<>".$comm[3]);
			}else{
				//パスワードが正しいかチェック
				//$comm2[0]=(日付) $comm2[1]=(パスワード)
				$comm2 = explode("<p>",$comm[3]);
				if($comm2[1] == $password){
					//成功用メッセージ
					$_SESSION["message"] = $comm[1]."<>".$comm[2]."<>".$comm2[0]." を削除しました。";
					$_SESSION["typeM"] = "1";
				}else{
					//パスワードが間違ってたら投稿内容を保存する
					$_SESSION["message"] = "パスワードが正しくありません。";
					$_SESSION["typeM"] = "-1";
					$num++;
					fwrite($fp, $num."<>".$comm[1]."<>".$comm[2]."<>".$comm[3]);
				}
			}
		}
		fclose($fp);
	}
}else{
	//エラー用メッセージ
	$_SESSION["message"] = "削除番号が入力されていません。";
	$_SESSION["typeM"] = "-1";
}


// 掲示板ページへリダイレクト
header("Location: ./mission_2-6.php");

?>