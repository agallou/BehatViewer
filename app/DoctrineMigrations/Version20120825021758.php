<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120825021758 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE behatviewer_configuration (id INT AUTO_INCREMENT NOT NULL, data LONGTEXT NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE behatviewer_project ADD configuration_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE behatviewer_project ADD CONSTRAINT FK_639CF9EC73F32DD8 FOREIGN KEY (configuration_id) REFERENCES behatviewer_configuration (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_639CF9EC73F32DD8 ON behatviewer_project (configuration_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE behatviewer_project DROP FOREIGN KEY FK_639CF9EC73F32DD8");
        $this->addSql("DROP TABLE behatviewer_configuration");
        $this->addSql("ALTER TABLE behatviewer_project DROP FOREIGN KEY FK_639CF9EC73F32DD8");
        $this->addSql("DROP INDEX UNIQ_639CF9EC73F32DD8 ON behatviewer_project");
        $this->addSql("ALTER TABLE behatviewer_project DROP configuration_id");
    }
}
