<?php

namespace Application\Doctrine\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Gedmo\Mapping\MappedEventSubscriber;

/**
 * Class TablePrefixSubscriber.
 *
 */
class TablePrefix extends MappedEventSubscriber
{
    const TABLE_PREFIX = 'lrphpt_';
    
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['loadClassMetadata'];
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            // if we are in an inheritance hierarchy, only apply this once
            return;
        }
        
        $classMetadata->setTableName(self::TABLE_PREFIX.$classMetadata->getTableName());
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                if (!empty($classMetadata->associationMappings[$fieldName]['joinTable'])) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = self::TABLE_PREFIX.$mappedTableName;
                }
            }
        }
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}