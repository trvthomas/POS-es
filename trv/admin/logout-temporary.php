<?php include $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";

setcookie($prefixCoookie . "TemporaryIdUser", "", time() - 3600, "/");
setcookie($prefixCoookie . "TemporaryUsernameUser", "", time() - 3600, "/");
header('Location:/trv/home.php');
exit("Sesión cerrada");
?>