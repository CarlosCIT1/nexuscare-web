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
    if (!isset($_SESSION['accesos']) && !isset($_SESSION['rolid'])) {
        return false;
    }

    $modulo = strtolower(trim((string) $modulo));
    if ($modulo === '') {
        return false;
    }

    $accesos = normalizarAccesos($_SESSION['accesos'] ?? []);

    if (in_array('admin', $accesos, true) || in_array('administrador', $accesos, true)) {
        return true;
    }

    if (isset($_SESSION['rolid']) && intval($_SESSION['rolid']) === 1) {
        return true;
    }

    return in_array($modulo, $accesos, true);
}

?>