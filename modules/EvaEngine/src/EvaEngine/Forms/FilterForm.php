<?php

namespace Eva\EvaEngine\Forms;

class FilterForm extends \Phalcon\Forms\Form
{
    protected $elements = array(
        'keyword' =>     array(
            'name' => 'keyword',
            'type' => 'text',
            'options' => array(
                'label' => 'Keyword',
            ),
        ),
        'order' =>     array(
            'name' => 'order',
            'type' => 'text',
            'options' => array(
                'label' => 'order',
            ),
            'attributes' => array(
            ),
        ),
        'status' => array(
            'options' => array(
                'empty_option' => 'Post Status',
            ),
            'attributes' => array(
                'value' => '',
            ),
        ),
        'visibility' => array(
            'options' => array(
                'empty_option' => 'Select Visibility',
            ),
            'attributes' => array(
                'value' => '',
            ),
        ),

        'category' => array(
            'name' => 'category',
            'type' => 'select',
            'options' => array(
                'label' => 'Category',
                'empty_option' => 'Select Category',
            ),
            'attributes' => array(
                'value' => '',
            ),
        ),

        'tag' => array(
            'name' => 'tag',
            'type' => 'text',
            'options' => array(
                'label' => 'Text',
            ),
            'attributes' => array(
                'value' => '',
            ),
        ),

        'rows' =>     array(
            'name' => 'rows',
            'type' => 'text',
            'options' => array(
                'label' => 'Rows',
            ),
            'attributes' => array(
                'value' => 10,
            ),
        ),
    
    );


    public function initialize($entity = null, $options = null)
    {
        $name = new Text('identify');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The field is required'
            ))
        ));
        $this->add($name);

        // Password
        $password = new Password('password');
        $password->setLabel('Password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'The password is required'
            )),
            new StringLength(array(
                'min' => 6,
                'max' => 26,
                'messageMinimum' => 'Password is too short. Minimum 6 characters',
                'messageMaximum' => 'Password is too long. Maximum 26 characters'
            )),
        ));
        $this->add($password);
    }
}
