<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Gestión de Usuarios";

include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/tools/header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/tools/navbar.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/tools/sidebar.php");
?>

<main class="app-main">

    <!-- Encabezado -->
    <div class="app-content-header">
        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">
                        Nexus Care - <?= $page_title; ?>
                    </h3>

                    <p class="text-muted mb-0">
                        Administración de pacientes, médicos y administradores del sistema.
                    </p>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">
                            <a href="/mi_proyecto">Inicio</a>
                        </li>

                        <li class="breadcrumb-item active">
                            <?= $page_title; ?>
                        </li>

                    </ol>

                </div>

            </div>

        </div>
    </div>

    <!-- Contenido -->
    <div class="app-content">

        <div class="container-fluid">

            <div class="row">

                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-header d-flex justify-content-between align-items-center">

                            <div>
                                <h5 class="mb-1">
                                    Usuarios registrados
                                </h5>

                                <small class="text-muted">
                                    Aquí puedes registrar, editar y administrar usuarios del sistema Nexus Care.
                                </small>
                            </div>

                            <div>
                                <?php
                                include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/modulos/usuarios/usuariosModal.php");
                                ?>
                            </div>

                        </div>

                        <div class="card-body">

                            <?php
                            include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/modulos/usuarios/usuariosTabla.php");
                            ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/mi_proyecto/tools/footer.php");
?>

<!-- Librerías -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<!-- Funciones del módulo -->
<script src="/mi_proyecto/modulos/usuarios/usuariosFunciones.js"></script>