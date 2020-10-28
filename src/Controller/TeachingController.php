<?php
// src/Controller/AdministrationController.php
namespace App\Controller;

use App\Entity\Club;

use App\Service\ListData;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/enseignement", name="teaching_")
 *
 * @IsGranted("ROLE_CP")
 */
class TeachingController extends AbstractController
{
    /**
     * @Route("/index_statistique", name="statistics_index")
     * @return Response
     */
    public function statisticsIndex()
    {
        $statistics = array();

        $provinces = new listData();

        foreach ($provinces->getProvince(0) as $province)
        {
            $statistics[$province]['Id'] = $province;

            $statistics[$province]['Total'][1] = 0;
            $statistics[$province]['Total'][2] = 0;
            $statistics[$province]['Total'][3] = 0;
        }

        $query = $this->getDoctrine()->getRepository(Club::class)->getProvinceMembersTotal();

        foreach ($query as $province)
        {
            if ($province['Sex'] == 1)
            {
                $statistics[$province['Province']]['Total'][1] = $statistics[$province['Province']]['Total'][1] + $province['Total'];
            }
            else
            {
                $statistics[$province['Province']]['Total'][2] = $statistics[$province['Province']]['Total'][2] + $province['Total'];
            }
        }

        $query = $this->getDoctrine()->getRepository(Club::class)->getProvinceTeachersTotal();

        foreach ($query as $province)
        {
            $statistics[$province['Province']]['Total'][3] = $statistics[$province['Province']]['Total'][3] + $province['Total'];
        }

        return $this->render('Teaching/Statistic/index.html.twig', array('statistics' => $statistics));
    }
}