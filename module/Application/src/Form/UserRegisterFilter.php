<?php
declare(strict_types=1);

namespace Application\Form;

use LmcUser\InputFilter\ProvidesEventsInputFilter;
use LmcUser\Options\RegistrationOptionsInterface;

class UserRegisterFilter extends ProvidesEventsInputFilter
{
    protected $emailValidator;
    protected $screennameValidator;
    
    /**
     * @var RegistrationOptionsInterface
     */
    protected $options;
    
    public function __construct($emailValidator, $screennameValidator, RegistrationOptionsInterface $options)
    {
        $this->setOptions($options);
        $this->emailValidator = $emailValidator;
        $this->screennameValidator = $screennameValidator;
        
        $this->add(
            array(
                'name'       => 'screenname',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 255,
                        ),
                    ),
                    $this->screennameValidator,
                ),
            )
        );

        $this->add(
            array(
                'name'       => 'email',
                'required'   => true,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                    $this->emailValidator
                ),
            )
        );

        $this->add(
            array(
                'name'       => 'password',
                'required'   => true,
                'filters'    => array(array('name' => 'StringTrim')),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            )
            );
        
        $this->add(
            array(
                'name'       => 'passwordVerify',
                'required'   => true,
                'filters'    => array(array('name' => 'StringTrim')),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token' => 'password',
                        ),
                    ),
                ),
            )
            );
    }
    
    public function getEmailValidator()
    {
        return $this->emailValidator;
    }
    
    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;
        return $this;
    }
    
    public function getUsernameValidator()
    {
        return $this->screennameValidator;
    }
    
    public function setUsernameValidator($usernameValidator)
    {
        $this->screennameValidator = $usernameValidator;
        return $this;
    }
    
    /**
     * set options
     *
     * @param RegistrationOptionsInterface $options
     */
    public function setOptions(RegistrationOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * get options
     *
     * @return RegistrationOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }
}
