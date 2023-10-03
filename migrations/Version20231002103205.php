<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002103205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFB7E3C61F9');
        $this->addSql('DROP INDEX IDX_1C119AFB7E3C61F9 ON user_review');
        $this->addSql('ALTER TABLE user_review CHANGE owner_id targeted_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFB1AE7495A FOREIGN KEY (targeted_user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_1C119AFB1AE7495A ON user_review (targeted_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFB1AE7495A');
        $this->addSql('DROP INDEX IDX_1C119AFB1AE7495A ON user_review');
        $this->addSql('ALTER TABLE user_review CHANGE targeted_user_id owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1C119AFB7E3C61F9 ON user_review (owner_id)');
    }
}
