<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$page_title = "Inicio";

$idUsuario     = $_SESSION['id'] ?? 0;
$nombreUsuario = $_SESSION['usuario'] ?? 'Usuario';
$rolUsuario    = strtolower(trim($_SESSION['rol'] ?? ''));

$totalUsuarios    = 0;
$totalCategorias  = 0;
$totalProductos   = 0;
$totalCitas       = 0;
$totalReportes    = 0;
$totalPendientes  = 0;
$totalAtendidas   = 0;
$totalCanceladas  = 0;

$ultimasCitas = false;
$ultimosReportes = false;


$existeReportes = false;
$qExisteReportes = $conn->prepare("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'reportes') AS existe");
$qExisteReportes->execute();
$rowExisteReportes = $qExisteReportes->fetch(PDO::FETCH_ASSOC);
if ($rowExisteReportes && !empty($rowExisteReportes['existe'])) {
    $existeReportes = true;
}


if ($rolUsuario == 'administrador') {

    // TOTAL USUARIOS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE status = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUsuarios = intval($row['total'] ?? 0);

    // TOTAL ESPECIALIDADES
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM especialidades WHERE status = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCategorias = intval($row['total'] ?? 0);

    // TOTAL SERVICIOS MÉDICOS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM servicios_medicos WHERE status = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalProductos = intval($row['total'] ?? 0);

    // TOTAL CITAS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCitas = intval($row['total'] ?? 0);

    // CITAS PENDIENTES
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND LOWER(estado) = 'pendiente'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPendientes = intval($row['total'] ?? 0);

    // CITAS ATENDIDAS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND LOWER(estado) = 'atendida'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAtendidas = intval($row['total'] ?? 0);

    // CITAS CANCELADAS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND LOWER(estado) = 'cancelada'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCanceladas = intval($row['total'] ?? 0);

    // REPORTES
    if ($existeReportes) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reportes WHERE status = 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalReportes = intval($row['total'] ?? 0);
    }

    // ÚLTIMAS CITAS GENERALES
   $sqlUltimasCitas = "
    SELECT
        c.id,
        p.nombre AS paciente,
        m.nombre AS medico,
        cat.nombre AS especialidad,
        pr.nombre AS servicio,
        c.fecha_cita,
        c.hora_cita,
        c.estado
    FROM citas c
    LEFT JOIN usuarios p ON c.id_paciente = p.id
    LEFT JOIN usuarios m ON c.id_medico = m.id
    LEFT JOIN especialidades cat ON c.id_especialidad = cat.id
    LEFT JOIN servicios_medicos pr ON c.id_servicio = pr.id
    WHERE c.status = 1
    ORDER BY c.id DESC
    LIMIT 5
