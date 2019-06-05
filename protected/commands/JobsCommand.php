<?php

spl_autoload_unregister(array('YiiBase', 'autoload'));
require_once Yii::getPathOfAlias('application') . '/helpers/phpQuery-onefile.php';
spl_autoload_register(array('YiiBase', 'autoload'));

class JobsCommand extends CConsoleCommand {

    private $urls = [
        'login' => 'https://accounts.craigslist.org/login',
        'home' => 'https://accounts.craigslist.org/login/home',
        'postNew' => 'https://post.craigslist.org/c/us?lang=en',
        'logout' => 'https://accounts.craigslist.org/logout',
    ];
    private $index = 0;
    private $xml;
    private $session_url;
    private $host = 'http://joinswift.com';
    private $xml_name = '';

    public function actionInit($index = 0, $xml = '') {
        $this->xml_name = str_replace('.xml', '', $xml);

        $this->index = (int) $index;
        $xml_file = dirname(__FILE__) . '/../../xml/' . $xml;

        if (file_exists($xml_file)) {
            $this->xml = simplexml_load_file($xml_file);
        } else {
            Yii::log('THERE IS NO XML FILE TO PARSE', 'trace', 'jobs');
            $this->retry();
            exit();
        }

        //test
        //var_dump($this->getNewPostJobData());
        //exit;

        $response = $this->getData($this->urls['home'], false);

        if ($response->status == 'success') {
            if ($response->httpCode == '302') {
                //need login
                $this->login();
            } else {
                $this->chooseArea();
            }
        } else {
            $this->retry();
            exit();
        }
    }

    private function normalizeArea($area) {
        $normalized_area = str_replace('-', '', $area);
        $normalized_area = str_replace('  ', '_', $normalized_area);
        $normalized_area = str_replace(' ', '_', $normalized_area);
        return $normalized_area;
    }

    private function getNewPostJobData() {
        $title = $this->xml->title;
        $description = $this->xml->description;
        $requirements = $this->xml->requirements;
        $compensation = $this->xml->compensation;

        $type = '';
        if ($this->xml->xmlVersion == 'v2') {
            $type = $this->xml->drivertype;
        } else {
            $type = strtolower($this->xml->drivertype);
        }

        //$short_url = substr($this->xml->areas->area[$this->index]->title, -2);
        $short_url = $this->normalizeArea($this->xml->areas->area[$this->index]->title);

        $getState = $this->getUnitedStates();
        $state = ucwords(strtolower($getState[$short_url]));

        $url_only = '';
        $url_apply = '';

        if ($this->xml->xmlVersion == 'v2') {
            $url_only = $this->host . '/landing-pages/' . $type . '/lp/' . strtolower($short_url);
            $url_apply = '<a href="' . $this->host . '/landing-pages/' . $type . '/lp/' . strtolower($short_url) . '" target="_blank">Click To Apply Online</a>';
        } else {
            $url_only = $this->host . '/landing-pages/l/craigslist/' . $type . '/' . strtolower($short_url);
            $url_apply = '<a href="' . $this->host . '/landing-pages/l/craigslist/' . $type . '/' . strtolower($short_url) . '" target="_blank">Click To Apply Online</a>';
        }

        $title = str_replace('%%area%%', $state, $title);
        $description = str_replace('%%area%%', $state, $description);
        $description = str_replace('%%url%%', $url_only, $description);
        $body = $description . '<br>' . $requirements;

        return array(
            'title' => (string) $title,
            'body' => $body,
            'compensation' => (string) $compensation
        );
    }

