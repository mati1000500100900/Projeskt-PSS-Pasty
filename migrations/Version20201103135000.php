<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103135000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pasta DROP INDEX UNIQ_9B3BBC81EE45BDBF, ADD INDEX IDX_9B3BBC81EE45BDBF (picture_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pasta DROP INDEX IDX_9B3BBC81EE45BDBF, ADD UNIQUE INDEX UNIQ_9B3BBC81EE45BDBF (picture_id)');
    }
}
