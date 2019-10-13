<?php

namespace SCGApi\Model;

use Zend\Filter\ToInt;
use Zend\Filter\StripTags;
use Zend\Filter\StringTrim;
use Zend\Validator\StringLength;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class SCG
{
    public $id;
    public $business;
    public $holding;
    public $telephone;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->business = !empty($data['business']) ? $data['business'] : null;
        $this->holding = !empty($data['holding']) ? $data['holding'] : null;
        $this->telephone = !empty($data['telephone']) ? $data['telephone'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'business' => $this->business,
            'holding' => $this->holding,
            'telephone' => $this->telephone,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'business',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 250,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'holding',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                ['name' => 'Regex', 'options' => ['pattern' => '/[0-9]/']],
            ],
        ]);

        $inputFilter->add([
            'name' => 'telephone',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 8,
                        'max' => 15,
                    ],
                ],
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[\d-]+$/'],
                    ],


            ],
        ]);

        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }
}
