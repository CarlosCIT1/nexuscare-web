<?php

require_once("tools/mypathdb.php");

/* DATOS */
$email      = trim($_POST['email'] ?? '');
$password   = trim($_POST['password'] ?? '');
$confirmar  = trim($_POST['confirmar'] ?? '');

/* VALIDAR */
if ($email == '' || $password == '' || $confirmar == '') {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Debe completar todos los campos"
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "El correo electrónico no es válido"
    ]);
    exit;
}

if ($password !== $confirmar) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Las contraseñas no coinciden"
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "La contraseña debe tener al menos 6 caracteres"
    ]);
    exit;
}

try {
    /* BUSCAR USUARIO + ROL */
    $stmt = $conn->prepare(
        "SELECT u.id, r.nombre AS rol
         FROM usuarios u
         INNER JOIN roles r ON u.rolid = r.id
         WHERE u.email = :email
           AND u.status = 1
         LIMIT 1"
    );
    $stmt->execute([":email" => $email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "No existe un usuario activo con ese correo"
        ]);
        exit;
    }

    $rol = strtolower(trim($data['rol']));

    /* SOLO PACIENTE Y MEDICO */
    if ($rol != 'paciente' && $rol != 'medico' && $rol != 'médico') {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "Solo pacientes y médicos pueden recuperar contraseña desde este apartado"
        ]);
        exit;
    }

    /* ENCRIPTAR NUEVA CONTRASEÑA */
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /* ACTUALIZAR */
    $stmtUpdate = $conn->prepare(
        "UPDATE usuarios
         SET password = :password
         WHERE id = :id"
    );
    $stmtUpdate->execute([
        ":password" => $passwordHash,
        ":id"       => intval($data['id'])
    ]);

    if ($stmtUpdate->rowCount() > 0) {
        echo json_encode([
            "exito"   => 1,
            "mensaje" => "Contraseña actualizada correctamente"
        ]);
    } else {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "No se pudo actualizar la contraseña"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Error al actualizar contraseña: " . $e->getMessage()
    ]);
}
?>