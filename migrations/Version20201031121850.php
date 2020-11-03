<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201031121850 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pasta (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, picture_id INT DEFAULT NULL, title LONGTEXT NOT NULL, content LONGTEXT NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_9B3BBC81F675F31B (author_id), UNIQUE INDEX UNIQ_9B3BBC81EE45BDBF (picture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes (pasta_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_49CA4E7D7FCDBC8C (pasta_id), INDEX IDX_49CA4E7DA76ED395 (user_id), PRIMARY KEY(pasta_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, data LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pasta ADD CONSTRAINT FK_9B3BBC81F675F31B FOREIGN KEY (author_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE pasta ADD CONSTRAINT FK_9B3BBC81EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D7FCDBC8C FOREIGN KEY (pasta_id) REFERENCES pasta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D7FCDBC8C');
        $this->addSql('ALTER TABLE pasta DROP FOREIGN KEY FK_9B3BBC81EE45BDBF');
        $this->addSql('DROP TABLE pasta');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE picture');
    }
}
