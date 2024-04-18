<?php
session_start();

// Verificar si la cookie 'userloged' está establecida
if(isset($_COOKIE['userloged'])) {
    $userloged = $_COOKIE['userloged'];
    
    // Verificar si el usuario es un administrador
    if($userloged === 'admin') {
        // Si el usuario es un administrador, mostrar el menú
 ?>
<html>
<head>
    <title>PÀGINA WEB DEL MENÚ PRINCIPAL DE L'APLICACIÓ D'ACCÉS A BASES DE DADES LDAP</title>
</head>
<body>
<h1>Benvingut <?php echo $userloged; ?>!</h1>
<h2>MENÚ PRINCIPAL DE L'APLICACIÓ D'ACCÉS A BASES DE DADES LDAP</h2>
<a href="crear.php"><b>Crear un usuari</b></a><br>
<a href="eliminar.php"><b>Eliminar un usuari</b></a><br>
<a href="modificar.php"><b>Modificar un usuari</b></a><br>
<a href="consultar.php"><b>Consultar usuari</b></a><br>
<br>
<a href="logout.php">Tancar sessió</a>
</body>
</html>
<?php
            exit;
        } else {
            header("Location: login.php");
            exit;
        }
    } else {
        header("Location: login.php");
        exit;
    }
?>
