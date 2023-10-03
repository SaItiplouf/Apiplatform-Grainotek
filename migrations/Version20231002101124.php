<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002101124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBC2D9760 FOREIGN KEY (trade_id) REFERENCES trade (id)');
        $this->addSql('CREATE INDEX IDX_1C119AFBC2D9760 ON user_review (trade_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFBC2D9760');
        $this->addSql('DROP INDEX IDX_1C119AFBC2D9760 ON user_review');
    }
}
