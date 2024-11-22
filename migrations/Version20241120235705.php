<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120235705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, prestation_id INT NOT NULL, commentaire LONGTEXT NOT NULL, INDEX IDX_67F068BC19EB6921 (client_id), INDEX IDX_67F068BC9E45C554 (prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, prestation_id INT NOT NULL, date_debut_prestation DATETIME NOT NULL, date_fin_prestation DATETIME NOT NULL, INDEX IDX_2694D7A519EB6921 (client_id), INDEX IDX_2694D7A59E45C554 (prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prestation (id INT AUTO_INCREMENT NOT NULL, prestataire_id INT NOT NULL, type_prestation_id INT NOT NULL, date_debut_disponibilite DATETIME NOT NULL, date_fin_disponibilite DATETIME NOT NULL, prix NUMERIC(10, 2) NOT NULL, prix_jour NUMERIC(10, 2) NOT NULL, INDEX IDX_51C88FADBE3DB2B7 (prestataire_id), INDEX IDX_51C88FADEEA87261 (type_prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_prestation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC19EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC9E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A59E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE prestation ADD CONSTRAINT FK_51C88FADBE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prestation ADD CONSTRAINT FK_51C88FADEEA87261 FOREIGN KEY (type_prestation_id) REFERENCES type_prestation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC19EB6921');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC9E45C554');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A519EB6921');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A59E45C554');
        $this->addSql('ALTER TABLE prestation DROP FOREIGN KEY FK_51C88FADBE3DB2B7');
        $this->addSql('ALTER TABLE prestation DROP FOREIGN KEY FK_51C88FADEEA87261');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE prestation');
        $this->addSql('DROP TABLE type_prestation');
    }
}
