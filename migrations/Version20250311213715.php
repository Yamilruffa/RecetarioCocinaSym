<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311213715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingrediente CHANGE png png VARCHAR(255) DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE paso CHANGE descripcion descripcion VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE receta CHANGE png png VARCHAR(255) DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(255) DEFAULT NULL, CHANGE tiempoprep tiempoprep VARCHAR(30) DEFAULT NULL, CHANGE porciones porciones VARCHAR(2) DEFAULT NULL, CHANGE dificultad dificultad VARCHAR(10) DEFAULT NULL, CHANGE visible visible VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingrediente CHANGE png png VARCHAR(255) DEFAULT \'NULL\', CHANGE descripcion descripcion VARCHAR(150) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE paso CHANGE descripcion descripcion VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE receta CHANGE png png VARCHAR(255) DEFAULT \'NULL\', CHANGE descripcion descripcion VARCHAR(255) DEFAULT \'NULL\', CHANGE tiempoprep tiempoprep VARCHAR(10) DEFAULT \'NULL\', CHANGE porciones porciones VARCHAR(2) DEFAULT \'NULL\', CHANGE dificultad dificultad VARCHAR(10) DEFAULT \'NULL\', CHANGE visible visible VARCHAR(5) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
