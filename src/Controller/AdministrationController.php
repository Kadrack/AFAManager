<?php
// src/Controller/AdministrationController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubTeacher;

use App\Service\ListData;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration", name="administration_")
 *
 * @IsGranted("ROLE_CA")
 */
class AdministrationController extends AbstractController
{
    /**
     * @Route("/index_statistique", name="statistics_index")
     * @return Response
     */
    public function statisticsIndex(): Response
    {
        $statistics = array();

        $provinces = new listData();

        foreach ($provinces->getProvince(0) as $province)
        {
            $statistics[$province]['Id'] = $province;

            for ($i = 1; $i <= 12; $i++)
            {
                $statistics[$province]['Limits'][$i] = array('Province' => $province, 'Sex' => is_int($i / 2) ? 2 : 1, 'Total' => 0);
            }

            $statistics[$province]['Total'][1] = 0;
            $statistics[$province]['Total'][2] = 0;
        }

        for ($i = 1; $i <= 12; $i++)
        {
            $total['Limits'][$i] = 0;
        }

        $total['Total'][1] = 0;
        $total['Total'][2] = 0;

        $date = new DateTime();

        $query = $this->getDoctrine()->getRepository(Club::class)->getProvinceMembersCount($date);

        $i = 1;

        foreach ($query as $limits)
        {
            foreach ($limits as $province)
            {
                if ($province['Sex'] == 1)
                {
                    $statistics[$province['Province']]['Total'][1] = $statistics[$province['Province']]['Total'][1] + $province['Total'];

                    $limit = ($i * 2) - 1;
                }
                else
                {
                    $statistics[$province['Province']]['Total'][2] = $statistics[$province['Province']]['Total'][2] + $province['Total'];

                    $limit = $i * 2;
                }

                $total['Limits'][$limit] = $total['Limits'][$limit] + $province['Total'];

                $statistics[$province['Province']]['Limits'][$limit] = $province;
            }

            $i++;
        }

        for ($i = 1; $i <= 12; $i++)
        {
            if (is_int($i / 2))
            {
                $total['Total'][2] = $total['Total'][2] + $total['Limits'][$i];
            }
            else
            {
                $total['Total'][1] = $total['Total'][1] + $total['Limits'][$i];
            }
        }

        return $this->render('Administration/Statistic/index.html.twig', array('statistics' => $statistics, 'total' => $total));
    }

    /**
     * @Route("/statistique_province/{province<\d+>}", name="statistics_province")
     * @param int $province
     * @return Response
     */
    public function statisticsProvince(int $province): Response
    {
        $statistics = array();

        $date = new DateTime();

        $query = $this->getDoctrine()->getRepository(Club::class)->getClubMembersCount($province, $date);

        foreach ($query['Clubs'] as $club)
        {
            $statistics[$club['Id']]['Club'] = $club;

            for ($i = 1; $i <= 12; $i++)
            {
                $statistics[$club['Id']]['Limits'][$i] = array('Id' => $club['Id'], 'Name' => $club['Name'], 'Sex' => is_int($i / 2) ? 2 : 1, 'Total' => 0);
            }

            $statistics[$club['Id']]['Total'][1] = 0;
            $statistics[$club['Id']]['Total'][2] = 0;
        }

        $i = 1;

        foreach ($query['Details'] as $limits)
        {
            foreach ($limits as $club)
            {
                if ($club['Sex'] == 1)
                {
                    $statistics[$club['Id']]['Total'][1] = $statistics[$club['Id']]['Total'][1] + $club['Total'];

                    $limit = ($i * 2) - 1;
                }
                else
                {
                    $statistics[$club['Id']]['Total'][2] = $statistics[$club['Id']]['Total'][2] + $club['Total'];

                    $limit = $i * 2;
                }

                $statistics[$club['Id']]['Limits'][$limit] = $club;
            }

            $i++;
        }

        return $this->render('Administration/Statistic/province_detail.html.twig', array('statistics' => $statistics));
    }

    /**
     * @Route("/liste-date-creation-club", name="creation_club_list")
     * @return Response
     */
    public function creationClubList(): Response
    {
        $list = $this->getDoctrine()->getRepository(Club::class)->getCreationDateList();

        return $this->render('Administration/Liste/club_creation_date.html.twig', array('list' => $list));
    }

    /**
     * @Route("/liste-anniversaire-pratique-dojo-cho", name="dojo_cho_starting_practice")
     * @return Response
     */
    public function dojoChoStartingPractice(): Response
    {
        $list = $this->getDoctrine()->getRepository(ClubTeacher::class)->getDojoChoStartingPractice();

        return $this->render('Administration/Liste/dojo_cho_starting_practice.html.twig', array('list' => $list));
    }
}