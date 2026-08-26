<?php
/**
 * Configuración global.
 */
declare(strict_types=1);

// URL base pública (sin slash final). Ej: 'http://localhost/crm' o 'https://crm.midominio.com'

///define('BASE_URL', 'http://localhost:80/lovable_migracion_callcenter/completo/crm_callcenter_php/public');   //8000
define('BASE_URL', 'http://192.168.56.150/crm/public');


//define('BASE_URL', '/callcenter_test/crm_callcenter/public/');   //8000  // PROD



define('APP_NAME', 'CRM GESTIONA');
define('APP_TZ',   'America/La_Paz');
define('DEBUG',    true);

date_default_timezone_set(APP_TZ);
mb_internal_encoding('UTF-8');
