<?php

$page_title = "Mi Perfil";

include_once($_SERVER['DOCUMENT_ROOT']."/nexuscare/tools/header.php");
include_once($_SERVER['DOCUMENT_ROOT']."/nexuscare/tools/navbar.php");
include_once($_SERVER['DOCUMENT_ROOT']."/nexuscare/tools/sidebar.php");

?>

<main class="app-main">

    <div class="app-content-header">

        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">

                    <h3 class="mb-0">

                        Nexus Care - <?php echo $page_title; ?>

                    </h3>

                    <p class="text-muted">

                        Administra tu información personal.

                    </p>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-end">

                        <li class="breadcrumb-item">

                            <a href="/nexuscare">

                                Inicio

                            </a>

                        </li>

                        <li class="breadcrumb-item active">

                            Perfil

                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>


    <div class="app-content">

        <div class="container-fluid">

            <?php

            include_once("perfilVista.php");

            ?>

        </div>

    </div>

</main>

<?php

include_once($_SERVER['DOCUMENT_ROOT']."/nexuscare/tools/footer.php");

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="/nexuscare/modulos/perfil/perfilFunciones.js"></script>