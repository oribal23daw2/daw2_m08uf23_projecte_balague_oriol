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
use Laminas\Ldap\Ldap; // Importar la clase Ldap

// Verificar si la cookie 'userloged' está establecida y tiene el valor 'admin'
if(isset($_COOKIE['userloged']) && $_COOKIE['userloged'] === 'admin') {
    // Si el usuario es un administrador, continuar con el borrado de usuarios
    
    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si se han enviado los parámetros uid y unitat_organitzativa
        if(isset($_POST['uid']) && isset($_POST['unitat_organitzativa'])) {
            // Obtener los valores de los parámetros
            $uid = $_POST['uid'];
            $unitat_organitzativa = $_POST['unitat_organitzativa'];
            
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
            
            // Intentar eliminar el usuario de LDAP
            try {
                // Crear conexión LDAP
                $ldap = new Ldap($opcions); // Crear una instancia de la clase Ldap
                $ldap->bind();
                
                // Crear el DN del usuario a eliminar
                $dn = 'uid=' . $uid . ',ou=' . $unitat_organitzativa . ',' . $domini;
                
                // Eliminar el usuario del directorio LDAP
                $ldap->delete($dn);
                
                // Mostrar mensaje de éxito
                echo "<script>alert('Usuari $uid eliminat amb èxit.');</script>";
            } catch (Laminas\Ldap\Exception\LdapException $e) {
                // Si hay un error al eliminar el usuario, mostrar mensaje de error
                echo "<script>alert('Error: $e');</script>";
            }
        } else {
            // Si falta algún parámetro, mostrar un mensaje de error
            echo "<script>alert('Falta algún parámetro.');</script>";
        }
    }
} else {
    // Si el usuario no es un administrador, redirigir a la página de inicio de sesión
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
