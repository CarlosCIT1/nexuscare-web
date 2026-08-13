-- DATOS DE EJEMPLO - NEXUSCARE
-- Base de datos: nexuscare


-- ============================================================
-- 1. ESPECIALIDADES


INSERT INTO especialidades
    (id, nombre, descripcion, imagen, fecha, status)
VALUES
    (1, 'Ginecología',
     'Especialidad médica dedicada al cuidado de la salud del sistema reproductor femenino.',
     'default.png', CURRENT_TIMESTAMP, 1),

    (2, 'Oftalmología',
     'Especialidad médica dedicada al diagnóstico y tratamiento de enfermedades de los ojos.',
     'default.png', CURRENT_TIMESTAMP, 1),

    (3, 'Odontología',
     'Especialidad encargada de la prevención, diagnóstico y tratamiento de enfermedades de la salud bucal.',
     'default.png', CURRENT_TIMESTAMP, 1),

    (4, 'Neurología',
     'Especialidad médica dedicada al estudio, diagnóstico y tratamiento de enfermedades del sistema nervioso.',
     'default.png', CURRENT_TIMESTAMP, 1);


-- ============================================================
-- 2. ROLES


INSERT INTO roles
    (id, nombre, descripcion, accesos, fecha, status)
VALUES
    (1, 'Administrador',
     'Administrador del sistema',
     'Todos',
     CURRENT_TIMESTAMP, 1),

    (2, 'Paciente',
     'Paciente del sistema',
     'Paciente',
     CURRENT_TIMESTAMP, 1),

    (3, 'Médico',
     'Médico del sistema',
     'Medico',
     CURRENT_TIMESTAMP, 1);


-- ============================================================
-- 3. USUARIOS
--
-- Todos los usuarios de prueba utilizan la misma contraseña:
-- 123456
--
-- El hash utilizado corresponde a la contraseña 123456
-- y es compatible con bcrypt.
--
-- Administrador:
-- email: admin@nexuscare.com
--
-- Médicos:
-- email: ana@nexuscare.com
-- email: carlos@nexuscare.com
--
-- Pacientes:
-- email: paciente@nexuscare.com
-- email: maria@nexuscare.com

INSERT INTO usuarios
    (id, nombre, direccion, email, password, rolid, id_especialidad,
     status, telefono, especialidad, fecha, foto)
VALUES
    (1, 'Administrador',
     'Ciudad de Mexico',
     'admin@nexuscare.com',
     '$2y$10$.6GXeB9uUgDd9Nfun1w4l.p2jyeNv6XeCBTyRT2q1bDW684Lt8y52',
     1, NULL, 1, '4421234567', NULL,
     CURRENT_TIMESTAMP, 'default.png'),

    (2, 'Ana López',
     'Ciudad de Mexico',
     'ana@nexuscare.com',
     '$2y$10$.6GXeB9uUgDd9Nfun1w4l.p2jyeNv6XeCBTyRT2q1bDW684Lt8y52',
     3, 1, 1, '4427836043', NULL,
     CURRENT_TIMESTAMP, 'default.png'),

    (3, 'Carlos Hernández',
     'Ciudad de Mexico',
     'carlos@nexuscare.com',
     '$2y$10$.6GXeB9uUgDd9Nfun1w4l.p2jyeNv6XeCBTyRT2q1bDW684Lt8y52',
     3, 4, 1, '4429876543', NULL,
     CURRENT_TIMESTAMP, 'default.png'),

    (4, 'Juan Pérez',
     'Ciudad de Mexico',
     'paciente@nexuscare.com',
     '$2y$10$.6GXeB9uUgDd9Nfun1w4l.p2jyeNv6XeCBTyRT2q1bDW684Lt8y52',
     2, NULL, 1, '4424567890', NULL,
     CURRENT_TIMESTAMP, 'default.png'),

    (5, 'María García',
     'Ciudad de Mexico',
     'maria@nexuscare.com',
     '$2y$10$.6GXeB9uUgDd9Nfun1w4l.p2jyeNv6XeCBTyRT2q1bDW684Lt8y52',
     2, NULL, 1, '4423456789', NULL,
     CURRENT_TIMESTAMP, 'default.png');


-- ============================================================
-- 4. SERVICIOS MÉDICOS


