<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101163203 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD pasta_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7FCDBC8C FOREIGN KEY (pasta_id) REFERENCES pasta (id)');
        $this->addSql('CREATE INDEX IDX_9474526C7FCDBC8C ON comment (pasta_id)');
        $this->addSql('ALTER TABLE pasta ADD stripped LONGTEXT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7FCDBC8C');
        $this->addSql('DROP INDEX IDX_9474526C7FCDBC8C ON comment');
        $this->addSql('ALTER TABLE comment DROP pasta_id');
        $this->addSql('ALTER TABLE pasta DROP stripped');
    }
}
