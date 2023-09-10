<?php

declare(strict_types=1);

namespace Lrphpt\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914153433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE permissions (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id INT UNSIGNED AUTO_INCREMENT NOT NULL, author_id BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, is_post INT UNSIGNED DEFAULT 1 NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_885DBAFAF675F31B (author_id), INDEX title_idx (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(48) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hierarchicalrole_hierarchicalrole (hierarchicalrole_source BIGINT NOT NULL, hierarchicalrole_target BIGINT NOT NULL, INDEX IDX_5707BC75CD934D59 (hierarchicalrole_source), INDEX IDX_5707BC75D4761DD6 (hierarchicalrole_target), PRIMARY KEY(hierarchicalrole_source, hierarchicalrole_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hierarchicalrole_permission (hierarchicalrole_id BIGINT NOT NULL, permission_id BIGINT NOT NULL, INDEX IDX_8D28B77E83B93C19 (hierarchicalrole_id), INDEX IDX_8D28B77EFED90CCA (permission_id), PRIMARY KEY(hierarchicalrole_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hierarchicalrole_user (hierarchicalrole_id BIGINT NOT NULL, user_id BIGINT NOT NULL, INDEX IDX_509196F983B93C19 (hierarchicalrole_id), INDEX IDX_509196F9A76ED395 (user_id), PRIMARY KEY(hierarchicalrole_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BIGINT AUTO_INCREMENT NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(150) NOT NULL, username VARCHAR(150) DEFAULT NULL, displayname VARCHAR(250) DEFAULT NULL, admin_notes VARCHAR(255) DEFAULT NULL, remember_me VARCHAR(150) DEFAULT NULL, oauth_provider VARCHAR(150) DEFAULT NULL, oauth_uid VARCHAR(150) DEFAULT NULL, codeconfirmation VARCHAR(150) DEFAULT NULL, passrecover VARCHAR(150) DEFAULT NULL, is_imported TINYINT(1) DEFAULT NULL, state TINYINT(1) DEFAULT 1, msg_sent INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX users_oauth_login_idx_idx (oauth_uid, oauth_provider, state), UNIQUE INDEX user_email_idx_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hierarchicalrole_hierarchicalrole ADD CONSTRAINT FK_5707BC75CD934D59 FOREIGN KEY (hierarchicalrole_source) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hierarchicalrole_hierarchicalrole ADD CONSTRAINT FK_5707BC75D4761DD6 FOREIGN KEY (hierarchicalrole_target) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hierarchicalrole_permission ADD CONSTRAINT FK_8D28B77E83B93C19 FOREIGN KEY (hierarchicalrole_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hierarchicalrole_permission ADD CONSTRAINT FK_8D28B77EFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hierarchicalrole_user ADD CONSTRAINT FK_509196F983B93C19 FOREIGN KEY (hierarchicalrole_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hierarchicalrole_user ADD CONSTRAINT FK_509196F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAF675F31B');
        $this->addSql('ALTER TABLE hierarchicalrole_hierarchicalrole DROP FOREIGN KEY FK_5707BC75CD934D59');
        $this->addSql('ALTER TABLE hierarchicalrole_hierarchicalrole DROP FOREIGN KEY FK_5707BC75D4761DD6');
        $this->addSql('ALTER TABLE hierarchicalrole_permission DROP FOREIGN KEY FK_8D28B77E83B93C19');
        $this->addSql('ALTER TABLE hierarchicalrole_permission DROP FOREIGN KEY FK_8D28B77EFED90CCA');
        $this->addSql('ALTER TABLE hierarchicalrole_user DROP FOREIGN KEY FK_509196F983B93C19');
        $this->addSql('ALTER TABLE hierarchicalrole_user DROP FOREIGN KEY FK_509196F9A76ED395');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE hierarchicalrole_hierarchicalrole');
        $this->addSql('DROP TABLE hierarchicalrole_permission');
        $this->addSql('DROP TABLE hierarchicalrole_user');
        $this->addSql('DROP TABLE user');
    }
}
