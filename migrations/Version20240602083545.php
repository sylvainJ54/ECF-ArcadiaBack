<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602083545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD veterinary_report_id INT NOT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F4CAB118 FOREIGN KEY (veterinary_report_id) REFERENCES veterinary_report (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F4CAB118 ON animal (veterinary_report_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F4CAB118');
        $this->addSql('DROP INDEX IDX_6AAB231F4CAB118 ON animal');
        $this->addSql('ALTER TABLE animal DROP veterinary_report_id');
    }
}
