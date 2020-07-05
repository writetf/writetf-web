<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124063711 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649989D9B63 ON user (slug)');
        $this->addSql('ALTER TABLE notification ADD comment_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\' after post_id');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF8697D14 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF8697D14 ON notification (comment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF8697D14');
        $this->addSql('DROP INDEX IDX_BF5476CAF8697D14 ON notification');
        $this->addSql('ALTER TABLE notification DROP comment_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649989D9B63 ON user');
    }
}
