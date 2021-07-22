<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210722144443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1EA9FDD75');
        $this->addSql('DROP INDEX IDX_64C19C1EA9FDD75 ON category');
        $this->addSql('ALTER TABLE category ADD media VARCHAR(255) NOT NULL, DROP media_id');
        $this->addSql('ALTER TABLE licence DROP FOREIGN KEY FK_1DAAE648EA9FDD75');
        $this->addSql('DROP INDEX IDX_1DAAE648EA9FDD75 ON licence');
        $this->addSql('ALTER TABLE licence ADD media VARCHAR(255) NOT NULL, DROP media_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD media_id INT DEFAULT NULL, DROP media');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1EA9FDD75 ON category (media_id)');
        $this->addSql('ALTER TABLE licence ADD media_id INT DEFAULT NULL, DROP media');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_1DAAE648EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_1DAAE648EA9FDD75 ON licence (media_id)');
    }
}
