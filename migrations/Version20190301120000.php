<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190301120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initiales Schema ImmoVerwalt (Übernahme aus Version 2.x)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE app_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE appointment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE building_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE charge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE handover_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE issue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE issue_status_change_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE owner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE property_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE prospect_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rental_unit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tenancy_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tenant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE viewing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, username VARCHAR(50) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(150) DEFAULT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9F85E0677 ON app_user (username)');
        $this->addSql('CREATE TABLE appointment (id INT NOT NULL, title VARCHAR(150) NOT NULL, type VARCHAR(20) NOT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, ref_type VARCHAR(20) NOT NULL, ref_id INT DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, property_id INT NOT NULL, unit_id INT DEFAULT NULL, tenancy_id INT DEFAULT NULL, owner_id INT NOT NULL, type VARCHAR(10) NOT NULL, date DATE NOT NULL, amount NUMERIC(10, 2) NOT NULL, category VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDE549213EC ON booking (property_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEF8BD700D ON booking (unit_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE8127EC24 ON booking (tenancy_id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDE7E3C61F9 ON booking (owner_id)');
        $this->addSql('CREATE TABLE building (id INT NOT NULL, property_id INT NOT NULL, building_number VARCHAR(20) NOT NULL, name VARCHAR(150) NOT NULL, address VARCHAR(255) DEFAULT NULL, floors INT DEFAULT NULL, construction_year INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E16F61D4549213EC ON building (property_id)');
        $this->addSql('CREATE TABLE charge (id INT NOT NULL, tenancy_id INT NOT NULL, month VARCHAR(7) NOT NULL, position VARCHAR(20) NOT NULL, due_date DATE NOT NULL, amount NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_556BA4348127EC24 ON charge (tenancy_id)');
        $this->addSql('CREATE TABLE handover (id INT NOT NULL, tenancy_id INT NOT NULL, date DATE NOT NULL, type VARCHAR(10) NOT NULL, meter_readings TEXT DEFAULT NULL, key_count INT DEFAULT NULL, remarks TEXT DEFAULT NULL, defects TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8019E0D48127EC24 ON handover (tenancy_id)');
        $this->addSql('CREATE TABLE issue (id INT NOT NULL, property_id INT NOT NULL, unit_id INT DEFAULT NULL, tenant_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, issue_number VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, category VARCHAR(20) NOT NULL, description TEXT NOT NULL, priority INT NOT NULL, status VARCHAR(20) NOT NULL, attachments VARCHAR(255) DEFAULT NULL, internal_notes TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_12AD233E64609573 ON issue (issue_number)');
        $this->addSql('CREATE INDEX IDX_12AD233E549213EC ON issue (property_id)');
        $this->addSql('CREATE INDEX IDX_12AD233EF8BD700D ON issue (unit_id)');
        $this->addSql('CREATE INDEX IDX_12AD233E9033212A ON issue (tenant_id)');
        $this->addSql('CREATE INDEX IDX_12AD233E59EC7D60 ON issue (assignee_id)');
        $this->addSql('CREATE TABLE issue_status_change (id INT NOT NULL, issue_id INT NOT NULL, old_status VARCHAR(20) DEFAULT NULL, new_status VARCHAR(20) NOT NULL, changed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, changed_by VARCHAR(50) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C77E8B05E7AA58C ON issue_status_change (issue_id)');
        $this->addSql('CREATE TABLE owner (id INT NOT NULL, owner_number VARCHAR(20) NOT NULL, name VARCHAR(150) NOT NULL, address TEXT DEFAULT NULL, contact_person VARCHAR(100) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF60E67C336A330A ON owner (owner_number)');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, tenancy_id INT NOT NULL, charge_id INT DEFAULT NULL, payment_date DATE NOT NULL, amount NUMERIC(10, 2) NOT NULL, reference VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D8127EC24 ON payment (tenancy_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D55284914 ON payment (charge_id)');
        $this->addSql('CREATE TABLE property (id INT NOT NULL, owner_id INT NOT NULL, property_number VARCHAR(20) NOT NULL, name VARCHAR(150) NOT NULL, street VARCHAR(150) NOT NULL, house_number VARCHAR(10) NOT NULL, zip VARCHAR(10) NOT NULL, city VARCHAR(100) NOT NULL, construction_year INT DEFAULT NULL, management_start DATE DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BF21CDEE8E77028 ON property (property_number)');
        $this->addSql('CREATE INDEX IDX_8BF21CDE7E3C61F9 ON property (owner_id)');
        $this->addSql('CREATE TABLE prospect (id INT NOT NULL, unit_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, request_date DATE NOT NULL, status VARCHAR(20) NOT NULL, remark TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C9CE8C7DF8BD700D ON prospect (unit_id)');
        $this->addSql('CREATE TABLE rental_unit (id INT NOT NULL, building_id INT NOT NULL, version INT DEFAULT 1 NOT NULL, unit_number VARCHAR(20) NOT NULL, type VARCHAR(3) NOT NULL, floor INT DEFAULT NULL, area NUMERIC(8, 2) DEFAULT NULL, cold_rent NUMERIC(10, 2) DEFAULT NULL, service_charge_advance NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, show_in_list BOOLEAN NOT NULL, css_class VARCHAR(30) DEFAULT NULL, last_modified TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_modified_by VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_53F753934D2A7E12 ON rental_unit (building_id)');
        $this->addSql('CREATE TABLE service_provider (id INT NOT NULL, company VARCHAR(150) NOT NULL, trade VARCHAR(30) NOT NULL, contact_person VARCHAR(100) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, property_id INT NOT NULL, unit_id INT DEFAULT NULL, issue_id INT DEFAULT NULL, responsible_id INT DEFAULT NULL, service_provider_id INT DEFAULT NULL, task_number VARCHAR(20) NOT NULL, title VARCHAR(150) NOT NULL, description TEXT DEFAULT NULL, contact_person VARCHAR(100) DEFAULT NULL, due_date DATE DEFAULT NULL, priority INT NOT NULL, status VARCHAR(20) NOT NULL, scheduled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB25AD399B87 ON task (task_number)');
        $this->addSql('CREATE INDEX IDX_527EDB25549213EC ON task (property_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25F8BD700D ON task (unit_id)');
        $this->addSql('CREATE INDEX IDX_527EDB255E7AA58C ON task (issue_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25602AD315 ON task (responsible_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25C6C98E06 ON task (service_provider_id)');
        $this->addSql('CREATE TABLE tenancy (id INT NOT NULL, unit_id INT NOT NULL, contract_number VARCHAR(20) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, notice_date DATE DEFAULT NULL, cold_rent NUMERIC(10, 2) NOT NULL, service_charge_advance NUMERIC(10, 2) NOT NULL, deposit NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, notice_given BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5DAAFB33AAD0FA19 ON tenancy (contract_number)');
        $this->addSql('CREATE INDEX IDX_5DAAFB33F8BD700D ON tenancy (unit_id)');
        $this->addSql('CREATE TABLE tenancy_tenant (tenancy_id INT NOT NULL, tenant_id INT NOT NULL, PRIMARY KEY(tenancy_id, tenant_id))');
        $this->addSql('CREATE INDEX IDX_524425BC8127EC24 ON tenancy_tenant (tenancy_id)');
        $this->addSql('CREATE INDEX IDX_524425BC9033212A ON tenancy_tenant (tenant_id)');
        $this->addSql('CREATE TABLE tenant (id INT NOT NULL, salutation VARCHAR(20) DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, birth_date DATE DEFAULT NULL, previous_address TEXT DEFAULT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE viewing (id INT NOT NULL, unit_id INT NOT NULL, prospect_id INT NOT NULL, employee_id INT DEFAULT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(20) NOT NULL, remark TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F5BB4698F8BD700D ON viewing (unit_id)');
        $this->addSql('CREATE INDEX IDX_F5BB4698D182060A ON viewing (prospect_id)');
        $this->addSql('CREATE INDEX IDX_F5BB46988C03F15C ON viewing (employee_id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE549213EC FOREIGN KEY (property_id) REFERENCES property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEF8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8127EC24 FOREIGN KEY (tenancy_id) REFERENCES tenancy (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D4549213EC FOREIGN KEY (property_id) REFERENCES property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE charge ADD CONSTRAINT FK_556BA4348127EC24 FOREIGN KEY (tenancy_id) REFERENCES tenancy (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE handover ADD CONSTRAINT FK_8019E0D48127EC24 FOREIGN KEY (tenancy_id) REFERENCES tenancy (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E549213EC FOREIGN KEY (property_id) REFERENCES property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EF8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E59EC7D60 FOREIGN KEY (assignee_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue_status_change ADD CONSTRAINT FK_2C77E8B05E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8127EC24 FOREIGN KEY (tenancy_id) REFERENCES tenancy (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D55284914 FOREIGN KEY (charge_id) REFERENCES charge (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE prospect ADD CONSTRAINT FK_C9CE8C7DF8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rental_unit ADD CONSTRAINT FK_53F753934D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25549213EC FOREIGN KEY (property_id) REFERENCES property (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB255E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25602AD315 FOREIGN KEY (responsible_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tenancy ADD CONSTRAINT FK_5DAAFB33F8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tenancy_tenant ADD CONSTRAINT FK_524425BC8127EC24 FOREIGN KEY (tenancy_id) REFERENCES tenancy (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tenancy_tenant ADD CONSTRAINT FK_524425BC9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT FK_F5BB4698F8BD700D FOREIGN KEY (unit_id) REFERENCES rental_unit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT FK_F5BB4698D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE viewing ADD CONSTRAINT FK_F5BB46988C03F15C FOREIGN KEY (employee_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE app_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE appointment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE building_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE charge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE handover_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE issue_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE issue_status_change_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE owner_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE property_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE prospect_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rental_unit_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_provider_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tenancy_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tenant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE viewing_id_seq CASCADE');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE549213EC');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDEF8BD700D');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE8127EC24');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE7E3C61F9');
        $this->addSql('ALTER TABLE building DROP CONSTRAINT FK_E16F61D4549213EC');
        $this->addSql('ALTER TABLE charge DROP CONSTRAINT FK_556BA4348127EC24');
        $this->addSql('ALTER TABLE handover DROP CONSTRAINT FK_8019E0D48127EC24');
        $this->addSql('ALTER TABLE issue DROP CONSTRAINT FK_12AD233E549213EC');
        $this->addSql('ALTER TABLE issue DROP CONSTRAINT FK_12AD233EF8BD700D');
        $this->addSql('ALTER TABLE issue DROP CONSTRAINT FK_12AD233E9033212A');
        $this->addSql('ALTER TABLE issue DROP CONSTRAINT FK_12AD233E59EC7D60');
        $this->addSql('ALTER TABLE issue_status_change DROP CONSTRAINT FK_2C77E8B05E7AA58C');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D8127EC24');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D55284914');
        $this->addSql('ALTER TABLE property DROP CONSTRAINT FK_8BF21CDE7E3C61F9');
        $this->addSql('ALTER TABLE prospect DROP CONSTRAINT FK_C9CE8C7DF8BD700D');
        $this->addSql('ALTER TABLE rental_unit DROP CONSTRAINT FK_53F753934D2A7E12');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25549213EC');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25F8BD700D');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB255E7AA58C');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25602AD315');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25C6C98E06');
        $this->addSql('ALTER TABLE tenancy DROP CONSTRAINT FK_5DAAFB33F8BD700D');
        $this->addSql('ALTER TABLE tenancy_tenant DROP CONSTRAINT FK_524425BC8127EC24');
        $this->addSql('ALTER TABLE tenancy_tenant DROP CONSTRAINT FK_524425BC9033212A');
        $this->addSql('ALTER TABLE viewing DROP CONSTRAINT FK_F5BB4698F8BD700D');
        $this->addSql('ALTER TABLE viewing DROP CONSTRAINT FK_F5BB4698D182060A');
        $this->addSql('ALTER TABLE viewing DROP CONSTRAINT FK_F5BB46988C03F15C');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE charge');
        $this->addSql('DROP TABLE handover');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE issue_status_change');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE prospect');
        $this->addSql('DROP TABLE rental_unit');
        $this->addSql('DROP TABLE service_provider');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE tenancy');
        $this->addSql('DROP TABLE tenancy_tenant');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE viewing');
    }
}
