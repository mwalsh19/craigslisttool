<?php

class ManagerController extends Controller {

    public $layout = '//layouts/manager';

    public function filters() {
        return array(
            'accessControl - login, logout', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'users'),
                'roles' => array('A', 'U'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
            'users' => array(
                'class' => 'application.controllers.manager.AdminAction',
            ),
        );
    }

    public function actionIndex() {
        $this->render('index');
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionLogin() {
        $this->layout = '//layouts/login';

        $model = new MLoginForm;

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['MLoginForm'])) {
            $model->attributes = $_POST['MLoginForm'];
            if ($model->validate() && $model->login())
                $this->redirect(array('index'));
        }
        $this->render('login', array('model' => $model));
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionTest() {
//$array_areas = Array_areas::getAreas();
//        $array_new_areas = array();
//        foreach ($array_areas as $area) {
//            $result = array();
//            preg_match("/value=\"(\\d+)\"/u", $area, $result);
//
//            $result_2 = array();
//            preg_match("/>([a-zA-Z0-9, '&amp; ()\-\/]+)</u", $area, $result_2);
//
//            //  var_dump($result_2[1]);
//            $array_new_areas[] = array(
//                'area_code' => $result[1],
//                'area_label' => $result_2[1]
//            );
//        }
//        $str = 'array(';
//        foreach ($array_new_areas as $value) {
//            $str .= "array('area_code' => '{$value['area_code']}', 'area_label' => '{$value['area_label']}'), \n";
//        }
//        $str .= ');';
//
//        echo $str;
//
//        $file = Yii::app()->getBaseUrl(true) . '/files/craigslist_metroarea_location.txt';
//        $handle = fopen($file, "r");
//        $str = 'array(';
//        if ($handle) {
//            while (($line = fgets($handle)) !== false) {
//                $str .= "array('id' => '', 'subarea' => '{$line}'), \n";
//            }
//
//            fclose($handle);
//        } else {
//
//        }
//        $str .= ');';
//        echo $str;
    }

}
