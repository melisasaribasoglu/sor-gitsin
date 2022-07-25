<?php // veri tabanı bağlantısı
	$ip = "localhost"; //host
	$user = "root";  // host id
	$password = "";  // password local olduğu için varsayılan şifre boş
	$db = "sorucevap"; // db adı
	
	//bağlantı
	try{
		$db = new PDO("mysql:host=$ip;dbname=$db",$user,$password);
		// türkçe karakter için utf8
		$db->exec("SET CHARSET UTF8");
		//eğer hata olursa pdo nun exception komutu ile ekrana yazdırıyoruz
	}catch(PDOException $e){
		die ("<h1>Veri Tabanı Bağlantı Hatası</h1>");
	}
?>