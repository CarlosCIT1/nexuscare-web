<?php

$page_title = "Contacto";

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

                    <p class="text-muted">

                        Información de contacto de ISVARIX.

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

                            Contacto

                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>


    <!-- CONTENIDO -->

    <div class="app-content">

        <div class="container-fluid">

            <?php

                include_once("contactoVista.php");

            ?>

        </div>

    </div>

</main>

<?php

include_once($_SERVER['DOCUMENT_ROOT']."/mi_proyecto/tools/footer.php");

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>