";
    $stmt = $conn->prepare($sqlUltimasCitas);
    $stmt->execute();
    $ultimasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ÚLTIMOS REPORTES GENERALES
    if ($existeReportes) {
        $sqlUltimosReportes = "
            SELECT 
                r.id,
                u.nombre AS usuario,
                ro.nombre AS rol,
                r.titulo,
                r.estado,
                r.fecha_reporte
            FROM reportes r
            INNER JOIN usuarios u ON r.id_usuario = u.id
            INNER JOIN roles ro ON u.rolid = ro.id
            WHERE r.status = 1
            ORDER BY r.id DESC
            LIMIT 5
        ";
        $stmt = $conn->prepare($sqlUltimosReportes);
        $stmt->execute();
        $ultimosReportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


if ($rolUsuario == 'medico' || $rolUsuario == 'médico') {

    // TOTAL DE SUS CITAS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_medico = :idUsuario");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCitas = intval($row['total'] ?? 0);

    // CITAS PENDIENTES DEL MÉDICO
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_medico = :idUsuario AND LOWER(estado) = 'pendiente'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPendientes = intval($row['total'] ?? 0);

    // CITAS ATENDIDAS DEL MÉDICO
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_medico = :idUsuario AND LOWER(estado) = 'atendida'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAtendidas = intval($row['total'] ?? 0);

    // CITAS CANCELADAS DEL MÉDICO
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_medico = :idUsuario AND LOWER(estado) = 'cancelada'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCanceladas = intval($row['total'] ?? 0);

    // REPORTES DEL MÉDICO
    if ($existeReportes) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reportes WHERE status = 1 AND id_usuario = :idUsuario");
        $stmt->execute([':idUsuario' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalReportes = intval($row['total'] ?? 0);
    }

    // ÚLTIMAS CITAS DEL MÉDICO
    $sqlUltimasCitas = "
        SELECT 
            c.id,
            p.nombre AS paciente,
            cat.nombre AS especialidad,
            pr.nombre AS servicio,
            c.fecha_cita,
            c.hora_cita,
            c.estado
        FROM citas c
        LEFT JOIN usuarios p ON c.id_paciente = p.id
        LEFT JOIN especialidades cat ON c.id_especialidad = cat.id
        LEFT JOIN servicios_medicos pr ON c.id_servicio = pr.id
        WHERE c.status = 1
          AND c.id_medico = :idUsuario
        ORDER BY c.fecha_cita DESC, c.hora_cita DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($sqlUltimasCitas);
    $stmt->execute([':idUsuario' => $idUsuario]);
    $ultimasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // REPORTES DEL MÉDICO
    if ($existeReportes) {
        $sqlUltimosReportes = "
            SELECT 
                id,
                titulo,
                estado,
                fecha_reporte
            FROM reportes
            WHERE status = 1
              AND id_usuario = :idUsuario
            ORDER BY id DESC
            LIMIT 5
        ";
        $stmt = $conn->prepare($sqlUltimosReportes);
        $stmt->execute([':idUsuario' => $idUsuario]);
        $ultimosReportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/* 
   PACIENTE
 */
if ($rolUsuario == 'paciente') {

    // TOTAL DE SUS CITAS
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_paciente = :idUsuario");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCitas = intval($row['total'] ?? 0);

    // CITAS PENDIENTES DEL PACIENTE
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_paciente = :idUsuario AND LOWER(estado) = 'pendiente'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPendientes = intval($row['total'] ?? 0);

    // CITAS ATENDIDAS DEL PACIENTE
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_paciente = :idUsuario AND LOWER(estado) = 'atendida'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAtendidas = intval($row['total'] ?? 0);

    // CITAS CANCELADAS DEL PACIENTE
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM citas WHERE status = 1 AND id_paciente = :idUsuario AND LOWER(estado) = 'cancelada'");
    $stmt->execute([':idUsuario' => $idUsuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCanceladas = intval($row['total'] ?? 0);

    // REPORTES DEL PACIENTE
    if ($existeReportes) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reportes WHERE status = 1 AND id_usuario = :idUsuario");
        $stmt->execute([':idUsuario' => $idUsuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalReportes = intval($row['total'] ?? 0);
    }

    // ÚLTIMAS CITAS DEL PACIENTE
    $sqlUltimasCitas = "
        SELECT 
            c.id,
            m.nombre AS medico,
            cat.nombre AS especialidad,
            pr.nombre AS servicio,
            c.fecha_cita,
            c.hora_cita,
            c.estado
        FROM citas c
        LEFT JOIN usuarios m ON c.id_medico = m.id
        LEFT JOIN especialidades cat ON c.id_especialidad = cat.id
        LEFT JOIN servicios_medicos pr ON c.id_servicio = pr.id
        WHERE c.status = 1
          AND c.id_paciente = :idUsuario
        ORDER BY c.fecha_cita DESC, c.hora_cita DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($sqlUltimasCitas);
    $stmt->execute([':idUsuario' => $idUsuario]);
    $ultimasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // REPORTES DEL PACIENTE
    if ($existeReportes) {
        $sqlUltimosReportes = "
            SELECT 
                id,
                titulo,
                estado,
                fecha_reporte
            FROM reportes
            WHERE status = 1
              AND id_usuario = :idUsuario
            ORDER BY id DESC
            LIMIT 5
        ";
        $stmt = $conn->prepare($sqlUltimosReportes);
        $stmt->execute([':idUsuario' => $idUsuario]);
        $ultimosReportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

include_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/header.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/navbar.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/sidebar.php');
?>

<main class="app-main">

    <!-- HEADER -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <h3 class="mb-0">Nexus Care - <?php echo $page_title; ?></h3>
                    <p class="text-muted mb-0">
                        Plataforma de gestión médica y administrativa.
                    </p>
                </div>

                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="/mi_proyecto">Inicio</a></li>
                        <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- BIENVENIDA -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="/mi_proyecto/img/nexus-care-logo.png"
                                 alt="Nexus Care"
                                 class="img-fluid"
                                 style="max-width: 220px;">
                        </div>

                        <div class="col-md-9">
                            <h2 class="fw-bold mb-2" style="color:#2f5fb3;">
                                Bienvenido(a), <?php echo htmlspecialchars($nombreUsuario); ?>
                            </h2>

                            <p class="mb-2" style="font-size:16px;">
                                <strong>Nexus Care</strong> es una plataforma web orientada a la gestión
                                de citas médicas, especialidades, servicios médicos y reportes del sistema,
                                diseñada para optimizar la atención al paciente y la administración médica.
                            </p>

                            <div class="alert mb-0" style="background:#f5f9ff; border-left:5px solid #2f5fb3;">
                                <strong>Rol actual:</strong>
                                <?php
                                    if ($rolUsuario == 'administrador') echo "Administrador";
                                    elseif ($rolUsuario == 'medico' || $rolUsuario == 'médico') echo "Médico";
                                    elseif ($rolUsuario == 'paciente') echo "Paciente";
                                    else echo "Usuario";
                                ?>
                                <br>
                                <span class="text-muted">
                                    <?php if ($rolUsuario == 'administrador') { ?>
                                        Desde aquí puedes visualizar el panorama general del sistema Nexus Care.
                                    <?php } elseif ($rolUsuario == 'medico' || $rolUsuario == 'médico') { ?>
                                        Desde aquí puedes revisar tus citas médicas, su estado y tus reportes.
                                    <?php } elseif ($rolUsuario == 'paciente') { ?>
                                        Desde aquí puedes consultar tus citas, su estado y tus reportes realizados.
                                    <?php } ?>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <?php if ($rolUsuario == 'administrador') { ?>

                <!-- TARJETAS -->
                <div class="row">

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Usuarios</h6>
                                <h2 class="fw-bold"><?php echo $totalUsuarios; ?></h2>
                                <p class="text-muted mb-0">Usuarios activos registrados.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Especialidades</h6>
                                <h2 class="fw-bold"><?php echo $totalCategorias; ?></h2>
                                <p class="text-muted mb-0">Especialidades médicas activas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Servicios Médicos</h6>
                                <h2 class="fw-bold"><?php echo $totalProductos; ?></h2>
                                <p class="text-muted mb-0">Servicios disponibles en el sistema.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Reportes</h6>
                                <h2 class="fw-bold"><?php echo $totalReportes; ?></h2>
                                <p class="text-muted mb-0">Reportes registrados por usuarios.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RESUMEN CITAS -->
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Estado de citas</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 p-3 rounded" style="background:#fff8e1;">
                                    <div class="d-flex justify-content-between">
                                        <span>Pendientes</span>
                                        <strong><?php echo $totalPendientes; ?></strong>
                                    </div>
                                </div>

                                <div class="mb-3 p-3 rounded" style="background:#e8f5e9;">
                                    <div class="d-flex justify-content-between">
                                        <span>Atendidas</span>
                                        <strong><?php echo $totalAtendidas; ?></strong>
                                    </div>
                                </div>

                                <div class="p-3 rounded" style="background:#ffebee;">
                                    <div class="d-flex justify-content-between">
                                        <span>Canceladas</span>
                                        <strong><?php echo $totalCanceladas; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Información general de Nexus Care</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nexus Care</strong> es una solución tecnológica orientada a la administración de procesos médicos y de atención al paciente.</p>
                                <ul class="mb-0">
                                    <li>Gestión de usuarios y roles.</li>
                                    <li>Administración de especialidades médicas.</li>
                                    <li>Control de servicios médicos.</li>
                                    <li>Registro y seguimiento de citas.</li>
                                    <li>Reportes de incidencias del sistema.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLAS ADMIN -->
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Últimas citas registradas</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Paciente</th>
                                                <th>Médico</th>
                                                <th>Especialidad</th>
                                                <th>Servicio</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($ultimasCitas)) {
                                                foreach ($ultimasCitas as $c) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $c['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($c['paciente'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['medico'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['especialidad'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['servicio'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['fecha_cita'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['hora_cita'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['estado'] ?? ''); ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="8" class="text-center">No hay citas registradas</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Últimos reportes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Usuario</th>
                                                <th>Rol</th>
                                                <th>Título</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($ultimosReportes)) {
                                                foreach ($ultimosReportes as $r) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $r['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($r['usuario'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($r['rol'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($r['titulo'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($r['estado'] ?? ''); ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" class="text-center">No hay reportes registrados</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <!-- DASHBOARD MÉDICO / PACIENTE-->
            <?php if ($rolUsuario == 'medico' || $rolUsuario == 'médico' || $rolUsuario == 'paciente') { ?>

                <!-- TARJETAS -->
                <div class="row">

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Mis citas</h6>
                                <h2 class="fw-bold"><?php echo $totalCitas; ?></h2>
                                <p class="text-muted mb-0">
                                    <?php echo ($rolUsuario == 'paciente') ? 'Citas registradas a tu nombre.' : 'Citas asignadas a tu atención.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Pendientes</h6>
                                <h2 class="fw-bold"><?php echo $totalPendientes; ?></h2>
                                <p class="text-muted mb-0">Citas que aún no han sido atendidas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Atendidas</h6>
                                <h2 class="fw-bold"><?php echo $totalAtendidas; ?></h2>
                                <p class="text-muted mb-0">Citas que ya fueron completadas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="text-muted">Mis reportes</h6>
                                <h2 class="fw-bold"><?php echo $totalReportes; ?></h2>
                                <p class="text-muted mb-0">Reportes o incidencias registradas por ti.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- INFO NEXUS CARE + RESUMEN -->
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Resumen de mis citas</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 p-3 rounded" style="background:#fff8e1;">
                                    <div class="d-flex justify-content-between">
                                        <span>Pendientes</span>
                                        <strong><?php echo $totalPendientes; ?></strong>
                                    </div>
                                </div>

                                <div class="mb-3 p-3 rounded" style="background:#e8f5e9;">
                                    <div class="d-flex justify-content-between">
                                        <span>Atendidas</span>
                                        <strong><?php echo $totalAtendidas; ?></strong>
                                    </div>
                                </div>

                                <div class="p-3 rounded" style="background:#ffebee;">
                                    <div class="d-flex justify-content-between">
                                        <span>Canceladas</span>
                                        <strong><?php echo $totalCanceladas; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Nexus Care</h5>
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>Nexus Care</strong> es una plataforma orientada a facilitar la
                                    gestión médica y mejorar la experiencia de atención dentro del sistema.
                                </p>

                                <?php if ($rolUsuario == 'medico' || $rolUsuario == 'médico') { ?>
                                    <ul class="mb-0">
                                        <li>Consulta tus citas asignadas.</li>
                                        <li>Marca citas como atendidas cuando se completen.</li>
                                        <li>Visualiza información de pacientes y servicios asociados.</li>
                                        <li>Reporta incidencias técnicas o problemas del sistema.</li>
                                    </ul>
                                <?php } ?>

                                <?php if ($rolUsuario == 'paciente') { ?>
                                    <ul class="mb-0">
                                        <li>Consulta tus citas médicas registradas.</li>
                                        <li>Revisa el estado de cada una de tus citas.</li>
                                        <li>Consulta tus servicios y especialidades médicas asignadas.</li>
                                        <li>Reporta problemas o incidencias dentro de la plataforma.</li>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLAS -->
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <?php echo ($rolUsuario == 'paciente') ? 'Mis últimas citas' : 'Últimas citas asignadas'; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>

                                                <?php if ($rolUsuario == 'paciente') { ?>
                                                    <th>Médico</th>
                                                <?php } else { ?>
                                                    <th>Paciente</th>
                                                <?php } ?>

                                                <th>Especialidad</th>
                                                <th>Servicio</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($ultimasCitas)) {
                                                foreach ($ultimasCitas as $c) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $c['id']; ?></td>

                                                        <?php if ($rolUsuario == 'paciente') { ?>
                                                            <td><?php echo htmlspecialchars($c['medico'] ?? ''); ?></td>
                                                        <?php } else { ?>
                                                            <td><?php echo htmlspecialchars($c['paciente'] ?? ''); ?></td>
                                                        <?php } ?>

                                                        <td><?php echo htmlspecialchars($c['especialidad'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['servicio'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['fecha_cita'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['hora_cita'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($c['estado'] ?? ''); ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="7" class="text-center">No hay citas registradas</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Mis reportes</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Título</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($ultimosReportes)) {
                                                foreach ($ultimosReportes as $r) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $r['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($r['titulo'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($r['estado'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($r['fecha_reporte'] ?? ''); ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo '<tr><td colspan="4" class="text-center">No hay reportes registrados</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

        </div>
    </div>

</main>

<?php include_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/footer.php'); ?>
<?php

?>