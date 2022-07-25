<?php
session_start();
if($_SESSION["oturum"]==false){
    header('Location: index.php');
    exit;
}
include ("conn.php");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru</title>
    
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
        <div class="row">
           <div class="col">
               <div class="position-absolute top-50 start-50 translate-middle">
                <form action="" method="post">
                    <div class="input-group input-group-lg mb-3">
                        <span class="input-group-text" id="inputGroup-sizing-default">Soru Başlığınız :  </span>
                        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="baslik">
                    </div>
                    
                    <select class="form-select mb-3" aria-label="Default select example" name="kategori">
                    <option selected value=0>Kategori Seçiniz</option>
                        <?php 
                        $query = $db->query("SELECT * FROM kategoriler", PDO::FETCH_ASSOC);
                        if ( $query->rowCount() ){
                            foreach( $query as $row ){
                                echo '
                            <option value='.$row['id'].'>'.$row['kategori_adi'].'</option>
                                ';

                            }}
                        ?>
                    
                    </select>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">Sorunuzu Yazınız :  </span>
                        <textarea class="form-control form-control-lg" aria-label="Sorunuzu Yazınız" name="soru"></textarea>
                    </div>
                    <input type="submit" class="btn btn-outline-primary mt-2" value="Sor" />
                </form>
        </div>
           </div> 
        </div>
   
    </div>

    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>

<?php 
include ("conn.php");
$uyeID = $_SESSION['uyeID'];

if(isset($_POST["baslik"]) && isset($_POST["soru"]) && isset($_POST["kategori"]) ){
    if(trim($_POST["baslik"])!="" && trim($_POST["soru"])!="" && trim($_POST["kategori"])!=""&& trim($_POST["kategori"])!=0){
         $baslik = $_POST["baslik"];
         $soru = $_POST["soru"];
         $kategori = $_POST["kategori"];
         $query = $db->prepare("INSERT INTO sorular SET
         baslik = ?,
         soru = ?,
         olusturan_kullanici_id = ?,
         kategori_id=?");
         $insert = $query->execute(array(
              $baslik,$soru,$uyeID,$kategori
         ));
         header('Location:anasayfa.php');
         
    }else{
         echo "<script> window.location.href = 'soru.php'; alert('Eksik ya da hatalı alanlar var. Lütfen tekrar kontrol ediniz.');</script>";
     }
         
}
?>