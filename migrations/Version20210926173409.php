<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210926173409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_lessons (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, first_name_trainer VARCHAR(255) NOT NULL, last_name_trainer VARCHAR(255) NOT NULL, patronymic_trainer VARCHAR(255) DEFAULT NULL, description VARCHAR(1000) NOT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group_lesson (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_lesson_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', notification_type VARCHAR(255) DEFAULT NULL, INDEX IDX_AB3ED021A76ED395 (user_id), INDEX IDX_AB3ED021D4B5D77 (group_lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, birthday DATETIME NOT NULL, email VARCHAR(180) NOT NULL, phone VARCHAR(255) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, login_attempt_counter INT NOT NULL, gender VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, create_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group_lesson ADD CONSTRAINT FK_AB3ED021A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group_lesson ADD CONSTRAINT FK_AB3ED021D4B5D77 FOREIGN KEY (group_lesson_id) REFERENCES group_lessons (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_group_lesson DROP FOREIGN KEY FK_AB3ED021D4B5D77');
        $this->addSql('ALTER TABLE user_group_lesson DROP FOREIGN KEY FK_AB3ED021A76ED395');
        $this->addSql('DROP TABLE group_lessons');
        $this->addSql('DROP TABLE user_group_lesson');
        $this->addSql('DROP TABLE users');
    }
}
