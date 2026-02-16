<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260213110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create commande table';
    }

    public function up(Schema $schema): void
    {
        // Legacy migrations created core tables as MyISAM on some environments.
        // Foreign keys require InnoDB.
        $this->addSql('ALTER TABLE `user` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ENGINE = InnoDB');

        // Keep migration idempotent if a previous run failed after table creation.
        $this->addSql('CREATE TABLE IF NOT EXISTS commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, items JSON NOT NULL, total DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C8F93DE7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('SET @fk_exists := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = \'commande\' AND CONSTRAINT_NAME = \'FK_C8F93DE7A76ED395\')');
        $this->addSql('SET @fk_sql := IF(@fk_exists = 0, \'ALTER TABLE commande ADD CONSTRAINT FK_C8F93DE7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)\', \'SELECT 1\')');
        $this->addSql('PREPARE fk_stmt FROM @fk_sql');
        $this->addSql('EXECUTE fk_stmt');
        $this->addSql('DEALLOCATE PREPARE fk_stmt');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_C8F93DE7A76ED395');
        $this->addSql('DROP TABLE commande');
    }
}
