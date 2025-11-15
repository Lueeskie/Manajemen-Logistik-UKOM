<?php
define('BASEURL', 'http://localhost/project_name/public/');
// Alias agar penulisan BASE_URL di view/controller tetap berfungsi.
if (!defined('BASE_URL')) {
    define('BASE_URL', BASEURL);
}
?>
