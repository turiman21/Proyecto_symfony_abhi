<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113192917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alquiler (id SERIAL NOT NULL, id_producto_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, precio DOUBLE PRECISION NOT NULL, fecha_alquiler TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_devolucion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_655BED396E57A479 ON alquiler (id_producto_id)');
        $this->addSql('CREATE INDEX IDX_655BED3979F37AE5 ON alquiler (id_user_id)');
        $this->addSql('CREATE TABLE producto (id SERIAL NOT NULL, id_venta_id INT DEFAULT NULL, nombre_producto VARCHAR(255) NOT NULL, precio_venta DOUBLE PRECISION NOT NULL, estado VARCHAR(255) NOT NULL, precio_alquiler DOUBLE PRECISION NOT NULL, tipo JSON DEFAULT NULL, imagen VARCHAR(255) DEFAULT NULL, stock INT DEFAULT NULL, descripcion VARCHAR(1000) DEFAULT NULL, marca VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A7BB0615A27E6CC8 ON producto (id_venta_id)');
        $this->addSql('CREATE TABLE reparacion (id SERIAL NOT NULL, id_producto_id INT DEFAULT NULL, id_user_id INT DEFAULT NULL, estado_reparacion VARCHAR(255) NOT NULL, costo_reparacion DOUBLE PRECISION NOT NULL, fecha_entrada TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fecha_salida TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, descripcion_usuario TEXT DEFAULT NULL, nota_tecnico TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42114EBB6E57A479 ON reparacion (id_producto_id)');
        $this->addSql('CREATE INDEX IDX_42114EBB79F37AE5 ON reparacion (id_user_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, nombre VARCHAR(255) NOT NULL, apellido1 VARCHAR(255) NOT NULL, apellido2 VARCHAR(255) NOT NULL, direcccion VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE venta (id SERIAL NOT NULL, id_user_id INT DEFAULT NULL, fecha TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, estado_venta VARCHAR(255) NOT NULL, precio_venta DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FE7EE5579F37AE5 ON venta (id_user_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE alquiler ADD CONSTRAINT FK_655BED396E57A479 FOREIGN KEY (id_producto_id) REFERENCES producto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alquiler ADD CONSTRAINT FK_655BED3979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE producto ADD CONSTRAINT FK_A7BB0615A27E6CC8 FOREIGN KEY (id_venta_id) REFERENCES venta (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reparacion ADD CONSTRAINT FK_42114EBB6E57A479 FOREIGN KEY (id_producto_id) REFERENCES producto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reparacion ADD CONSTRAINT FK_42114EBB79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE venta ADD CONSTRAINT FK_8FE7EE5579F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE alquiler DROP CONSTRAINT FK_655BED396E57A479');
        $this->addSql('ALTER TABLE alquiler DROP CONSTRAINT FK_655BED3979F37AE5');
        $this->addSql('ALTER TABLE producto DROP CONSTRAINT FK_A7BB0615A27E6CC8');
        $this->addSql('ALTER TABLE reparacion DROP CONSTRAINT FK_42114EBB6E57A479');
        $this->addSql('ALTER TABLE reparacion DROP CONSTRAINT FK_42114EBB79F37AE5');
        $this->addSql('ALTER TABLE venta DROP CONSTRAINT FK_8FE7EE5579F37AE5');
        $this->addSql('DROP TABLE alquiler');
        $this->addSql('DROP TABLE producto');
        $this->addSql('DROP TABLE reparacion');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE venta');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
