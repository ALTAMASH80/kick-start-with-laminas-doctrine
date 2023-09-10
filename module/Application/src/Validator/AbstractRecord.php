<?php
declare(strict_types=1);

namespace Application\Validator;


use LmcUser\Validator\AbstractRecord as LmcUserAbstractRecord;

class AbstractRecord extends LmcUserAbstractRecord{
    
    public function isValid($value){
        parent::isValid($value);
    }
    
    /**
     * Grab the user from the mapper
     *
     * @param  string $value
     * @return mixed
     */
    protected function query($value)
    {
        $result = false;
        
        switch ($this->getKey()) {
            case 'email':
                $result = $this->getMapper()->findByEmail($value);
                break;
                
            case 'screenname':
                $result = $this->getMapper()->findByScreenname($value);
                break;
                
            default:
                throw new \Exception('Invalid key used in LmcUser validator');
                break;
        }
        
        return $result;
    }
}