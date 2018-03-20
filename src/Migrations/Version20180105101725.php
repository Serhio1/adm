<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180105101725 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE partition_participant DROP FOREIGN KEY FK_1CF31CEC9D1C3019');
        $this->addSql('ALTER TABLE partition_participant DROP FOREIGN KEY FK_1CF31CEC30D5A4AB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE `partition`');
        $this->addSql('DROP TABLE partition_participant');
        $this->addSql('ALTER TABLE user ADD schedule_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A40BC2D5 ON user (schedule_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A40BC2D5');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `partition` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partition_participant (participant_id INT NOT NULL, partition_id INT NOT NULL, INDEX IDX_1CF31CEC9D1C3019 (participant_id), INDEX IDX_1CF31CEC30D5A4AB (partition_id), PRIMARY KEY(participant_id, partition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partition_participant ADD CONSTRAINT FK_1CF31CEC30D5A4AB FOREIGN KEY (partition_id) REFERENCES `partition` (id)');
        $this->addSql('ALTER TABLE partition_participant ADD CONSTRAINT FK_1CF31CEC9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP INDEX IDX_8D93D649A40BC2D5 ON user');
        $this->addSql('ALTER TABLE user DROP schedule_id, DROP name');
    }
}
