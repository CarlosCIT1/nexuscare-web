<?php

function normalizarAccesos($accesos): array
{
    if ($accesos === null) {
        return [];
    }

    if (is_array($accesos)) {
        $accesos = array_filter($accesos, static function ($valor) {
            return $valor !== null && trim((string) $valor) !== '';
        });

        return array_values(array_map(static function ($valor) {
            return strtolower(trim((string) $valor));
        }, $accesos));
    }

    $valor = trim((string) $accesos);
    if ($valor === '') {
        return [];
    }

    $decoded = json_decode($valor, true);
    if (is_array($decoded)) {
        return array_values(array_map(static function ($item) {
            return strtolower(trim((string) $item));
        }, $decoded));
    }

    if (preg_match('/^\{.*\}$/', $valor)) {
        $valor = substr($valor, 1, -1);
        $partes = str_getcsv($valor, ',', '"');
        $partes = array_map('trim', $partes);
        return array_values(array_filter($partes, static function ($item) {
            return $item !== '';
        }));
    }

    $partes = preg_split('/[\s,;]+/', $valor);
    $partes = array_map('trim', $partes);

    return array_values(array_filter($partes, static function ($item) {
        return $item !== '';
    }));
}

function tieneAcceso($modulo)
{
    if (!isset($_SESSION['accesos']) && !isset($_SESSION['rolid']) && !isset($_SESSION['rol'])) {
        return false;
    }

    $modulo = strtolower(trim((string) $modulo));
    if ($modulo === '') {
        return false;
    }

    $accesos = normalizarAccesos($_SESSION['accesos'] ?? []);

    $rolid = isset($_SESSION['rolid']) ? intval($_SESSION['rolid']) : 0;
    $rolNombre = isset($_SESSION['rol']) ? strtolower(trim((string) $_SESSION['rol'])) : '';

    // Admin (by rolid or role name) has full access
    if ($rolid === 1 || $rolNombre === 'administrador' || $rolNombre === 'admin') {
        return true;
    }

    // Permisos por rol (clave normalizada)
    $permisosPorRol = [
        'paciente' => ['dashboard', 'perfil', 'citas', 'reportes', 'recetas'],
        'medico'   => ['dashboard', 'perfil', 'citas', 'pacientes', 'reportes', 'recetas'],
    ];

    // Prefer mapping por rolid cuando exista (IDs conocidos en la BD)
    $mapById = [
        4 => 'paciente',
        7 => 'medico',
        1 => 'administrador',
    ];

    $rolKey = '';
    if (isset($mapById[$rolid])) {
        $rolKey = $mapById[$rolid];
    } elseif ($rolNombre !== '') {
        // Normalizar acentos y caracteres para comparar nombres como "Médico" -> "medico"
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
        ];
        $normalized = strtr($rolNombre, $replacements);
        $normalized = preg_replace('/[^a-z0-9]/', '', strtolower($normalized));
        $rolKey = $normalized;
    }

    if ($rolKey !== '' && array_key_exists($rolKey, $permisosPorRol)) {
        return in_array($modulo, $permisosPorRol[$rolKey], true);
    }

    // Backwards compatibility: if accesos array explicitly lists the module
    if (in_array($modulo, $accesos, true)) {
        return true;
    }

    // If role is unknown, only allow dashboard (inicio)
    return $modulo === 'dashboard';
}

?>