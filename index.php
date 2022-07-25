<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Giriş Sayfası</title>
 
<link rel="stylesheet" href="style.css">
</head>
<body>
<a href="register.php"><button class="redirectButton">Kayıt Ol</button></a>
    <form action="girisIsle.php" method="post">
        <h3>Giriş Yap</h3>

        <label for="kullaniciAdi">Kullanıcı Adı</label>
        <input type="text" placeholder="Kullanıcı Adı" name="kullaniciAdi" id="kullaniciAdi">

        <label for="password">Şifre</label>
        <input type="password" placeholder="Şifre" id="password" name="password">

        <input type="submit" value="Giriş Yap" class="button">
        
    </form>
</body>
</html>
