
<div class="form-box" id="login-box">
    <!--<div class="header"><img src="<?php echo Yii::app()->baseUrl ?>/manager/images/williams_sm_logo.png" class="icon"></div>-->
    <div class="header"><h2>Welcome</h2></div>

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ));
    ?>

    <div class="body bg-gray">
        <div class="form-group">
            <?php echo $form->textField($model, 'username', array('placeholder' => 'User ID', 'class' => 'form-control')); ?>
            <?php echo $form->error($model, 'username'); ?>
        </div>
        <div class="form-group">
            <?php echo $form->passwordField($model, 'password', array('placeholder' => 'Password', 'class' => 'form-control')); ?>
            <?php echo $form->error($model, 'password'); ?>
        </div>
        <div class="form-group">
            <?php echo $form->checkBox($model, 'rememberMe'); ?> Remember me
        </div>
    </div>
    <div class="footer">
        <button type="submit" class="btn bg-olive btn-block">Sign me in</button>
    </div>

    <?php $this->endWidget(); ?>

</div>

