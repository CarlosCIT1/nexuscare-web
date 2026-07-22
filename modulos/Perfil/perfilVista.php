<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['id'])){
    header("Location: /mi_proyecto/login.php");
    exit;
}

$idUsuario = intval($_SESSION['id']);

try {
    $stmt = $conn->prepare("SELECT
            nombre,
            direccion,
            telefono,
            email,
            foto
        FROM usuarios
        WHERE id = :idUsuario
        LIMIT 1");
    $stmt->execute([
        ':idUsuario' => $idUsuario
    ]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo '
        <div class="alert alert-danger">
            No se encontró la información del usuario.
        </div>';
        exit;
    }
} catch (PDOException $e) {
    die("Error SQL: " . $e->getMessage());
}

/*
FOTO DE PERFIL
*/

$foto = "default.png";

if(
    !empty($usuario["foto"]) &&
    file_exists($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/uploads/perfiles/".$usuario["foto"])
){
    $foto = $usuario["foto"];
}

/*
PERMISOS SEGÚN EL ROL
*/

$rol = strtolower(trim($_SESSION['rol'] ?? ''));

/*
Paciente:
- Puede editar todo.
- Puede cambiar foto.
- Puede cambiar contraseña.
*/
$soloLectura = false;
$puedePassword = true;

if($rol == "administrador"){

    // Solo cambia foto
    $soloLectura = true;
    $puedePassword = false;

}elseif($rol == "medico" || $rol == "médico"){

    // Solo cambia foto y contraseña
    $soloLectura = true;
    $puedePassword = true;

}

?>

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h4 class="mb-0">

                    <i class="bi bi-person-circle"></i>

                    Mi Perfil

                </h4>

            </div>

            <div class="card-body">

                <form
                    id="formPerfil"
                    enctype="multipart/form-data">

                    <div class="text-center mb-4">

                        <img
                            id="previewFoto"
                            src="/mi_proyecto/uploads/perfiles/<?php echo $foto; ?>"
                            class="rounded-circle shadow"
                            style="width:180px;height:180px;object-fit:cover;border:4px solid #dee2e6;">

                    </div>

                    <div class="mb-4">

                        <label class="form-label">

                            Fotografía de perfil

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            id="foto"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp">

                        <small class="text-muted">

                            Formatos permitidos:
                            JPG, PNG y WEBP.
                            Máximo 2 MB.

                        </small>

                    </div>

                    <hr>
                                        <!-- NOMBRE -->

                    <div class="mb-3">

                        <label class="form-label">

                            Nombre completo

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nombre"
                            name="nombre"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($usuario["nombre"]); ?>"
                            <?php echo $soloLectura ? "readonly" : ""; ?>
                            required>

                    </div>


                    <!-- DIRECCIÓN -->

                    <div class="mb-3">

                        <label class="form-label">

                            Dirección

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="direccion"
                            name="direccion"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($usuario["direccion"]); ?>"
                            <?php echo $soloLectura ? "readonly" : ""; ?>
                            required>

                    </div>


                    <!-- TELÉFONO -->

                    <div class="mb-3">

                        <label class="form-label">

                            Teléfono

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="telefono"
                            name="telefono"
                            maxlength="20"
                            value="<?php echo htmlspecialchars($usuario["telefono"]); ?>"
                            <?php echo $soloLectura ? "readonly" : ""; ?>
                            required>

                    </div>


                    <!-- CORREO -->

                    <div class="mb-4">

                        <label class="form-label">

                            Correo electrónico

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="100"
                            value="<?php echo htmlspecialchars($usuario["email"]); ?>"
                            <?php echo $soloLectura ? "readonly" : ""; ?>
                            required>

                    </div>

                    <hr>

<?php if($puedePassword){ ?>

                    <h5 class="mb-3">

                        Cambiar contraseña (Opcional)

                    </h5>

                    <div class="mb-3">

                        <label class="form-label">

                            Nueva contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Dejar vacío para conservar la contraseña actual">

                    </div>

                    <div class="mb-4">

                        <label class="form-label">

                            Confirmar contraseña

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="confirmar"
                            name="confirmar"
                            placeholder="Repita la nueva contraseña">

                    </div>

<?php } ?>
                    <div class="text-end">

<?php if($soloLectura){ ?>

                        <button
                            type="submit"
                            id="btnGuardarPerfil"
                            class="btn btn-primary">

                            <i class="bi bi-camera"></i>

                            Actualizar foto

                        </button>

<?php }else{ ?>

                        <button
                            type="submit"
                            id="btnGuardarPerfil"
                            class="btn btn-success">

                            <i class="bi bi-floppy"></i>

                            Guardar cambios

                        </button>

<?php } ?>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<?php

?>