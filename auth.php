<?php
require 'vendor/autoload.php';
use Laminas\Ldap\Ldap;

ini_set('display_errors', 0);

if ($_POST['cts'] && $_POST['adm']) {
    $opcions = [
        'host' => 'zends-orbaam',
        'username' => "cn=admin,dc=fjeclot,dc=net",
        'password' => 'fjeclot',
        'bindRequiresDn' => true,
        'accountDomainName' => 'fjeclot.net',
        'baseDn' => 'dc=fjeclot,dc=net',
    ];
    $ldap = new Ldap($opcions);
    $dn = 'cn=' . $_POST['adm'] . ',dc=fjeclot,dc=net';
    $ctsnya = $_POST['cts'];
    try {
        $ldap->bind($dn, $ctsnya);
        
        // Si la autenticación es exitosa, establece una cookie que expire en 1 hora
        $expiry = time() + 3600; // 1 hora
        setcookie('userloged', $_POST['adm'], $expiry, '/');
        header("location: menu.php");
    } catch (Exception $e) {
        echo "<b>Contrasenya incorrecta</b><br><br>";
    }
}
?>
<html>
<head>
    <title>AUTENTICACIÓ AMB LDAP</title>
</head>
<body>
    <a href="https://zends-orbaam/projecte/index.php">Torna a la pàgina inicial</a>
</body>
</html>
