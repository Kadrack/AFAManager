<?php
// src/Service/ListData.php
namespace App\Service;

use Symfony\Component\Intl\Countries;

/**
 * Class ListData
 * @package App\Service
 */
class ListData
{
    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getAccessType(int $type = 0): array|bool|int|string
    {
        $access = array('Aucun' => 1, 'CT' => 2, 'Stage' => 3, 'CA' => 4, 'CP' => 5, 'Comptabilité' => 6);

        if ($type == 0)
        {
            return $access;
        }
        else if ($type > sizeof($access))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $access);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getClubType(int $type): array|bool|int|string
    {
        $types = array('ASBL' => 1, 'Association de fait' => 2, 'Autres' => 3);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }

    /**
     * @param string $country
     * @return string
     */
    public function getCountryName(string $country): string
    {
        $countries = Countries::getNames('fr');

        return $countries[$country];
    }

    /**
     * @param int $day
     * @return array|bool|int|string
     */
    public function getDay(int $day): array|bool|int|string
    {
        $days = array('Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7);

        if ($day == 0)
        {
            return $days;
        }
        else if ($day > sizeof($days))
        {
            return "Autre";
        }
        else
        {
            return array_search($day, $days);
        }
    }

    /**
     * @param int $result
     * @return array|bool|int|string
     */
    public function getExamResult(int $result): array|bool|int|string
    {
        $results = array('Postulant' => 1, 'Candidat' => 2, 'Refusé' => 3, 'Réussi' => 4, 'Promu' => 5, 'En Attente' => 6, 'Reconnu' => 7);

        if ($result == 0)
        {
            return $results;
        }
        else if ($result > sizeof($results))
        {
            return "Autre";
        }
        else
        {
            return array_search($result, $results);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getExamType(int $type): array|bool|int|string
    {
        $types = array('Examen' => 1, 'Kagami Biraki' => 2, 'Enseignant Adeps' => 3, 'Enseignant Aïkikaï' => 4, 'Reconnaissance' => 5, 'Autre' => 6);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }

    /**
     * @param int $grade
     * @return bool|int|array|string
     */
    public function getGrade(int $grade = 0): bool|int|array|string
    {
        if ($grade == 0)
        {
            return array_merge($this->getGradeKyu(), $this->getGradeDan());
        }
        else if (($grade >= 0) and (($grade <= 6)))
        {
            return array_search($grade, $this->getGradeKyu());
        }
        else if (($grade >= 7) and (($grade <= 24)))
        {
            return array_search($grade, $this->getGradeDan());
        }
        else
        {
            return "Autres";
        }
    }

    /**
     * @return int[]
     */
    public function getGradeDan(): array
    {
        return array('Shodan National' => 7, 'Shodan Aïkikaï' => 8, 'Nidan National' => 9, 'Nidan Aïkikaï' => 10, 'Sandan National' => 11, 'Sandan Aïkikaï' => 12, 'Yondan National' => 13, 'Yondan Aïkikaï' => 14, 'Godan National' => 15, 'Godan Aïkikaï' => 16, 'Rokudan National' => 17, 'Rokudan Aïkikaï' => 18, 'Nanadan National' => 19, 'Nanadan Aïkikaï' => 20, 'Hachidan National' => 21, 'Hachidan Aïkikaï' => 22, 'Kudan National' => 23, 'Kudan Aïkikaï' => 24);
    }

    /**
     * @return int[]
     */
    public function getGradeKyu(): array
    {
        return array('6ème kyu' => 1, '5ème kyu' => 2, '4ème kyu' => 3, '3ème kyu' => 4, '2ème kyu' => 5, '1er kyu'  => 6);
    }

    /**
     * @param int $title
     * @return array|bool|int|string
     */
    public function getGradeTitle(int $title): array|bool|int|string
    {
        $titles = array('Fuku Shidoïn' => 1, 'Shidoïn' => 2, 'Shihan' => 3, 'Initiateur' => 4, 'Aide-Moniteur' => 5, 'Moniteur' => 6, 'Moniteur Animateur' => 7, 'Moniteur Initiateur' => 8, 'Moniteur Educateur' => 9, 'Autre' => 10);

        if ($title == 0)
        {
            return $titles;
        }
        else if ($title > sizeof($titles))
        {
            return "Autre";
        }
        else
        {
            return array_search($title, $titles);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getPaymentType(int $type): array|bool|int|string
    {
        $types = array('Cash' => 1, 'Carte' => 2);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }

    /**
     * @param int $province
     * @return array|bool|int|string
     */
    public function getProvince(int $province): array|bool|int|string
    {
        $provinces = array('Bruxelles' => 1, 'Brabant Wallon' => 2, 'Hainaut' => 3, 'Liège' => 4, 'Luxembourg' => 5, 'Namur' => 6, 'Brabant Flamand' => 7, 'Frigo' => 20);

        if ($province == 0)
        {
            return $provinces;
        }
        else if ($province > sizeof($provinces))
        {
            return "Autre";
        }
        else
        {
            return array_search($province, $provinces);
        }
    }

    /**
     * @param int $sex
     * @return array|bool|int|string
     */
    public function getSex(int $sex): array|bool|int|string
    {
        $sexes = array('Masculin' => 1, 'Féminin' => 2);

        if ($sex == 0)
        {
            return $sexes;
        }
        else if ($sex > sizeof($sexes))
        {
            return "Autre";
        }
        else
        {
            return array_search($sex, $sexes);
        }
    }

    /**
     * @param int $title
     * @return array|bool|int|string
     */
    public function getTeacherTitle(int $title): array|bool|int|string
    {
        $titles = array('Dojo Cho' => 1, 'Professeur' => 2, 'Assistant' => 3);

        if ($title == 0)
        {
            return $titles;
        }
        else if ($title > sizeof($titles))
        {
            return "Autre";
        }
        else
        {
            return array_search($title, $titles);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getTeacherType(int $type): array|bool|int|string
    {
        $types = array('Adultes' => 1, 'Enfants' => 2, 'Adultes/Enfants' => 3);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getLessonType(int $type): array|bool|int|string
    {
        $types = array('Cours Adultes' => 1, 'Cours Enfants' => 2, 'Cours Adultes/Enfants' => 3);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }

    /**
     * @param int $type
     * @return array|bool|int|string
     */
    public function getTrainingType(int $type): array|bool|int|string
    {
        $types = array('Fédéral' => 1, 'Privé' => 2);

        if ($type == 0)
        {
            return $types;
        }
        else if ($type > sizeof($types))
        {
            return "Autre";
        }
        else
        {
            return array_search($type, $types);
        }
    }
}
