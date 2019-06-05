<?php

/**
 * This is the model class for table "tbl_admin_user".
 *
 * The followings are the available columns in table 'tbl_admin_user':
 * @property string $id_admin_user
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $create_date
 * @property string $status
 * @property string $role
 *
 * The followings are the available model relations:
 * @property TblProjectDremel[] $tblProjectDremels
 */
class MUser extends CActiveRecord {

    public $password_repeat;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tbl_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, password, first_name, last_name, email, status, role', 'required'),
            array('email', 'email'),
            array('password', 'compare', 'on' => 'reset', 'message' => Yii::t('general', 'Password must be repeated exactly.')),
            array('password_repeat, approve', 'safe'),
            array('password', 'unsafe', 'on' => 'update')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    public function scopes() {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id_admin_user' => 'Id Admin User',
            'username' => 'Username',
            'password' => 'Password',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'create_date' => 'Create Date',
            'status' => 'Status',
            'role' => 'Role',
            'password_repeat' => 'Repeat Password',
        );
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
