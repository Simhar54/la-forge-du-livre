<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817121803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, choice_id INT NOT NULL, stat_definition_id INT NOT NULL, operator VARCHAR(10) NOT NULL, value INT NOT NULL, INDEX IDX_47CC8C92998666D1 (choice_id), INDEX IDX_47CC8C92E7B55CFA (stat_definition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE choice (id INT AUTO_INCREMENT NOT NULL, source_paragraph_id INT NOT NULL, destination_paragraph_id INT NOT NULL, text VARCHAR(255) NOT NULL, INDEX IDX_C1AB5A925DBA85C7 (source_paragraph_id), INDEX IDX_C1AB5A923A7E82B0 (destination_paragraph_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `condition` (id INT AUTO_INCREMENT NOT NULL, choice_id INT NOT NULL, stat_definition_id INT NOT NULL, comparison_operator VARCHAR(10) NOT NULL, value INT NOT NULL, INDEX IDX_BDD68843998666D1 (choice_id), INDEX IDX_BDD68843E7B55CFA (stat_definition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paragraph (id INT AUTO_INCREMENT NOT NULL, story_id INT NOT NULL, content LONGTEXT NOT NULL, is_start_paragraph TINYINT(1) NOT NULL, INDEX IDX_7DD39862AA5D4036 (story_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stat_definition (id INT AUTO_INCREMENT NOT NULL, story_id INT NOT NULL, name VARCHAR(100) NOT NULL, initial_value INT NOT NULL, INDEX IDX_A7E45870AA5D4036 (story_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92998666D1 FOREIGN KEY (choice_id) REFERENCES choice (id)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92E7B55CFA FOREIGN KEY (stat_definition_id) REFERENCES stat_definition (id)');
        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A925DBA85C7 FOREIGN KEY (source_paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE choice ADD CONSTRAINT FK_C1AB5A923A7E82B0 FOREIGN KEY (destination_paragraph_id) REFERENCES paragraph (id)');
        $this->addSql('ALTER TABLE `condition` ADD CONSTRAINT FK_BDD68843998666D1 FOREIGN KEY (choice_id) REFERENCES choice (id)');
        $this->addSql('ALTER TABLE `condition` ADD CONSTRAINT FK_BDD68843E7B55CFA FOREIGN KEY (stat_definition_id) REFERENCES stat_definition (id)');
        $this->addSql('ALTER TABLE paragraph ADD CONSTRAINT FK_7DD39862AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
        $this->addSql('ALTER TABLE stat_definition ADD CONSTRAINT FK_A7E45870AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92998666D1');
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92E7B55CFA');
        $this->addSql('ALTER TABLE choice DROP FOREIGN KEY FK_C1AB5A925DBA85C7');
        $this->addSql('ALTER TABLE choice DROP FOREIGN KEY FK_C1AB5A923A7E82B0');
        $this->addSql('ALTER TABLE `condition` DROP FOREIGN KEY FK_BDD68843998666D1');
        $this->addSql('ALTER TABLE `condition` DROP FOREIGN KEY FK_BDD68843E7B55CFA');
        $this->addSql('ALTER TABLE paragraph DROP FOREIGN KEY FK_7DD39862AA5D4036');
        $this->addSql('ALTER TABLE stat_definition DROP FOREIGN KEY FK_A7E45870AA5D4036');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE choice');
        $this->addSql('DROP TABLE `condition`');
        $this->addSql('DROP TABLE paragraph');
        $this->addSql('DROP TABLE stat_definition');
        $this->addSql('DROP TABLE story');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
