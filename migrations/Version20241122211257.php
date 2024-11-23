<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241122211257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('alter table user add `salt` varchar(255) DEFAULT NULL');
        $this->addSql('alter table user add `roles` longtext NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('alter table user drop `salt`');
        $this->addSql('alter table user drop `roles`');

    }
}
