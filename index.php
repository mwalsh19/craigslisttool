<?php

// change the following paths if necessary
$yii = dirname(__FILE__) . '/../../yii-1.1.14.f0fee9/framework/yii.php';
$config_static = dirname(__FILE__) . '/protected/config/main.php';
$config_dynamic = dirname(__FILE__) . '/protected/config/config.json';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once($yii);
$config = CMap::mergeArray(require($config_static), json_decode(file_get_contents($config_dynamic), true));
Yii::createWebApplication($config)->run();
