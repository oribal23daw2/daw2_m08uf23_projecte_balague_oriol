<?php
session_start();

// Verificar si la cookie 'userloged' está establecida
if(isset($_COOKIE['userloged'])) {
    $userloged = $_COOKIE['userloged'];
} else {
    // Si la cookie no está establecida, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
use Laminas\Ldap\Attribute;
use Laminas\Ldap\Ldap;

ini_set('display_errors', 0);

// Obtener los valores del formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    $unorg = $_POST['unitat_organitzativa'];
    $num_id = $_POST['uidNumber'];
    $grup = $_POST['gidNumber'];
    $dir_pers = $_POST['Directori_personal'];
    $sh = $_POST['Shell'];
    $cn = $_POST['cn'];
    $sn = $_POST['sn'];
    $nom = $_POST['givenName'];
    $adressa = $_POST['PostalAdress'];
    $mobil = $_POST['mobile'];
    $telefon = $_POST['telephoneNumber'];
    $titol = $_POST['title'];
    $descripcio = $_POST['description'];
    
    // Configuración de conexión LDAP
    $domini = 'dc=fjeclot,dc=net';
    $opcions = [
        'host' => 'zends-orbaam',
        'username' => "cn=admin,$domini",
        'password' => 'fjeclot',
        'bindRequiresDn' => true,
        'accountDomainName' => 'fjeclot.net',
        'baseDn' => 'dc=fjeclot,dc=net',
    ];
    
    // Conexión LDAP
    $ldap = new Ldap($opcions);
    $ldap->bind();
    
    // Creación de la nueva entrada en el directorio LDAP
    $nova_entrada = [];
    Attribute::setAttribute($nova_entrada, 'objectClass', ['inetOrgPerson', 'organizationalPerson', 'person', 'posixAccount', 'shadowAccount', 'top']);
    Attribute::setAttribute($nova_entrada, 'uid', $uid);
    Attribute::setAttribute($nova_entrada, 'uidNumber', $num_id);
    Attribute::setAttribute($nova_entrada, 'gidNumber', $grup);
    Attribute::setAttribute($nova_entrada, 'homeDirectory', $dir_pers);
    Attribute::setAttribute($nova_entrada, 'loginShell', $sh);
    Attribute::setAttribute($nova_entrada, 'cn', $cn);
    Attribute::setAttribute($nova_entrada, 'sn', $sn);
    Attribute::setAttribute($nova_entrada, 'givenName', $nom);
    Attribute::setAttribute($nova_entrada, 'mobile', $mobil);
    Attribute::setAttribute($nova_entrada, 'postalAddress', $adressa);
    Attribute::setAttribute($nova_entrada, 'telephoneNumber', $telefon);
    Attribute::setAttribute($nova_entrada, 'title', $titol);
    Attribute::setAttribute($nova_entrada, 'description', $descripcio);
    $dn = 'uid=' . $uid . ',ou=' . $unorg . ',dc=fjeclot,dc=net';
    if ($ldap->add($dn, $nova_entrada)) {
        echo "<script>alert('Usuari creat');</script>";
    }
}
?>

<html>
<head>
    <title>PÀGINA WEB DEL MENÚ PRINCIPAL DE L'APLICACIÓ D'ACCÉS A BASES DE DADES LDAP</title>
</head>
<body>
<h1>Benvingut <?php echo $userloged; ?>!</h1>
<h2>Creació d'usuaris D'ACCÉS A BASES DE DADES LDAP</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    uid: <input type="text" name="uid"><br>
    <label for="unitat_organitzativa">Unitat organitzativa:</label>
        <select id="unitat_organitzativa" name="unitat_organitzativa">
            <option value="usuaris">usuaris</option>
            <option value="desenvolupadors">desenvolupadors</option>
            <option value="administradors">administradors</option>
        </select><br>
    uidNumber: <input type="text" name="uidNumber"><br>
    gidNumber: <input type="text" name="gidNumber"><br>
    Directori personal: <input type="text" name="Directori_personal"><br>
    Shell: <input type="text" name="Shell"><br>
    cn: <input type="text" name="cn"><br>
    sn: <input type="text" name="sn"><br>
    givenName: <input type="text" name="givenName"><br>
    PostalAdress: <input type="text" name="PostalAdress"><br>
    mobile: <input type="text" name="mobile"><br>
    telephoneNumber: <input type="text" name="telephoneNumber"><br>
    title: <input type="text" name="title"><br>
    description: <input type="text" name="description"><br>
    <input type="submit" value="Crear usuari">
</form><br>
<a href="menu.php">Tornar al menú principal</a><br>
<a href="logout.php">Tancar sessió</a>
</body>
</html>
