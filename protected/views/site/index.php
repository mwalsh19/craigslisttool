<?php
Yii::app()->clientScript->registerPackage('jobList');
Yii::app()->clientScript->registerScript('datatable', "
  $('#jobs').DataTable({
        'pageLength': 50
    });
", CClientScript::POS_END);
?>
<?php $this->renderPartial('_header'); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left">
                <h3>Craigslist Tool</h3>
            </div>
            <div class="pull-right">
                <a href="<?php echo Yii::app()->createUrl('site/create') ?>" class="btn btn-primary">Create new</a>
            </div>
        </div>
    </div>
    <hr>
    <?php
    $user = Yii::app()->user;
    if ($user->hasFlash('result')) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success" role="alert">
                    <?php echo $user->getFlash('result'); ?>
                </div>
            </div>
        </div>
        <hr>
        <?php
    }
    ?>
    <?php
    $user = Yii::app()->user;
    if ($user->hasFlash('error')) {
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger" role="alert">
                    <?php echo $user->getFlash('error'); ?>
                </div>
            </div>
        </div>
        <hr>
        <?php
    }
    ?>


    <div class="row">
        <div class="col-sm-12">
            <div role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#create" aria-controls="create" role="tab" data-toggle="tab">Job List</a></li>
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/postings') ?>">Postings</a></li>
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/search') ?>">Search</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="create"></div>
                </div>

            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-right">
                <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="publishProccess-btn">
                    <i class="glyphicon glyphicon-eye-open"></i>
                    View publish proccess
                </a>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Job list</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped" id="jobs">
                        <thead>
                            <tr>
                                <th style="width: 80%;">Job</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $basedir = Yii::getPathOfAlias('webroot') . '/xml';
                            $shell_scripts_dir = Yii::getPathOfAlias('application') . "/runtime/";
                            if ($handle = opendir($basedir)) {
                                while (false !== ($entry = readdir($handle))) {
                                    if ($entry != "." && $entry != "..") {
                                        $file = str_replace('.xml', '', $entry);
                                        $shell_exists = "{$shell_scripts_dir}{$file}.sh";
                                        ?>
                                        <tr>
                                            <td style="width: 60%;"><?php echo $entry; ?></td>
                                            <td><?php echo (file_exists($shell_exists)) ? '<span class="label label-success">Published</span>' : '<span class="label label-danger">Pending</span>'; ?></td>
                                            <td>
                                                <a href="<?php echo Yii::app()->createUrl('site/publish', array('file' => $file)) ?>" class="btn btn-primary btn-sm">
                                                    <i class="glyphicon glyphicon-share"></i> Publish
                                                </a>
                                                <?php
                                                echo CHtml::link('<i class="glyphicon glyphicon-trash"></i> Delete', array('site/delete', 'file' => $file), array(
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'confirm' => 'Are you sure you want to delete this job?',
                                                ));
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }

                                closedir($handle);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="publishProccess-modal" tabindex="-1" role="dialog" aria-labelledby="publishProccess-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Publish Process</h4>
            </div>
            <div class="modal-body">
                <strong>Starting publish</strong>&nbsp;&nbsp;<img  class="loadingImg" src="<?php echo Yii::app()->getBaseUrl(true) . '/images/loading.gif' ?>" style="max-width:16px;">
                <hr>
                <div class="publish-logs">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>