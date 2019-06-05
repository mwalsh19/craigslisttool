<?php
Yii::app()->clientScript->registerPackage('create');
Yii::app()->clientScript->registerScript('initialScripts', "
    tinymce.init({
        selector: \".tinyMCE\",
        height: 200,
        forced_root_block: false,
        plugins: \"code\"
    });

    $('.checkall').on('click', function(){
           if($(this).is(':checked')){
               $('#subareas_container input[type=\'checkbox\']').prop('checked', true);
           }else{
               $('#subareas_container input[type=\'checkbox\']').prop('checked', false);
           }

    });

    var q2OptionsContainer = $('.option-q2-2016');
    $('#MCraiglist_drivertype').on('change', function(){
        var selectedOption = $(this).find('option:selected').val();
        if(selectedOption == 'q22016'){
            q2OptionsContainer.show();
        }else{
            q2OptionsContainer.hide();
        }
    });
");
?>
<?php $this->renderPartial('_header'); ?>
<div class="container">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'admin-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'htmlOptions' => array(
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data'
        ),
    ));
    ?>
    <div class="row">
        <div class="col-sm-12">
            <h3>Craigslist Job</h3>
        </div>
    </div>
    <hr>
    <?php
    $user = Yii::app()->user;
    if ($user->hasFlash('result')) {
        ?>
        <div class="row">
            <div class="alert alert-success" role="alert">
                <?php echo $user->getFlash('result'); ?>
                <a href="<?php echo Yii::app()->createUrl('site/index'); ?>" class="btn btn-default btn-sm">Back to job list</a>
            </div>
        </div>
        <hr>
        <?php
    }
    ?>

    <div class="row">
        <div class="col-sm-8">
            <div class="panel panel-info">
                <div class="panel-heading">Nickname </div>
                <div class="panel-body">
                    <?php echo $form->textField($model, 'nickname', array('class' => 'form-control input-sm')); ?>
                    <?php echo $form->error($model, 'nickname', array('class' => 'help-block text-red')); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="panel panel-info">
                <div class="panel-heading">Driver types </div>
                <div class="panel-body">
                    <?php
                    echo $form->dropDownList($model, 'drivertype', array(
                        'student' => 'Students',
                        'recentGrad' => 'RecentGrad',
                        'experienced' => 'Experienced',
                        'experienced/signonbonus' => 'Experienced (Signon Bonus)',
                        'dedicated' => 'Dedicated (General)',
                        'dedicated-refrigerated' => 'Dedicated (Refrigerated)',
                        'flatbed' => 'Flatbed',
                        'swiftrefrigerated' => 'Swift Refrigerated',
                        'Jan2016NewRoadAhead' => 'Jan 2016 New Road Ahead',
                        'q22016' => 'Q2 2016'
                            ), array('class' => 'form-control'));
                    ?>
                    <?php echo $form->error($model, 'drivertype', array('class' => 'help-block text-red')); ?>
                    <div class="option-q2-2016" style="<?php echo $model->drivertype == 'q22016' ? '' : 'display: none;' ?>">
                        <hr>
                        <div>
                            <p>Company:</p>
                            <?php
                            echo $form->dropDownList($model, 'company', array(
                                'SR' => 'Swift Refrigerated',
                                'DR' => 'Swift Transportation'
                                    ), array('class' => 'form-control input-sm', 'prompt' => 'Select a Company'));
                            ?>
                            <?php echo $form->error($model, 'company', array('class' => 'help-block text-red')); ?>
                        </div>
                        <div>
                            <p>Driver Type:</p>
                            <?php
                            echo $form->dropDownList($model, 'q2_drivertype', array(
                                'experienced' => 'Experienced',
                                'student' => 'Student',
                                'recentGrad' => 'RecentGrad',
                                    ), array('class' => 'form-control input-sm', 'prompt' => 'Select a Driver Type'));
                            ?>
                            <?php echo $form->error($model, 'q2_drivertype', array('class' => 'help-block text-red')); ?>
                        </div>
                        <div>
                            <p>Creative:</p>
                            <?php
                            echo $form->textField($model, 'creative', array('class' => 'form-control input-sm'));
                            ?>
                            <?php echo $form->error($model, 'creative', array('class' => 'help-block text-red')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">Areas/Sub-areas
                    <hr>
                    <div><input type="checkbox" class="checkall">&nbsp;Check All</div>
                </div>
                <div class="panel-body" id="subareas_container">
                    <?php

                    function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
                        foreach ($array as $subarray) {
                            $keys[] = $subarray[$subkey];
                        }
                        array_multisort($keys, $sortType, $array);
                    }

                    sortBySubkey($subareas, 'subarea');

                    if (!empty($subareas)) {
                        $index = 0;
                        foreach ($subareas as $subarea) {
                            echo '<div class="col-sm-4">';
                            echo CHtml::checkBoxList("subarea[$index]", !empty($subarea['default']) ? $subarea['id'] : array(), array($subarea['id'] => $subarea['subarea']), array(
                                'container' => 'label',
                                'template' => '{input}&nbsp;&nbsp;{labelTitle}',
                                'labelOptions' => array(
                                    'class' => 'checkbox-inline'
                            )));
                            echo $form->hiddenField($model, "subarea_name[$index]", array('value' => $subarea['subarea']));
                            echo '</div>';
                            $index++;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">Title for Job Posts </div>
                <div class="panel-body">
                    <?php echo $form->textField($model, 'title', array('class' => 'form-control input-sm')); ?>
                    <?php echo $form->error($model, 'title', array('class' => 'help-block text-red')); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">Compensation</div>
                <div class="panel-body">
                    <?php echo $form->textField($model, 'compensation', array('class' => 'form-control input-sm')); ?>
                    <?php echo $form->error($model, 'compensation', array('class' => 'help-block text-red')); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <label for="">Description for Job Posts: </label>
            <?php echo $form->textArea($model, 'description', array('class' => 'form-control tinyMCE', 'rows' => 5)); ?>
            <?php echo $form->error($model, 'description', array('class' => 'help-block text-red')); ?>
        </div>
        <div class="col-sm-12">
            <label for="">Requirements for Job Posts: </label>
            <?php echo $form->textArea($model, 'requirements', array('class' => 'form-control tinyMCE', 'rows' => 5)); ?>
            <?php echo $form->error($model, 'requirements', array('class' => 'help-block text-red')); ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <input type="submit" value="Save" class="btn btn-primary">
            <a href="<?php echo Yii::app()->createUrl('site/index'); ?>" class="btn btn-danger">Cancel</a>
        </div>
    </div>
    &nbsp;
    &nbsp;
    &nbsp;
    <?php $this->endWidget(); ?>
</div>