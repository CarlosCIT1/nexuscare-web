<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/permisos.php');
$page = $_GET['page'] ?? 'dashboard';
$page = trim($page);
function cargarModuloSeguro($rutaModulo, $requiereAcceso = null)
{
    if ($requiereAcceso !== null && !tieneAcceso($requiereAcceso)) {
        include("modulos/home/index.php");
        exit;
    }
    if (file_exists($rutaModulo)) {
        include($rutaModulo);
    } else {
        include("modulos/home/index.php");
    }
    exit;
}
if ($page == "dashboard" || $page == "home" || $page == "") {
    include("modulos/home/index.php");
    exit;
}
if ($page == "perfil") {
    if (file_exists("modulos/perfil/index.php")) {
        include("modulos/perfil/index.php");
    } else {
        include("modulos/home/index.php");
    }
    exit;
}
if ($page == "contacto") {
    if (file_exists("modulos/contacto/index.php")) {
        include("modulos/contacto/index.php");
    } else {
        include("modulos/home/index.php");
    }
    exit;
}
if ($page == "usuarios") {
    cargarModuloSeguro("modulos/usuarios/index.php", "usuarios");
}
if ($page == "roles") {
    cargarModuloSeguro("modulos/roles/index.php", "roles");
}
if ($page == "especialidades") {
    cargarModuloSeguro("modulos/especialidades/index.php", "categorias");
}
if ($page == "serviciosMedicos") {
    cargarModuloSeguro("modulos/serviciosMedicos/index.php", "productos");
}
if ($page == "citas") {
    cargarModuloSeguro("modulos/citas/index.php", "citas");
}
if ($page == "pacientes") {
    cargarModuloSeguro("modulos/pacientes/index.php", "pacientes");
}
if ($page == "recetas") {
    cargarModuloSeguro("modulos/recetas/index.php", "recetas");
}
if ($page == "reportes") {
    cargarModuloSeguro("modulos/reportes/index.php", "reportes");
}
include("modulos/home/index.php");
exit;

?>
