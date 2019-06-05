<?php
Yii::import('application.extensions.SAImageDisplayer');
class CSSImageDisplayer extends SAImageDisplayer {
    
    protected function createImagesIfNotExists() {
        if (!file_exists($this->imageFile) && !$this->isDefault) {
            Yii::import('application.extensions.image.Image');
            $image_info = getimagesize($this->originalFile);
            $master = $image_info[0]>$image_info[1]? Image::HEIGHT :  Image::WIDTH;
            $image = new Image($this->originalFile);
            $image->quality(80);
            $image->resize($this->width, $this->height, $master);
            $image->save($this->imageFile);
        }
    }

    public function run() {
        echo '<div style="background: url(' . $this->src . ') no-repeat center center; background-size: cover;" class="' . $this->class . '"></div>';
    }

    public function toString() {
        return '<div style="background-size: cover; background: url(' . $this->src . ') no-repeat center center; background-size: cover;" class="' . $this->class . '"></div>';
    }

}
