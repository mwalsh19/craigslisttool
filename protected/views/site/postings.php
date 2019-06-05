<?php
$baseUrl = Yii::app()->getBaseUrl(true);
$urlRequest = Yii::app()->createUrl("site/request");
Yii::app()->clientScript->registerPackage('postings');
?>
<?php $this->renderPartial('_header'); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h3>Craigslist Postings</h3>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/index') ?>">Job List</a></li>
                    <li role="presentation" class="active"><a href="#postings" aria-controls="postings" role="tab" data-toggle="tab">Postings</a></li>
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/search') ?>">Search</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane" id="postings"></div>
                </div>

            </div>
        </div>
    </div>
    <br />

    <div class="row">
        <div class="col-sm-12">
            <div class="pull-right" id="loadingPostings"> <img src="<?php echo $baseUrl . '/images/loading.gif' ?>" style="max-width:16px;"> Loading Postings, please wait..</div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-12">
            Showing posts posted during: <strong class="currentDate"></strong>
            <div class="pull-right">
                Filter by date: <select id="dates"></select>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Postings</h3>
                </div>
                <div class="panel-body">

                    <!--                    <div class="tb-info"></div>
                                        <div class="tb-search"></div>-->
                    <table class="table table-striped table-responsive dt-responsive" id="postings-table" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Manage</th>
                                <th>Posting Title</th>
                                <th>Area and Category</th>
                                <th>Posted date</th>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Active</td>
                                <td style="width: 25%;">
                                    <form action="https://post.craigslist.org/manage/5037039112/5pjgg" method="GET">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="submit" name="go" value="delete" class="managebtn btn btn-danger btn-sm">
                                    </form>
                                    <form action="https://post.craigslist.org/manage/5037039112/5pjgg" method="GET">
                                        <input type="hidden" name="action" value="repost">
                                        <input type="submit" name="go" value="repost" class="managebtn btn btn-success btn-sm">
                                    </form>
                                    <form action="https://post.craigslist.org/manage/5037039112/5pjgg" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="crypt" value="U2FsdGVkX184MDI3ODAyN29y1XFukAEnkKlw2lC5yATRBSkI0KriVQ">
                                        <input type="submit" name="go" value="edit" class="managebtn btn btn-primary btn-sm">
                                    </form>
                                </td>
                                <td><a href="#">
                                        DRIVER JOBS! Hiring Recent CDL Grads Now! (Arizona)
                                    </a>
                                </td>
                                <td><strong>phx</strong> transportation</td>
                                <td>2015-05-22 01:03</td>
                                <td>5037039112</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <!--                    <div class="row">
                                            <div class="col-sm-12">
                                                <div class="pull-right">
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="previousPage">Previous</a>
                                                    <a href="javascript:void(0)" class="btn btn-primary" id="nextPage">Next</a>
                                                </div>
                                            </div>
                                        </div>-->
                </div>
            </div>

        </div>
    </div>
</div>
