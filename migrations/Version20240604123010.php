<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604123010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494CAB118');
        $this->addSql('DROP INDEX IDX_8D93D6494CAB118 ON user');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', DROP veterinary_report_id, DROP username, DROP name, DROP firstname, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD veterinary_report_id INT NOT NULL, ADD username VARCHAR(50) NOT NULL, ADD name VARCHAR(50) NOT NULL, ADD firstname VARCHAR(50) NOT NULL, DROP email, DROP roles, CHANGE password password VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494CAB118 FOREIGN KEY (veterinary_report_id) REFERENCES veterinary_report (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494CAB118 ON user (veterinary_report_id)');
    }
}
