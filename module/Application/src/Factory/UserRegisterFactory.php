<?php

namespace Application\Factory;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Form;
use Application\Validator;

class UserRegisterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        $options = $serviceManager->get('lmcuser_module_options');
        $form = new Form\UserRegister(null, $options);
        
        //$form->setCaptchaElement($sm->get('lmcuser_captcha_element'));
        $form->setHydrator($serviceManager->get('lmcuser_register_form_hydrator'));
        $form->setInputFilter(
            new Form\UserRegisterFilter(
                new Validator\NoRecordExists(
                    array(
                        'mapper' => $serviceManager->get('lmcuser_user_mapper'),
                        'key'    => 'email'
                    )
                    ),
                new Validator\NoRecordExists(
                    array(
                        'mapper' => $serviceManager->get('lmcuser_user_mapper'),
                        'key'    => 'screenname'
                    )
                ),
                $options
                )
            );
        
        return $form;
    }
}
