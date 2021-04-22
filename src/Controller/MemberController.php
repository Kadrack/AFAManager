<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Grade;
use App\Entity\MemberModification;

use App\Form\GradeType;
use App\Form\MemberType;

use App\Service\ClubTools;
use App\Service\MemberTools;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MemberController
 * @package App\Controller
 *
 * @IsGranted("ROLE_MEMBER")
 */
#[Route('/membre', name:'member-')]
class MemberController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name:'index')]
    public function index(): Response
    {
        return $this->render('Member/index.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/tout', name:'allData')]
    public function allData(MemberTools $memberTools, ClubTools $clubTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());
        $clubTools->setClub($this->getUser()->getUserMember()->getMemberActualClub());

        return $this->render('Member/all_data.html.twig', array('member' => $this->getUser()->getUserMember(), 'memberTools' => $memberTools, 'clubTools' => $clubTools));
    }

    /**
     * @return Response
     */
    #[Route('/mes-donnees', name:'myData')]
    public function myData(): Response
    {
        return $this->render('Member/my_data.html.twig', array('member' => $this->getUser()->getUserMember()));
    }

    /**
     * @param Request $request
     * @param MemberTools $memberTools
     * @return Response
     */
    #[Route('/mes-donnees/modifier', name:'myDataUpdate')]
    public function myDataUpdate(Request $request, MemberTools $memberTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        $form = $this->createForm(MemberType::class, $memberTools->getModification(), array('form' => 'myDataUpdate', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->setModification($form->getData(), $form['MemberModificationPhoto']->getData(), $form['MemberModificationCountry']->getData());

            return $this->render('Member/my_data.html.twig', array('member' => $memberTools->getMember()));
        }

        return $this->render('Member/my_data_update.html.twig', array('form' => $form->createView(), 'memberTools' => $memberTools));
    }

    /**
     * @param MemberTools $memberTools
     * @return Response
     */
    #[Route('/mes-grades', name:'myGrades')]
    public function myGrades(MemberTools $memberTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_grades.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param MemberTools $memberTools
     * @return Response
     */
    #[Route('/ma-licence', name:'myLicence')]
    public function myLicence(MemberTools $memberTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_licence.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param MemberTools $memberTools
     * @return Response
     */
    #[Route('/mes-stages', name:'myStages')]
    public function myStages(MemberTools $memberTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_stages.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param MemberTools $memberTools
     * @return Response
     */
    #[Route('/mes-titres', name:'myTitles')]
    public function myTitles(MemberTools $memberTools): Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_titles.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param Request $request
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/ma-candidature/{type<\d+>}', name:'myApplication')]
    public function myApplication(Request $request, MemberTools $memberTools): RedirectResponse|Response
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        $form = $this->createForm(GradeType::class, $memberTools->getGrades()['exam']['grade'], array('form' => 'examApplication', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->application($form->getData());

            return $this->redirectToRoute('member-myData');
        }

        return $this->render('Member/my_application.html.twig', array('form' => $form->createView(), 'exam' => $grade->getGradeExam()));
    }

    /**
     * @param ClubTools $clubTools
     * @return Response
     */
    #[Route('/mon-club', name:'myClub')]
    public function myClub(ClubTools $clubTools): Response
    {
        $clubTools->setClub($this->getUser()->getUserMember()->getMemberActualClub());

        return $this->render('Member/my_club.html.twig', array('clubTools' => $clubTools));
    }
}
