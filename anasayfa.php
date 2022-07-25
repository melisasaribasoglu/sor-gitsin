<?php
session_start();
ob_start();
if ($_SESSION["oturum"] == false) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
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
                <?php echo '<span class="navbar-text ms-auto "> Hoş Geldin : ' . $_SESSION['name'] . ' </span>';  ?>

            </div>
        </div>
    </nav>

    <div class="container mt-2">
        <div class="row row-cols-1 row-cols-md-2 g-4">


            <?php
            include("conn.php");

            $query = $db->query("SELECT q.*,l.num
                FROM sorular q 
                LEFT JOIN 
                (SELECT COUNT(durum) AS num,begenilen_paylasim_id
                FROM begeniler
                WHERE durum = 1
                GROUP BY begenilen_paylasim_id) l ON q.id = l.begenilen_paylasim_id
                ORDER bY num DESC ", PDO::FETCH_ASSOC);


            if ($query->rowCount()) {
                foreach ($query as $row) {
                    $begenilensayi = $db->prepare("SELECT count(*) as toplam_begeni FROM begeniler WHERE begenilen_paylasim_id=:pay AND durum=:d");
                    $begenilensayi->execute(array(':pay' => $row['id'], ':d' => 1));
                    $begenilensayirow = $begenilensayi->fetch(PDO::FETCH_ASSOC);

                    $begenilmeyensayi = $db->prepare("SELECT count(*) as toplam_begenilmeyen FROM begeniler WHERE begenilen_paylasim_id=:pay AND durum=:d");
                    $begenilmeyensayi->execute(array(':pay' => $row['id'], ':d' => 2));
                    $begenilmeyensayirow = $begenilmeyensayi->fetch(PDO::FETCH_ASSOC);


                    $sorgu = $db->query('SELECT username FROM uyeler WHERE id =' . $row["olusturan_kullanici_id"] . '');
                    $cikti = $sorgu->fetch(PDO::FETCH_ASSOC);

                    $sorguKategori = $db->query('SELECT kategori_adi FROM kategoriler WHERE id =' . $row["kategori_id"] . '');
                    $ciktiKategori = $sorguKategori->fetch(PDO::FETCH_ASSOC);

                    echo '<div class="col">
                    <div class="card">

                    <div class="card-body">
                        <h5 class="card-title"><h5>' . $row['baslik'] . ' </h5>
                        <h6 class="card-subtitle mb-2 text-muted">Tarih :  ' . date_format(date_create($row['soru_tarihi']), "d/m/Y H:i:s") . '</h6> 
                        <h6 class="card-subtitle mb-2 text-muted">Kategori :  ' . $ciktiKategori['kategori_adi'] . '</h6> 
                        <p class="card-text">' . $row['soru'] . '</p>
                    </div>
                    
                    <div class="card-footer d-flex justify-content-start align-items-center">
                        <small class="text-muted m-2">' . $cikti['username'] . '</small>';
                    $varmi = $db->prepare("SELECT * FROM begeniler WHERE begenen_id=:id AND begenilen_paylasim_id=:pay");
                    $varmi->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $row['id']));
                    if ($varmi->rowCount()) {
                        $varrow = $varmi->fetch(PDO::FETCH_ASSOC);



                        if ($varrow['durum'] == 1) {
                            echo '<span  class="badge bg-dark btn-sm m-2"><i class="fas fa-heart p-1"><span class="m-1">' . $begenilensayirow['toplam_begeni'] . '</span></i></span> | <a href ="anasayfa.php?oyumusil&id=' . $row['id'] . '"><button type="button" class="btn btn-dark btn-sm m-2" title="Oyumu Sil" ><i class="fas fa-backspace"></i></button></a> ';
                        } else {
                            echo '<span  class="badge bg-dark btn-sm m-2 "><i class="fas fa-heart-broken p-1"><span class="m-1">' . $begenilmeyensayirow['toplam_begenilmeyen'] . '</span></i></span> | <a href ="anasayfa.php?oyumusil&id=' . $row['id'] . '"><button type="button" class="btn btn-dark btn-sm m-2" title="Oyumu Sil"><i class="fas fa-backspace"></i></button></a>';
                        }
                    } else {
                        echo '<a href ="anasayfa.php?begen&id=' . $row['id'] . '"><button type="button" class="btn btn-dark btn-sm m-2" title="Beğen"><i class="fas fa-heart p-1"><span class="m-1">' . $begenilensayirow['toplam_begeni'] . '</span></i></button></a> | <a href ="anasayfa.php?begenme&id=' . $row['id'] . '"><button type="button" class="btn btn-dark btn-sm m-2" title="Beğenme"><i class="fas fa-heart-broken p-1"><span class="m-1">' . $begenilmeyensayirow['toplam_begenilmeyen'] . '</span></i></button></a> ';
                    }
                    echo '
                        
                        
                        <a href ="cevap.php?cevap&id=' . $row['id'] . '"><button type="button" class="btn btn-dark btn-sm m-2 ms-auto"><i class="fas fa-comment-dots"></i></button></a>
                    </div>

                    </div>
                </div>';
                }
            } else {
                echo '<div class="col">
                <div class="position-absolute top-50 start-50 translate-middle">
                   <h1>Henüz soru sorulmamış.</h1>
                </div>
                </div>
                ';
            }

            if (isset($_GET['begen'])) {
                $id = $_GET['id'];
                if (!$id) {
                    header("location: anasayfa.php");
                } else {
                    $varmi = $db->prepare("SELECT * FROM begeniler WHERE begenen_id=:id AND begenilen_paylasim_id=:pay");
                    $varmi->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id));
                    if ($varmi->rowCount()) {

                        $begen = $db->prepare("UPDATE begeniler SET 
                        durum = :durum WHERE begenen_id = :id AND begenilen_paylasim_id=:pay");
                        $begen->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id, ':durum' => 1));
                        if ($begen) {
                            header("location: anasayfa.php");
                        }
                    } else {
                        $begen = $db->prepare("INSERT INTO begeniler SET 
                        begenen_id = :id,
                        begenilen_paylasim_id=:pay,
                        durum= :durum");
                        $begen->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id, ':durum' => 1));
                        if ($begen) {
                            header("location: anasayfa.php");
                        }
                    }
                }
            }


            if (isset($_GET['begenme'])) {
                $id = $_GET['id'];
                if (!$id) {
                    header("location: anasayfa.php");
                } else {
                    $varmi = $db->prepare("SELECT * FROM begeniler WHERE begenen_id=:id AND begenilen_paylasim_id=:pay");
                    $varmi->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id));
                    if ($varmi->rowCount()) {

                        $begenme = $db->prepare("UPDATE begeniler SET 
                        durum = :durum WHERE begenen_id = :id AND begenilen_paylasim_id=:pay");
                        $begenme->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id, ':durum' => 2));
                        if ($begenme) {
                            header("location: anasayfa.php");
                        }
                    } else {
                        $begenme = $db->prepare("INSERT INTO begeniler SET 
                        begenen_id = :id,
                        begenilen_paylasim_id=:pay,
                        durum= :durum");
                        $begenme->execute(array(':id' => $_SESSION['uyeID'], ':pay' => $id, ':durum' => 2));
                        if ($begenme) {
                            header("location: anasayfa.php");
                        }
                    }
                }
            }

            if (isset($_GET['oyumusil'])) {
                $id = $_GET['id'];
                if (!$id) {
                    header("location: anasayfa.php");
                } else {
                    $sil = $db->prepare("DELETE FROM begeniler WHERE begenilen_paylasim_id=:pay AND begenen_id = :id");
                    $sil->execute(array(':pay' => $id, ':id' => $_SESSION['uyeID']));
                    if ($sil) {
                        header("location: anasayfa.php");
                    }
                }
            }

            ?>




        </div>

    </div>

    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!--JavaScript dosyaso dahil edildi-->
</body>

</html>
<?php ob_flush(); ?>