<?php

declare(strict_types=1);

namespace templates;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113191149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE producto ADD imagen VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE producto DROP COLUMN stock');
        $this->addSql('ALTER TABLE producto DROP imagenes');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE producto ADD imagenes JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE producto DROP imagen');
        $this->addSql('ALTER TABLE producto DROP stock');
    }
}
