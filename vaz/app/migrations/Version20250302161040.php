<?php

declare(strict_types=1);

namespace DB\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302161040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_actions CHANGE action_type_enum action_type_enum ENUM(\'Start adopce\', \'Konec adopce\', \'Přerušení adopce\', \'Kontakt\', \'Telefonát\', \'Osobní kontakt\', \'Ověření\', \'Adopce se zdařila\', \'Adopce se nezdařila\') NOT NULL');
        $this->addSql('DROP INDEX idx_adoption_description ON adoptions');
        $this->addSql('ALTER TABLE adoptions CHANGE adoption_type adoption_type ENUM(\'Virtuální adopce\', \'Předadopce\', \'Plná adopce\', \'Dočasná péče\') NOT NULL, CHANGE action_type action_type ENUM(\'Start adopce\', \'Konec adopce\', \'Přerušení adopce\', \'Kontakt\', \'Telefonát\', \'Osobní kontakt\', \'Ověření\', \'Adopce se zdařila\', \'Adopce se nezdařila\') NOT NULL');
        $this->addSql('DROP INDEX ip_adress ON analytics');
        $this->addSql('DROP INDEX host ON analytics');
        $this->addSql('DROP INDEX action ON analytics');
        $this->addSql('DROP INDEX temp_id ON analytics');
        $this->addSql('DROP INDEX comment ON analytics');
        $this->addSql('DROP INDEX params ON analytics');
        $this->addSql('DROP INDEX birth_date ON animals');
        $this->addSql('DROP INDEX description ON animals');
        $this->addSql('DROP INDEX name ON animals');
        $this->addSql('ALTER TABLE animals CHANGE adoption_type adoption_type ENUM(\'Virtuální adopce\', \'Předadopce\', \'Plná adopce\', \'Dočasná péče\') NOT NULL');
        $this->addSql('DROP INDEX idx_azyl ON azyls');
        $this->addSql('DROP INDEX description ON azyls');
        $this->addSql('DROP INDEX short_description ON azyls');
        $this->addSql('DROP INDEX city_name ON citys');
        $this->addSql('DROP INDEX region ON citys');
        $this->addSql('DROP INDEX collection_description ON collections');
        $this->addSql('DROP INDEX collection_name ON collections');
        $this->addSql('DROP INDEX created_at ON contract_parts');
        $this->addSql('DROP INDEX content ON contract_parts');
        $this->addSql('DROP INDEX name ON contract_parts');
        $this->addSql('DROP INDEX name ON contracts');
        $this->addSql('DROP INDEX comment ON conversations');
        $this->addSql('DROP INDEX created_at ON faq');
        $this->addSql('DROP INDEX question ON faq');
        $this->addSql('DROP INDEX answer ON faq');
        $this->addSql('DROP INDEX ip ON firewall_logs');
        $this->addSql('DROP INDEX created_at ON firewall_logs');
        $this->addSql('DROP INDEX notes ON firewall_logs');
        $this->addSql('DROP INDEX help_content ON help');
        $this->addSql('DROP INDEX title ON help');
        $this->addSql('DROP INDEX conversation_id ON messages');
        $this->addSql('DROP INDEX message ON messages');
        $this->addSql('DROP INDEX title ON messages');
        $this->addSql('ALTER TABLE messages CHANGE type type ENUM(\'frs\', \'fra\', \'fru\', \'fou\', \'foa\', \'fos\') NOT NULL');
        $this->addSql('DROP INDEX created_at ON news');
        $this->addSql('DROP INDEX content ON news');
        $this->addSql('DROP INDEX title ON news');
        $this->addSql('DROP INDEX link ON pages');
        $this->addSql('DROP INDEX content ON pages');
        $this->addSql('DROP INDEX title ON pages');
        $this->addSql('DROP INDEX created_at ON payments');
        $this->addSql('DROP INDEX comment ON payments');
        $this->addSql('DROP INDEX account_id ON paymentsIn');
        $this->addSql('DROP INDEX details ON paymentsIn');
        $this->addSql('DROP INDEX comment ON paymentsIn');
        $this->addSql('DROP INDEX account_from ON paymentsOut');
        $this->addSql('DROP INDEX datum ON paymentsOut');
        $this->addSql('DROP INDEX comment ON paymentsOut');
        $this->addSql('DROP INDEX message_for_recipient ON paymentsOut');
        $this->addSql('ALTER TABLE species CHANGE sex sex ENUM(\'Sameček\', \'Samička\', \'Nevíme\', \'Hermafrodit\') NOT NULL');
        $this->addSql('DROP INDEX created_by ON users');
        $this->addSql('DROP INDEX message_address ON users');
        $this->addSql('DROP INDEX user_name ON users');
        $this->addSql('DROP INDEX description ON users');
        $this->addSql('DROP INDEX review ON users');
        $this->addSql('ALTER TABLE users CHANGE role role ENUM(\'admin\', \'user\', \'guest\', \'superadmin\', \'azyl\', \'azyladmin\', \'adopter\', \'adopteradmin\', \'owner\', \'reviewer\') NOT NULL');
        $this->addSql('DROP INDEX created_at ON users_ratings');
        $this->addSql('DROP INDEX rating ON users_ratings');
        $this->addSql('DROP INDEX review ON users_ratings');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE species CHANGE sex sex VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adoption_actions CHANGE action_type_enum action_type_enum VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adoptions CHANGE adoption_type adoption_type VARCHAR(255) NOT NULL, CHANGE action_type action_type VARCHAR(255) NOT NULL');
        $this->addSql('CREATE FULLTEXT INDEX idx_adoption_description ON adoptions (description)');
        $this->addSql('CREATE FULLTEXT INDEX idx_azyl ON azyls (azyl_name)');
        $this->addSql('CREATE FULLTEXT INDEX description ON azyls (description)');
        $this->addSql('CREATE FULLTEXT INDEX short_description ON azyls (short_description)');
        $this->addSql('CREATE FULLTEXT INDEX city_name ON citys (city_name)');
        $this->addSql('CREATE FULLTEXT INDEX region ON citys (region)');
        $this->addSql('ALTER TABLE animals CHANGE adoption_type adoption_type VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX birth_date ON animals (birth_date)');
        $this->addSql('CREATE FULLTEXT INDEX description ON animals (description)');
        $this->addSql('CREATE FULLTEXT INDEX name ON animals (name)');
        $this->addSql('CREATE INDEX created_at ON payments (created_at)');
        $this->addSql('CREATE FULLTEXT INDEX comment ON payments (comment)');
        $this->addSql('ALTER TABLE users CHANGE role role VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX created_by ON users (created_by)');
        $this->addSql('CREATE INDEX message_address ON users (message_address)');
        $this->addSql('CREATE INDEX user_name ON users (user_name)');
        $this->addSql('CREATE FULLTEXT INDEX description ON users (description)');
        $this->addSql('CREATE FULLTEXT INDEX review ON users (review)');
        $this->addSql('CREATE INDEX account_id ON paymentsIn (account_id)');
        $this->addSql('CREATE INDEX details ON paymentsIn (details)');
        $this->addSql('CREATE FULLTEXT INDEX comment ON paymentsIn (comment)');
        $this->addSql('CREATE INDEX account_from ON paymentsOut (account_from)');
        $this->addSql('CREATE INDEX datum ON paymentsOut (datum)');
        $this->addSql('CREATE FULLTEXT INDEX comment ON paymentsOut (comment)');
        $this->addSql('CREATE FULLTEXT INDEX message_for_recipient ON paymentsOut (message_for_recipient)');
        $this->addSql('CREATE INDEX link ON pages (link)');
        $this->addSql('CREATE FULLTEXT INDEX content ON pages (content)');
        $this->addSql('CREATE FULLTEXT INDEX title ON pages (title)');
        $this->addSql('CREATE INDEX created_at ON news (created_at)');
        $this->addSql('CREATE FULLTEXT INDEX content ON news (content)');
        $this->addSql('CREATE FULLTEXT INDEX title ON news (title)');
        $this->addSql('ALTER TABLE messages CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX conversation_id ON messages (conversation_id)');
        $this->addSql('CREATE FULLTEXT INDEX message ON messages (message)');
        $this->addSql('CREATE FULLTEXT INDEX title ON messages (title)');
        $this->addSql('CREATE FULLTEXT INDEX help_content ON help (help_content)');
        $this->addSql('CREATE FULLTEXT INDEX title ON help (title)');
        $this->addSql('CREATE INDEX ip ON firewall_logs (ip)');
        $this->addSql('CREATE INDEX created_at ON firewall_logs (created_at)');
        $this->addSql('CREATE FULLTEXT INDEX notes ON firewall_logs (notes)');
        $this->addSql('CREATE INDEX created_at ON faq (created_at)');
        $this->addSql('CREATE FULLTEXT INDEX question ON faq (question)');
        $this->addSql('CREATE FULLTEXT INDEX answer ON faq (answer)');
        $this->addSql('CREATE FULLTEXT INDEX comment ON conversations (comment)');
        $this->addSql('CREATE FULLTEXT INDEX collection_description ON collections (collection_description)');
        $this->addSql('CREATE FULLTEXT INDEX collection_name ON collections (collection_name)');
        $this->addSql('CREATE INDEX ip_adress ON analytics (ip_adress)');
        $this->addSql('CREATE INDEX host ON analytics (host)');
        $this->addSql('CREATE INDEX action ON analytics (action)');
        $this->addSql('CREATE INDEX temp_id ON analytics (temp_id)');
        $this->addSql('CREATE FULLTEXT INDEX comment ON analytics (comment)');
        $this->addSql('CREATE FULLTEXT INDEX params ON analytics (params)');
        $this->addSql('CREATE INDEX name ON contracts (name)');
        $this->addSql('CREATE INDEX created_at ON contract_parts (created_at)');
        $this->addSql('CREATE FULLTEXT INDEX content ON contract_parts (content)');
        $this->addSql('CREATE FULLTEXT INDEX name ON contract_parts (name)');
        $this->addSql('CREATE INDEX created_at ON users_ratings (created_at)');
        $this->addSql('CREATE INDEX rating ON users_ratings (rating)');
        $this->addSql('CREATE FULLTEXT INDEX review ON users_ratings (review)');
    }
}
