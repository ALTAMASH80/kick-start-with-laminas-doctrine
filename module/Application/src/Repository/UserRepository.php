<?php

namespace Application\Repository;

use LmcUserDoctrineORM\Mapper\User as LmcUserMapper;
use LmcUser\Entity\UserInterface;

class UserRepository extends LmcUserMapper{
    
    /**
     * @param string $username
     *
     * @return UserInterface|object|null
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function findByScreenname($screenname)
    {
        $er = $this->em->getRepository($this->options->getUserEntityClass());
        
        return $er->findOneBy(['screenname' => $screenname]);
    }
}