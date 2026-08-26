<?php
declare(strict_types=1);

/**
 * Módulo Base — CRUD para catálogos: empresas, proyectos, campañas,
 * canales, agentes y niveles MQL. Solo admin/supervisor.
 */
final class BaseController extends Controller
{
    /** Configuración por pestaña: tabla, campo principal, si requiere empresa_id. */
    private const TABS = [
        'empresas'   => ['label' => 'Empresas',   'field' => 'nombre', 'needsEmpresa' => false, 'icon' => 'bi-building'],
        'proyectos'  => ['label' => 'Proyectos',  'field' => 'nombre', 'needsEmpresa' => true,  'icon' => 'bi-kanban'],
        'campanas'   => ['label' => 'Campañas',   'field' => 'nombre', 'needsEmpresa' => true,  'icon' => 'bi-megaphone'],
        'canales'    => ['label' => 'Canales',    'field' => 'nombre', 'needsEmpresa' => false, 'icon' => 'bi-broadcast'],
        'agentes'    => ['label' => 'Agentes',    'field' => 'nombre', 'needsEmpresa' => false, 'icon' => 'bi-people'],
        'mql_status' => ['label' => 'MQL',        'field' => 'nivel',  'needsEmpresa' => false, 'icon' => 'bi-reception-4'],
    ];

    public function index(): void
    {
        $this->requireRole('admin', 'supervisor');
        $tab = $this->currentTab();
        $cfg = self::TABS[$tab];
        $db  = Database::pdo();

        $order = $cfg['field'];
        $items = $db->query("SELECT * FROM {$tab} ORDER BY {$order} ASC")->fetchAll();

        $empresas = $db->query("SELECT id, nombre FROM empresas ORDER BY nombre ASC")->fetchAll();

        // Mapa empresa_id => nombre para render en tabla
        $empresasMap = [];
        foreach ($empresas as $e) { $empresasMap[$e['id']] = $e['nombre']; }

        $this->view('base/index', [
            'tabs'        => self::TABS,
            'currentTab'  => $tab,
            'cfg'         => $cfg,
            'items'       => $items,
            'empresas'    => $empresas,
            'empresasMap' => $empresasMap,
            'error'       => Helpers::flash('base_error'),
            'ok'          => Helpers::flash('base_ok'),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $tab = $this->currentTab(true);
        $cfg = self::TABS[$tab];
        $val = trim((string)($_POST[$cfg['field']] ?? ''));
        if ($val === '') {
            Helpers::flash('base_error', 'El campo es obligatorio.');
            Helpers::redirect('/base?tab='.$tab);
        }

        $cols = [$cfg['field']];
        $vals = [$val];
        if ($cfg['needsEmpresa']) {
            $emp = trim((string)($_POST['empresa_id'] ?? ''));
            if ($emp === '') {
                Helpers::flash('base_error', 'Debes seleccionar una empresa.');
                Helpers::redirect('/base?tab='.$tab);
            }
            $cols[] = 'empresa_id';
            $vals[] = $emp;
        }

        try {
            $id = Helpers::uuid();
            $allCols = array_merge(['id'], $cols);
            $allVals = array_merge([$id], $vals);
            $ph = implode(',', array_fill(0, count($allVals), '?'));
            $sql = "INSERT INTO {$tab} (".implode(',', $allCols).") VALUES ({$ph})";
            Database::pdo()->prepare($sql)->execute($allVals);
            Helpers::flash('base_ok', 'Registro creado correctamente.');
        } catch (Throwable $e) {
            Helpers::flash('base_error', 'No se pudo crear: '.$e->getMessage());
        }
        Helpers::redirect('/base?tab='.$tab);
    }

    public function update(string $id): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $tab = $this->currentTab(true);
        $cfg = self::TABS[$tab];
        $val = trim((string)($_POST[$cfg['field']] ?? ''));
        if ($val === '') {
            Helpers::flash('base_error', 'El campo es obligatorio.');
            Helpers::redirect('/base?tab='.$tab);
        }

        $sets  = ["{$cfg['field']} = ?"];
        $binds = [$val];
        if ($cfg['needsEmpresa']) {
            $emp = trim((string)($_POST['empresa_id'] ?? ''));
            if ($emp === '') {
                Helpers::flash('base_error', 'Debes seleccionar una empresa.');
                Helpers::redirect('/base?tab='.$tab);
            }
            $sets[]  = 'empresa_id = ?';
            $binds[] = $emp;
        }
        $binds[] = $id;

        try {
            $sql = "UPDATE {$tab} SET ".implode(', ', $sets)." WHERE id = ?";
            Database::pdo()->prepare($sql)->execute($binds);
            Helpers::flash('base_ok', 'Registro actualizado.');
        } catch (Throwable $e) {
            Helpers::flash('base_error', 'No se pudo actualizar: '.$e->getMessage());
        }
        Helpers::redirect('/base?tab='.$tab);
    }

    public function destroy(string $id): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $tab = $this->currentTab(true);
        try {
            Database::pdo()->prepare("DELETE FROM {$tab} WHERE id = ?")->execute([$id]);
            Helpers::flash('base_ok', 'Registro eliminado.');
        } catch (Throwable $e) {
            Helpers::flash('base_error', 'No se pudo eliminar. Puede tener datos relacionados.');
        }
        Helpers::redirect('/base?tab='.$tab);
    }

    /** Lee la pestaña activa desde GET/POST y valida contra el whitelist. */
    private function currentTab(bool $fromPost = false): string
    {
        $src = $fromPost ? ($_POST['_tab'] ?? $_GET['tab'] ?? 'empresas') : ($_GET['tab'] ?? 'empresas');
        $src = (string)$src;
        return array_key_exists($src, self::TABS) ? $src : 'empresas';
    }
}
