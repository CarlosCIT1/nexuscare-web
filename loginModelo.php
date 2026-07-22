<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("tools/mypathdb.php");

header('Content-Type: application/json; charset=utf-8');

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    echo json_encode([
        "error" => 1,
        "mensaje" => "Debe completar correo y contraseña"
    ]);
    exit;
}

// Cambiado
try {
    $stmtUsuario = $conn->prepare("SELECT id, nombre, rolid FROM fn_login_usuario(:email)");
    $stmtUsuario->execute([":email" => $email]);
    $usuarioBase = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioBase) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "El correo no existe o el usuario está inactivo"
        ]);
        exit;
    }

    $stmtDetalle = $conn->prepare("SELECT id, nombre, email, password, rolid, foto, status FROM usuarios WHERE id = :id AND status = 1 LIMIT 1");
    $stmtDetalle->execute([":id" => intval($usuarioBase['id'])]);
    $usuario = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "El correo no existe o el usuario está inactivo"
        ]);
        exit;
    }

    if (!password_verify($password, $usuario['password'])) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "La contraseña es incorrecta"
        ]);
        exit;
    }

    $rolid = intval($usuario['rolid']);

    $stmtRol = $conn->prepare("SELECT id, nombre, accesos FROM roles WHERE id = :rolid AND status = 1 LIMIT 1");
    $stmtRol->execute([":rolid" => $rolid]);
    $rol = $stmtRol->fetch(PDO::FETCH_ASSOC);

    if (!$rol) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "El rol del usuario no existe o está inactivo"
        ]);
        exit;
    }

    $foto = (!empty($usuario["foto"]))
        ? $usuario["foto"]
        : "default.png";

    $accesosRol = isset($rol['accesos']) ? trim((string) $rol['accesos']) : '';
    if ($accesosRol === '' && intval($rol['id']) === 1) {
        $accesosRol = 'dashboard,categorias,productos,citas,pacientes,recetas,reportes,roles,usuarios';
    }

    $_SESSION['id']       = $usuario['id'];
    $_SESSION['usuario']  = $usuario['nombre'];
    $_SESSION['email']    = $usuario['email'];
    $_SESSION['rolid']    = $rol['id'];
    $_SESSION['rol']      = $rol['nombre'];
    $_SESSION['accesos']  = $accesosRol;
    $_SESSION['foto']     = $foto;

    echo json_encode([
        "exito"   => 1,
        "mensaje" => "Inicio de sesión correcto",
        "usuario" => $usuario['nombre'],
        "rol"     => $rol['nombre'],
        "accesos" => $rol['accesos'],
        "foto"    => $foto
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "error" => 1,
        "mensaje" => "Error al consultar usuario: " . $e->getMessage()
    ]);
}

?>