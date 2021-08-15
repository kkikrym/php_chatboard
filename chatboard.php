<!DOCTYPE html>
<html lang = ja>

<html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <style>
        .redchar{
            color:red;
        }
        .bigger{
            font-size:30px;
        }
        .graybg{
            background-color:#eee;
        }
    </style>
</head>

<body>

<?php

    $dsn = 'mysql:dbname=DATABASE;host=localhost';
    $user = UNAME
    $password = PASSWORD
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql = 'SELECT * FROM testbd';
    $stmt = $pdo -> query($sql);
    $result = $stmt->fetchAll();
    $len = count($result);

    $whether_edit = false;
    $wrongpass = false;
    $notfilled = false;

    if(!empty($_POST["ediform"]) or !empty($_POST["pass_edi"])){
        if($_POST["ediform"]!="" && $_POST["pass_edi"]!=""){
            foreach($result as $row){

                if($row['commentnum'] == $_POST["ediform"]){
                    if($row['password'] == $_POST["pass_edi"]){
                        $whether_edit = true;
                    }else{
                        $wrongpass = true;
                    }
                }

            }
        }else{
            $notfilled = true;
        }
    }

?>




<form action="" method="POST">
<!--編集用フォームが記入済みであればその番号の投稿を表示 -->
    <input type="hidden" name="edi_num" value="<?php if($whether_edit){
        echo $_POST['ediform'];
    } ?>">

    コメント：<input type="text" name="comment" value="<?php if($whether_edit){
        foreach($result as $row){
            if($_POST["ediform"] == $row['commentnum']){
                echo $row['comment'];
            }
        }
    } ?>"  placeholder="comment">
    お名前：<input type="text" name="name" value="<?php if($whether_edit){
        foreach($result as $row){
            if($_POST["ediform"] == $row['commentnum']){
                echo $row['username'];
            }
        }
    } ?>" placeholder="name">

    パスワード：<input type="text" name="password" value="" placeholder="password" >
	<input type="submit" name="submit" value="送信">
<br>
<hr>
    編集用フォーム：<input type="number" min="1" max="<?php echo $len;?>" name="ediform" placeholder="">
	パスワード：<input type="text" name="pass_edi" value="" placeholder="password" >
	<input type="submit" name="submit" value="送信">
<br>
    削除用フォーム：<input type="number" min="1" max="<?php echo $len;?>"  name="delform" placeholder="">
	パスワード：<input type="text" name="pass_del" value="" placeholder="password" >
	<input type="submit" name="submit" value="送信">
</form>

<div class="graybg">
<hr>
<?php
    if (!empty($_POST["name"]) or !empty($_POST["comment"]) or !empty($_POST["password"])){
        if ($_POST["comment"] == ""){
            $notfilled = true;
        }
        if ($_POST["name"] == ""){
            $notfilled = true;
        }
        if ($_POST["password"] == ""){
            $notfilled = true;
        }
        if (!$notfilled){
            if($_POST["edi_num"]){
                $sql = $pdo -> prepare('UPDATE testbd SET username=:username,comment=:comment,date=:date,password=:password WHERE commentnum=:this');
                $sql -> bindParam(':username', $username, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> bindParam(':this', $edi_num, PDO::PARAM_STR);

                $username = $_POST["name"];
                $comment = $_POST["comment"];
                $password = $_POST["password"];
                $date = date("Y/m/d H:i:s");
                $edi_num = $_POST["edi_num"];
                $sql -> execute();
            }else{
                $sql = $pdo -> prepare("INSERT INTO testbd (commentnum, username, comment, date, password)
                VALUES (:commentnum, :username, :comment, :date, :password)");

                $sql -> bindParam(':commentnum', $num, PDO::PARAM_STR);
                $sql -> bindParam(':username', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);

                $num = $len+1;
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $password = $_POST["password"];
                $sql -> execute();
            }
        }
    }

    if(!empty($_POST["delform"]) or !empty($_POST["pass_del"])){
        if($_POST["delform"]!="" && $_POST["pass_del"]!=""){
            $del_num = $_POST["delform"];
            $sql = 'SELECT * FROM testbd';
            $stmt = $pdo -> query($sql);
            $result = $stmt->fetchAll();
            $have_deleted = false;
            foreach ($result as $row) {
                if ($del_num == $row['commentnum']) {
                    if ($_POST["pass_del"] == $row['password']) {
                        $sql = 'DELETE FROM testbd WHERE commentnum ='.$row['commentnum'].';';
                        $pdo -> query($sql);
                        $have_deleted = true;
                    } else {
                        $wrongpass = true;
                    }
                }else{
                    if($have_deleted){
                        $updatenum = $row['commentnum'] - 1;
                        $sql = 'UPDATE testbd SET commentnum='.$updatenum.' WHERE commentnum='.$row['commentnum'].';';
                        $pdo->query($sql);

                    }
                }
            }
        }else{
            $notfilled = true;
        }
    }



    $sql = 'SELECT * FROM testbd';
    $stmt = $pdo -> query($sql);
    $result = $stmt->fetchAll();
    foreach ($result as $row){
        echo $row["commentnum"].". ";
        echo $row["username"]." (";
        echo $row["date"].")<br> ";
        echo $row["comment"];
        echo '<br><hr>';
    }

    if($notfilled){
        echo '<div class="redchar bigger">　　！ちゃんと埋めてください！</div><hr>';
    }
    if($wrongpass){
        echo '<div class="redchar bigger">　　！パスワードが違います！</div><hr>';
    }


?>
</div>
</body>
</html>