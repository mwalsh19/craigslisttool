<?php

class UserController extends Controller {

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
                'actions' => array('index', 'create', 'update', 'delete'),
                'roles' => array('A', 'U'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionIndex(){
         $data = MUser::model()->findAll();
        $this->render('index', array(
            'data' => $data
        ));
    }

      public function actionCreate() {
        $model = new MUser();
        $form = new CustomForm('application.views.user.CformConfig.UserForm', $model);

        if ($form->submitted('save') && $form->validate()) {
            $model->password = CPasswordHelper::hashPassword(trim($model->password));
            if ($model->save(false)) {
                $this->redirect(array('user/index'));
            }
        }

        $this->render('create', array('form' => $form));
    }

    public function actionUpdate() {
        $model = MUser::model()->findByPk(Yii::app()->request->getQuery('id'));
        $form = new CustomForm('application.views.user.CformConfig.UserForm', $model);

        if ($form->submitted('save') && $form->validate()) {
            if ($model->save(false)) {
                $this->redirect(array('user/index'));
            }
        }

        $this->render('update', array('form' => $form));
    }

    public function actionDelete() {
        $model = MUser::model()->findByPk(Yii::app()->request->getQuery('id'));
        if (!empty($model)) {
            $model->delete();
        }
        $this->redirect(array('user/index'));
    }

    protected function actionReset() {
        $model = MUser::model()->findByPk(Yii::app()->request->getQuery('id'));
        $model->scenario = 'reset';

        $form = new CustomForm('application.views.user.CformConfig.ResetForm', $model);

        if ($form->submitted('save') && $form->validate()) {
            $user = MUser::model()->findByPk(Yii::app()->request->getQuery('id'));
            if (!empty($user)) {
                $model->password = CPasswordHelper::hashPassword(trim($model->password));
                if ($model->save(false)) {
                    $this->redirect(array('user/index'));
                }
            }
        }

        $this->render('reset', array('form' => $form));
    }
}