INSERT INTO servicios_medicos
    (id, nombre, marca, descripcion, precio, stock,
     id_especialidad, imagen, fecha, status)
VALUES
    (1, 'Consulta de Ginecología',
     'NexusCare',
     'Consulta médica especializada en ginecología.',
     500.00, 20, 1, 'default.png',
     CURRENT_DATE, 1),

    (2, 'Consulta de Oftalmología',
     'NexusCare',
     'Consulta médica para evaluación y diagnóstico de problemas visuales.',
     450.00, 15, 2, 'default.png',
     CURRENT_DATE, 1),

    (3, 'Consulta de Odontología',
     'NexusCare',
     'Consulta para revisión y valoración de la salud bucal.',
     400.00, 15, 3, 'default.png',
     CURRENT_DATE, 1),

    (4, 'Consulta de Neurología',
     'NexusCare',
     'Consulta especializada para evaluación del sistema nervioso.',
     600.00, 10, 4, 'default.png',
     CURRENT_DATE, 1);


-- ============================================================
-- 5. CITAS


INSERT INTO citas
    (id, id_paciente, id_especialidad, id_medico, id_servicio,
     fecha_cita, hora_cita, observaciones, estado,
     fecha_registro, status)
VALUES
    (1, 4, 1, 2, 1,
     CURRENT_DATE + 1,
     '10:00:00',
     'Consulta de seguimiento.',
     'Pendiente',
     CURRENT_TIMESTAMP, 1),

    (2, 5, 4, 3, 4,
     CURRENT_DATE + 2,
     '12:30:00',
     'Dolor de cabeza recurrente.',
     'Confirmada',
     CURRENT_TIMESTAMP, 1),

    (3, 4, 1, 2, 1,
     CURRENT_DATE - 5,
     '09:30:00',
     'Consulta general.',
     'Atendida',
     CURRENT_TIMESTAMP, 1),

    (4, 5, 2, 2, 2,
     CURRENT_DATE - 10,
     '16:00:00',
     'Revisión de la vista.',
     'Cancelada',
     CURRENT_TIMESTAMP, 0);


-- ============================================================
-- 6. RECETAS


INSERT INTO recetas
    (id, id_paciente, id_medico, id_cita,
     diagnostico, medicamentos, indicaciones,
     cedula_profesional, fecha_receta, status)
VALUES
    (1, 4, 2, 3,
     'Consulta de seguimiento sin complicaciones.',
     'Paracetamol 500 mg',
     'Tomar una tableta cada 8 horas durante 3 días.',
     '12345678',
     CURRENT_TIMESTAMP, 1),

    (2, 5, 3, 2,
     'Cefalea recurrente.',
     'Paracetamol 500 mg',
     'Tomar una tableta en caso de dolor. No exceder 3 tabletas al día.',
     '87654321',
     CURRENT_TIMESTAMP, 1);


-- ============================================================
-- 7. REPORTES


INSERT INTO reportes
    (id_usuario, titulo, descripcion, estado,
     fecha_reporte, fecha_solucion, status)
VALUES
    (4,
     'Problema al solicitar una cita',
     'El usuario reporta dificultades al momento de seleccionar el horario de una cita.',
     'Pendiente',
     CURRENT_TIMESTAMP,
     NULL,
     1),

    (5,
     'Error en información del perfil',
     'El usuario reporta información incorrecta en su perfil.',
     'En proceso',
     CURRENT_TIMESTAMP,
     NULL,
     1);


-- ============================================================
-- 8. REAJUSTE DE SECUENCIAS
--
-- Esto permite que los siguientes registros creados desde
-- la aplicación continúen con el ID correcto.


SELECT setval(
    'especialidades_id_seq',
    (SELECT MAX(id) FROM especialidades)
);

SELECT setval(
    'roles_id_seq',
    (SELECT MAX(id) FROM roles)
);

SELECT setval(
    'usuarios_id_seq',
    (SELECT MAX(id) FROM usuarios)
);

SELECT setval(
    'servicios_medicos_id_seq',
    (SELECT MAX(id) FROM servicios_medicos)
);

SELECT setval(
    'citas_id_seq',
    (SELECT MAX(id) FROM citas)
);

SELECT setval(
    'recetas_id_seq',
    (SELECT MAX(id) FROM recetas)
);

SELECT setval(
    'reportes_id_seq',
    (SELECT MAX(id) FROM reportes)
);
