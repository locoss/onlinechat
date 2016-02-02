<?php
$host = $_SERVER['HTTP_HOST'];
if($host == 'localhost'){
	$uri = $_SERVER['REQUEST_URI'];
	$uri = explode('/', $uri);
	
	$host = $host . "/" . $uri[1];
}
define('URL', $host);
define('BASE_DIR', __DIR__);

try {
    require __DIR__ . '/app/bootstrap.php';
} catch (Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}


Chat\Framework\Bootstrap::app();
