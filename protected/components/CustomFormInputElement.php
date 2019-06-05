<?php

class CustomFormInputElement extends CFormInputElement {

    public $layout = "{label}\n<div class=\"col-sm-10\">\n{input}{error}\n</div>";

    public function render() {
        if ($this->type === 'hidden')
            return $this->renderInput();
        $output = array(
            '{label}' => $this->renderLabel(),
            '{input}' => $this->renderInput(),
            '{hint}' => $this->renderHint(),
            '{error}' => !$this->getParent()->showErrors ? '' : $this->renderError(),
        );

        return strtr($this->layout, $output);
    }

    public function renderLabel() {
        $options = array(
            'label' => $this->getLabel(),
            'required' => $this->getRequired(),
            'class' => 'control-label col-sm-2'
        );

        if (!empty($this->attributes['id'])) {
            $options['for'] = $this->attributes['id'];
        }

        return CHtml::activeLabel($this->getParent()->getModel(), $this->name, $options);
    }

}
