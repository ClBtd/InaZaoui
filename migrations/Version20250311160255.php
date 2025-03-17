<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311160255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA76ED395');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C1137ABCF');
        $this->addSql('ALTER TABLE media ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER password DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER access DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10ca76ed395');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10c1137abcf');
        $this->addSql('CREATE SEQUENCE media_id_seq');
        $this->addSql('SELECT setval(\'media_id_seq\', (SELECT MAX(id) FROM media))');
        $this->addSql('ALTER TABLE media ALTER id SET DEFAULT nextval(\'media_id_seq\')');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10ca76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT fk_6a2ca10c1137abcf FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE SEQUENCE user_id_seq');
        $this->addSql('SELECT setval(\'user_id_seq\', (SELECT MAX(id) FROM "user"))');
        $this->addSql('ALTER TABLE "user" ALTER id SET DEFAULT nextval(\'user_id_seq\')');
        $this->addSql('ALTER TABLE "user" ALTER password SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE "user" ALTER roles SET DEFAULT \'[]\'');
        $this->addSql('ALTER TABLE "user" ALTER access SET DEFAULT true');
    }
}
