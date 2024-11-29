<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241129120113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, medical LONGTEXT NOT NULL, psycological LONGTEXT NOT NULL, food LONGTEXT NOT NULL, comment LONGTEXT NOT NULL, additional LONGTEXT NOT NULL, school SMALLINT NOT NULL, UNIQUE INDEX UNIQ_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_can (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_can_order (order_can_id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_367B9B0D1A860E6D (order_can_id), INDEX IDX_367B9B0D8D9F6D38 (order_id), PRIMARY KEY(order_can_id, order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_noes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_noes_order (order_noes_id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_97FC555BF865176F (order_noes_id), INDEX IDX_97FC555B8D9F6D38 (order_id), PRIMARY KEY(order_noes_id, order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_want (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_want_order (order_want_id INT NOT NULL, order_id INT NOT NULL, INDEX IDX_1C480633DA2EA6AA (order_want_id), INDEX IDX_1C4806338D9F6D38 (order_id), PRIMARY KEY(order_want_id, order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_can_order ADD CONSTRAINT FK_367B9B0D1A860E6D FOREIGN KEY (order_can_id) REFERENCES order_can (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_can_order ADD CONSTRAINT FK_367B9B0D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_noes_order ADD CONSTRAINT FK_97FC555BF865176F FOREIGN KEY (order_noes_id) REFERENCES order_noes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_noes_order ADD CONSTRAINT FK_97FC555B8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_want_order ADD CONSTRAINT FK_1C480633DA2EA6AA FOREIGN KEY (order_want_id) REFERENCES order_want (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_want_order ADD CONSTRAINT FK_1C4806338D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_can_order DROP FOREIGN KEY FK_367B9B0D1A860E6D');
        $this->addSql('ALTER TABLE order_can_order DROP FOREIGN KEY FK_367B9B0D8D9F6D38');
        $this->addSql('ALTER TABLE order_noes_order DROP FOREIGN KEY FK_97FC555BF865176F');
        $this->addSql('ALTER TABLE order_noes_order DROP FOREIGN KEY FK_97FC555B8D9F6D38');
        $this->addSql('ALTER TABLE order_want_order DROP FOREIGN KEY FK_1C480633DA2EA6AA');
        $this->addSql('ALTER TABLE order_want_order DROP FOREIGN KEY FK_1C4806338D9F6D38');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_can');
        $this->addSql('DROP TABLE order_can_order');
        $this->addSql('DROP TABLE order_noes');
        $this->addSql('DROP TABLE order_noes_order');
        $this->addSql('DROP TABLE order_want');
        $this->addSql('DROP TABLE order_want_order');
    }
}
