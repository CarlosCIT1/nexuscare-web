-- 0. LIMPIEZA PREVIA
DROP TABLE IF EXISTS recetas CASCADE;
DROP TABLE IF EXISTS reportes CASCADE;
DROP TABLE IF EXISTS citas CASCADE;
DROP TABLE IF EXISTS servicios_medicos CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;
DROP TABLE IF EXISTS especialidades CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- 1. CREACIÓN DE TABLAS
CREATE TABLE especialidades (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255),
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    accesos VARCHAR(100) NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    direccion VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    rolid INT NOT NULL,
    id_especialidad INT,
    status INT NOT NULL DEFAULT 1,
    telefono VARCHAR(10),
    especialidad VARCHAR(100),
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    foto VARCHAR(255) DEFAULT 'default.png',

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (rolid)
        REFERENCES roles(id),

    CONSTRAINT fk_usuario_especialidad
        FOREIGN KEY (id_especialidad)
        REFERENCES especialidades(id)
);

CREATE TABLE servicios_medicos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(100),
    descripcion TEXT,
    precio NUMERIC(10,2),
    stock INT NOT NULL,
    id_especialidad INT NOT NULL,
    imagen VARCHAR(255),
    fecha DATE NOT NULL,
    status INT NOT NULL DEFAULT 1,

    CONSTRAINT fk_servicio_especialidad
        FOREIGN KEY (id_especialidad)
        REFERENCES especialidades(id)
);

CREATE TABLE citas (
    id SERIAL PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_especialidad INT NOT NULL,
    id_medico INT NOT NULL,
    id_servicio INT NOT NULL,
    fecha_cita DATE NOT NULL,
    hora_cita TIME NOT NULL,
    observaciones TEXT,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'
        CHECK (estado IN ('Pendiente','Confirmada','Cancelada','Atendida')),
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 1,

    CONSTRAINT fk_cita_paciente
        FOREIGN KEY (id_paciente)
        REFERENCES usuarios(id),

    CONSTRAINT fk_cita_medico
        FOREIGN KEY (id_medico)
        REFERENCES usuarios(id),

    CONSTRAINT fk_cita_especialidad
        FOREIGN KEY (id_especialidad)
        REFERENCES especialidades(id),

    CONSTRAINT fk_cita_servicio
        FOREIGN KEY (id_servicio)
        REFERENCES servicios_medicos(id)
);

CREATE TABLE recetas (
    id SERIAL PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    id_cita INT,
    diagnostico TEXT NOT NULL,
    medicamentos TEXT NOT NULL,
    indicaciones TEXT NOT NULL,
    cedula_profesional VARCHAR(8) NOT NULL,
    fecha_receta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status INT NOT NULL DEFAULT 1,

    CONSTRAINT fk_receta_paciente
        FOREIGN KEY (id_paciente)
        REFERENCES usuarios(id),

    CONSTRAINT fk_receta_medico
        FOREIGN KEY (id_medico)
        REFERENCES usuarios(id),

    CONSTRAINT fk_receta_cita
        FOREIGN KEY (id_cita)
        REFERENCES citas(id)
);

CREATE TABLE reportes (
    id SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente'
        CHECK (estado IN ('Pendiente','En proceso','Completado')),
    fecha_reporte TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_solucion TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 1,

    CONSTRAINT fk_reporte_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id)
);

-- FUNCIÓN

CREATE OR REPLACE FUNCTION public.fn_estado_citas(
    p_id_usuario INT DEFAULT NULL,
    p_rol VARCHAR DEFAULT 'administrador'
)
RETURNS TABLE(
    pendientes BIGINT,
    atendidas BIGINT,
    canceladas BIGINT
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    SELECT
        COUNT(*) FILTER (WHERE estado = 'Pendiente') AS pendientes,
        COUNT(*) FILTER (WHERE estado = 'Atendida') AS atendidas,
        COUNT(*) FILTER (WHERE estado = 'Cancelada') AS canceladas
    FROM citas c
    WHERE c.status = 1
      AND (
            LOWER(p_rol) = 'administrador'
         OR (LOWER(p_rol) IN ('medico','médico') AND c.id_medico = p_id_usuario)
         OR (LOWER(p_rol) = 'paciente' AND c.id_paciente = p_id_usuario)
      );
END;
$$;

-- PROCEDIMIENTO

CREATE OR REPLACE PROCEDURE public.sp_registrar_cita(
    p_id_paciente INT,
    p_id_especialidad INT,
    p_id_medico INT,
    p_id_servicio INT,
    p_fecha_cita DATE,
    p_hora_cita TIME,
    p_observaciones TEXT
)
LANGUAGE plpgsql
AS $$
BEGIN
    INSERT INTO citas(
        id_paciente,
        id_especialidad,
        id_medico,
        id_servicio,
        fecha_cita,
        hora_cita,
        observaciones,
        estado,
        fecha_registro,
        status
    )
    VALUES(
        p_id_paciente,
        p_id_especialidad,
        p_id_medico,
        p_id_servicio,
        p_fecha_cita,
        p_hora_cita,
        p_observaciones,
        'Pendiente',
        CURRENT_TIMESTAMP,
        1
    );
END;
$$;
