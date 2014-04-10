<?php

namespace Eva\EvaEngine;

class Form extends \Phalcon\Forms\Form
{
    protected $prefix;

    protected $exclude;

    protected $model;

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
            $element = $this->getElementByPropertyAnnotations($key, $property, $formProperty);
            if($element) {
                $this->add($element);
            }
        }
        return $this;
    }

    protected function getElementByPropertyAnnotations($elementName, \Phalcon\Annotations\Collection $modelProperty, \Phalcon\Annotations\Collection $formProperty = null)
    {
        $elementType = 'Phalcon\Forms\Element\Text';
        if(!$formProperty) {
            return new $elementType($elementName);
        }

        if($formProperty->has('Type')) {
            $typeArguments = $formProperty->get('Type')->getArguments();
            $alias = isset($typeArguments[0]) ? strtolower($typeArguments[0]) : null;
            $elementType = isset($this->elementAlias[$alias]) ? $this->elementAlias[$alias] : $elementType;
        }

        if($formProperty->has('Name')) {
            $arguments = $formProperty->get('Name')->getArguments();
            $elementName = isset($arguments[0]) ? $arguments[0] : $elementName;
        }

        $element = new $elementType($elementName);
        if($formProperty->has('Attr')) {
            $element->setAttributes($formProperty->get('Attr')->getArguments());
        }

        if($formProperty->has('Validator')) {
            foreach($formProperty as $annotation) {
                if($annotation->getName() != 'Validator') {
                    continue;
                }
                $arguments = $annotation->getArguments();
                if(!isset($arguments[0])) {
                    continue;
                }
                $validatorName = strtolower($arguments[0]);
                if(!isset($this->validatorAlias[$validatorName])) {
                    continue;
                }
                $validator = $this->validatorAlias[$validatorName];
                $element->addValidator(new $validator($arguments));
            }
        }

        if($formProperty->has('Options')) {
            $element->setAttributes($formProperty->get('Options')->getArguments());
        }

        if($formProperty->has('Option')) {
            $options = array();
            foreach($formProperty as $annotation) {
                if($annotation->getName() != 'Option') {
                    continue;
                }
                $options += $annotation->getArguments();
            }
            $element->setOptions($options);
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
