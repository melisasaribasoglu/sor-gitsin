<?php 


include ("conn.php");

if(isset($_POST["kullaniciAdi"]) && isset($_POST["password"]) ){
if(trim($_POST["kullaniciAdi"])!="" && trim($_POST["password"])!=""){
		$kAdi =$_POST["kullaniciAdi"];
		$pass =$_POST["password"];

		$query  = $db->prepare("SELECT * FROM uyeler WHERE username=:k AND password=:p");
		$query->execute(array(':k'=>$kAdi,':p'=>$pass));
		if ( $query -> rowCount() ){

				$row = $query->fetch(PDO::FETCH_ASSOC);
				session_start();
				$_SESSION['oturum']=true;
				$_SESSION['name']=$kAdi;
				$_SESSION['pass']=$pass;
				$_SESSION['uyeID']=$row["id"];
                header('Location: anasayfa.php');
                exit;
				
			}
		}else{
            echo "<script> window.location.href = 'index.php'; alert('Kullanıcı Adı ve Şifre bilgileriniz eksik ya da hatalı. Lütfen Tekrar deneyiniz.');</script>";
        }
    }

?>