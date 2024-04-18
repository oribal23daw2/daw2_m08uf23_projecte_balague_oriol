<?php
session_start();

if(isset($_COOKIE['userloged'])) {
    $userloged = $_COOKIE['userloged'];
} else {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
use Laminas\Ldap\Attribute;
use Laminas\Ldap\Ldap;

ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['uid']) && isset($_POST['unitat_organitzativa'])) {
        $uid = $_POST['uid'];
        $unitat_organitzativa = $_POST['unitat_organitzativa'];
        
        $atributo = $_POST['atributo'];
        if ($atributo) {
            $opciones = [
                'host' => 'zends-orbaam',
                'username' => 'cn=admin,dc=fjeclot,dc=net',
                'password' => 'fjeclot',
                'bindRequiresDn' => true,
                'accountDomainName' => 'fjeclot.net',
                'baseDn' => 'dc=fjeclot,dc=net',
            ];
            
            $ldap = new Ldap($opciones);
            $ldap->bind();
            
            $dn = 'uid='.$uid.',ou='.$unitat_organitzativa.',dc=fjeclot,dc=net';
            
            $entrada = $ldap->getEntry($dn);
            if ($entrada) {
                $valor_actual = isset($entrada[$atributo][0]) ? $entrada[$atributo][0] : "";
                
                if (isset($_POST['nuevo_valor'])) {
                    $nuevo_valor = $_POST['nuevo_valor'];
                    Attribute::setAttribute($entrada, $atributo, $nuevo_valor);
                    $ldap->update($dn, $entrada);
                    echo "<script>alert('Atribut $atributo modificat amb èxit');</script>";
                } else {
                    echo "<script>alert('No s'ha proporcionat un nou valor al atribut $atributo');</script>";
                }
            } else {
                echo "<script>alert('L'entrada no existeix');</script>";
            }
        } else {
            echo "<script>alert('No s'ha seleccionat un atribut a modificar);</script>";
        }
    } else {
        echo "<script>alert('Falta algún paràmetre');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuari LDAP</title>
</head>
<body>
	<h1>Benvingut <?php echo $userloged; ?>!</h1>
    <h2>Modificar Usuario LDAP</h2>
   <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="uid">UID:</label>
    <input type="text" id="uid" name="uid"><br><br>
    <label for="unitat_organitzativa">Unitat organitzativa:</label>
    <select id="unitat_organitzativa" name="unitat_organitzativa">
        <option value="usuaris">usuaris</option>
        <option value="desenvolupadors">desenvolupadors</option>
        <option value="administradors">administradors</option>
    </select><br><br>
    <p>Selecciona el atributo a modificar:</p>
    <input type="radio" id="uidNumber" name="atributo" value="uidNumber">
    <label for="uidNumber">uidNumber</label><br>
    <input type="radio" id="gidNumber" name="atributo" value="gidNumber">
    <label for="gidNumber">gidNumber</label><br>
    <input type="radio" id="homeDirectory" name="atributo" value="homeDirectory">
    <label for="homeDirectory">Directorio personal</label><br>
    <input type="radio" id="loginshell" name="atributo" value="loginshell">
    <label for="loginshell">Shell</label><br>
    <input type="radio" id="cn" name="atributo" value="cn">
    <label for="cn">cn</label><br>
    <input type="radio" id="sn" name="atributo" value="sn">
    <label for="sn">sn</label><br>
    <input type="radio" id="givenName" name="atributo" value="givenName">
    <label for="givenName">givenName</label><br>
    <input type="radio" id="postalAddress" name="atributo" value="postalAddress">
    <label for="postalAddress">PostalAdress</label><br>
    <input type="radio" id="mobile" name="atributo" value="mobile">
    <label for="mobile">mobile</label><br>
    <input type="radio" id="telephoneNumber" name="atributo" value="telephoneNumber">
    <label for="telephoneNumber">telephoneNumber</label><br>
    <input type="radio" id="title" name="atributo" value="title">
    <label for="title">title</label><br>
    <input type="radio" id="description" name="atributo" value="description">
    <label for="description">description</label><br><br>
    <label for="nuevo_valor">Nou valor:</label><br>
    <input type="text" id="nuevo_valor" name="nuevo_valor"><br><br>
    <input type="submit" value="Modificar">
</form>
<br>
    <a href="menu.php">Tornar al menú principal</a><br>
    <a href="logout.php">Tancar Sessió</a>
</body>
</html>
