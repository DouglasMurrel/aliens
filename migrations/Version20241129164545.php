<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241129164545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('insert into order_can (name) values ("Тихий ученик, который всем помогает в учебе")');
        $this->addSql('insert into order_can (name) values ("Примерный ученик с синдромом отличника")');
        $this->addSql('insert into order_can (name) values ("Умный, но не особо социализированный ученик (\“ботан\”)")');
        $this->addSql('insert into order_can (name) values ("Спортивный, но не очень умный ученик")');
        $this->addSql('insert into order_can (name) values ("Ученик с явной маскулинностью, пытается ухаживать за любой женщиной")');
        $this->addSql('insert into order_can (name) values ("Девушка-соблазнительница, \“роковая красотка\”")');
        $this->addSql('insert into order_can (name) values ("Сплетница, все про всех знает")');
        $this->addSql('insert into order_can (name) values ("Шутник")');
        $this->addSql('insert into order_can (name) values ("Стервозная ученица, лидер группы")');
        $this->addSql('insert into order_can (name) values ("Одна из подпевал стервозной ученицы")');
        $this->addSql('insert into order_can (name) values ("Ученик из обедневшей семьи, который скрывает, что обеднел")');
        $this->addSql('insert into order_can (name) values ("Ученик, который хулиганит напоказ")');
        $this->addSql('insert into order_can (name) values ("Первый красавец/красавица класса")');
        $this->addSql('insert into order_can (name) values ("Девушка, которая завидует первой красавице и хочет быть как она")');
        $this->addSql('insert into order_can (name) values ("Ученик, который выглядит обыкновенным. А на самом деле вампир. Или инопланетянин")');
        $this->addSql('insert into order_can (name) values ("Глубоко религиозный ученик. Возможно, лицемер")');
        $this->addSql('insert into order_can (name) values ("Ученик, который кажется очень правильным, а на самом деле хочет быть как остальные")');
        $this->addSql('insert into order_can (name) values ("Ученик, который кажется очень правильным, а на самом деле задумал недоброе")');
        $this->addSql('insert into order_can (name) values ("Девушка-спортсменка, пытается равняться с парнями. Может быть феминисткой")');
        $this->addSql('insert into order_can (name) values ("Музыкант-неформал")');
        $this->addSql('insert into order_can (name) values ("Милая няшняя девочка")');
        $this->addSql('insert into order_can (name) values ("Ученик с каким-то хобби, о котором он говорит всегда в любой ситуации ")');
        $this->addSql('insert into order_can (name) values ("Растяпа, который все говорит и делает не к месту и нелепо")');
        $this->addSql('insert into order_can (name) values ("Модник/модница")');
        $this->addSql('insert into order_can (name) values ("Бедная трудолюбивая девушка")');
        $this->addSql('insert into order_can (name) values ("Учитель, который терпеть не может детей")');
        $this->addSql('insert into order_can (name) values ("Учитель который знает ответ на любой вопрос")');
        $this->addSql('insert into order_can (name) values ("Учитель, который все организует")');
        $this->addSql('insert into order_can (name) values ("Учитель, который все организует")');
        $this->addSql('insert into order_want (name) values ("Тихий ученик, который всем помогает в учебе")');
        $this->addSql('insert into order_want (name) values ("Примерный ученик с синдромом отличника")');
        $this->addSql('insert into order_want (name) values ("Умный, но не особо социализированный ученик (\“ботан\”)")');
        $this->addSql('insert into order_want (name) values ("Спортивный, но не очень умный ученик")');
        $this->addSql('insert into order_want (name) values ("Ученик с явной маскулинностью, пытается ухаживать за любой женщиной")');
        $this->addSql('insert into order_want (name) values ("Девушка-соблазнительница, \“роковая красотка\”")');
        $this->addSql('insert into order_want (name) values ("Сплетница, все про всех знает")');
        $this->addSql('insert into order_want (name) values ("Шутник")');
        $this->addSql('insert into order_want (name) values ("Стервозная ученица, лидер группы")');
        $this->addSql('insert into order_want (name) values ("Одна из подпевал стервозной ученицы")');
        $this->addSql('insert into order_want (name) values ("Ученик из обедневшей семьи, который скрывает, что обеднел")');
        $this->addSql('insert into order_want (name) values ("Ученик, который хулиганит напоказ")');
        $this->addSql('insert into order_want (name) values ("Первый красавец/красавица класса")');
        $this->addSql('insert into order_want (name) values ("Девушка, которая завидует первой красавице и хочет быть как она")');
        $this->addSql('insert into order_want (name) values ("Ученик, который выглядит обыкновенным. А на самом деле вампир. Или инопланетянин")');
        $this->addSql('insert into order_want (name) values ("Глубоко религиозный ученик. Возможно, лицемер")');
        $this->addSql('insert into order_want (name) values ("Ученик, который кажется очень правильным, а на самом деле хочет быть как остальные")');
        $this->addSql('insert into order_want (name) values ("Ученик, который кажется очень правильным, а на самом деле задумал недоброе")');
        $this->addSql('insert into order_want (name) values ("Девушка-спортсменка, пытается равняться с парнями. Может быть феминисткой")');
        $this->addSql('insert into order_want (name) values ("Музыкант-неформал")');
        $this->addSql('insert into order_want (name) values ("Милая няшняя девочка")');
        $this->addSql('insert into order_want (name) values ("Ученик с каким-то хобби, о котором он говорит всегда в любой ситуации ")');
        $this->addSql('insert into order_want (name) values ("Растяпа, который все говорит и делает не к месту и нелепо")');
        $this->addSql('insert into order_want (name) values ("Модник/модница")');
        $this->addSql('insert into order_want (name) values ("Бедная трудолюбивая девушка")');
        $this->addSql('insert into order_want (name) values ("Учитель, который терпеть не может детей")');
        $this->addSql('insert into order_want (name) values ("Учитель который знает ответ на любой вопрос")');
        $this->addSql('insert into order_want (name) values ("Учитель, который все организует")');
        $this->addSql('insert into order_want (name) values ("Учитель, который все организует")');
        $this->addSql('insert into order_noes (name) values ("Тихий ученик, который всем помогает в учебе")');
        $this->addSql('insert into order_noes (name) values ("Примерный ученик с синдромом отличника")');
        $this->addSql('insert into order_noes (name) values ("Умный, но не особо социализированный ученик (\“ботан\”)")');
        $this->addSql('insert into order_noes (name) values ("Спортивный, но не очень умный ученик")');
        $this->addSql('insert into order_noes (name) values ("Ученик с явной маскулинностью, пытается ухаживать за любой женщиной")');
        $this->addSql('insert into order_noes (name) values ("Девушка-соблазнительница, \“роковая красотка\”")');
        $this->addSql('insert into order_noes (name) values ("Сплетница, все про всех знает")');
        $this->addSql('insert into order_noes (name) values ("Шутник")');
        $this->addSql('insert into order_noes (name) values ("Стервозная ученица, лидер группы")');
        $this->addSql('insert into order_noes (name) values ("Одна из подпевал стервозной ученицы")');
        $this->addSql('insert into order_noes (name) values ("Ученик из обедневшей семьи, который скрывает, что обеднел")');
        $this->addSql('insert into order_noes (name) values ("Ученик, который хулиганит напоказ")');
        $this->addSql('insert into order_noes (name) values ("Первый красавец/красавица класса")');
        $this->addSql('insert into order_noes (name) values ("Девушка, которая завидует первой красавице и хочет быть как она")');
        $this->addSql('insert into order_noes (name) values ("Ученик, который выглядит обыкновенным. А на самом деле вампир. Или инопланетянин")');
        $this->addSql('insert into order_noes (name) values ("Глубоко религиозный ученик. Возможно, лицемер")');
        $this->addSql('insert into order_noes (name) values ("Ученик, который кажется очень правильным, а на самом деле хочет быть как остальные")');
        $this->addSql('insert into order_noes (name) values ("Ученик, который кажется очень правильным, а на самом деле задумал недоброе")');
        $this->addSql('insert into order_noes (name) values ("Девушка-спортсменка, пытается равняться с парнями. Может быть феминисткой")');
        $this->addSql('insert into order_noes (name) values ("Музыкант-неформал")');
        $this->addSql('insert into order_noes (name) values ("Милая няшняя девочка")');
        $this->addSql('insert into order_noes (name) values ("Ученик с каким-то хобби, о котором он говорит всегда в любой ситуации ")');
        $this->addSql('insert into order_noes (name) values ("Растяпа, который все говорит и делает не к месту и нелепо")');
        $this->addSql('insert into order_noes (name) values ("Модник/модница")');
        $this->addSql('insert into order_noes (name) values ("Бедная трудолюбивая девушка")');
        $this->addSql('insert into order_noes (name) values ("Учитель, который терпеть не может детей")');
        $this->addSql('insert into order_noes (name) values ("Учитель который знает ответ на любой вопрос")');
        $this->addSql('insert into order_noes (name) values ("Учитель, который все организует")');
        $this->addSql('insert into order_noes (name) values ("Учитель, который все организует")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
