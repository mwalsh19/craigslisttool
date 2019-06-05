<div class="login-container">
    <?php echo CHtml::beginForm(); ?>
    <div>
        Username: <?php echo CHtml::activeTextField($model, 'username'); ?>
        <?php echo CHtml::error($model, 'username', array('class' => 'error')); ?>
    </div>
    <div>
        Password: <?php echo CHtml::activePasswordField($model, 'password'); ?>
        <?php echo CHtml::error($model, 'password', array('class' => 'error')); ?>
    </div>
    <div><?php echo CHtml::submitButton('Login'); ?></div>
    <?php echo CHtml::endForm(); ?>
</div>