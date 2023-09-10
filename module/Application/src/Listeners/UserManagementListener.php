<?php
declare(strict_types=1);
namespace Application\Listeners;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;

class UserManagementListener extends AbstractListenerAggregate {
    public function attach(EventManagerInterface $events, $priority = -1)
    {
        $this->listeners[] = $events->getSharedManager()
        ->attach('LmcUser\Service\User', 'register.post', [$this, 'onMemberRegisterSendAnEmail'], 2);
        $this->listeners[] = $events->getSharedManager()
        ->attach('LmcUser\Service\User', 'register.post', [$this, 'onMemberRegisterAssignRole'], 1);
    }

    public function onMemberRegisterSendAnEmail($e): void
    {
        $params = $e->getParams();
        $userObject = $params['user'];
        $filePath = realpath(__DIR__ . '/../../../../data/mail') . '/';
        //        $newRole = $target->getServiceManager()->get('your_email_service');
        $fileTransport = new \Laminas\Mail\Transport\File(
            new \Laminas\Mail\Transport\FileOptions(
            [
                'path'             => $filePath,
                'callback' => function (\Laminas\Mail\Transport\File $transport) {
                return sprintf(
                    'Message_%s_%s.txt',
                    date('Y-m-d_H-i'),
                    \Laminas\Math\Rand::getString(8)
                    );
                },
            ])
        );
        $message = new \Laminas\Mail\Message();
        $message->addTo($userObject->getEmail())
            ->addFrom('contact@youradmin.com')
            ->setSubject('Email confirmation code.')
            ->setBody('Enter your message here what ever suits you.');
        $fileTransport->send($message);
    }

    public function onMemberRegisterAssignRole($e){
        $target = $e->getTarget();
        $params = $e->getParams();
        $userObject = $params['user'];
        $newRole = $target->getServiceManager()
        ->get('doctrine.entitymanager.orm_default')
        ->getRepository( \Application\Entity\HierarchicalRole::class)
        ->findOneBy(['id' => 2]);
        $userObject->addRoles($newRole);

        $target->getUserMapper()->update($userObject);
    }
}
