<?php

if(isset($_COOKIE['userloged'])) {
    $userloged = $_COOKIE['userloged'];
} else {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';
use Laminas\Ldap\Ldap;

$domain = 'dc=fjeclot,dc=net';
$options = [
    'host' => 'zends-orbaam',
    'username' => "cn=admin,$domain",
    'password' => 'fjeclot',
    'bindRequiresDn' => true,
    'accountDomainName' => 'fjeclot.net',
    'baseDn' => $domain,
];

$ldap = new Ldap($options);
$ldap->bind();

$error_message = '';
$user_attributes = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['uid']) && isset($_POST['unitat_organitzativa'])) {
        $uid = htmlspecialchars($_POST['uid']);
        $unitat_organitzativa = htmlspecialchars($_POST['unitat_organitzativa']);
        
        try {
            $dn = "uid=$uid,ou=$unitat_organitzativa,$domain";
            $user_entry = $ldap->getEntry($dn);
            if ($user_entry) {
                foreach ($user_entry as $attribute => $values) {
                    if ($attribute != "objectclass" && $attribute != "dn") {  // Excluir objectclass y dn
                        if (is_array($values)) {
                            $user_attributes[$attribute] = $values;
                        } else {
                            $user_attributes[$attribute] = [$values]; // Convierte valores no array en array
                        }
                    }
                }
            } else {
                $error_message = "L'usuari no s'ha trobat al directori LDAP.";
            }
        } catch (Laminas\Ldap\Exception\LdapException $e) {
            $error_message = "Error al connectar amb el servidor LDAP: " . $e->getMessage();
        }
    } else {
        $error_message = "Falta algún parámetro.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuari LDAP</title>
</head>
<body>
	<h1>Benvingut <?php echo $userloged; ?>!</h1>
    <h2>Buscar Usuari LDAP</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="uid">UID de l'usuari:</label>
        <input type="text" id="uid" name="uid" required><br><br>
        <label for="unitat_organitzativa">Unitat Organitzativa:</label>
        <select id="unitat_organitzativa" name="unitat_organitzativa" required>
            <option value="usuaris">usuaris</option>
            <option value="desenvolupadors">desenvolupadors</option>
            <option value="administradors">administradors</option>
        </select><br><br>
        <input type="submit" value="Buscar">
    </form>
    <br>
    <a href="menu.php">Tornar al menú principal</a><br>
    <a href="logout.php">Tancar Sessió</a>
    <br>
    <?php if (!empty($user_attributes)): ?>
        <h2>Dades de l'usuari</h2>
        <div>
            <?php foreach ($user_attributes as $attribute => $values): ?>
                <strong><?php echo $attribute; ?>:</strong>
                <?php foreach ($values as $value): ?>
                    <?php echo $value; ?><br>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <p>Error: <?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html>