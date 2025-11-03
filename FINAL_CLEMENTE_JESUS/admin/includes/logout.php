<?php
require_once("sessions.php");
$sesion = new Sessions();
$sesion->cerrarSesion();
header("Location: ../../login.php");
exit();
?>