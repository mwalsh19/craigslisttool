<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'My Console Application',
    // preloading 'log' component
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.models.base.*',
        'application.helpers.*'
    ),
    // application components
    'components' => array(
//		'db'=>array(
//			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
//		),
        // uncomment the following to use a MySQL database

        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=craigslist_tool;unix_socket:/Applications/MAMP/tmp/mysql/mysql.sock',
            'emulatePrepare' => true,
            'username' => 'dev',
            'password' => 'XDRseATFrrC8EVuB',
            'charset' => 'utf8',
            'enableParamLogging' => true,
            'tablePrefix' => 'tbl_',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                'stdlog' => array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                'jobslog' => array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'jobs.log',
                    'categories' => 'jobs.*',
                ),
                'postlist' => array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'post.list.log',
                    'categories' => 'post.list.*',
                )
            ),
        ),
    ),
);
