<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <title>mission5</title>
        
    </head>
    <body>

<?php
    echo '<font size="2px">';
    echo "名前、コメント、任意のパスワードを入力してください<br>".
         "（投稿の編集、削除を行う場合はパスワードの入力が必要です)<br><br>";
    echo'</font>';
            
?>



<?php
//エラー非表示
error_reporting(E_ALL & ~E_NOTICE); 
//4-1データベース作成
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//パスワード取得するために番号定義(削除か編集かは入力番号でチェック)
if(!empty($_POST['d_num'])){ 
    $d_num=$_POST['d_num'];
    $id= $d_num; 
}elseif(!empty($_POST['e_num'])){
    $e_num=$_POST['e_num'];
    $e_pass=$_POST['e_pass'];
    $id= $e_num;

}

$sql = 'SELECT * FROM mission5_1 WHERE id=:id ';
$stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
$stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
$stmt->execute();                             // ←SQLを実行する。
$results = $stmt->fetchAll(); 
foreach ($results as $row){
        $passcheck=$row['password'];
    }

    //編集要素を取り出す
if(!empty($e_num)&&$passcheck==$e_pass){//編集のときは、上記で編集対象のパスワードを取得している
    $id=$e_num;
    $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll(); 
    foreach ($results as $row){
        //以下3つはフォームで表示させるための変数
        $editnumber=$row['id'];
        $editname=$row['name'];
        $editcomment=$row['comment'];
            
    }
}


?>

<form action=""method="POST" ><!--フォーム作成(valueでフォームの入力欄にあらかじめいれることばを定義)-->
             
             <input type="text" name="name" placeholder="名前"value=<?php echo $editname;?>><br>
             <input type="text" name="comment" placeholder="コメント"value=<?php echo $editcomment;?>><br>
              <!--hiddenにしてこのボックスは隠す-->
              <input type="hidden" name="e_num2" placeholder="編集する投稿番号" value=<?php echo $editnumber;?>>
              <input type="text" name="pass" placeholder="パスワード"value=<?php echo $passcheck;?> >
              <input type="submit" name="submit" value="送信する">
        </form>
        <form action=""method="POST" ><!--削除フォーム-->
            <input type="text" name="d_num" placeholder="削除対象番号"><br>
            <input type="text" name="d_pass" placeholder="パスワード" >
            <input type="submit" name="submit" value="削除">
            
        </form>
        <form action=""method="POST" ><!--編集フォーム-->
            <input type="text" name="e_num" placeholder="編集対象番号" ><br>
            <input type="text" name="e_pass" placeholder="パスワード" > 
            <input type="submit" name="submit" value="編集">
        </form>   

<?php
$name = $_POST['name'];
$comment = $_POST['comment'];
$pass=$_POST['pass'];
$e_num2=$_POST['e_num2'];

//投稿機能
if(!empty($name&&$comment)&& empty($e_num2)){//ここで重要なのは$enum2。これによって編集かそうでないか判断
    if(empty($pass)){
        echo "パスワードが未入力です<br>";
    }else{
        
        $sql = $pdo -> prepare("INSERT INTO mission5_1 (name,comment,date,password)VALUES (:name, :comment,:date,:password)"); //テーブル名は5table
        $sql -> bindParam(':name', $name , PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $pass, PDO::PARAM_STR);
        $date=date("Y-m-d H:i:s");
        $sql -> execute();
        echo "投稿されました<br><br>";
    }    
}
//編集機能
if(!empty($e_num)){
    if( empty($e_pass)or $e_pass!=$passcheck){
        echo "パスワードを正しく入力して下さい<br>";
    }
}
if(!empty($e_num2)){
    $id=$e_num2;
    /*$ename = $_POST['name'];//これないとだめ？
    $ecomment = $_POST['comment'];*/
    
    $sql = 'update mission5_1 set name=:name,comment=:comment where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    echo  $id."番を編集しました<br><br>";

    }
    
//削除機能
if(!empty($d_num)){
    $d_pass=$_POST['d_pass'];
    if(empty($d_pass)or $d_pass!=$passcheck){
        echo "パスワードを正しく入力して下さい<br><br>";
    }else{
    
        $id = $d_num;
        $sql = 'delete from mission5_1 where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        echo  $id."番を削除しました<br><br>";
    }
}


//表示
$sql = 'SELECT * FROM mission5_1';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].'：';//[]中は、4-2で指定したカラムの名称に併せること
    echo $row['name'].'<br>';
    echo $row['comment'].'<br>';
    echo $row['date'].'<br>';
    echo "<hr>";
}
?>
</body>
</html>