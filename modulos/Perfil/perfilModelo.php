<?php

session_start();

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

header('Content-Type: application/json; charset=utf-8');

/*
| VALIDAR SESIÓN
*/

if (!isset($_SESSION['id'])) {

    echo json_encode([
        "error" => 1,
        "mensaje" => "La sesión ha expirado."
    ]);

    exit;
}

/*
| CREAR CARPETA DE PERFILES
*/

$ruta = $_SERVER['DOCUMENT_ROOT']."/mi_proyecto/uploads/perfiles/";

if (!file_exists($ruta)) {

    mkdir($ruta, 0777, true);

}

/*
| DATOS DE SESIÓN
*/

$idUsuario = intval($_SESSION['id']);
$rol = mb_strtolower(trim($_SESSION['rol']), 'UTF-8');

/*
| RECIBIR DATOS DEL FORMULARIO
*/

$nombre     = trim($_POST['nombre'] ?? '');
$direccion  = trim($_POST['direccion'] ?? '');
$telefono   = trim($_POST['telefono'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = trim($_POST['password'] ?? '');
$confirmar  = trim($_POST['confirmar'] ?? '');

/*
| CONSULTAR USUARIO ACTUAL
*/

try {
    $stmt = $conn->prepare("SELECT *
        FROM usuarios
        WHERE id = :idUsuario
        LIMIT 1");
    $stmt->execute([
        ':idUsuario' => $idUsuario
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "No se encontró el usuario."
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        "error" => 1,
        "mensaje" => "Error al consultar el usuario: " . $e->getMessage()
    ]);
    exit;
}

/*
| DATOS ACTUALES
*/

$nombreActual     = $usuario["nombre"];
$direccionActual  = $usuario["direccion"];
$telefonoActual   = $usuario["telefono"];
$emailActual      = $usuario["email"];
$fotoActual       = $usuario["foto"];

if ($fotoActual == "" || $fotoActual == null) {

    $fotoActual = "default.png";

}

$nuevaFoto = $fotoActual;

/*
| PERMISOS SEGÚN EL ROL
| Paciente:
|   Puede modificar todo.
| Médico:
|   Solo puede cambiar foto y contraseña.
| Administrador:
|   Solo puede cambiar foto.
*/

if ($rol == "administrador") {

    // Mantener siempre los datos originales
    $nombre    = $nombreActual;
    $direccion = $direccionActual;
    $telefono  = $telefonoActual;
    $email     = $emailActual;

    // No permitir cambio de contraseña
    $password = "";
    $confirmar = "";

} elseif ($rol == "medico" || $rol == "médico") {

    // Mantener siempre los datos originales
    $nombre    = $nombreActual;
    $direccion = $direccionActual;
    $telefono  = $telefonoActual;
    $email     = $emailActual;

}

/*
| ESCAPAR DATOS
*/

/*
| VALIDAR CORREO (SOLO PACIENTE)
*/

if ($rol == "paciente") {

    if (
        $nombre == "" ||
        $direccion == "" ||
        $telefono == "" ||
        $email == ""
    ) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "Debe completar todos los campos."
        ]);

        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "Correo electrónico inválido."
        ]);

        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT id
                  FROM usuarios
                  WHERE email = :email
                  AND id <> :idUsuario
                  LIMIT 1");
        $stmt->execute([
            ':email' => $email,
            ':idUsuario' => $idUsuario
        ]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode([
                "error" => 1,
                "mensaje" => "Ese correo ya está registrado."
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "Error al validar el correo: " . $e->getMessage()
        ]);
        exit;
    }

}

/*
| SUBIR FOTO DE PERFIL
*/

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {

    $extension = strtolower(
        pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION)
    );

    $permitidas = [
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    if (!in_array($extension, $permitidas)) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "Solo se permiten imágenes JPG, JPEG, PNG y WEBP."
        ]);

        exit;
    }

    if ($_FILES["foto"]["size"] > (2 * 1024 * 1024)) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "La imagen no puede ser mayor a 2 MB."
        ]);

        exit;
    }

    $nombreFoto = time() . "_" . $idUsuario . "." . $extension;

    if (!move_uploaded_file(
        $_FILES["foto"]["tmp_name"],
        $ruta . $nombreFoto
    )) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "No fue posible guardar la imagen."
        ]);

        exit;
    }

    if (
        $fotoActual != "" &&
        $fotoActual != "default.png" &&
        file_exists($ruta . $fotoActual)
    ) {

        unlink($ruta . $fotoActual);

    }

    $nuevaFoto = $nombreFoto;

}

/*
| VALIDAR CONTRASEÑA
*/

$passwordHash = "";

if ($password != "") {

    if (strlen($password) < 6) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "La contraseña debe tener al menos 6 caracteres."
        ]);

        exit;
    }

    if ($password != $confirmar) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "Las contraseñas no coinciden."
        ]);

        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

}
/*
| ACTUALIZAR SEGÚN EL ROL
*/

$params = [
    ':foto' => $nuevaFoto,
    ':idUsuario' => $idUsuario
];

if ($rol == "paciente") {

    /*
    | PACIENTE
    | Puede actualizar:
    | - Nombre
    | - Direccion
    | - Teléfono
    | - Correo
    | - Foto
    | - Contraseña    
    */

    if ($password != "") {

        $sql = "UPDATE usuarios SET
                    nombre = :nombre,
                    direccion = :direccion,
                    telefono = :telefono,
                    email = :email,
                    password = :password,
                    foto = :foto
                WHERE id = :idUsuario";

        $params[':nombre'] = $nombre;
        $params[':direccion'] = $direccion;
        $params[':telefono'] = $telefono;
        $params[':email'] = $email;
        $params[':password'] = $passwordHash;

    } else {

        $sql = "UPDATE usuarios SET
                    nombre = :nombre,
                    direccion = :direccion,
                    telefono = :telefono,
                    email = :email,
                    foto = :foto
                WHERE id = :idUsuario";

        $params[':nombre'] = $nombre;
        $params[':direccion'] = $direccion;
        $params[':telefono'] = $telefono;
        $params[':email'] = $email;

    }

} elseif ($rol == "medico" || $rol == "médico") {

    /*
    | MÉDICO
    | Solo puede actualizar:
    | - Foto
    | - Contraseña
    */

    if ($password != "") {

        $sql = "UPDATE usuarios SET
                    password = :password,
                    foto = :foto
                WHERE id = :idUsuario";

        $params[':password'] = $passwordHash;

    } else {

        $sql = "UPDATE usuarios SET
                    foto = :foto
                WHERE id = :idUsuario";

    }

} else {

    /*
    | ADMINISTRADOR
    | Solo puede actualizar:
    | - Foto
    */

    $sql = "UPDATE usuarios SET
                foto = :foto
            WHERE id = :idUsuario";

}

/*
| EJECUTAR UPDATE
*/

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
} catch (PDOException $e) {
    echo json_encode([
        "error" => 1,
        "mensaje" => "Error al actualizar el perfil: " . $e->getMessage()
    ]);
    exit;
}
/*
| ACTUALIZAR VARIABLES DE SESIÓN
*/

$_SESSION["foto"] = $nuevaFoto;

if ($rol == "paciente") {

    $_SESSION["usuario"] = $nombre;
    $_SESSION["email"]   = $email;

}

/*
| RESPUESTA EXITOSA
*/

echo json_encode([

    "exito" => 1,

    "mensaje" => "Perfil actualizado correctamente."

]);

?>