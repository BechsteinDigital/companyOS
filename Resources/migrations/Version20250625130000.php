<?php

declare(strict_types=1);

namespace CompanyOS\Core\Bundle;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create company_settings table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE company_settings (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            company_name VARCHAR(255) NOT NULL,
            legal_name VARCHAR(255) DEFAULT NULL,
            tax_number VARCHAR(100) DEFAULT NULL,
            vat_number VARCHAR(100) DEFAULT NULL,
            street VARCHAR(255) NOT NULL,
            house_number VARCHAR(20) NOT NULL,
            postal_code VARCHAR(20) NOT NULL,
            city VARCHAR(255) NOT NULL,
            country VARCHAR(100) NOT NULL,
            state VARCHAR(100) DEFAULT NULL,
            email VARCHAR(255) NOT NULL COMMENT \'(DC2Type:email)\',
            phone VARCHAR(50) DEFAULT NULL,
            fax VARCHAR(50) DEFAULT NULL,
            website VARCHAR(255) DEFAULT NULL,
            support_email VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:email)\',
            default_language VARCHAR(10) NOT NULL,
            default_currency VARCHAR(10) NOT NULL,
            timezone VARCHAR(50) NOT NULL,
            date_format VARCHAR(20) NOT NULL,
            time_format VARCHAR(20) NOT NULL,
            number_format VARCHAR(20) NOT NULL,
            system_name VARCHAR(100) NOT NULL,
            logo_url VARCHAR(255) DEFAULT NULL,
            default_user_role VARCHAR(100) NOT NULL,
            session_timeout INT NOT NULL,
            maintenance_mode TINYINT(1) NOT NULL,
            email_from_name VARCHAR(255) NOT NULL,
            email_from_address VARCHAR(255) NOT NULL COMMENT \'(DC2Type:email)\',
            email_reply_to VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:email)\',
            smtp_host VARCHAR(255) NOT NULL,
            smtp_port INT NOT NULL,
            smtp_encryption VARCHAR(10) NOT NULL,
            smtp_username VARCHAR(255) DEFAULT NULL,
            smtp_password VARCHAR(255) DEFAULT NULL,
            salutations JSON NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE company_settings');
    }
} 