<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Kayıt Sayfası</title>
 
<link rel="stylesheet" href="style.css">
</head>
<body>
<a href="index.php"><button class="redirectButton">Giriş</button></a>
    <form action="registerIsle.php" method="post">
        <h3>Üye Ol</h3>

        <label for="kullaniciAdi">Kullanıcı Adı</label>
        <input type="text" placeholder="Kullanıcı Adı" name="kullaniciAdi" id="kullaniciAdi">

        <label for="password">Şifre</label>
        <input type="password" placeholder="Şifre" id="password" name="password">

        <input type="submit" value="Üye Ol" class="button">
        
    </form>
	
</body>
</html>
