<?php
/**
 * Definición de rutas. Todas pasan por el front controller public/index.php.
 * @var Router $router
 */
declare(strict_types=1);

$auth = [[AuthMiddleware::class, 'check']];

// -------- Público --------
$router->get('/',        [AuthController::class, 'redirectHome']);
$router->get('/login',   [AuthController::class, 'showLogin']);
$router->post('/login',  [AuthController::class, 'login']);
$router->any('/logout',  [AuthController::class, 'logout']);

// -------- App (requiere sesión) --------
$router->get('/dashboard', [DashboardController::class, 'index'], $auth);

// Leads
$router->get('/leads',                [LeadsController::class, 'index'],   $auth);
$router->get('/leads/nuevo',          [LeadsController::class, 'create'],  $auth);
$router->post('/leads',               [LeadsController::class, 'store'],   $auth);
$router->get('/leads/{id}/editar',    [LeadsController::class, 'edit'],    $auth);
$router->post('/leads/{id}',          [LeadsController::class, 'update'],  $auth);
$router->post('/leads/{id}/eliminar', [LeadsController::class, 'destroy'], $auth);
$router->post('/leads/{id}/derivar',  [LeadsController::class, 'derivar'], $auth);
$router->get('/leads/export',         [LeadsController::class, 'export'],  $auth);

// Seguimientos (AJAX JSON)
$router->post('/leads/{id}/seguimientos',     [LeadsController::class, 'storeSeguimiento'],  $auth);
$router->post('/seguimientos/{id}/eliminar',  [LeadsController::class, 'deleteSeguimiento'], $auth);

// Leads Redes
$router->get('/leads-redes',                     [LeadsRedesController::class, 'index'],   $auth);
$router->get('/leads-redes/nuevo',               [LeadsRedesController::class, 'create'],  $auth);
$router->post('/leads-redes',                    [LeadsRedesController::class, 'store'],   $auth);
$router->get('/leads-redes/export',              [LeadsRedesController::class, 'export'],  $auth);
$router->get('/leads-redes/{id}/editar',         [LeadsRedesController::class, 'edit'],    $auth);
$router->post('/leads-redes/{id}',               [LeadsRedesController::class, 'update'],  $auth);
$router->post('/leads-redes/{id}/eliminar',      [LeadsRedesController::class, 'destroy'], $auth);
$router->post('/leads-redes/{id}/seguimientos',  [LeadsRedesController::class, 'storeSeguimiento'], $auth);

// -------- Stubs para próximos módulos (devuelven "En construcción") --------
$router->get('/derivados',        [DerivadosController::class, 'index'], $auth);
$router->get('/reportes',         [ReportesController::class, 'index'], $auth);
$router->get('/calendario',       [CalendarioController::class, 'index'], $auth);
$router->get('/ventas',           [VentasController::class, 'index'], $auth);
$router->get('/seguimiento-ventas',        [SeguimientoVentasController::class, 'index'], $auth);
$router->post('/seguimiento-ventas/{id}',  [SeguimientoVentasController::class, 'store'], $auth);
$router->get('/usuarios',                 [UsuariosController::class, 'index'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/usuarios',                [UsuariosController::class, 'store'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/usuarios/{id}/rol',       [UsuariosController::class, 'updateRole'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/usuarios/{id}/toggle',    [UsuariosController::class, 'toggleActive'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));

$router->post('/usuarios/{id}/editar',    [UsuariosController::class, 'update'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
    

// Base (catálogos)
$router->get('/base',                    [BaseController::class, 'index'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/base',                   [BaseController::class, 'store'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/base/{id}',              [BaseController::class, 'update'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
$router->post('/base/{id}/eliminar',     [BaseController::class, 'destroy'],
    array_merge($auth, [AuthMiddleware::role('admin','supervisor')]));
