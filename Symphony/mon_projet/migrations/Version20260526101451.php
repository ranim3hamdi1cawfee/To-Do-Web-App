<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260526101451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, priority VARCHAR(10) NOT NULL, movement VARCHAR(20) NOT NULL, tags JSON NOT NULL, due_date VARCHAR(20) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tasks DROP FOREIGN KEY `tasks_ibfk_1`');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tasks (id VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, title VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, priority ENUM(\'low\', \'medium\', \'high\') CHARACTER SET utf8mb4 DEFAULT \'\'\'medium\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`, status ENUM(\'todo\', \'doing\', \'done\') CHARACTER SET utf8mb4 DEFAULT \'\'\'todo\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`, due_date DATE DEFAULT \'NULL\', created_by INT DEFAULT NULL, created_at DATETIME DEFAULT \'current_timestamp()\', updated_at DATETIME DEFAULT \'NULL\', INDEX created_by (created_by), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, role ENUM(\'admin\', \'user\') CHARACTER SET utf8mb4 DEFAULT \'\'\'user\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`, created_at DATETIME DEFAULT \'current_timestamp()\', UNIQUE INDEX username (username), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('DROP TABLE task');
    }
}
