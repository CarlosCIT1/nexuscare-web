<?php

$page_title = "Servicios Médicos";

include_once($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/tools/header.php");
include_once($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/tools/navbar.php");
include_once($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/tools/sidebar.php");

?>

<main class="app-main">

    <!-- ENCABEZADO -->
    <div class="app-content-header">

        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">

                    <h3 class="mb-0">

                        Nexus Care - <?php echo $page_title; ?>

                    </h3>

                    <p class="text-muted mb-0">

                        Administración de Servicios Médicos

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


    <!-- CONTENIDO -->
    <div class="app-content">

        <div class="container-fluid">

            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        Servicios Médicos registrados

                    </h5>

                    <?php include_once(__DIR__."/serviciosMedicosModal.php"); ?>

                </div>

                <div class="card-body">

                    <?php include_once(__DIR__."/serviciosMedicosTabla.php"); ?>

                </div>

            </div>

        </div>

    </div>

</main>

<?php

include_once($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/tools/footer.php");

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="/mi_proyecto/modulos/serviciosMedicos/serviciosMedicosFunciones.js?v=1"></script>