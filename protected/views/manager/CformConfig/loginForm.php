<?php

return array(
    'elements' => array(
        'username' => array(
            'type' => 'text',
            'placeholder' => 'User ID',
            'class' => 'form-control',
            'layout' => "<div class=\"form-group\">
                        {input} {error}
                    </div>",
        ),
        'password' => array(
            'type' => 'password',
            'placeholder' => 'Password',
            'class' => 'form-control',
            'layout' => "<div class=\"form-group\">
                        {input} {error}
                    </div>",
        ),
        'rememberMe' => array(
            'type' => 'checkbox',
            'layout' => "<div class=\"form-group\">
                        {input} Remember me
                    </div>",
        )
    ),
    'buttons' => array(
        'login' => array(
            'type' => 'submit',
            'label' => 'Sign me in',
            'class' => 'btn bg-olive btn-block',
        ),
    ),
);