    private function doPlubish($options = array()) {
        Yii::log('PUBLISHING DRAFT NOW WAIT', 'trace', 'jobs');
        echo "PUBLISHING DRAFT NOW WAIT\n";

        $postData = array(
            'continue' => $options['cont'],
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go']
        );

        var_dump($postData) . "\n";

        $response = $this->postData($this->session_url, $postData);

        if ($response->status == 'success') {
            if (strpos($response->url, 's=mailoop') !== false) {
                Yii::log('DRAFT PUBLISHED SUCCESSFULLY, CHECK YOUR MAIL', 'error', 'jobs');
                exit();
            }
        } else {
            Yii::log('CAN\'T PUBLISH DRAFT', 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function editimage($options = array()) {
        Yii::log('EDITING IMAGE OF DRAFT', 'trace', 'jobs');
        echo "EDITING IMAGE OF DRAFT\n";

        $postData = array(
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'a' => $options['a'],
            'go' => $options['go']
        );

        var_dump($postData) . "\n";

        $response = $this->postData($options['url'], $postData);

        if ($response->status == 'success') {
            if (strpos($response->url, 's=preview') !== false) {
                $response = $this->getData($this->session_url . '?s=preview');

                $dom = phpQuery::newDocument($response->data);
                $draftWarning = $dom->find('.draft_warning');
                $formAction = $draftWarning->find('form')->attr('action');
                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                $options = array(
                    'url' => $formAction,
                    'cryptedStepCheck' => $cryptedStepCheck,
                    'cont' => 'y',
                    'go' => 'Continue'
                );
                $this->doPlubish($options);
            } else {
                Yii::log('CAN\'T PREVIEW DRAFT FROM IMAGE EDIT', 'error', 'jobs');
                $this->retry();
                exit();
            }
        } else {
            Yii::log('CAN\'T EDIT IMAGE OF DRAFT', 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function createPost($options = array()) {
        Yii::log('CREATING DRAFT WAIT', 'trace', 'jobs');
        echo "CREATING DRAFT WAIT\n";

        $postData = array(
            'PostingTitle' => $options['title'],
            'PostingBody' => $options['body'],
            'remuneration' => $options['compensation'],
            'Privacy' => 'A',
            'employment_type' => '1',
            'FromEMail' => 'noreply@swifttrans.com',
            'ConfirmEMail' => 'noreply@swifttrans.com',
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go'],
            'contact_phone' => '',
            'contact_name' => '',
            'GeographicArea' => '',
            'xstreet0' => '',
            'xstreet1' => '',
            'city' => '',
            'region' => '',
            'postal' => '',
            'postal' => '90277',
            'PONumber' => '',
            'id2' => '1614x1222X1614x799X2560x1600',
            'browserinfo' => '%7B%0A%09%22plugins%22%3A%20%22Plugin%200%3A%20Chrome%20PDF%20Viewer%3B%20%3B%20mhjfbmdgcfjbbpaeojofohoefgiehjai%3B%20%28%3B%20application/pdf%3B%20pdf%29.%20Plugin%201%3A%20Chrome%20PDF%20Viewer%3B%20Portable%20Document%20Format%3B%20internal-pdf-viewer%3B%20%28Portable%20Document%20Format%3B%20application/x-google-chrome-pdf%3B%20pdf%29.%20Plugin%202%3A%20Chrome%20Remote%20Desktop%20Viewer%3B%20This%20plugin%20allows%20you%20to%20securely%20access%20other%20computers%20that%20have%20been%20shared%20with%20you.%20To%20use%20this%20plugin%20you%20must%20first%20install%20the%20%3Ca%20href%3D%5C%22https%3A//chrome.google.com/remotedesktop%5C%22%3EChrome%20Remote%20Desktop%3C/a%3E%20webapp.%3B%20internal-remoting-viewer%3B%20%28%3B%20application/vnd.chromium.remoting-viewer%3B%20%29.%20Plugin%203%3A%20Native%20Client%3B%20%3B%20internal-nacl-plugin%3B%20%28Native%20Client%20Executable%3B%20application/x-nacl%3B%20%29%20%28Portable%20Native%20Client%20Executable%3B%20application/x-pnacl%3B%20%29.%20Plugin%204%3A%20Shockwave%20Flash%3B%20Shockwave%20Flash%2019.0%20r0%3B%20PepperFlashPlayer.plugin%3B%20%28Shockwave%20Flash%3B%20application/x-shockwave-flash%3B%20swf%29%20%28FutureSplash%20Player%3B%20application/futuresplash%3B%20spl%29.%20Plugin%205%3A%20Widevine%20Content%20Decryption%20Module%3B%20Enables%20Widevine%20licenses%20for%20playback%20of%20HTML%20audio/video%20content.%20%28version%3A%201.4.8.824%29%3B%20widevinecdmadapter.plugin%3B%20%28Widevine%20Content%20Decryption%20Module%3B%20application/x-ppapi-widevine-cdm%3B%20%29.%20%22%2C%0A%09%22timezone%22%3A%20360%2C%0A%09%22video%22%3A%20%222560x1600x24%22%2C%0A%09%22supercookies%22%3A%20%22DOM%20localStorage%3A%20Yes%2C%20DOM%20sessionStorage%3A%20Yes%2C%20IE%20userData%3A%20No%22%0A%7D',
        );

        var_dump($postData) . "\n";

        //echo $this->session_url;
        $response = $this->postData($this->session_url, $postData, $option = '100fix');

        if ($response->status == 'success') {
            //$response = $this->getData($this->session_url . '?s=preview');

            if (strpos($response->url, 's=preview') !== false) {
                $response = $this->getData($this->session_url . '?s=preview');

                $dom = phpQuery::newDocument($response->data);
                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                if ($response->status == 'success') {
                    $formAction = $dom->find('form')->attr('action');

                    $options = array(
                        'url' => $formAction,
                        'cryptedStepCheck' => $cryptedStepCheck,
                        'cont' => 'y',
                        'go' => 'Continue'
                    );

                    $this->doPlubish($options);
                } else {
                    Yii::log('CAN\'T PREVIEW DRAFT', 'error', 'jobs');
                    $this->retry();
                    exit();
                }
            } else if (strpos($response->url, 's=editimage') !== false) {
                $response = $this->getData($this->session_url . '?s=editimage');

                $dom = phpQuery::newDocument($response->data);
                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                if ($response->status == 'success') {
                    $formAction = $dom->find('form')->attr('action');

                    $options = array(
                        'url' => $formAction,
                        'cryptedStepCheck' => $cryptedStepCheck,
                        'a' => 'fin',
                        'go' => 'Done with Images'
                    );

                    $this->editimage($options);
                } else {
                    Yii::log('CAN\'T EDIT IMAGE OF DRAFT', 'error', 'jobs');
                    $this->retry();
                    exit();
                }
            }
        } else {
            Yii::log('CAN\'T CREATE DRAFT', 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function chooseSubArea($options = array()) {
        Yii::log('CHOOSING SUBAREA', 'trace', 'jobs');
        echo "CHOOSING SUBAREA\n";

        $subarea = explode('-', $this->xml->areas->area[$this->index]->code);
        $n = $subarea[1];

        $postData = array(
            'n' => $n,
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go'],
        );

        var_dump($postData) . "\n";

        $response = $this->postData($this->session_url, $postData);

        if ($response->status == 'success') {
            $response = $this->getData($this->session_url . '?s=edit');

            if ($response->status == 'success') {
                $jobDataOptions = $this->getNewPostJobData();
                $dom = phpQuery::newDocument($response->data);
                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();
                $mapCheckBox = $dom->find('#wantamap');
                $mapCheckBox->removeAttr('checked');

                $this->createPost(array_merge($jobDataOptions, array(
                    'cryptedStepCheck' => $cryptedStepCheck,
                    'go' => $options['go'],
                )));
            } else {
                Yii::log('WAS NOT ABLE TO GO TO EDIT SECTION', 'error', 'jobs');
                $this->retry();
                exit();
            }
        } else {
            Yii::log('CAN\'T CHOOSE SUBAREA', 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function goToCreatePostOrChooseSubArea($options = array()) {
        Yii::log('CREATING DRAFT OR CHOOSING SUBAREA', 'trace', 'jobs');
        echo "CREATING DRAFT OR CHOOSING SUBAREA\n";

        $postData = array(
            'id' => $options['id'],
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go'],
        );

        var_dump($postData) . "\n";

        $response = $this->postData($this->session_url, $postData);

        if ($response->status == 'success') {
            if (strpos($response->url, 's=edit') !== false) {

                $response = $this->getData($this->session_url . '?s=edit');

                if ($response->status == 'success') {
                    $dom = phpQuery::newDocument($response->data);
                    $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                    $postJobDataOptions = $this->getNewPostJobData();

                    $this->createPost(array_merge($postJobDataOptions, array(
                        'cryptedStepCheck' => $cryptedStepCheck,
                        'go' => $options['go'],
                    )));
                } else {
                    Yii::log('WAS NOT ABLE TO GO TO EDIT SECTION', 'error', 'jobs');
                    $this->retry();
                    exit();
                }
            } else if (strpos($response->url, 's=subarea') !== false) {
                $response = $this->getData($this->session_url . '?s=subarea');

                if ($response->status == 'success') {
                    $dom = phpQuery::newDocument($response->data);
                    $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                    $this->chooseSubArea(array(
                        'cryptedStepCheck' => $cryptedStepCheck,
                        'go' => 'Continue'
                    ));
                } else {
                    Yii::log('WAS NOT ABLE TO GO TO SUBAREA SECTION', 'error', 'jobs');
                    $this->retry();
                    exit();
                }
            } else {
                Yii::log('WAS NOT ABLE TO GO TO SUBAREA SECTION', 'error', 'jobs');
                exit();
            }
        } else {
            Yii::log('WAS DRAFT POST DATA HAS REJECTED ', 'error', 'jobs');
//            Yii::log($response->data, 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function chooseCat($options = array()) {
        Yii::log('CHOOSING CAT', 'trace', 'jobs');
        echo "CHOOSING CAT\n";

        $postData = array(
            'id' => $options['id'],
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go'],
        );

        var_dump($postData) . "\n";

        $response = $this->postData($this->session_url, $postData);

        if ($response->status == 'success') {
            $response = $this->getData($this->session_url . '?s=cat');

            if ($response->status == 'success') {
                $dom = phpQuery::newDocument($response->data);

                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                $this->goToCreatePostOrChooseSubArea(array(
                    'id' => 125,
                    'cryptedStepCheck' => $cryptedStepCheck,
                    'go' => 'Continue'
                ));
            } else {
                Yii::log('WAS NOT ABLE TO GO TO CAT SECTION', 'error', 'jobs');
                $this->retry();
                exit();
            }
        } else {
            Yii::log('CAN\'T CHOOSE CAT', 'error', 'jobs');
//            Yii::log($response->data, 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function chooseType($options = array()) {
        Yii::log('CHOOSING TYPE', 'trace', 'jobs');
        echo "CHOOSING TYPE\n";

        $postData = array(
            'n' => $options['n'],
            'cryptedStepCheck' => $options['cryptedStepCheck'],
            'go' => $options['go'],
        );

        var_dump($postData) . "\n";

        $response = $this->postData($this->session_url, $postData);

        if ($response->status == 'success') {
            $response = $this->getData($this->session_url . '?s=type');

            if ($response->status == 'success') {
                $dom = phpQuery::newDocument($response->data);

                $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

                $this->chooseCat(array(
                    'id' => 'jo',
                    'cryptedStepCheck' => $cryptedStepCheck,
                    'go' => 'Continue'
                ));
            } else {
                Yii::log('WAS NOT ABLE TO GO TO TYPE SECTION', 'error', 'jobs');
                $this->retry();
                exit();
            }
        } else {
            Yii::log('CAN\'T CHOOSE TYPE', 'error', 'jobs');
//            Yii::log($response->data, 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function chooseArea() {
        Yii::log('**********************************************************************', 'trace', 'jobs');
        Yii::log('<strong>STARTING DRAFT CREATION NUMBER ' . ($this->index + 1) . '</strong>', 'trace', 'jobs');
        echo "STARTING DRAFT CREATION NUMBER\n";

        $response = $this->getData($this->urls['postNew']);

        if ($response->status == 'success') {
            $dom = phpQuery::newDocument($response->data);

            $form = $dom->find('form.picker');
            $area = explode('-', $this->xml->areas->area[$this->index]->code);
            $n = $area[0];
            $cryptedStepCheck = $dom->find('input[name=cryptedStepCheck]')->val();

            //echo "cryptedStepCheck " . $cryptedStepCheck . "\n";

            $this->session_url = $form->attr('action');

            $this->chooseType(array(
                'n' => $n,
                'cryptedStepCheck' => $cryptedStepCheck,
                'go' => 'Continue'
            ));
        } else {
            Yii::log('CAN\'T CHOOSE AREA', 'error', 'jobs');
//            Yii::log($response->data, 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function login() {
        Yii::log('USER NOT LOGGED IN, LOGIN IN NOW', 'trace', 'jobs');
        echo "USER NOT LOGGED IN, LOGIN IN NOW\n";

        $response = $this->postData($this->urls['login'], array(
            'step' => 'confirmation',
            'rt' => '',
            'rp' => '',
            'p' => '0',
            'inputEmailHandle' => 'mwalsh@lacedagency.com',
            'inputPassword' => 'w00dm0use'));
        if ($response->status == 'success') {
            Yii::log('LOGGED', 'trace', 'jobs');
            $this->chooseArea();
        } else {
            Yii::log('CAN\'T LOGIN', 'error', 'jobs');
//            Yii::log($response->data, 'error', 'jobs');
            $this->retry();
            exit();
        }
    }

    private function retry() {
        $applicationDir = Yii::getPathOfAlias('application');
        $retryPosts = $applicationDir . "/runtime/{$this->xml_name}_temp.txt";

        if (!file_exists($retryPosts)) {
            touch($retryPosts); //Acces moment and modification
            chmod($retryPosts, 0755); //Set permisions
        }
        $area_subarea = $this->xml->areas->area[$this->index]->title;

        $fh = fopen($retryPosts, 'w');
        $current = "{$area_subarea} | {$this->index}\n";
        fwrite($fh, $current);
        fclose($fh);
    }

    public function actionRetryPosts($file = '') {
        $applicationDir = Yii::getPathOfAlias('application');
        $retryPosts = $applicationDir . "/runtime/{$file}_temp.txt";
        $fileJobsLog = $applicationDir . "/runtime/jobs.log";

        if (file_exists($retryPosts)) {

            $retryPostsContent = file_get_contents($retryPosts);

            $fh = fopen($fileJobsLog, 'a');

            $startSeparator = "\n\n***************************************************\n";
            fwrite($fh, $startSeparator);

            $someContent = "SOME DRAFT WEREN'T CREATED\n";
            $someContent.= "AREA | SUBAREA | INDEX\n";

            fwrite($fh, $someContent);
            fwrite($fh, $retryPostsContent);

            $endSeparator = "***************************************************\n\n";
            fwrite($fh, $endSeparator);

            fclose($fh);

            @unlink($retryPosts);
        }
    }

    private function getData($url, $followLocation = true) {
        $cookejar = dirname(__FILE__) . '/../runtime/cookie-jar.txt';

        $curl = curl_init();
        echo "GET\n";
        echo "GET URL " . $url . "\n";

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_VERBOSE => 0,
            CURLOPT_COOKIEJAR => $cookejar,
            CURLOPT_COOKIEFILE => $cookejar,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => $followLocation,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36',
            CURLOPT_HTTPHEADER => array(
                "Referer:" . $this->urls['login']
            ),
        ));

        $last_response = curl_exec($curl);
        $err = curl_error($curl);

        echo "EFFECTIVE URL " . curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "\n";
        echo "REDIRECT URL " . curl_getinfo($curl, CURLINFO_REDIRECT_URL) . "\n";
        echo curl_getinfo($curl, CURLINFO_HTTP_CODE) . "\n";

        //echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //echo curl_getinfo($curl, CURLINFO_REDIRECT_URL);

        $resp = new stdClass();

        if ($err) {
            $resp->status = 'fail';
            $resp->data = $err;
        } else {
            $resp->status = 'success';
            $resp->data = $last_response;
            $resp->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $resp->url = $resp->httpCode == 302 ? curl_getinfo($curl, CURLINFO_REDIRECT_URL) : curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        }

        curl_close($curl);

        echo "**********************************************************************\n";

        return $resp;
    }

    private function postData($url, $postData, $option = false) {
        $cookejar = dirname(__FILE__) . '/../runtime/cookie-jar.txt';

        $curl = curl_init();
        //echo var_dump($postData);
        echo "POST\n";
        echo "POST URL " . $url . "\n";

        if ($option == '100fix') {
            $headers = array(
                "Referer:" . $this->urls['login'],
                "content-type: application/x-www-form-urlencoded"
            );
        } else {
            $headers = array(
                "Referer:" . $this->urls['login'],
                "content-type: application/x-www-form-urlencoded"
            );
        }


        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_VERBOSE => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => $cookejar,
            CURLOPT_COOKIEFILE => $cookejar,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($postData),
//            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FAILONERROR => 1,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36'
        ));

        $last_response = curl_exec($curl);
        $err = curl_error($curl);

        echo "EFFECTIVE URL " . curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) . "\n";
        echo "REDIRECT URL " . curl_getinfo($curl, CURLINFO_REDIRECT_URL) . "\n";
        echo curl_getinfo($curl, CURLINFO_HTTP_CODE) . "\n";

        $resp = new stdClass();

        if ($err) {
            $resp->status = 'fail';
            $resp->data = $err;
            echo "HAS ERROR\n";
            echo $resp->data . "\n";
        } else {
            $resp->status = 'success';
            $resp->data = $last_response;
            $resp->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $resp->url = $resp->httpCode == 302 ? curl_getinfo($curl, CURLINFO_REDIRECT_URL) : curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        }

        curl_close($curl);

        echo "**********************************************************************\n";

        return $resp;
    }

    private function getUnitedStates() {
        return array(
            'AL' => 'ALABAMA',
            'AK' => 'ALASKA',
            'AS' => 'AMERICAN SAMOA',
            'AZ' => 'ARIZONA',
            'AR' => 'ARKANSAS',
            'CA' => 'CALIFORNIA',
            'CO' => 'COLORADO',
            'CT' => 'CONNECTICUT',
            'DE' => 'DELAWARE',
            'DC' => 'DISTRICT OF COLUMBIA',
            'FM' => 'FEDERATED STATES OF MICRONESIA',
            'FL' => 'FLORIDA',
            'GA' => 'GEORGIA',
            'GU' => 'GUAM GU',
            'HI' => 'HAWAII',
            'ID' => 'IDAHO',
            'IL' => 'ILLINOIS',
            'IN' => 'INDIANA',
            'IA' => 'IOWA',
            'KS' => 'KANSAS',
            'KY' => 'KENTUCKY',
            'LA' => 'LOUISIANA',
            'ME' => 'MAINE',
            'MH' => 'MARSHALL ISLANDS',
            'MD' => 'MARYLAND',
            'MA' => 'MASSACHUSETTS',
            'MI' => 'MICHIGAN',
            'MN' => 'MINNESOTA',
            'MS' => 'MISSISSIPPI',
            'MO' => 'MISSOURI',
            'MT' => 'MONTANA',
            'NE' => 'NEBRASKA',
            'NV' => 'NEVADA',
            'NH' => 'NEW HAMPSHIRE',
            'NJ' => 'NEW JERSEY',
            'NM' => 'NEW MEXICO',
            'NY' => 'NEW YORK',
            'NC' => 'NORTH CAROLINA',
            'ND' => 'NORTH DAKOTA',
            'MP' => 'NORTHERN MARIANA ISLANDS',
            'OH' => 'OHIO',
            'OK' => 'OKLAHOMA',
            'OR' => 'OREGON',
            'PW' => 'PALAU',
            'PA' => 'PENNSYLVANIA',
            'PR' => 'PUERTO RICO',
            'RI' => 'RHODE ISLAND',
            'SC' => 'SOUTH CAROLINA',
            'SD' => 'SOUTH DAKOTA',
            'TN' => 'TENNESSEE',
            'TX' => 'TEXAS',
            'UT' => 'UTAH',
            'VT' => 'VERMONT',
            'VI' => 'VIRGIN ISLANDS',
            'VA' => 'VIRGINIA',
            'WA' => 'WASHINGTON',
            'WV' => 'WEST VIRGINIA',
            'WI' => 'WISCONSIN',
            'WY' => 'WYOMING',
            'AE' => 'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
            'AA' => 'ARMED FORCES AMERICA (EXCEPT CANADA)',
            'AP' => 'ARMED FORCES PACIFIC');
    }

}
