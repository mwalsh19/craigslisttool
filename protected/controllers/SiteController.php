<?php

class SiteController extends Controller {

    public $layout = '//layouts/frontend';

//    public function filters() {
//        return array('accessControl - login, logout',
//            'postOnly + delete',
//        );
//    }
//
//    public function accessRules() {
//        return array(
//            array('allow',
//                'actions' => array(
//                    'index',
//                    'postings',
//                    'search',
//                    'create',
//                    'searchNear',
//                    'publish',
//                    'request',
//                    'delete',
//                    'logs'
//                ),
//                'roles' => array('A'),
//                'users' => array('*'),
//            ),
//            array('deny',
//                'users' => array('*'),
//            )
//        );
//    }

    public function actions() {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionIndex() {
        $this->render('index');
    }

    public function actionLogin() {
        $model = new MLoginForm();

        if (!Yii::app()->user->isGuest) {
            $this->redirect(array('index'));
        } else {
            if (isset($_POST['MLoginForm'])) {
                $model->attributes = $_POST['MLoginForm'];
                if ($model->validate() && $model->login())
                    $this->redirect(array('index'));
            }
            $this->render('login', array('model' => $model));
        }
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(array('login'));
    }

    public function actionCreate() {
        $baseurl = Yii::getPathOfAlias('webroot');
        $model = new MCraiglist();
        $subareas = Array_areas::getSubareas();

        if (!empty($_POST['MCraiglist'])) {
            $model->attributes = $_POST['MCraiglist'];
            $model->subarea = !empty($_POST['subarea']) ? $_POST['subarea'] : null;

            if ($model->drivertype == 'q22016') {
                $model->scenario = 'q22016_option';
            }

            if ($model->validate() && !empty($_POST['subarea'])) {
                $xml_document = new DOMDocument();

                $sub_areas_ids = $model->subarea;
                $sub_areas_names = $model->subarea_name;
                //
                $jobs = $xml_document->createElement('jobs');
                //
                $nickname = $xml_document->createElement('nickname');
                $nickname->nodeValue = $model->nickname;
                $jobs->appendChild($nickname);
                //
                $drivertype = $xml_document->createElement('drivertype');
                $drivertype->nodeValue = $model->drivertype == 'q22016' ? $model->company . '/' . $model->q2_drivertype . '/craigslist/' . $model->creative : $model->drivertype;
                $jobs->appendChild($drivertype);
                //
                $xmlVersion = $xml_document->createElement('xmlVersion');
                $xmlVersion->nodeValue = $model->drivertype == 'q22016' ? 'v2' : 'v1';
                $jobs->appendChild($xmlVersion);
                //
                $title = $xml_document->createElement('title');
                $cdata = $xml_document->createCDATASection($model->title);
                $title->appendChild($cdata);
                $jobs->appendChild($title);
                //
                $compensation = $xml_document->createElement('compensation');
                $cdata = $xml_document->createCDATASection($model->compensation);
                $compensation->appendChild($cdata);
                $jobs->appendChild($compensation);
                //
                $description = $xml_document->createElement('description');
                $cdata = $xml_document->createCDATASection($model->description);
                $description->appendChild($cdata);
                $jobs->appendChild($description);
                //
                $requirements = $xml_document->createElement('requirements');
                $cdata = $xml_document->createCDATASection($model->requirements);
                $requirements->appendChild($cdata);
                $jobs->appendChild($requirements);

                $areas_container = $xml_document->createElement('areas');

                for ($index = 0; $index < count($sub_areas_names); $index++) {
                    if (empty($sub_areas_ids[$index][0])) {
                        continue;
                    }
                    $area = $xml_document->createElement('area');
                    $title = $xml_document->createElement('title');
                    $title->nodeValue = $sub_areas_names[$index];
                    $code = $xml_document->createElement('code');
                    $code->nodeValue = $sub_areas_ids[$index][0];
                    $area->appendChild($title);
                    $area->appendChild($code);
                    $areas_container->appendChild($area);
                }

                $jobs->appendChild($areas_container);
                $xml_document->appendChild($jobs);

                $xml_document->save($baseurl . '/xml/' . strtolower(str_replace(' ', '_', $model->nickname)) . '.xml');

                Yii::app()->user->setFlash('result', 'Your job has been saved');
                $this->redirect($this->createUrl('site/create'));
            }
        }
        $this->render('create', array('model' => $model, 'subareas' => $subareas));
    }

    private function publishPhp($file, $app_dir, $areas_total, $xml, $namewithoutext) {
        if (!file_exists($file)) {
            touch($file); //Acces moment and modification
            chmod($file, 0755); //Set permisions

            file_put_contents($app_dir . '/runtime/jobs.log', ''); //clear old logs

            $fh = fopen($file, 'w');
            for ($i = 0; $i < $areas_total; $i++) {
                $current = "php {$app_dir}/yiic jobs init --index={$i} --xml='{$xml}'\n";
                fwrite($fh, $current);
            }
            $current = "php {$app_dir}/yiic jobs retryPosts --file='{$namewithoutext}'\n";
            fwrite($fh, $current);

            fclose($fh);

            //exec($file . " > " . $output . " &");

            Yii::app()->user->setFlash('result', 'It may take a few minutes to be published, please wait');
            $this->redirect($this->createUrl('site/index', array('publish' => 1)));
        } else {
            Yii::app()->user->setFlash('error', 'Sorry, the list of work has already been published before.');
            $this->redirect(Yii::app()->createUrl('site/index'));
        }
    }

    private function publishPhantom($file, $app_dir, $areas_total, $output, $xml) {
        $phantomBasePath = Yii::app()->params['phantomjs_path'];
        $phantomIndexJS = $app_dir . '/helpers/phantom/index.js';

        if (!file_exists($file)) {
            touch($file); //Acces moment and modification
            chmod($file, 0755); //Set permisions


            $fh = fopen($file, 'w');
            for ($i = 0; $i < $areas_total; $i++) {
                $current = $phantomBasePath . " {$phantomIndexJS} --index={$i} --xml='{$xml}'\n";
                fwrite($fh, $current);
            }
            fwrite($fh, $current);

            fclose($fh);

            // exec($file . " > " . $output . " &");

            Yii::app()->user->setFlash('result', 'It may take a few minutes to be published, please wait');
            $this->redirect($this->createUrl('site/index', array('publish' => 1)));
        } else {
            Yii::app()->user->setFlash('error', 'Sorry, the list of work has already been published before.');
            $this->redirect(Yii::app()->createUrl('site/index'));
        }
    }

    public function actionPublish() {
        ini_set('memory_limit', '256M');
        $file = Yii::app()->request->getParam('file');

        if (empty($file)) {
            throw new CHttpException(404, 'The file cannot be found.');
        }
        $webroot = Yii::getPathOfAlias('webroot');
        $baseDir = $webroot . '/xml/';
        $xml = $baseDir . $file . '.xml';
        $onlyname = $file . '.xml';
        $namewithoutext = $file;

        $doc = new SimpleXMLElement(file_get_contents($xml));
        $areas_total = $doc->areas->area->count();

        $app_dir = Yii::getPathOfAlias('application');
        $fileName = strtolower(str_replace(' ', '_', $file));
        $file = $app_dir . "/runtime/{$fileName}.sh";

        $output = $app_dir . "/runtime/output.txt";

        $this->publishPhp($file, $app_dir, $areas_total, $onlyname, $namewithoutext);
//        $this->publishPhantom($file, $app_dir, $areas_total, $output, $xml);
    }

    public function actionPostings() {
        $this->render('postings');
    }

    public function actionRequest($filter_page = 1, $filter_cat = 0, $filter_date = "", $filter_active = 0, $show_tab = 'postings') {
        $webroot = Yii::getPathOfAlias('application');
        $file = $webroot . "/runtime/posts_list.sh";
        $applicationDir = Yii::getPathOfAlias('application');
        if (!file_exists($file)) {
            touch($file); //Acces moment and modification
            chmod($file, 0755); //Set permisions
        } else {
            chmod($file, 0755); //Set permisions
        }

        $fh = fopen($file, 'w');
        $current = "php {$applicationDir}/yiic postslist init --filter_page='" . $filter_page . "' --filter_date='" . $filter_date . "'";

        fwrite($fh, $current);
        fclose($fh);

        exec($file . " 2>&1", $output);

        $result = end($output);
        $posts = json_decode($result);

        $arrayPosts = array();
        $arrayDates = '';

//        var_dump($output);
//        die();

        if (!empty($posts->posts) && !empty($posts->dates)) {
            $index = 0;
            foreach ($posts->posts as $row) {
                $arrayPosts[$index] = array(
                    $row->status,
                    trim($row->actions),
                    trim($row->title),
                    trim($row->areacat),
                    trim($row->date),
                    trim($row->id)
                );
                $index++;
            }
            $arrayDates = $posts->dates;
        }

        $obj = new stdClass();
        $obj->dates = $arrayDates;
        $obj->data = $arrayPosts;

        header('Content-Type: application/json');
        echo json_encode($obj);
    }

    public function actionLogs($linecount = 0) {
        $baseDir = Yii::getPathOfAlias('application');
        $file = $baseDir . "/runtime/jobs.log";

        $array_lines = array();
        $total_lines = 0;

        if (file_exists($file)) {
            $lines = file($file);
            if (!empty($lines)) {
                for ($index = $linecount; $index < count($lines); $index++) {
                    $array_lines[] = trim($lines[$index]);
                    $total_lines++;
                }
            }
        }

        $obj = new stdClass();
        $obj->total = $total_lines;
        $obj->data = $array_lines;

        header('Content-Type: application/json');
        echo json_encode($obj);
    }

    public function actionDelete() {
        $file = Yii::app()->request->getParam('file');
        if (empty($file)) {
            throw new CHttpException(404, 'The specified file cannot be found.');
        }
        $xml_dir = Yii::getPathOfAlias('webroot') . '/xml';
        $shell_script_dir = Yii::getPathOfAlias('application') . "/runtime";
        //
        Utils::deleteFileFromProject($xml_dir, array($file . '.xml'));
        Utils::deleteFileFromProject($shell_script_dir, array($file . '.sh'));
        //
        Yii::app()->user->setFlash('result', 'Your job has been removed');
        $this->redirect($this->createUrl('site/index'));
    }

    public function actionSearch() {
        $this->render('search');
    }

    public function actionSearchNear() {
        $param = Yii::app()->request->getParam('address');
        $address = str_replace(array(' - ', '-', ' '), array('+', '+', '+'), $param);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://maps.google.com/maps/api/geocode/json?address={$address}&sensor=false&key=AIzaSyC1gLtynpOdw1XehaptfjtTwxApUdoelHQ",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $obj = new stdClass();
            $obj->status = "error";
            $obj->message = $err;
        } else {
            $jsonObject = json_decode($response);

            $responseObj = new stdClass();
            if ($jsonObject->status == "OK") {
                $resultObject = $jsonObject->results[0];

                $lat = $resultObject->geometry->location->lat;
                $lng = $resultObject->geometry->location->lng;

                $dist = 81;

                $cmd = Yii::app()->db->createCommand("CALL `geodist`(:mylat, :mylon, :dist);");
                $cmd->bindParam(':mylat', $lat, PDO::PARAM_STR);
                $cmd->bindParam(':mylon', $lng, PDO::PARAM_STR);
                $cmd->bindParam(':dist', $dist, PDO::PARAM_STR);
                $area_list = $cmd->queryAll();

                $responseObj->status = "OK";
                $responseObj->data = $area_list;
                $responseObj->lat = $lat;
                $responseObj->lng = $lng;
            } else {
                $responseObj->status = $jsonObject->error_message;
                $responseObj->data = "";
            }

            header('Content-Type: application/json');
            echo json_encode($responseObj);
        }
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error ', $error);
        }
    }

}
