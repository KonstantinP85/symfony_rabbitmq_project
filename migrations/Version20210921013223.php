<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210921013223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_confirmation_tokens (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', token VARCHAR(255) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expire_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7C168F29A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_lessons (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, first_name_trainer VARCHAR(255) NOT NULL, last_name_trainer VARCHAR(255) NOT NULL, patronymic_trainer VARCHAR(255) DEFAULT NULL, description VARCHAR(1000) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_lesson_user (group_lesson_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_915A0FECD4B5D77 (group_lesson_id), INDEX IDX_915A0FECA76ED395 (user_id), PRIMARY KEY(group_lesson_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, birthday DATETIME NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, login_attempt_counter INT NOT NULL, gender VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, notification_type VARCHAR(255) DEFAULT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_confirmation_tokens ADD CONSTRAINT FK_7C168F29A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE group_lesson_user ADD CONSTRAINT FK_915A0FECD4B5D77 FOREIGN KEY (group_lesson_id) REFERENCES group_lessons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_lesson_user ADD CONSTRAINT FK_915A0FECA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_lesson_user DROP FOREIGN KEY FK_915A0FECD4B5D77');
        $this->addSql('ALTER TABLE email_confirmation_tokens DROP FOREIGN KEY FK_7C168F29A76ED395');
        $this->addSql('ALTER TABLE group_lesson_user DROP FOREIGN KEY FK_915A0FECA76ED395');
        $this->addSql('DROP TABLE email_confirmation_tokens');
        $this->addSql('DROP TABLE group_lessons');
        $this->addSql('DROP TABLE group_lesson_user');
        $this->addSql('DROP TABLE users');
    }
}
