<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Craiglist Tool',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.models.base.*',
        'application.helpers.*',
        'ext.YiiMailer.YiiMailer',
    ),
    'modules' => array(
// uncomment the following to enable the Gii tool

        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123456',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'clientScript' => array(
            'coreScriptPosition' => CClientScript::POS_END,
            'packages' => array(
                'jquery' => array(
                    'baseUrl' => '//ajax.googleapis.com',
                    'js' => array(
                        'ajax/libs/jquery/1.11.1/jquery.min.js',
                    )
                ),
                'external' => array(
                    'baseUrl' => '//maxcdn.bootstrapcdn.com',
                    'css' => array(
                        'font-awesome/4.2.0/css/font-awesome.min.css',
                        'bootstrap/3.3.4/css/bootstrap.min.css',
                        'bootstrap/3.3.4/css/bootstrap-theme.min.css',
                    ),
                    'js' => array(
                        'bootstrap/3.3.4/js/bootstrap.min.js'
                    ),
                ),
                'dataTables' => array(
                    'baseUrl' => '//cdn.datatables.net',
                    'css' => array(
                        '1.10.6/css/jquery.dataTables.css',
                    ),
                    'js' => array(
                        '1.10.6/js/jquery.dataTables.min.js',
                    ),
                    'depends' => array('frontEnd')
                ),
                'jobList' => array(
                    'baseUrl' => '',
                    'js' => array(
                        'js/publishProccess.js'
                    ),
                    'depends' => array('dataTables')
                ),
                'postings' => array(
                    'baseUrl' => '',
                    'js' => array(
                        'js/postings.js'
                    ),
                    'depends' => array('dataTables')
                ),
                'create' => array(
                    'baseUrl' => '//tinymce.cachefly.net',
                    'js' => array(
                        '4.1/tinymce.min.js'
                    )
                ),
                'init' => array(
                    'baseUrl' => '',
                    'css' => array(
                        'css/normalize.css',
                    ),
                    'depends' => array('jquery')
                ),
                'frontEnd' => array(
                    'baseUrl' => '',
                    'css' => array(
                        'css/main.css'
                    ),
                    'depends' => array('init', 'external')
                ),
            )
        ),
        'user' => array(
            'loginUrl' => array('manager/login'),
            'allowAutoLogin' => true,
            'autoRenewCookie' => true,
            'class' => 'WebUser',
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'craiglist-tool/<action:\w+>' => '<controller>/<action>',
                '<action:\w+>' => 'site/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
//        'db' => array(
//            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/data.db',
//        ),
        // uncomment the following to use a MySQL database
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=craigslist_tool',
            'emulatePrepare' => true,
            'username' => 'dev',
            'password' => 'XDRseATFrrC8EVuB',
            'charset' => 'utf8',
            'enableParamLogging' => true,
        ),
        'widgetFactory' => array(
            'widgets' => array(
                'SAImageDisplayer' => array(
                    'baseDir' => 'uploads',
                    'originalFolderName' => 'originals',
                    'defaultImage' => 'empty.png',
                    'sizes' => array(
                        'thumb' => array('width' => 300, 'height' => 300),
                        'productsFiles' => array('width' => 220, 'height' => 220),
                    ),
                ),
                'CSSImageDisplayer' => array(
                    'baseDir' => 'uploads',
                    'originalFolderName' => 'originals',
                    'defaultImage' => 'empty.png',
                    'sizes' => array(
                        'thumb' => array('width' => 300, 'height' => 300),
                    ),
                ),
            ),
        ),
        'errorHandler' => array(
        // use 'site/error' action to display errors
//            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => '', //'error, warning', //,info, trace
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
        'phantomjs_path' => '/Applications/phantomjs-2.0.0-macosx/bin/phantomjs'
    ),
);
