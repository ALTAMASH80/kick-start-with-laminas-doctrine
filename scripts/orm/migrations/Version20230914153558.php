<?php

declare(strict_types=1);

namespace Lrphpt\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914153558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Dumping some data into database.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO `roles` (`name`) VALUES ("guest");');
        $this->addSql('INSERT INTO `roles` (`name`) VALUES ("member");');
        $this->addSql('INSERT INTO `roles` (`name`) VALUES ("editor");');
        $this->addSql('INSERT INTO `roles` (`name`) VALUES ("admin");');
        $this->addSql('INSERT INTO `hierarchicalrole_hierarchicalrole` (`hierarchicalrole_source`, `hierarchicalrole_target`) VALUES ("2", "1");');
        $this->addSql('INSERT INTO `hierarchicalrole_hierarchicalrole` (`hierarchicalrole_source`, `hierarchicalrole_target`) VALUES ("3", "2");');
        $this->addSql('INSERT INTO `hierarchicalrole_hierarchicalrole` (`hierarchicalrole_source`, `hierarchicalrole_target`) VALUES ("4", "3");');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        $this->addSql('TRUNCATE `hierarchicalrole_hierarchicalrole`;');
        $this->addSql('TRUNCATE `roles`;');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }
}
