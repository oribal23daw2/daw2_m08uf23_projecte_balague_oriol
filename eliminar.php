<?php
session_start();

if(isset($_COOKIE['userloged'])) {
    $userloged = $_COOKIE['userloged'];
} else {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
use Laminas\Ldap\Ldap;

if(isset($_COOKIE['userloged']) && $_COOKIE['userloged'] === 'admin') {
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['uid']) && isset($_POST['unitat_organitzativa'])) {
            
            $uid = $_POST['uid'];
            $unitat_organitzativa = $_POST['unitat_organitzativa'];
            
            $domini = 'dc=fjeclot,dc=net';
            $opcions = [
                'host' => 'zends-orbaam',
                'username' => "cn=admin,$domini",
                'password' => 'fjeclot',
                'bindRequiresDn' => true,
                'accountDomainName' => 'fjeclot.net',
                'baseDn' => 'dc=fjeclot,dc=net',
            ];
            
            try {
                $ldap = new Ldap($opcions);
                $ldap->bind();
                
                $dn = 'uid=' . $uid . ',ou=' . $unitat_organitzativa . ',' . $domini;
                
                $ldap->delete($dn);
                
                echo "<script>alert('Usuari $uid eliminat amb èxit.');</script>";
            } catch (Laminas\Ldap\Exception\LdapException $e) {
                echo "<script>alert('Error: $e');</script>";
            }
        } else {
            echo "<script>alert('Falta algún paràmetre.');</script>";
        }
    }
} else {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuari LDAP</title>
</head>
<body>
	<h1>Benvingut <?php echo $userloged; ?>!</h1>
    <h2>Eliminar Usuari LDAP</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid"><br><br>
        <label for="unitat_organitzativa">Unitat organitzativa:</label>
        <select id="unitat_organitzativa" name="unitat_organitzativa">
            <option value="usuaris">usuaris</option>
            <option value="desenvolupadors">desenvolupadors</option>
            <option value="administradors">administradors</option>
        </select><br><br>
        <input type="submit" value="Eliminar Usuari">
    </form>
    <br>
    <a href="menu.php">Tornar al menú principal</a><br>
    <a href="logout.php">Tancar sessió</a>
</body>
</html>
