<?php 

     include ("conn.php");
     if(isset($_POST["kullaniciAdi"]) && isset($_POST["password"]) ){
          if(trim($_POST["kullaniciAdi"])!="" && trim($_POST["password"])!=""){
               $kullaniciAdi = $_POST["kullaniciAdi"];
               $password = $_POST["password"];
               $query = $db->prepare("INSERT INTO uyeler SET
               username = ?,
               password = ?");
               $insert = $query->execute(array(
                    $kullaniciAdi,$password
               ));
               if ( $insert ){
               $last_id = $db->lastInsertId();
               echo "<script> window.location.href = 'index.php'; alert('Kayıt İşlemi Başarılı!');</script>";
               }
          }else{
               echo "<script> window.location.href = 'register.php'; alert('Eksik alanlar var. Lütfen bilgileri eksiksiz doldurunuz.');</script>";
           }
               
     }

?>