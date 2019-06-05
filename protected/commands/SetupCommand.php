<?php

class SetupCommand extends CConsoleCommand {
    private $basePath;
    private $asset_dir;
    private $xml_dir;
    
    public function actionInit() {
        $this->basePath = realpath(dirname(__FILE__) . '/../../');
        $this->asset_dir = $this->basePath . '/assets';
        $this->xml_dir = $this->basePath . '/xml';
        
        if(!file_exists($this->asset_dir)) {
            mkdir($this->asset_dir);
        }
        if(!file_exists($this->xml_dir)) {
            mkdir($this->xml_dir);
        }
        $this->recurse_chown_chgrp($this->basePath, 'www-data', 'www-data');
        echo $this->basePath;
    }
    
    

    private function recurse_chown_chgrp($mypath, $uid, $gid) {
        $d = opendir($mypath);
        while (($file = readdir($d)) !== false) {
            if ($file != "." && $file != "..") {

                $typepath = $mypath . "/" . $file;

                //print $typepath. " : " . filetype ($typepath). "<BR>" ; 
                if (filetype($typepath) == 'dir') {
                    $this->recurse_chown_chgrp($typepath, $uid, $gid);
                }

                chown($typepath, $uid);
                chgrp($typepath, $gid);
            }
        }
    }

}
