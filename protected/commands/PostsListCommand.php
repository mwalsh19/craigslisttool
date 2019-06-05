<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once Yii::getPathOfAlias('application') . '/helpers/phpQuery-onefile.php';
spl_autoload_register(array('YiiBase', 'autoload'));

class PostsListCommand extends CConsoleCommand {

    private $urls = [
        'login' => 'https://accounts.craigslist.org/login',
        'home' => 'https://accounts.craigslist.org/login/home'
    ];

    public function actionInit($filter_page = '', $filter_date = '2015-05') {

        $response = $this->getData($this->urls['home'], false);

        if ($response->status == 'success') {
            if ($response->httpCode == '302') {
                $this->login($filter_page, $filter_date);
            } else {
                $this->parsePosts($filter_page, $filter_date);
            }
        }
    }

    private function login($filter_page, $filter_date) {
        Yii::log('USER NOT LOGGED IN, LOGIN IN NOW', 'trace', 'post.list');
        $response = $this->postData($this->urls['login'], array(
            'step' => 'confirmation',
            'rt' => '',
            'rp' => '',
            'p' => '0',
            'inputEmailHandle' => 'mwalsh@lacedagency.com',
            'inputPassword' => 'w00dm0use'));

        if ($response->status == 'success') {
            Yii::log('LOGGED', 'trace', 'post.list');
            $this->parsePosts($filter_page, $filter_date);
        } else {
            Yii::log('CAN\'T LOGIN', 'error', 'post.list');
            Yii::log($response->data, 'error', 'post.list');
        }
    }

    private function parsePosts($filter_page, $filter_date) {
//        set_time_limit(0);

        Yii::log('GET POSTS LIST FROM CRAIGLISTS', 'trace', 'post.list');

        $params = array(
            'filter_page' => $filter_page,
            'filter_cat' => 0,
            'filter_active' => 0,
            'show_tab' => 'postings',
            'filter_date' => $filter_date
        );
        $queryParams = http_build_query($params);

        $response = $this->getData($this->urls['home'] . '?' . $queryParams, false);

        $obj = new stdClass();
        $postsArray = array();
        $datesArray = array();

//        print_r($response);

        if ($response->status == 'success') {
            $dom = phpQuery::newDocument($response->data);
            $table = $dom->find('.accthp_postings');
            $rows = $table->find('tr');

            if (count($rows) > 0) {
                for ($index = 0; $index < count($rows); $index++) {
                    $isRowHeaders = $rows->eq($index)->hasClass('headers');

                    if (!$isRowHeaders) {

                        $status = $rows->eq($index)->find('.gc')->text();
                        $forms = $rows->eq($index)->find('form');

                        $formsHtml = '';
                        if (count($forms) > 0) {
                            for ($index1 = 0; $index1 < count($forms); $index1++) {
                                $rows->eq($index)->find(".buttons form:eq({$index1})")->attr('style', 'display: inline-block; margin-left: 5px;')->attr('target', '_blank');
                                $rows->eq($index)->find(".buttons form:eq({$index1}) input[type=\"submit\"]")->addClass('btn btn-primary btn-sm');
                                $formsHtml.= $rows->eq($index)->find(".buttons form:eq({$index1})");
                            }
                        }

                        $rows->eq($index)->find('.title a')->attr('target', '_blank');
                        $title = $rows->eq($index)->find('.title')->html();

                        $areacat = $rows->eq($index)->find('.areacat')->html();
                        $date = $rows->eq($index)->find('.dates')->text();
                        $postingID = $rows->eq($index)->find('.postingID')->text();

                        array_push($postsArray, array(
                            'status' => $status,
                            'actions' => $formsHtml,
                            'title' => $title,
                            'areacat' => $areacat,
                            'date' => $date,
                            'id' => $postingID
                        ));
                    }
                }

                $datesOptions = $dom->find('#datePick option');
                if (count($datesOptions) > 0) {
                    for ($index2 = 0; $index2 < count($datesOptions); $index2++) {
                        $dateOptionText = $datesOptions->eq($index2)->text();
                        $dateOptionVal = $datesOptions->eq($index2)->val();

                        $obj2 = new stdClass();
                        $obj2->text = $dateOptionText;
                        $obj2->value = $dateOptionVal;
                        $datesArray[] = $obj2;
                    }
                }
            } else {
                Yii::log('ROWS NOT FOUND - TOTAL: ' . count($rows), 'error', 'post.list');
                exit();
            }
        } else {
            Yii::log('CAN\'T LOAD HOME PAGE', 'error', 'post.list');
            Yii::log($response->data, 'error', 'post.list');
            exit();
        }

        $obj->dates = $datesArray;
        $obj->posts = $postsArray;
        header('Content-Type: application/json');
        echo json_encode($obj);
    }

    private function getData($url, $followLocation = true) {
        $cookejar = dirname(__FILE__) . '/../runtime/cookie-jar.txt';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_COOKIEJAR => $cookejar,
            CURLOPT_COOKIEFILE => $cookejar,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => $followLocation,
            CURLOPT_HTTPHEADER => array(
                "Referer: {$this->urls['login']}"
            ),
        ));

        $last_response = curl_exec($curl);
        $err = curl_error($curl);

        //echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        echo curl_getinfo($curl, CURLINFO_REDIRECT_URL);

        $resp = new stdClass();

        if ($err) {
            $resp->status = 'fail';
            $resp->data = $err;
        } else {
            $resp->status = 'success';
            $resp->data = $last_response;
            $resp->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        curl_close($curl);

        return $resp;
    }

    private function postData($url, $postData) {
        $cookejar = dirname(__FILE__) . '/../runtime/cookie-jar.txt';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => $cookejar,
            CURLOPT_COOKIEFILE => $cookejar,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_HTTPHEADER => array(
                "Referer: {$this->urls['login']}",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $last_response = curl_exec($curl);
        $err = curl_error($curl);

        $resp = new stdClass();

        if ($err) {
            $resp->status = 'fail';
            $resp->data = $err;
        } else {
            $resp->status = 'success';
            $resp->data = $last_response;
            $resp->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $resp->url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        }

        curl_close($curl);

        return $resp;
    }

}
