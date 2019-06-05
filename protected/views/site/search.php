<?php
Yii::app()->clientScript->registerScript('map', "
    var map;
    function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 33.8409858, lng: -118.3879423},
        zoom: 8
      });
    }
", CClientScript::POS_BEGIN);
Yii::app()->clientScript->registerScriptFile('https://maps.googleapis.com/maps/api/js?key=AIzaSyC1gLtynpOdw1XehaptfjtTwxApUdoelHQ&callback=initMap',
        CClientScript::POS_END, array('async' => 'async', 'defer' => 'defer'));
Yii::app()->clientScript->registerScriptFile('js/search.js', CClientScript::POS_END);
?>
<?php $this->renderPartial('_header'); ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="pull-left">
                <h3>Search</h3>
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
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/index') ?>">Job List</a></li>
                    <li role="presentation"><a href="<?php echo $this->createUrl('site/postings') ?>">Postings</a></li>
                    <li role="presentation" class="active"><a href="#" aria-controls="create" role="tab" data-toggle="tab">Search</a></li>
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
        <div class="col-sm-6">
            <label>Please type a city name with a state name (i.e. New Jersey NY)</label><br><br>
            <input type="text" class="form-control input-sm" id="searchInput" value="new jersey ny" placeholder="Type your search terms..." style="width: 300px; display: inline-block;">
            <button href="javascript:void(0);" class="btn btn-primary btn-sm" id="searchBtn">Search</button> &nbsp; &nbsp;
            <img id="loading" src="<?php echo Yii::app()->getBaseUrl(true) . '/images/loading.gif' ?>" style="max-width:16px; display: none;"><br><br>
            <div id="results">
                <label style="display: none;" class="notfoundlabel">Not found, please try with a different keyword</label>
                <ul></ul>
            </div>
        </div>
        <div class="col-ms-6">
            <div id="map"></div>
        </div>
    </div>
 