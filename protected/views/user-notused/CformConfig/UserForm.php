<?php

return array(
    'elements' => array(
        'first_name' => array(
            'type' => 'text',
            'class' => 'form-control',
        ),
        'last_name' => array(
            'type' => 'text',
            'class' => 'form-control',
        ),
        'username' => array(
            'type' => 'text',
            'class' => 'form-control',
        ),
        'password' => array(
            'type' => 'password',
            'class' => 'form-control',
        ),
        'email' => array(
            'type' => 'text',
            'class' => 'form-control',
        ),
        'role' => array(
            'type' => 'dropdownlist',
            'items' => array('A' => 'Administrador', 'U' => 'Usuario'),
            'prompt' => 'Please select:',
            'class' => 'form-control',
        ),
        'status' => array(
            'type' => 'dropdownlist',
            'items' => array(1=> 'Activo', 0 => 'Inactivo'),
            'prompt' => 'Please select:',
            'class' => 'form-control',
        )
    ),
    'buttons' => array(
        'save' => array(
            'type' => 'submit',
            'label' => 'Guardar',
            'class' => 'btn btn-primary'
        ),
        'link' => array(
            'type' => 'htmlButton',
            'label' => 'Cancelar',
            'class' => 'btn btn-default',
            'onclick' => 'window.location=\'' . Yii::app()->createUrl('user/index') . '\'',
        ),
    ),
);
