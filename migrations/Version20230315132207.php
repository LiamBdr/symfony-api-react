<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315132207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE origin (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `character` ADD location_id INT DEFAULT NULL, ADD origin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB03464D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB03456A273CC FOREIGN KEY (origin_id) REFERENCES origin (id)');
        $this->addSql('CREATE INDEX IDX_937AB03464D218E ON `character` (location_id)');
        $this->addSql('CREATE INDEX IDX_937AB03456A273CC ON `character` (origin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB03464D218E');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB03456A273CC');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE origin');
        $this->addSql('DROP INDEX IDX_937AB03464D218E ON `character`');
        $this->addSql('DROP INDEX IDX_937AB03456A273CC ON `character`');
        $this->addSql('ALTER TABLE `character` DROP location_id, DROP origin_id');
    }
}
