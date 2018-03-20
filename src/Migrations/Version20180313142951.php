<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180313142951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE schedule_user (schedule_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6DDBF612A40BC2D5 (schedule_id), INDEX IDX_6DDBF612A76ED395 (user_id), PRIMARY KEY(schedule_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_user ADD CONSTRAINT FK_6DDBF612A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE schedule_user ADD CONSTRAINT FK_6DDBF612A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A40BC2D5');
        $this->addSql('DROP INDEX IDX_8D93D649A40BC2D5 ON user');
        $this->addSql('ALTER TABLE user DROP schedule_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE schedule_user');
        $this->addSql('ALTER TABLE user ADD schedule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A40BC2D5 ON user (schedule_id)');
    }
}
