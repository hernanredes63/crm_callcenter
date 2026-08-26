<?php
// Wrapper que renderiza el stub dentro del layout principal.
$content = (function () {
    ob_start();
    require ROOT_PATH . '/app/views/stub_content.php';
    return ob_get_clean();
})();
require ROOT_PATH . '/app/views/layout/main.php';
