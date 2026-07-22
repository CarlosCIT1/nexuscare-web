<?php

$page_title = "Pacientes";

include_once("tools/header.php");
include_once("tools/navbar.php");
include_once("tools/sidebar.php");

?>

<main class="app-main">

    <div class="app-content-header">

        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">

                    <h3 class="mb-0">

                        Nexus Care - <?php echo $page_title; ?>

                    </h3>

                    <p class="text-muted mb-0">

                        Consulta de pacientes asignados al médico.

                    </p>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">

                            <a href="/mi_proyecto">

                                Inicio

                            </a>

                        </li>

                        <li class="breadcrumb-item active">

                            <?php echo $page_title; ?>

                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>

    <div class="app-content">

        <div class="container-fluid">

            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="card-title mb-1">

                            Pacientes

                        </h5>

                        <small class="text-muted">

                            Pacientes con citas registradas.
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <?php include_once("modulos/pacientes/pacientesTabla.php"); ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once("modulos/pacientes/pacientesModal.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="/mi_proyecto/modulos/pacientes/pacientesFunciones.js"></script>
<?php include_once("tools/footer.php"); ?>