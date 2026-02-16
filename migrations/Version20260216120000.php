<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status to commande';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE commande ADD status VARCHAR(30) NOT NULL DEFAULT 'En attente'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commande DROP status');
    }
}

