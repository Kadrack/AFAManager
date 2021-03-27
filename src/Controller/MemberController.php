<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Grade;
use App\Entity\MemberModification;

use App\Form\GradeType;
use App\Form\MemberType;

use App\Service\ClubTools;
use App\Service\Mailing;
use App\Service\MemberTools;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/membre", name="member_")
 *
 * @IsGranted("ROLE_MEMBER")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Member/index.html.twig');
    }

    /**
     * @Route("/mes_donnees", name="my_data")
     * @return Response
     */
    public function myData(MailerInterface $mailer)
    {
        $mail = new Mailing($mailer);

        $mail->sendEmail();

        return $this->render('Member/my_data.html.twig', array('member' => $this->getUser()->getUserMember()));
    }

    /**
     * @Route("/mes_donnees/modifier", name="my_data_update")
     * @param Request $request
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myDataUpdate(Request $request, MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        $form = $this->createForm(MemberType::class, $memberTools->getModification(), array('form' => 'my_data_update', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->setModification($form->getData(), $form['MemberModificationPhoto']->getData(), $form['MemberModificationCountry']->getData());

            return $this->render('Member/my_data.html.twig', array('member' => $memberTools->getMember()));
        }

        return $this->render('Member/my_data_update.html.twig', array('form' => $form->createView(), 'memberTools' => $memberTools));
    }

    /**
     * @Route("/mes_grades", name="my_grades")
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myGrades(MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_grades.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/ma_licence", name="my_licence")
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myLicence(MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_licence.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/mes_stages", name="my_stages")
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myStages(MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_stages.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/mes_titres", name="my_titles")
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myTitles(MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        return $this->render('Member/my_titles.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/ma_candidature/{type<\d+>}", name="my_application")
     * @param Request $request
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    public function myApplication(Request $request, MemberTools $memberTools)
    {
        $memberTools->setMember($this->getUser()->getUserMember());

        $form = $this->createForm(GradeType::class, $memberTools->getGrades()['exam']['grade'], array('form' => 'exam_application', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->application($form->getData());

            return $this->redirectToRoute('member_my_data');
        }

        return $this->render('Member/my_application.html.twig', array('form' => $form->createView(), 'exam' => $grade->getGradeExam()));
    }

    /**
     * @Route("/mon_club", name="my_club")
     * @param ClubTools $clubTools
     * @return Response
     */
    public function myClub(ClubTools $clubTools)
    {
        $clubTools->setClub($this->getUser()->getUserMember()->getMemberActualClub());

        return $this->render('Member/my_club.html.twig', array('clubTools' => $clubTools));
    }
}
