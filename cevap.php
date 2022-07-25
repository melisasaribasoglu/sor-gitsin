<?php
session_start();
ob_start();
include ("conn.php");
if($_SESSION["oturum"]==false){
    header('Location: index.php');
    exit;
    $id='';
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soruyu Cevapla</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="fontawesome/all.min.css"> 
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="anasayfa.php">SOR GİTSİN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-link active" aria-current="page" href="anasayfa.php"> <i class="fas fa-home"> Ana Sayfa </i> </a>
            <a class="nav-link" href="soru.php" title="Soru sormak için tıklayın."><i class="fas fa-plus-circle"><span class="ms-1">Soru Sor</span></i></a>
            <a class="nav-link" href="logout.php">   <i class="fas fa-sign-out-alt"> Çıkış Yap </i>    </a>
        </div>
        <?php echo '<span class="navbar-text ms-auto "> Hoş Geldin : '.$_SESSION['name'].' </span>';  ?>
            
        </div>
    </div>
    </nav>
<div class="container mt-2">
<div class="row row-cols-1 row-cols-md-2 g-4">
    <?php
    
    if(isset($_GET['cevap'])){
        $id=$_GET['id'];
        if(!$id){
            header("location: anasayfa.php");
        }else{
            $query = $db->query("SELECT * FROM sorular WHERE id='".$id."'", PDO::FETCH_ASSOC);
            if ( $query->rowCount() ){
            foreach( $query as $row ){
                echo '<div class="col">
                    <div class="card">

                    <div class="card-body">
                        <h5 class="card-title"><h5>'.$row['baslik'].' </h5>
                        <h6 class="card-subtitle mb-2 text-muted">Soru Tarihi : '.date_format(date_create($row['soru_tarihi']), "d/m/Y H:i:s") .'</h6> 
                        <p class="card-text">'.$row['soru'].'</p>
                    </div>
                    
            

                    </div>
                </div>';
        }

    }
    echo'
    
    <div class="col">
         <form action="" method="post">
             <div class="input-group input-group-lg">
                 <span class="input-group-text">Cevabınızı Yazınız :  </span>
                 <textarea class="form-control form-control-lg" aria-label="Cevabınızı Yazınız" name="cevap"></textarea>
             </div>
             <input type="submit" class="btn btn-outline-primary mt-2" value="Yanıtla" />
         </form>
         </div>
         </div>

   ';

   $uyeID = $_SESSION['uyeID'];

if(isset($_POST["cevap"])){
    if(trim($_POST["cevap"])!="" ){
         $cevap = $_POST["cevap"];
        
         $query = $db->prepare("INSERT INTO cevaplar SET
         cevap = ?,
         cevap_veren_id = ?,
         cevap_verilen_soru_id= ?");
         $insert = $query->execute(array(
              $cevap,$uyeID,$id
         ));
         header('Location:cevap.php?cevap&id='.$id.'');
         
    }else{
         echo "<script> window.location.href = 'cevap.php?cevap&id=".$id."'; alert('Eksik alanlar var. Lütfen bilgileri eksiksiz doldurunuz.');</script>";
     }
         
}

    echo '<div class="row g-4 mt-2 mb-1">';

    
    $varmi = $db ->prepare("SELECT q.*,l.num,u.username
    FROM cevaplar q 
    INNER JOIN uyeler u ON  q.cevap_veren_id= u.id
    LEFT JOIN 
     (SELECT COUNT(durum) AS num,begenilen_yorum_id
      FROM cevap_begeniler
      WHERE durum = 1
      GROUP BY begenilen_yorum_id) l ON q.id = l.begenilen_yorum_id
    WHERE q.cevap_verilen_soru_id=:pay
    ORDER bY num DESC
    ");
    
        $varmi -> execute(array(':pay'=>$id));
        if($varmi->rowCount()) {

            foreach( $varmi as $row ){
                $begenilensayi = $db->prepare("SELECT count(*) as toplam_begeni FROM cevap_begeniler WHERE begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid AND durum=:d");
                $begenilensayi -> execute(array(':pay'=>$id,':pid'=>$row['id'],':d'=>1));
                $begenilensayirow = $begenilensayi->fetch(PDO::FETCH_ASSOC);

                $begenilmeyensayi = $db->prepare("SELECT count(*) as toplam_begenilmeyen FROM cevap_begeniler WHERE begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid AND durum=:d");
                $begenilmeyensayi -> execute(array(':pay'=>$id,':pid'=>$row['id'],':d'=>2));
                $begenilmeyensayirow = $begenilmeyensayi->fetch(PDO::FETCH_ASSOC);
                

                echo '
                
                <div class="col mt-1">
                    <div class="card">

                    <div class="card-body">
                        <h5 class="card-title"><h5>'.$row['username'].' </h5>
                        <h6 class="card-subtitle mb-2 text-muted">Cevap Tarihi :  ' . date_format(date_create($row['cevap_tarihi']), "d/m/Y H:i:s") . '</h6>
                        <p class="card-text">'.$row['cevap'].'</p>
                    </div>
                    </div>
                    <div class="card-footer d-flex justify-content-start align-items-center">';
                        
                        $varmiki = $db ->prepare("SELECT * FROM cevap_begeniler WHERE begenen_id=:id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
                        $varmiki -> execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$row['id']));
                        if($varmiki->rowCount()) {
                            $varrow = $varmiki->fetch(PDO::FETCH_ASSOC);

                         

                            if($varrow['durum'] == 1){
                                echo'<span  class="badge bg-dark btn-sm m-2"><i class="fas fa-heart p-1"><span class="m-1">'.$begenilensayirow['toplam_begeni'].'</span></i></span> | <a href ="cevap.php?cevap&id='.$id.'&oyumusil&silid='.$row['id'].'"><button type="button" class="btn btn-dark btn-sm m-2" title="Oyumu Sil" ><i class="fas fa-backspace"></i></button></a> ';
                            }else{
                                echo'<span  class="badge bg-dark btn-sm m-2 "><i class="fas fa-heart-broken p-1"><span class="m-1">'.$begenilmeyensayirow['toplam_begenilmeyen'].'</span></i></span> | <a href ="cevap.php?cevap&id='.$id.'&oyumusil&silid='.$row['id'].'"><button type="button" class="btn btn-dark btn-sm m-2" title="Oyumu Sil"><i class="fas fa-backspace"></i></button></a>';
                            }

                        }else{
                            echo'<a href ="cevap.php?cevap&id='.$id.'&begen&begenId='.$row['id'].'"><button type="button" class="btn btn-dark btn-sm m-2" title="Beğen"><i class="fas fa-heart p-1"><span class="m-1">'.$begenilensayirow['toplam_begeni'].'</span></i></button></a> | <a href ="cevap.php?cevap&id='.$id.'&begenme&begenmeId='.$row['id'].'"><button type="button" class="btn btn-dark btn-sm m-2" title="Beğenme"><i class="fas fa-heart-broken p-1"><span class="m-1">'.$begenilmeyensayirow['toplam_begenilmeyen'].'</span></i></button></a> ';
                        }
                        echo'</div>';
            }
        }else{
            echo '<div class="col">
                
                   <h1>Henüz cevap verilmemiş.</h1>
                
                </div>
                ';
        }
        }
        echo '</div>';
    }
  
    if(isset($_GET['begen'])){
        $idBegen=$_GET['begenId'];
        if(!$idBegen){
            header("location: cevap.php?cevap&id=".$id."");
        }else{
            $varmi = $db -> prepare("SELECT * FROM cevap_begeniler WHERE begenen_id=:id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
            $varmi->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegen));
            if($varmi->rowCount()){

                $begen = $db ->prepare("UPDATE cevap_begeniler SET 
                durum = :durum WHERE begenen_id = :id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
                $begen->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegen,':durum'=>1));
                if($begen){
                    header("location: cevap.php?cevap&id=".$id."");
                }
            }

            else{
                $begen = $db ->prepare("INSERT INTO cevap_begeniler SET 
                begenen_id = :id,
                begeniye_ait_soru_id=:pay,
                begenilen_yorum_id=:pid,
                durum= :durum");
                $begen->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegen,':durum'=>1));
                if($begen){
                    header("location: cevap.php?cevap&id=".$id."");
                }
            }
        }
    }


    if(isset($_GET['begenme'])){
        $idBegenme=$_GET['begenmeId'];
        if(!$idBegenme){
            header("location: cevap.php?cevap&id=".$id."");
        }else{
            $varmi = $db -> prepare("SELECT * FROM cevap_begeniler WHERE begenen_id=:id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
            $varmi->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegenme));
            if($varmi->rowCount()){

                $begenme = $db ->prepare("UPDATE cevap_begeniler SET 
                durum = :durum WHERE begenen_id = :id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
                $begenme->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegenme,':durum'=>2));
                if($begenme){
                    header("location: anasayfa.php");
                }
            }

            else{
                $begenme = $db ->prepare("INSERT INTO cevap_begeniler SET 
                 begenen_id = :id,
                 begeniye_ait_soru_id=:pay,
                begenilen_yorum_id=:pid,
                durum= :durum");
                $begenme->execute(array(':id'=>$_SESSION['uyeID'],':pay'=>$id,':pid'=>$idBegenme,':durum'=>2));
                if($begenme){
                    header("location: cevap.php?cevap&id=".$id."");
                }
            }
        }
    }

    if(isset($_GET['oyumusil'])){
        $idOyumuSil=$_GET['silid'];
        if(!$idOyumuSil){
            header("location: cevap.php?cevap&id=".$id."");

        }else{
            $sil = $db->prepare("DELETE FROM cevap_begeniler WHERE begenen_id = :id AND begeniye_ait_soru_id=:pay AND begenilen_yorum_id=:pid");
            $sil->execute(array(':pay'=>$id,':id'=>$_SESSION['uyeID'],':pid'=>$idOyumuSil));
            if($sil){
                header("location: cevap.php?cevap&id=".$id."");
            }
        }
    }
    
    

    
    ?>

</div>

</div>
        <script src="bootstrap/js/bootstrap.min.js"></script>   <!--JavaScript dosyaso dahil edildi-->              

</body>
</html>
<?php ob_flush(); ?>