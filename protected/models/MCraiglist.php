<?php

class MCraiglist extends CFormModel {

    public $nickname;
    public $drivertype;
    public $title;
    public $description;
    public $requirements;
    public $subarea;
    public $subarea_name;
    public $compensation;
    public $company;
    public $q2_drivertype;
    public $creative;

    public function rules() {
        return array(
            array('nickname, drivertype, title, description, compensation', 'required'),
            array('nickname, drivertype, title, description, compensation, company, q2_drivertype, creative', 'required', 'on' => 'q22016_option'),
            array('subarea, subarea_name, requirements, company, q2_drivertype, creative', 'safe')
        );
    }

}
