<?php
/**
 * Configuración global.
 */
declare(strict_types=1);

// URL base pública (sin slash final). Ej: 'http://localhost/crm' o 'https://crm.midominio.com'
if (!defined('BASE_URL')) {
    $scheme    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    define('BASE_URL', "{$scheme}://{$host}{$scriptDir}");
}



define('APP_NAME', 'CRM GESTIONA');
define('APP_TZ',   'America/La_Paz');
define('DEBUG',    true);

date_default_timezone_set(APP_TZ);
mb_internal_encoding('UTF-8');
