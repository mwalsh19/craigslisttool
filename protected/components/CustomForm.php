<?php

class CustomForm extends CForm {

    public $inputElementClass = 'CustomFormInputElement';

    public function getActiveFormWidget() {
        if ($this->_activeForm !== null) {
            return $this->_activeForm;
        } else {
            return $this->getRoot()->_activeForm;
        }
    }

    public function renderBegin() {
        if ($this->getParent() instanceof self) {
            return '';
        } else {
            $options = $this->activeForm;
            if (isset($options['class'])) {
                $class = $options['class'];
                unset($options['class']);
            } else {
                $class = 'CActiveForm';
            }

            $options['action'] = $this->action;
            $options['method'] = $this->method;
            if (isset($options['htmlOptions'])) {
                foreach ($this->attributes as $name => $value) {
                    $options['htmlOptions'][$name] = $value;
                }
            } else {
                $options['htmlOptions'] = $this->attributes;
            }

            if (isset($options['htmlOptions']['class'])) {
                $options['htmlOptions']['class'].= ' form-horizontal';
            } else {
                $options['htmlOptions']['class'] = 'form-horizontal';
            }

            if (!empty($this->enctype)) {
                $options['htmlOptions']['enctype'] = $this->enctype;
            }

            ob_start();
            $this->_activeForm = $this->getOwner()->beginWidget($class, $options);
            return ob_get_clean() . "<div style=\"visibility:hidden\">" . CHtml::hiddenField($this->getUniqueID(), 1) . "</div>\n";
        }
    }

    public function renderElement($element) {

        if (is_string($element)) {
            if (($e = $this[$element]) === null && ($e = $this->getButtons()->itemAt($element)) === null) {
                return $element;
            } else {
                $element = $e;
            }
        }

        if ($element->getVisible()) {
            if ($element instanceof CFormInputElement) {
                if ($element->type === 'hidden') {
                    return "<div style=\"visibility:hidden\">\n" . $element->render() . "</div>\n";
                } else {
                    return "<div class=\"form-group\">\n" . $element->render() . "</div>\n";
                }
            } elseif ($element instanceof CFormButtonElement) {
                return $element->render() . "\n";
            } else {
                return $element->render();
            }
        }
        return '';
    }

    public function renderButtons() {
        $output = '';
        foreach ($this->getButtons() as $button) {
            $output.=$this->renderElement($button);
        }

        return $output !== '' ? "<div class=\"box-footer\">" . $output . "</div>\n" : '';
    }

    public function render() {
        $output = $this->renderBegin();

        foreach ($this->getElements() as $element) {
            $output .= $this->renderElement($element);
        }

        $output .= "\n" . $this->renderButtons() . "\n";

        $output .= $this->renderEnd();

        return $output;
    }

}
