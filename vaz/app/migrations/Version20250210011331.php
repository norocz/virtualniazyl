<?php

declare(strict_types=1);

namespace DB\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210011331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_actions CHANGE action_type_enum action_type_enum ENUM(\'Start adopce\', \'Konec adopce\', \'Přerušení adopce\', \'Kontakt\', \'Telefonát\', \'Osobní kontakt\', \'Ověření\', \'Adopce se zdařila\', \'Adopce se nezdařila\') NOT NULL');
        $this->addSql('ALTER TABLE adoptions CHANGE adoption_type adoption_type ENUM(\'Virtuální adopce\', \'Předadopce\', \'Plná adopce\', \'Dočasná péče\') NOT NULL, CHANGE action_type action_type ENUM(\'Start adopce\', \'Konec adopce\', \'Přerušení adopce\', \'Kontakt\', \'Telefonát\', \'Osobní kontakt\', \'Ověření\', \'Adopce se zdařila\', \'Adopce se nezdařila\') NOT NULL');
        $this->addSql('ALTER TABLE animals CHANGE adoption_type adoption_type ENUM(\'Virtuální adopce\', \'Předadopce\', \'Plná adopce\', \'Dočasná péče\') NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE type type ENUM(\'frs\', \'fra\', \'fru\', \'fou\', \'foa\', \'fos\') NOT NULL');
        $this->addSql('ALTER TABLE payments ADD payment_status ENUM(\'expected\', \'paired\', \'sent\', \'closed\') NOT NULL');
        $this->addSql('ALTER TABLE species CHANGE sex sex ENUM(\'Sameček\', \'Samička\', \'Nevíme\', \'Hermafrodit\') NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'admin\', \'user\', \'guest\', \'superadmin\', \'azyl\', \'azyladmin\', \'adopter\', \'adopteradmin\', \'owner\', \'reviewer\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_actions CHANGE action_type_enum action_type_enum VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adoptions CHANGE adoption_type adoption_type VARCHAR(255) NOT NULL, CHANGE action_type action_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE animals CHANGE adoption_type adoption_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE messages CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE payments DROP payment_status');
        $this->addSql('ALTER TABLE species CHANGE sex sex VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE role role VARCHAR(255) NOT NULL');
    }
}
