<?php

namespace Eva\EvaEngine;

use Phalcon\Annotations\Collection as Property;

class Form extends \Phalcon\Forms\Form
{
    protected $prefix;

    protected $exclude;

    protected $model;

    protected $values;

    protected $elementAlias = array(
        'check' => 'Phalcon\Forms\Element\Check',
        'date' => 'Phalcon\Forms\Element\Date',
        'email' => 'Phalcon\Forms\Element\Email',
        'file' => 'Phalcon\Forms\Element\File',
        'hidden' => 'Phalcon\Forms\Element\Hidden',
        'numeric' => 'Phalcon\Forms\Element\Numeric',
        'number' => 'Phalcon\Forms\Element\Numeric',
        'password' => 'Phalcon\Forms\Element\Password',
        'select' => 'Phalcon\Forms\Element\Select',
        'submit' => 'Phalcon\Forms\Element\Submit',
        'text' => 'Phalcon\Forms\Element\Text',
        'textarea' => 'Phalcon\Forms\Element\TextArea',
    );

    protected $validatorAlias = array(
        'between' => 'Phalcon\Validation\Validator\Between',
        'confirmation' => 'Phalcon\Validation\Validator\Confirmation',
        'email' => 'Phalcon\Validation\Validator\Email',
        'exclusionin' => 'Phalcon\Validation\Validator\ExclusionIn',
        'exclusion' => 'Phalcon\Validation\Validator\ExclusionIn',
        'identical' => 'Phalcon\Validation\Validator\Identical',
        'inclusionin' => 'Phalcon\Validation\Validator\InclusionIn',
        'inclusion' => 'Phalcon\Validation\Validator\InclusionIn',
        'presenceof' => 'Phalcon\Validation\Validator\PresenceOf',
        'regex' => 'Phalcon\Validation\Validator\Regex',
        'stringlength' => 'Phalcon\Validation\Validator\StringLength',
    );

    public function getValues()
    {
        return $this->values;
    }

    public function setValues(array $data)
    {
        if(!$data) {
            return $this;
        }
        foreach($data as $key => $value) {
            if($this->has($key)) {
                $this->get($key)->setDefault($value);
            }
        }
        $this->values = $data;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        if(!$this->model) {
            return $this;
        }

        /*
        $elements = $this->_elements;
        foreach($elements as $key => $element) {
            $element->setName($prefix . '[' . $element->getName() . ']');
        }
        */
        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }

    public function getExclude()
    {
        return $this->exclude;
    }

    public function setModel(\Phalcon\Mvc\Model $model, $autoParse = true)
    {
        $this->model = $model;
        $this->setEntity($model);
        $reader = new \Phalcon\Annotations\Adapter\Memory();
        $columns = $model->columnMap();
        $modelProperties = $reader->getProperties($model);
        $formProperties = $reader->getProperties($this);
        foreach($modelProperties as $key => $property) {
            //already added in initialize
            if($this->has($key)) {
                continue;
            }
            $formProperty = isset($formProperties[$key]) ? $formProperties[$key] : null;
            $element = $this->createElementByProperty($key, $property, $formProperty);
            if($element) {
                $this->add($element);
            }
        }
        return $this;
    }

    public function initializeFromAnnotations()
    {
        $reader = new \Phalcon\Annotations\Adapter\Memory();
        $formProperties = $reader->getProperties($this);
        foreach($formProperties as $key => $property) {
            $formProperty = isset($formProperties[$key]) ? $formProperties[$key] : null;
            $element = $this->createElementByProperty($key, $property);
            if($element && $element instanceof \Phalcon\Forms\ElementInterface) {
                $this->add($element);
            }
        }
        return $this;
    }

    protected function createElementByProperty($elementName, Property $baseProperty, Property $mergeProperty = null)
    {
        $elementType = 'Phalcon\Forms\Element\Text';
        if(!$baseProperty && !$mergeProperty) {
            return new $elementType($elementName);
        }

        $property = $mergeProperty && $mergeProperty->has('Type') ? $mergeProperty : $baseProperty;
        if($property->has('Type')) {
            $typeArguments = $property->get('Type')->getArguments();
            $alias = isset($typeArguments[0]) ? strtolower($typeArguments[0]) : null;
            $elementType = isset($this->elementAlias[$alias]) ? $this->elementAlias[$alias] : $elementType;
        }

        $property = $mergeProperty && $mergeProperty->has('Name') ? $mergeProperty : $baseProperty;
        if($property->has('Name')) {
            $arguments = $property->get('Name')->getArguments();
            $elementName = isset($arguments[0]) ? $arguments[0] : $elementName;
        }
        $element = new $elementType($elementName);

        $property = $mergeProperty && $mergeProperty->has('Attr') ? $mergeProperty : $baseProperty;
        if($property->has('Attr')) {
            $element->setAttributes($property->get('Attr')->getArguments());
        }

        $addValidator = function($property, $element, $validatorAlias) {
            foreach($property as $annotation) {
                if($annotation->getName() != 'Validator') {
                    continue;
                }
                $arguments = $annotation->getArguments();
                if(!isset($arguments[0])) {
                    continue;
                }
                $validatorName = strtolower($arguments[0]);
                if(!isset($validatorAlias[$validatorName])) {
                    continue;
                }
                $validator = $validatorAlias[$validatorName];
                $element->addValidator(new $validator($arguments));
            }        
            return $element;
        };
        if($baseProperty->has('Validator')) {
            $element = $addValidator($baseProperty, $element, $this->validatorAlias);
        }
        if($mergeProperty && $mergeProperty->has('Validator')) {
            $element = $addValidator($mergeProperty, $element, $this->validatorAlias);
        }


        $property = $mergeProperty && $mergeProperty->has('Options') ? $mergeProperty : $baseProperty;
        if($property->has('Options')) {
            $element->setAttributes($property->get('Options')->getArguments());
        }

        $addOption = function($property, $element) {
            $options = array();
            foreach($property as $annotation) {
                if($annotation->getName() != 'Option') {
                    continue;
                }
                $options += $annotation->getArguments();
            }
            $element->setOptions($options);
            return $element;
        };

        if($baseProperty->has('Option')) {
            $element = $addOption($baseProperty, $element);
        }
        if($mergeProperty && $mergeProperty->has('Option')) {
            $element = $addOption($mergeProperty, $element);
        }
        return $element;
    }

    public function registerElementAlias($elementAlias, $elementClass)
    {
        $this->elementAlias[$elementAlias] = $elementClass;
        return $this;
    }

    public function getElementAlias()
    {
        return $this->elementAlias;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function render($name, $attributes = null)
    {
        if(!$this->prefix) {
            return parent::render($name, $attributes);
        }
        $attributes = array_merge(array(
           'name' => $this->prefix . '[' . $this->get($name)->getName() . ']'
        ), $attributes);
        return parent::render($name, $attributes);
    }

    public function addForm($prefix, $form)
    {
    }

}
