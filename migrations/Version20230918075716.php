<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918075716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_A99CE55FA76ED395 (user_id), INDEX IDX_A99CE55F4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_comment_like (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, postcomment_id INT NOT NULL, liked TINYINT(1) NOT NULL, INDEX IDX_21689F8CA76ED395 (user_id), INDEX IDX_21689F8C149BA2F9 (postcomment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_A99CE55F4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post_comment_like ADD CONSTRAINT FK_21689F8CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post_comment_like ADD CONSTRAINT FK_21689F8C149BA2F9 FOREIGN KEY (postcomment_id) REFERENCES post_comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55FA76ED395');
        $this->addSql('ALTER TABLE post_comment DROP FOREIGN KEY FK_A99CE55F4B89032C');
        $this->addSql('ALTER TABLE post_comment_like DROP FOREIGN KEY FK_21689F8CA76ED395');
        $this->addSql('ALTER TABLE post_comment_like DROP FOREIGN KEY FK_21689F8C149BA2F9');
        $this->addSql('DROP TABLE post_comment');
        $this->addSql('DROP TABLE post_comment_like');
    }
}
