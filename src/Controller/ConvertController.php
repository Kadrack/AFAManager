<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\GradeTitle;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;

use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class ConvertController extends AbstractController
{
    /**
     * @Route("/import", name="import_index")
     */
    public function import()
    {
        return $this->render('Import/index.html.twig');
    }

    /**
     * @Route("/import_dojos", name="import_dojos")
     */
    public function importDojos()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_DOJO ORDER BY DOJO_ID");
        $query->execute();
        $query->bind_result($id, $name, $address, $zip, $city, $country_id, $phone, $fax, $bank, $type_id, $tatami, $memo, $province_id, $email, $url, $email_renew, $asbl, $creation, $contact_name, $contact_firstname, $contact_gsm, $bce, $affiliation);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $club = new Club();

            $club->setClubId(intval($id));
            $club->setClubName($name);
            $club->setClubIban($bank);
            $club->setClubUrl($url);
            $club->setClubCreation(new DateTime($creation));
            $club->setClubEmailPublic($email);
            $club->setClubEmailContact($email_renew);
            $club->setClubAddress($address);
            $club->setClubZip(intval($zip));
            $club->setClubCity($city);
            $club->setClubType($asbl == "ASBL" ? 1 : 2);
            $club->setClubBceNumber($bce);
            $club->setClubComment($memo);

            switch ($province_id) {
                case 1:
                    $club->setClubProvince(1);
                    break;
                case 2:
                    $club->setClubProvince(2);
                    break;
                case 3:
                    $club->setClubProvince(3);
                    break;
                case 4:
                    $club->setClubProvince(6);
                    break;
                case 5:
                    $club->setClubProvince(4);
                    break;
                case 6:
                    $club->setClubProvince(5);
                    break;
                case 7:
                    $club->setClubProvince(7);
                    break;
                default:
                    $club->setClubProvince(20);
            }

            $history = new ClubHistory();

            $history->setClubHistoryStatus(1);
            $history->setClubHistoryUpdate(new DateTime($affiliation));

            $club->addClubHistories($history);
            $club->setClubLastHistory($history);

            $entityManager->persist($club);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_pratiquants", name="import_pratiquants")
     */
    public function importPratiquants()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $cpt = 0;

        $query = $old_db->stmt_init();

        $query->prepare("DELETE FROM MYSQL_PRATIQUANT WHERE LICENCE_ID=45171");
        $query->execute();

        $query->prepare("Select * FROM MYSQL_PRATIQUANT ORDER BY LICENCE_ID");
        $query->execute();
        $query->bind_result($licence_id, $nom, $prenom, $adresse, $code_postale, $localite, $pays_id, $profession_id, $gsm, $tel_prive, $tel_bureau, $fax_prive, $fax_bureau, $date_de_naissance, $nationalite_id, $date_echeance, $date_payement, $date_certificat_medical, $adeps_id, $statut_id_1, $memo, $est_actif, $dojo_id, $est_enfant, $sexe, $email, $grade_no, $aikikai_no, $recoit_le_flash, $status_id_2, $recoit_le_flash_enfant, $date_card, $date_creation, $date_derniere_modification, $federation_id, $date_nouvelle_echeance, $accord_partenariat, $date_debut_pratique, $est_pratiquant, $est_administrateur, $date_interruption_de, $date_interruption_a, $date_interruption_mois);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => 3037]);

            while (++$cpt < $licence_id)
            {
                $member = new Member();

                $member->setMemberFirstname("Remplissage");
                $member->setMemberName("Remplissage");
                $member->setMemberPhoto("aucune.jpg");
                $member->setMemberSex(1);
                $member->setMemberCountry("BE");
                $member->setMemberAddress("Aucune");
                $member->setMemberCity("Aucune");
                $member->setMemberBirthday(new DateTime('today'));
                $member->setMemberActualClub($club);

                $entityManager->persist($member);
            }

            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $dojo_id]);

            $member = new Member();

            $member->setMemberFirstname(is_null($prenom) ? "" : $prenom);
            $member->setMemberName(is_null($nom) ? "" : $nom);
            $member->setMemberPhoto($licence_id . ".jpg");
            $member->setMemberSex($sexe == "M" ? 1 : 2);
            $member->setMemberAddress(is_null($adresse) ? "" : $adresse);
            $member->setMemberZip(is_null($code_postale) ? "" : $code_postale);
            $member->setMemberCity(is_null($localite) ? "" : $localite);
            $member->setMemberEmail(is_null($email) ? "" : $email);
            $member->setMemberBirthday(new DateTime($date_de_naissance));
            $member->setMemberComment(is_null($memo) ? "" : $memo);
            $member->setMemberActualClub($club);

            switch ($pays_id)
            {
                case 39:
                    $member->setMemberCountry("BE");
                    break;
                case 20:
                    $member->setMemberCountry("PT");
                    break;
                case 18:
                    $member->setMemberCountry("NL");
                    break;
                case 16:
                    $member->setMemberCountry("LU");
                    break;
                case 10:
                    $member->setMemberCountry("FR");
                    break;
                case 8:
                    $member->setMemberCountry("DE");
                    break;
                case 6:
                    $member->setMemberCountry("BE");
                    break;
                case 0:
                    $member->setMemberCountry("BE");
                    break;
                default:
                    $member->setMemberCountry("BE");
            }

            $first = false;

            if (($date_debut_pratique != null) AND ($date_debut_pratique != '0000-00-00') AND (new DateTime($date_nouvelle_echeance) > new DateTime('+1 year ' . $date_debut_pratique)))
            {
                $licence = new MemberLicence();

                $licence->setMemberLicenceStatus(0);
                $licence->setMemberLicenceClub($club);
                $licence->setMemberLicenceUpdate(new DateTime('today'));
                $licence->setMemberLicenceMedicalCertificate(new DateTime($date_debut_pratique));
                $licence->setMemberLicenceDeadline(new DateTime('+1 year ' . $licence->getMemberLicenceMedicalCertificate()->format('Y-m-d')));

                $member->addMemberLicences($licence);
                $member->setMemberFirstLicence($licence);
                $member->setMemberLastLicence($licence);

                $first = true;
            }

            if (($date_nouvelle_echeance != null) AND ($date_nouvelle_echeance != '0000-00-00'))
            {
                $licence = new MemberLicence();

                $licence->setMemberLicenceStatus(1);
                $licence->setMemberLicenceClub($club);
                $licence->setMemberLicenceUpdate(new DateTime('today'));
                $licence->setMemberLicenceDeadline(new DateTime($date_nouvelle_echeance));
                $licence->setMemberLicenceMedicalCertificate(new DateTime($date_certificat_medical));

                $member->addMemberLicences($licence);
                $member->setMemberLastLicence($licence);

                $first ? $first = true : $member->setMemberFirstLicence($licence);
            }

            $entityManager->persist($member);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        $clean = $this->getDoctrine()->getRepository(Member::class)->findBy(['member_firstname' => 'Remplissage', 'member_name' => 'Remplissage', 'member_photo' => 'aucune.jpg']);

        foreach ($clean as $member)
        {
            $entityManager->remove($member);
        }

        $entityManager->flush();

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_kyus", name="import_kyus")
     */
    public function importKyus()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>0 AND GRADE_NO<7 ORDER BY LICENCE_ID AND GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_id, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $licence_id]);

            $kyu = new Grade();

            $kyu->setGradeMember($member);
            $kyu->setGradeRank($grade_no);
            $kyu->setGradeDate(new DateTime($date_examen));
            $kyu->setGradeStatus(4);
            $kyu->setGradeClub($member->getMemberActualClub());

            switch ($federation_id)
            {
                case 100:
                    $kyu->setGradeComment("UBéa");
                    break;
                case 115:
                    $kyu->setGradeComment("Aïkido Harmonie");
                    break;
                default:
                    $kyu->setGradeComment(null);
            }

            $member->setMemberLastGrade($kyu);

            $entityManager->persist($kyu);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_dan_sessions", name="import_dan_sessions")
     */
    public function importDanSession()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>6 AND GRADE_NO<23 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_id, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            if (($federation_id == 96) OR ($federation_id == 97))
            {
                $type = 1;
            }
            else
            {
                $type = 5;
            }

            if (($est_recu_par_kagami == 1) AND ($type == 1))
            {
                $type = 2;
            }

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $session == null ? $create = true : $create = false;

            if ($create)
            {
                $session = new GradeSession();

                $session->setGradeSessionType($type);
                $session->setGradeSessionDate(new DateTime($date));
                $session->setGradeSessionCandidateOpen(new DateTime($date));
                $session->setGradeSessionCandidateClose(new DateTime($date));

                $entityManager->persist($session);
                $entityManager->flush();
            }
        }

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_grades_dan", name="import_grades_dan")
     */
    public function importGradesDan()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>6 AND GRADE_NO<23 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_no, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            if (($federation_id == 96) OR ($federation_id == 97))
            {
                $type = 1;
            }
            else
            {
                $type = 5;
            }

            if (($est_recu_par_kagami == 1) AND ($type == 1))
            {
                $type = 2;
            }

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            //$session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $licence_id]);

            $grade = new Grade();

            $grade->setGradeDate(new DateTime($date));
            $grade->setGradeMember($member);
            $grade->setGradeRank($grade_no);
            //$grade->setGradeExam($session);

            if (($grade_no == 7) OR ($grade_no == 9) OR ($grade_no == 11) OR ($grade_no == 13) OR ($grade_no == 15) OR ($grade_no == 17) OR ($grade_no == 19) OR ($grade_no == 21) OR ($grade_no == 23))
            {
                $grade->setGradeStatus(4);
                $grade->setGradeCertificate(utf8_encode($diplome_national_no));
            }
            else
            {
                $grade->setGradeStatus(5);
                $grade->setGradeCertificate(utf8_encode($diplome_aikikai_no));
            }

            $member->setMemberLastGrade($grade);

            $entityManager->persist($grade);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_teach_sessions", name="import_teach_sessions")
     */
    public function importTeachSession()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>22 AND GRADE_NO<26 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_id, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $type = 4;

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $session == null ? $create = true : $create = false;

            if ($create)
            {
                $session = new GradeSession();

                $session->setGradeSessionType($type);
                $session->setGradeSessionDate(new DateTime($date));
                $session->setGradeSessionCandidateOpen(new DateTime($date));
                $session->setGradeSessionCandidateClose(new DateTime($date));

                $entityManager->persist($session);
                $entityManager->flush();
            }
        }

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_grades_teach", name="import_grades_teach")
     */
    public function importGradesTeach()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>22 AND GRADE_NO<26 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_no, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $type = 4;

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $licence_id]);

            $grade = new GradeTitle();

            $grade->setGradeTitleStatus(1);
            $grade->setGradeTitleMember($member);
            $grade->setGradeTitleExam($session);
            $grade->setGradeTitleCertificate(utf8_encode($diplome_national_no));

            switch ($grade_no)
            {
                case 23: $grade->setGradeTitleRank(1); break;
                case 24: $grade->setGradeTitleRank(2); break;
                case 25: $grade->setGradeTitleRank(3); break;
            }

            $entityManager->persist($grade);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_adeps_sessions", name="import_adeps_sessions")
     */
    public function importAdepsSession()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>25 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_id, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $type = 3;

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $session == null ? $create = true : $create = false;

            if ($create)
            {
                $session = new GradeSession();

                $session->setGradeSessionType($type);
                $session->setGradeSessionDate(new DateTime($date));
                $session->setGradeSessionCandidateOpen(new DateTime($date));
                $session->setGradeSessionCandidateClose(new DateTime($date));

                $entityManager->persist($session);
                $entityManager->flush();
            }
        }

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_grades_adeps", name="import_grades_adeps")
     */
    public function importGradesAdeps()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_HIST_DES_GRADES WHERE GRADE_NO>25 ORDER BY LICENCE_ID, GRADE_NO");
        $query->execute();
        $query->bind_result($licence_id, $grade_no, $date_examen, $date_diplome, $diplome_national_no, $diplome_aikikai_no, $est_recu_par_kagami, $federation_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $type = 3;

            $date_examen == null ? $date = $date_diplome : $date = $date_examen;

            $date == null ? $date = '1900-01-01' : $date;

            $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_date' => new DateTime($date), 'grade_session_type' => $type]);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $licence_id]);

            $grade = new GradeTitle();

            $grade->setGradeTitleStatus(1);
            $grade->setGradeTitleMember($member);
            $grade->setGradeTitleExam($session);
            $grade->setGradeTitleCertificate(utf8_encode($diplome_national_no));

            switch ($grade_no)
            {
                case 26: $grade->setGradeTitleRank(4); break;
                case 27: $grade->setGradeTitleRank(5); break;
                case 28: $grade->setGradeTitleRank(6); break;
            }

            $entityManager->persist($grade);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_stages", name="import_stages")
     */
    public function importStages()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select * FROM MYSQL_STAGE ORDER BY DATE_STAGE");
        $query->execute();
        $query->bind_result($stage_id, $lieu, $sensei, $date_stage, $nombre_heures, $memo, $prix, $date_stage_au, $dojo_id);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $stage = new Training();

            $stage->setTrainingOldId($stage_id);
            $stage->setTrainingName(utf8_encode($sensei));
            $stage->setTrainingComment(utf8_encode($memo));

            if ($dojo_id == null)
            {
                $stage->setTrainingType(4);
            }
            else
            {
                $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $dojo_id]);

                $stage->setTrainingClub($club);
                $stage->setTrainingType(5);
            }

            $entityManager->persist($stage);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_stages_session", name="import_stages_session")
     */
    public function importStagesSession()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select p.PARTICIPANTS_STAGE_ID, p.STAGE_ID, p.LICENCE_ID, p.REMARQUE, p.NOM, p.PRENOM, p.CLUB, p.PRIX, p.PAYEMENT_PAR_CARTE, p.NOMBRE_HEURES, p.MEMO, s.DATE_STAGE FROM MYSQL_PARTICIPANTS_STAGE p JOIN mysql_stage s ON p.STAGE_ID = s.STAGE_ID ORDER BY STAGE_ID");
        $query->execute();
        $query->bind_result($participants_stage_id, $stage_id, $licence_id, $remarque, $nom, $prenom, $club, $prix, $payement_par_carte, $nombre_heures, $memo, $date_stage);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $session = $this->getDoctrine()->getRepository(TrainingSession::class)->findOneBy(['training_session_old_id' => $stage_id, 'training_session_duration' => $nombre_heures*60]);

            if ($session == null)
            {
                $stage = $this->getDoctrine()->getRepository(Training::class)->findOneBy(['training_old_id' => $stage_id]);

                if ($stage->getTrainingTotalSessions() == null)
                {
                    $stage->setTrainingTotalSessions(1);
                }
                else
                {
                    $stage->setTrainingTotalSessions($stage->getTrainingTotalSessions() + 1);
                }

                $session = new TrainingSession();

                $session->setTraining($stage);
                $session->setTrainingSessionOldId($stage_id);
                $session->setTrainingSessionDuration($nombre_heures*60);
                $session->setTrainingSessionDate(new DateTime($date_stage));

                $stage->setTrainingFirstSession($session);

                $entityManager->persist($session);
                $entityManager->flush();
            }
        }

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }

    /**
     * @Route("/import_stages_presence", name="import_stages_presence")
     */
    public function importStagesPresence()
    {
        $old_db = mysqli_connect("localhost", "root", "", "aikidobekxmydb");

        $query = $old_db->stmt_init();

        $query->prepare("Select p.PARTICIPANTS_STAGE_ID, p.STAGE_ID, p.LICENCE_ID, p.REMARQUE, p.NOM, p.PRENOM, p.CLUB, p.PRIX, p.PAYEMENT_PAR_CARTE, p.NOMBRE_HEURES, p.MEMO, s.DATE_STAGE FROM MYSQL_PARTICIPANTS_STAGE p JOIN mysql_stage s ON p.STAGE_ID = s.STAGE_ID ORDER BY STAGE_ID");
        $query->execute();
        $query->bind_result($participants_stage_id, $stage_id, $licence_id, $remarque, $nom, $prenom, $club, $prix, $payement_par_carte, $nombre_heures, $memo, $date_stage);

        $entityManager = $this->getDoctrine()->getManager();

        while ($query->fetch())
        {
            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $licence_id]);

            $stage = $this->getDoctrine()->getRepository(Training::class)->findOneBy(['training_old_id' => $stage_id]);

            $session = $this->getDoctrine()->getRepository(TrainingSession::class)->findOneBy(['training_session_old_id' => $stage_id, 'training_session_duration' => $nombre_heures*60]);

            $presence = new TrainingAttendance();

            $presence->setTraining($stage);
            $presence->setTrainingAttendanceMember($member);
            $presence->setTrainingAttendanceSession($session);
            $presence->setTrainingAttendanceUnique(microtime());
            $presence->setTrainingAttendanceName(utf8_encode($prenom.' '.$nom));
            $presence->setTrainingAttendanceComment(utf8_encode($club.' '.$remarque.' '.$memo));
            $presence->setTrainingAttendancePayment($prix == null ? 0 : $prix);
            $presence->setTrainingAttendancePaymentType($payement_par_carte == 1 ? 2 : 1);

            $entityManager->persist($presence);
        }

        $entityManager->flush();

        mysqli_close($old_db);

        return $this->redirectToRoute('import_index');
    }
}
