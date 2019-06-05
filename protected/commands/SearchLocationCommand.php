<?php

class SearchLocationCommand extends CConsoleCommand {

    public function actionRun() {
        $subareas = Array_areas::getSubareas();

        $array_areas = array();

        $counter = 0;
        foreach ($subareas as $subarea) {
            $address = str_replace(array(' - ', '-', ' '), array('+', '+', '+'), $subarea['subarea']) . "\n\r";
            if ($counter > 0 && $counter % 5 == 0) {
                sleep(1);
            }
            $result = $this->getLocation($address);

            if ($result->status == "OK") {
                $resultObject = $result->results[0];
                if (!empty($resultObject) && $resultObject->geometry->location) {
                    $array_areas[] = array('name' => $subarea['subarea'], 'lat' => $resultObject->geometry->location->lat, 'lng' => $resultObject->geometry->location->lng);
                }
            } else {
                echo $result->error_message;
                exit();
            }
            $counter++;
        }


        $builder = Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('tbl_area', $array_areas);
        $command->execute();
    }

    public function getLocation($address) {
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
            return json_decode($response);
        }
    }

}
