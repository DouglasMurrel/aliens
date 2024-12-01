<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241201184435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('update order_want set name="Милая няшная девочка" where id=21');
        $this->addSql('update order_want set name="Учитель, который знает ответ на любой вопрос" where id=27');
        $this->addSql('update order_want set name="Учитель, с которым можно поговорить о проблемах" where id=29');
        $this->addSql('update order_noes set name="Милая няшная девочка" where id=21');
        $this->addSql('update order_noes set name="Учитель, который знает ответ на любой вопрос" where id=27');
        $this->addSql('update order_noes set name="Учитель, с которым можно поговорить о проблемах" where id=29');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('update order_want set name="Милая няшняя девочка" where id=21');
        $this->addSql('update order_want set name="Учитель который знает ответ на любой вопрос" where id=27');
        $this->addSql('update order_want set name="Учитель, который все организует" where id=29');
        $this->addSql('update order_noes set name="Милая няшняя девочка" where id=21');
        $this->addSql('update order_noes set name="Учитель который знает ответ на любой вопрос" where id=27');
        $this->addSql('update order_noes set name="Учитель, который все организует" where id=29');
    }
}
