<?php
declare(strict_types=1);

namespace Application\Form;

use LmcUser\Form\ProvidesEventsForm;
use Laminas\Form\Element;

class UserRegister extends ProvidesEventsForm{

    public function __construct($name = 'register') {
        parent::__construct($name);
        $this->add(
            [
                'name' => 'email',
                'options' => [
                    'label' => 'Email',
                ],
                'attributes' => [
                    'type' => 'email'
                ],
            ]
        );

        $this->add(
            array(
                'name' => 'screenname',
                'options' => array(
                    'label' => 'Screen Name',
                ),
                'attributes' => array(
                    'type' => 'text'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'password',
                'type' => 'password',
                'options' => array(
                    'label' => 'Password',
                ),
                'attributes' => array(
                    'type' => 'password'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'passwordVerify',
                'type' => 'password',
                'options' => array(
                    'label' => 'Password Verify',
                ),
                'attributes' => array(
                    'type' => 'password'
                ),
            )
        );

        $submitElement = new Element\Button('submit');
        $submitElement
        ->setLabel('Register')
        ->setAttributes(
            array(
                'type'  => 'submit',
            )
        );

        $this->add(
            $submitElement,
            array(
                'priority' => -100,
            )
        );

    }

    public function init()
    {
    }
}