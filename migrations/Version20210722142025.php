<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210722142025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, media_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_64C19C1EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, number_order VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, date DATE NOT NULL, city VARCHAR(255) NOT NULL, zipcode DOUBLE PRECISION NOT NULL, INDEX IDX_8ECAEAD4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, content LONGTEXT NOT NULL, date DATE NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licence (id INT AUTO_INCREMENT NOT NULL, media_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_1DAAE648EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, src VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, INDEX IDX_6A2CA10C4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT NOT NULL, note DOUBLE PRECISION NOT NULL, date DATE NOT NULL, INDEX IDX_CFBDFA14A76ED395 (user_id), INDEX IDX_CFBDFA144584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, licence_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD26EF07C9 (licence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_command (product_id INT NOT NULL, command_id INT NOT NULL, INDEX IDX_5F13F1644584665A (product_id), INDEX IDX_5F13F16433E1689A (command_id), PRIMARY KEY(product_id, command_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, media_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE licence ADD CONSTRAINT FK_1DAAE648EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA144584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD26EF07C9 FOREIGN KEY (licence_id) REFERENCES licence (id)');
        $this->addSql('ALTER TABLE product_command ADD CONSTRAINT FK_5F13F1644584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_command ADD CONSTRAINT FK_5F13F16433E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_command DROP FOREIGN KEY FK_5F13F16433E1689A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD26EF07C9');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1EA9FDD75');
        $this->addSql('ALTER TABLE licence DROP FOREIGN KEY FK_1DAAE648EA9FDD75');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EA9FDD75');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4584665A');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C4584665A');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA144584665A');
        $this->addSql('ALTER TABLE product_command DROP FOREIGN KEY FK_5F13F1644584665A');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE licence');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_command');
        $this->addSql('DROP TABLE user');
    }
}
