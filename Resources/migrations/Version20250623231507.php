<?php

declare(strict_types=1);

namespace CompanyOS\Core\Bundle;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250623231507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE roles (id CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, display_name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, permissions JSON NOT NULL, is_system TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_roles (id CHAR(36) NOT NULL, assigned_at DATETIME NOT NULL, user_id CHAR(36) NOT NULL, role_id CHAR(36) NOT NULL, INDEX IDX_54FCD59FA76ED395 (user_id), INDEX IDX_54FCD59FD60322AC (role_id), UNIQUE INDEX user_role_unique (user_id, role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE roles
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_roles
        SQL);
    }
}
