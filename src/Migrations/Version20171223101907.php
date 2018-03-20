<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171223101907 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partition_participant (participant_id INT NOT NULL, partition_id INT NOT NULL, INDEX IDX_1CF31CEC9D1C3019 (participant_id), INDEX IDX_1CF31CEC30D5A4AB (partition_id), PRIMARY KEY(participant_id, partition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `partition` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partition_participant ADD CONSTRAINT FK_1CF31CEC9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE partition_participant ADD CONSTRAINT FK_1CF31CEC30D5A4AB FOREIGN KEY (partition_id) REFERENCES `partition` (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE partition_participant DROP FOREIGN KEY FK_1CF31CEC9D1C3019');
        $this->addSql('ALTER TABLE partition_participant DROP FOREIGN KEY FK_1CF31CEC30D5A4AB');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE partition_participant');
        $this->addSql('DROP TABLE `partition`');
    }
}
