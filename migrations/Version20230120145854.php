<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120145854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP password, DROP registered_at, DROP last_login_at, DROP status, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD last_login_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD status TINYINT(1) NOT NULL, CHANGE first_name first_name VARCHAR(20) NOT NULL, CHANGE last_name last_name VARCHAR(20) NOT NULL');
    }
}
