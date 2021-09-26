<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210922014509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_group_lesson (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_lesson_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', notification_type VARCHAR(255) NOT NULL, INDEX IDX_AB3ED021A76ED395 (user_id), INDEX IDX_AB3ED021D4B5D77 (group_lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group_lesson ADD CONSTRAINT FK_AB3ED021A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_lesson ADD CONSTRAINT FK_AB3ED021D4B5D77 FOREIGN KEY (group_lesson_id) REFERENCES group_lessons (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_group_lesson');
    }
}
