<?php

namespace SCG\Form;

use Zend\Form\Form;

class SCGForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('scg');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'business',
            'type' => 'text',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'holding',
            'type' => 'number',
            'options' => [
                'label' => 'Holding(%)',
            ],
        ]);
        $this->add([
            'name' => 'telephone',
            'type' => 'tel',
            'options' => [
                'label' => 'Telepone',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
