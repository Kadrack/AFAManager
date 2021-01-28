<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210128110252 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE afamanager_club (club_id INT NOT NULL, club_join_club_last_history INT DEFAULT NULL, club_join_club_main_teacher INT DEFAULT NULL, club_name VARCHAR(255) NOT NULL, club_address VARCHAR(255) DEFAULT NULL, club_zip INT DEFAULT NULL, club_city VARCHAR(255) DEFAULT NULL, club_province INT DEFAULT NULL, club_creation DATE DEFAULT NULL, club_type INT DEFAULT NULL, club_bce_number VARCHAR(255) DEFAULT NULL, club_iban VARCHAR(255) DEFAULT NULL, club_url VARCHAR(255) DEFAULT NULL, club_email_public VARCHAR(255) DEFAULT NULL, club_name_contact VARCHAR(255) DEFAULT NULL, club_email_contact VARCHAR(255) DEFAULT NULL, club_phone_contact VARCHAR(255) DEFAULT NULL, club_address_contact VARCHAR(255) DEFAULT NULL, club_zip_contact INT DEFAULT NULL, club_city_contact VARCHAR(255) DEFAULT NULL, club_comment LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_4E5E06CCBF36FF2B (club_join_club_last_history), UNIQUE INDEX UNIQ_4E5E06CCBABDBC14 (club_join_club_main_teacher), PRIMARY KEY(club_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_club_dojo (club_dojo_id INT AUTO_INCREMENT NOT NULL, club_dojo_join_club INT DEFAULT NULL, club_dojo_name VARCHAR(255) DEFAULT NULL, club_dojo_street VARCHAR(255) DEFAULT NULL, club_dojo_zip INT DEFAULT NULL, club_dojo_city VARCHAR(255) DEFAULT NULL, club_dojo_tatamis INT DEFAULT NULL, club_dojo_dea TINYINT(1) DEFAULT NULL, club_dojo_dea_formation DATE DEFAULT NULL, club_dojo_comment LONGTEXT DEFAULT NULL, INDEX IDX_C8E7879D27119C5A (club_dojo_join_club), PRIMARY KEY(club_dojo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_club_history (club_history_id INT AUTO_INCREMENT NOT NULL, club_history_join_club INT NOT NULL, club_history_update DATE NOT NULL, club_history_status INT NOT NULL, club_history_comment LONGTEXT DEFAULT NULL, INDEX IDX_CC6C79DB6893800E (club_history_join_club), PRIMARY KEY(club_history_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_club_lesson (club_lesson_id INT AUTO_INCREMENT NOT NULL, club_lesson_join_club_dojo INT DEFAULT NULL, club_lesson_join_club INT DEFAULT NULL, club_lesson_day INT DEFAULT NULL, club_lesson_starting_hour TIME DEFAULT NULL, club_lesson_ending_hour TIME DEFAULT NULL, club_lesson_type INT NOT NULL, club_lesson_comment LONGTEXT DEFAULT NULL, INDEX IDX_204BDB3690CCD273 (club_lesson_join_club_dojo), INDEX IDX_204BDB362CE17380 (club_lesson_join_club), PRIMARY KEY(club_lesson_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_club_teacher (club_teacher_id INT AUTO_INCREMENT NOT NULL, club_teacher_join_club INT NOT NULL, club_teacher_join_member INT DEFAULT NULL, club_teacher_firstname VARCHAR(255) DEFAULT NULL, club_teacher_name VARCHAR(255) DEFAULT NULL, club_teacher_grade INT DEFAULT NULL, club_teacher_grade_title_aikikai INT DEFAULT NULL, club_teacher_grade_title_adeps INT DEFAULT NULL, club_teacher_title INT NOT NULL, club_teacher_type INT NOT NULL, club_teacher_comment LONGTEXT DEFAULT NULL, INDEX IDX_5B20AF4584B19FF7 (club_teacher_join_club), INDEX IDX_5B20AF457EA29C62 (club_teacher_join_member), PRIMARY KEY(club_teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_commission (commission_id INT AUTO_INCREMENT NOT NULL, commission_name VARCHAR(255) NOT NULL, commission_role VARCHAR(255) DEFAULT NULL, PRIMARY KEY(commission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_commission_member (commission_member_id INT AUTO_INCREMENT NOT NULL, commission_member_join_commission INT NOT NULL, commission_member_join_member INT NOT NULL, commission_member_date_in DATE NOT NULL, commission_member_date_out DATE DEFAULT NULL, INDEX IDX_FB98DA5B40693046 (commission_member_join_commission), INDEX IDX_FB98DA5BDF061EC0 (commission_member_join_member), PRIMARY KEY(commission_member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_email (email_id INT AUTO_INCREMENT NOT NULL, email_creation_date DATE NOT NULL, email_title VARCHAR(255) NOT NULL, email_from VARCHAR(255) NOT NULL, email_to VARCHAR(255) NOT NULL, email_body LONGTEXT NOT NULL, PRIMARY KEY(email_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_grade (grade_id INT AUTO_INCREMENT NOT NULL, grade_join_club INT DEFAULT NULL, grade_join_grade_session INT DEFAULT NULL, grade_join_member INT NOT NULL, grade_date DATE DEFAULT NULL, grade_rank INT NOT NULL, grade_status INT NOT NULL, grade_certificate VARCHAR(255) DEFAULT NULL, grade_comment LONGTEXT DEFAULT NULL, INDEX IDX_757580815BFFECA0 (grade_join_club), INDEX IDX_75758081C3D6A839 (grade_join_grade_session), INDEX IDX_757580814254A839 (grade_join_member), PRIMARY KEY(grade_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_grade_session (grade_session_id INT AUTO_INCREMENT NOT NULL, grade_session_date DATE NOT NULL, grade_session_type INT NOT NULL, grade_session_place VARCHAR(255) DEFAULT NULL, grade_session_street VARCHAR(255) DEFAULT NULL, grade_session_zip INT DEFAULT NULL, grade_session_city VARCHAR(255) DEFAULT NULL, grade_session_candidate_open DATE NOT NULL, grade_session_candidate_close DATE NOT NULL, grade_session_comment LONGTEXT DEFAULT NULL, PRIMARY KEY(grade_session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_grade_title (grade_title_id INT AUTO_INCREMENT NOT NULL, grade_title_join_member INT NOT NULL, grade_title_join_grade_session INT DEFAULT NULL, grade_title_date DATE DEFAULT NULL, grade_title_rank INT NOT NULL, grade_title_certificate VARCHAR(255) DEFAULT NULL, grade_title_status INT NOT NULL, grade_title_comment LONGTEXT DEFAULT NULL, INDEX IDX_CBDF190D5B3D454E (grade_title_join_member), INDEX IDX_CBDF190DF9F043E9 (grade_title_join_grade_session), PRIMARY KEY(grade_title_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_member (member_id INT AUTO_INCREMENT NOT NULL, member_join_member_first_licence INT DEFAULT NULL, member_join_member_last_licence INT DEFAULT NULL, member_join_member_actual_club INT NOT NULL, member_join_last_grade INT DEFAULT NULL, member_join_member_modification INT DEFAULT NULL, member_firstname VARCHAR(255) NOT NULL, member_name VARCHAR(255) NOT NULL, member_photo VARCHAR(255) NOT NULL, member_sex INT NOT NULL, member_address LONGTEXT NOT NULL, member_zip VARCHAR(255) DEFAULT NULL, member_city VARCHAR(255) NOT NULL, member_country VARCHAR(255) NOT NULL, member_email VARCHAR(255) DEFAULT NULL, member_phone VARCHAR(255) DEFAULT NULL, member_birthday DATE NOT NULL, member_aikikai_id VARCHAR(255) DEFAULT NULL, member_comment LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_CBC39255C8439838 (member_join_member_first_licence), UNIQUE INDEX UNIQ_CBC3925512EDE43A (member_join_member_last_licence), INDEX IDX_CBC392553510B9DD (member_join_member_actual_club), UNIQUE INDEX UNIQ_CBC392551FFB939 (member_join_last_grade), UNIQUE INDEX UNIQ_CBC3925586AF1BE8 (member_join_member_modification), PRIMARY KEY(member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_member_licence (member_licence_id INT AUTO_INCREMENT NOT NULL, member_licence_join_grade INT DEFAULT NULL, member_licence_join_club INT NOT NULL, member_licence_join_member INT NOT NULL, member_licence_update DATE NOT NULL, member_licence_deadline DATE NOT NULL, member_licence_medical_certificate DATE NOT NULL, member_licence_status INT NOT NULL, UNIQUE INDEX UNIQ_5CA2852B238C0FE (member_licence_join_grade), INDEX IDX_5CA2852D4B6CDB7 (member_licence_join_club), INDEX IDX_5CA2852BBEB3B8 (member_licence_join_member), PRIMARY KEY(member_licence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_member_modification (member_modification_id INT NOT NULL, member_modification_firstname VARCHAR(255) DEFAULT NULL, member_modification_name VARCHAR(255) DEFAULT NULL, member_modification_photo VARCHAR(255) DEFAULT NULL, member_modification_sex INT DEFAULT NULL, member_modification_address LONGTEXT DEFAULT NULL, member_modification_zip VARCHAR(255) DEFAULT NULL, member_modification_city VARCHAR(255) DEFAULT NULL, member_modification_country VARCHAR(255) DEFAULT NULL, member_modification_email VARCHAR(255) DEFAULT NULL, member_modification_phone VARCHAR(255) DEFAULT NULL, member_modification_birthday DATE DEFAULT NULL, member_modification_aikikai_id VARCHAR(255) DEFAULT NULL, member_modification_comment LONGTEXT DEFAULT NULL, PRIMARY KEY(member_modification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_member_printout (member_printout_id INT AUTO_INCREMENT NOT NULL, member_printout_join_member_licence INT DEFAULT NULL, member_printout_creation DATE NOT NULL, member_printout_action INT NOT NULL, member_printout_done DATE DEFAULT NULL, INDEX IDX_E4F002B1FA7D7843 (member_printout_join_member_licence), PRIMARY KEY(member_printout_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_secretariat_supporter (secretariat_supporter_id INT AUTO_INCREMENT NOT NULL, secretariat_supporter_name VARCHAR(255) NOT NULL, secretariat_supporter_address VARCHAR(255) NOT NULL, secretariat_supporter_zip INT NOT NULL, secretariat_supporter_city VARCHAR(255) NOT NULL, secretariat_supporter_comment LONGTEXT DEFAULT NULL, PRIMARY KEY(secretariat_supporter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_training (training_id INT AUTO_INCREMENT NOT NULL, training_join_training_first_session INT DEFAULT NULL, training_join_club INT DEFAULT NULL, training_name VARCHAR(255) DEFAULT NULL, training_type INT NOT NULL, training_place VARCHAR(255) DEFAULT NULL, training_street VARCHAR(255) DEFAULT NULL, training_zip INT DEFAULT NULL, training_city VARCHAR(255) DEFAULT NULL, training_total_sessions INT DEFAULT NULL, training_old_id INT DEFAULT NULL, training_comment LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_B651822D9A409BF3 (training_join_training_first_session), INDEX IDX_B651822D473A161F (training_join_club), PRIMARY KEY(training_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_training_attendance (training_attendance_id INT AUTO_INCREMENT NOT NULL, training_attendance_join_training INT DEFAULT NULL, training_attendance_join_member INT DEFAULT NULL, training_attendance_join_training_session INT DEFAULT NULL, training_attendance_name VARCHAR(255) DEFAULT NULL, training_attendance_unique VARCHAR(255) DEFAULT NULL, training_attendance_sex INT DEFAULT NULL, training_attendance_country VARCHAR(255) DEFAULT NULL, training_attendance_payment INT DEFAULT NULL, training_attendance_payment_type INT DEFAULT NULL, training_attendance_comment LONGTEXT DEFAULT NULL, INDEX IDX_D4E06C106523904F (training_attendance_join_training), INDEX IDX_D4E06C10FC9E9833 (training_attendance_join_member), INDEX IDX_D4E06C106B931D71 (training_attendance_join_training_session), PRIMARY KEY(training_attendance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_training_session (training_session_id INT AUTO_INCREMENT NOT NULL, training_join_training_session INT DEFAULT NULL, training_session_date DATE DEFAULT NULL, training_session_starting_hour TIME DEFAULT NULL, training_session_ending_hour TIME DEFAULT NULL, training_session_duration INT DEFAULT NULL, training_session_old_id INT DEFAULT NULL, training_session_comment LONGTEXT DEFAULT NULL, INDEX IDX_81A39E1370D8AF95 (training_join_training_session), PRIMARY KEY(training_session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_user (id INT AUTO_INCREMENT NOT NULL, user_join_member INT DEFAULT NULL, login VARCHAR(180) NOT NULL, user_firstname VARCHAR(255) DEFAULT NULL, user_real_name VARCHAR(255) DEFAULT NULL, roles VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, user_last_activity DATE DEFAULT NULL, user_status INT NOT NULL, UNIQUE INDEX UNIQ_7B23E8F7AA08CB10 (login), UNIQUE INDEX UNIQ_7B23E8F77111AECB (user_join_member), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_user_access (user_access_id INT AUTO_INCREMENT NOT NULL, user_access_join_user_user INT DEFAULT NULL, user_access_join_club INT DEFAULT NULL, user_access_role VARCHAR(255) DEFAULT NULL, INDEX IDX_9ECB2326B4103C91 (user_access_join_user_user), INDEX IDX_9ECB232651022CD (user_access_join_club), PRIMARY KEY(user_access_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE afamanager_user_audit_trail (user_audit_trail_id INT AUTO_INCREMENT NOT NULL, user_audit_trail_join_user_user INT DEFAULT NULL, user_audit_trail_join_user_who INT DEFAULT NULL, user_audit_trail_join_club INT DEFAULT NULL, user_audit_trail_date DATETIME NOT NULL, user_audit_trail_login VARCHAR(255) DEFAULT NULL, user_audit_trail_action INT NOT NULL, INDEX IDX_90E1F324CE14F958 (user_audit_trail_join_user_user), INDEX IDX_90E1F324301041C9 (user_audit_trail_join_user_who), INDEX IDX_90E1F3246949CCFE (user_audit_trail_join_club), PRIMARY KEY(user_audit_trail_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE afamanager_club ADD CONSTRAINT FK_4E5E06CCBF36FF2B FOREIGN KEY (club_join_club_last_history) REFERENCES afamanager_club_history (club_history_id)');
        $this->addSql('ALTER TABLE afamanager_club ADD CONSTRAINT FK_4E5E06CCBABDBC14 FOREIGN KEY (club_join_club_main_teacher) REFERENCES afamanager_club_teacher (club_teacher_id)');
        $this->addSql('ALTER TABLE afamanager_club_dojo ADD CONSTRAINT FK_C8E7879D27119C5A FOREIGN KEY (club_dojo_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_club_history ADD CONSTRAINT FK_CC6C79DB6893800E FOREIGN KEY (club_history_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_club_lesson ADD CONSTRAINT FK_204BDB3690CCD273 FOREIGN KEY (club_lesson_join_club_dojo) REFERENCES afamanager_club_dojo (club_dojo_id)');
        $this->addSql('ALTER TABLE afamanager_club_lesson ADD CONSTRAINT FK_204BDB362CE17380 FOREIGN KEY (club_lesson_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_club_teacher ADD CONSTRAINT FK_5B20AF4584B19FF7 FOREIGN KEY (club_teacher_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_club_teacher ADD CONSTRAINT FK_5B20AF457EA29C62 FOREIGN KEY (club_teacher_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_commission_member ADD CONSTRAINT FK_FB98DA5B40693046 FOREIGN KEY (commission_member_join_commission) REFERENCES afamanager_commission (commission_id)');
        $this->addSql('ALTER TABLE afamanager_commission_member ADD CONSTRAINT FK_FB98DA5BDF061EC0 FOREIGN KEY (commission_member_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_grade ADD CONSTRAINT FK_757580815BFFECA0 FOREIGN KEY (grade_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_grade ADD CONSTRAINT FK_75758081C3D6A839 FOREIGN KEY (grade_join_grade_session) REFERENCES afamanager_grade_session (grade_session_id)');
        $this->addSql('ALTER TABLE afamanager_grade ADD CONSTRAINT FK_757580814254A839 FOREIGN KEY (grade_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_grade_title ADD CONSTRAINT FK_CBDF190D5B3D454E FOREIGN KEY (grade_title_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_grade_title ADD CONSTRAINT FK_CBDF190DF9F043E9 FOREIGN KEY (grade_title_join_grade_session) REFERENCES afamanager_grade_session (grade_session_id)');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC39255C8439838 FOREIGN KEY (member_join_member_first_licence) REFERENCES afamanager_member_licence (member_licence_id)');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC3925512EDE43A FOREIGN KEY (member_join_member_last_licence) REFERENCES afamanager_member_licence (member_licence_id)');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC392553510B9DD FOREIGN KEY (member_join_member_actual_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC392551FFB939 FOREIGN KEY (member_join_last_grade) REFERENCES afamanager_grade (grade_id)');
        $this->addSql('ALTER TABLE afamanager_member ADD CONSTRAINT FK_CBC3925586AF1BE8 FOREIGN KEY (member_join_member_modification) REFERENCES afamanager_member_modification (member_modification_id)');
        $this->addSql('ALTER TABLE afamanager_member_licence ADD CONSTRAINT FK_5CA2852B238C0FE FOREIGN KEY (member_licence_join_grade) REFERENCES afamanager_grade (grade_id)');
        $this->addSql('ALTER TABLE afamanager_member_licence ADD CONSTRAINT FK_5CA2852D4B6CDB7 FOREIGN KEY (member_licence_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_member_licence ADD CONSTRAINT FK_5CA2852BBEB3B8 FOREIGN KEY (member_licence_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_member_printout ADD CONSTRAINT FK_E4F002B1FA7D7843 FOREIGN KEY (member_printout_join_member_licence) REFERENCES afamanager_member_licence (member_licence_id)');
        $this->addSql('ALTER TABLE afamanager_training ADD CONSTRAINT FK_B651822D9A409BF3 FOREIGN KEY (training_join_training_first_session) REFERENCES afamanager_training_session (training_session_id)');
        $this->addSql('ALTER TABLE afamanager_training ADD CONSTRAINT FK_B651822D473A161F FOREIGN KEY (training_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_training_attendance ADD CONSTRAINT FK_D4E06C106523904F FOREIGN KEY (training_attendance_join_training) REFERENCES afamanager_training (training_id)');
        $this->addSql('ALTER TABLE afamanager_training_attendance ADD CONSTRAINT FK_D4E06C10FC9E9833 FOREIGN KEY (training_attendance_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_training_attendance ADD CONSTRAINT FK_D4E06C106B931D71 FOREIGN KEY (training_attendance_join_training_session) REFERENCES afamanager_training_session (training_session_id)');
        $this->addSql('ALTER TABLE afamanager_training_session ADD CONSTRAINT FK_81A39E1370D8AF95 FOREIGN KEY (training_join_training_session) REFERENCES afamanager_training (training_id)');
        $this->addSql('ALTER TABLE afamanager_user ADD CONSTRAINT FK_7B23E8F77111AECB FOREIGN KEY (user_join_member) REFERENCES afamanager_member (member_id)');
        $this->addSql('ALTER TABLE afamanager_user_access ADD CONSTRAINT FK_9ECB2326B4103C91 FOREIGN KEY (user_access_join_user_user) REFERENCES afamanager_user (id)');
        $this->addSql('ALTER TABLE afamanager_user_access ADD CONSTRAINT FK_9ECB232651022CD FOREIGN KEY (user_access_join_club) REFERENCES afamanager_club (club_id)');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F324CE14F958 FOREIGN KEY (user_audit_trail_join_user_user) REFERENCES afamanager_user (id)');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F324301041C9 FOREIGN KEY (user_audit_trail_join_user_who) REFERENCES afamanager_user (id)');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail ADD CONSTRAINT FK_90E1F3246949CCFE FOREIGN KEY (user_audit_trail_join_club) REFERENCES afamanager_club (club_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afamanager_club_dojo DROP FOREIGN KEY FK_C8E7879D27119C5A');
        $this->addSql('ALTER TABLE afamanager_club_history DROP FOREIGN KEY FK_CC6C79DB6893800E');
        $this->addSql('ALTER TABLE afamanager_club_lesson DROP FOREIGN KEY FK_204BDB362CE17380');
        $this->addSql('ALTER TABLE afamanager_club_teacher DROP FOREIGN KEY FK_5B20AF4584B19FF7');
        $this->addSql('ALTER TABLE afamanager_grade DROP FOREIGN KEY FK_757580815BFFECA0');
        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC392553510B9DD');
        $this->addSql('ALTER TABLE afamanager_member_licence DROP FOREIGN KEY FK_5CA2852D4B6CDB7');
        $this->addSql('ALTER TABLE afamanager_training DROP FOREIGN KEY FK_B651822D473A161F');
        $this->addSql('ALTER TABLE afamanager_user_access DROP FOREIGN KEY FK_9ECB232651022CD');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail DROP FOREIGN KEY FK_90E1F3246949CCFE');
        $this->addSql('ALTER TABLE afamanager_club_lesson DROP FOREIGN KEY FK_204BDB3690CCD273');
        $this->addSql('ALTER TABLE afamanager_club DROP FOREIGN KEY FK_4E5E06CCBF36FF2B');
        $this->addSql('ALTER TABLE afamanager_club DROP FOREIGN KEY FK_4E5E06CCBABDBC14');
        $this->addSql('ALTER TABLE afamanager_commission_member DROP FOREIGN KEY FK_FB98DA5B40693046');
        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC392551FFB939');
        $this->addSql('ALTER TABLE afamanager_member_licence DROP FOREIGN KEY FK_5CA2852B238C0FE');
        $this->addSql('ALTER TABLE afamanager_grade DROP FOREIGN KEY FK_75758081C3D6A839');
        $this->addSql('ALTER TABLE afamanager_grade_title DROP FOREIGN KEY FK_CBDF190DF9F043E9');
        $this->addSql('ALTER TABLE afamanager_club_teacher DROP FOREIGN KEY FK_5B20AF457EA29C62');
        $this->addSql('ALTER TABLE afamanager_commission_member DROP FOREIGN KEY FK_FB98DA5BDF061EC0');
        $this->addSql('ALTER TABLE afamanager_grade DROP FOREIGN KEY FK_757580814254A839');
        $this->addSql('ALTER TABLE afamanager_grade_title DROP FOREIGN KEY FK_CBDF190D5B3D454E');
        $this->addSql('ALTER TABLE afamanager_member_licence DROP FOREIGN KEY FK_5CA2852BBEB3B8');
        $this->addSql('ALTER TABLE afamanager_training_attendance DROP FOREIGN KEY FK_D4E06C10FC9E9833');
        $this->addSql('ALTER TABLE afamanager_user DROP FOREIGN KEY FK_7B23E8F77111AECB');
        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC39255C8439838');
        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC3925512EDE43A');
        $this->addSql('ALTER TABLE afamanager_member_printout DROP FOREIGN KEY FK_E4F002B1FA7D7843');
        $this->addSql('ALTER TABLE afamanager_member DROP FOREIGN KEY FK_CBC3925586AF1BE8');
        $this->addSql('ALTER TABLE afamanager_training_attendance DROP FOREIGN KEY FK_D4E06C106523904F');
        $this->addSql('ALTER TABLE afamanager_training_session DROP FOREIGN KEY FK_81A39E1370D8AF95');
        $this->addSql('ALTER TABLE afamanager_training DROP FOREIGN KEY FK_B651822D9A409BF3');
        $this->addSql('ALTER TABLE afamanager_training_attendance DROP FOREIGN KEY FK_D4E06C106B931D71');
        $this->addSql('ALTER TABLE afamanager_user_access DROP FOREIGN KEY FK_9ECB2326B4103C91');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail DROP FOREIGN KEY FK_90E1F324CE14F958');
        $this->addSql('ALTER TABLE afamanager_user_audit_trail DROP FOREIGN KEY FK_90E1F324301041C9');
        $this->addSql('DROP TABLE afamanager_club');
        $this->addSql('DROP TABLE afamanager_club_dojo');
        $this->addSql('DROP TABLE afamanager_club_history');
        $this->addSql('DROP TABLE afamanager_club_lesson');
        $this->addSql('DROP TABLE afamanager_club_teacher');
        $this->addSql('DROP TABLE afamanager_commission');
        $this->addSql('DROP TABLE afamanager_commission_member');
        $this->addSql('DROP TABLE afamanager_email');
        $this->addSql('DROP TABLE afamanager_grade');
        $this->addSql('DROP TABLE afamanager_grade_session');
        $this->addSql('DROP TABLE afamanager_grade_title');
        $this->addSql('DROP TABLE afamanager_member');
        $this->addSql('DROP TABLE afamanager_member_licence');
        $this->addSql('DROP TABLE afamanager_member_modification');
        $this->addSql('DROP TABLE afamanager_member_printout');
        $this->addSql('DROP TABLE afamanager_secretariat_supporter');
        $this->addSql('DROP TABLE afamanager_training');
        $this->addSql('DROP TABLE afamanager_training_attendance');
        $this->addSql('DROP TABLE afamanager_training_session');
        $this->addSql('DROP TABLE afamanager_user');
        $this->addSql('DROP TABLE afamanager_user_access');
        $this->addSql('DROP TABLE afamanager_user_audit_trail');
    }
}
