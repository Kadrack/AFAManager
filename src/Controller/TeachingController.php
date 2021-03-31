<?php
// src/Controller/TeachingController.php
namespace App\Controller;

use App\Entity\Club;

use App\Service\ListData;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeachingController
 * @package App\Controller
 *
 * @IsGranted("ROLE_CP")
 */
#[Route('/enseignement', name:'teaching-')]
class TeachingController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/index-statistique', name:'statisticsIndex')]
    public function statisticsIndex(): Response
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

        $date = new DateTime('today');

        $query = $this->getDoctrine()->getRepository(Club::class)->getProvinceMembersTotal($date);

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

        $date = new DateTime('today');

        $query = $this->getDoctrine()->getRepository(Club::class)->getProvinceTeachersTotal($date);

        foreach ($query as $province)
        {
            $statistics[$province['Province']]['Total'][3] = $statistics[$province['Province']]['Total'][3] + $province['Total'];
        }

        return $this->render('Teaching/Statistic/index.html.twig', array('statistics' => $statistics));
    }
}