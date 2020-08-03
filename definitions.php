<?php 


define('SITE','webjump');
define('UPLOADS', __DIR__. '/uploads/');
define('LOGS', __DIR__. '/logs/');
define('IMGS', __DIR__. '/assets/img/produtos/');
define('POR_PAG', 10);

@chmod(UPLOADS, 0777);
@chmod(LOGS, 0777);
@chmod(IMGS, 0777);

$url = sprintf("%s://%s%s",$_SERVER["REQUEST_SCHEME"],$_SERVER["SERVER_NAME"],$_SERVER['SCRIPT_NAME']);
define('URL',$url);
