<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Grade;
use App\Entity\MemberModification;

use App\Form\GradeType;
use App\Form\MemberType;

use App\Service\ClubTools;
use App\Service\MemberTools;
use App\Service\PhotoUploader;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/membre", name="member_")
 *
 * @IsGranted("ROLE_MEMBER")
 */
class MemberController extends AbstractController
{
    private $passwordEncoder;

    /**
     * ClubController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

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
    public function myData()
    {
        $member = $this->getUser()->getUserMember();

        return $this->render('Member/my_data.html.twig', array('member' => $member));
    }

    /**
     * @Route("/mes_donnees/modifier", name="my_data_update")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @return Response
     */
    public function myDataUpdate(Request $request, PhotoUploader $photoUploader)
    {
        $member = $this->getUser()->getUserMember();

        if ($member->getMemberModification() == null)
        {
            $member_modification = new MemberModification();

            $member_modification->setMemberModificationId($member->getMemberId());
        }
        else
        {
            $member_modification = $member->getMemberModification();
        }

        $form = $this->createForm(MemberType::class, $member_modification, array('form' => 'my_data_update', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (($form['MemberModificationPhoto']->getData() != 'nophoto.png') && ($member_modification->getMemberModificationPhoto() !== null))
            {
                $member_modification->setMemberModificationPhoto($photoUploader->upload($form['MemberModificationPhoto']->getData(), $member_modification->getMemberModificationPhoto()));
            }

            $entityManager = $this->getDoctrine()->getManager();

            if ($member->getMemberModification() == null)
            {
                $member->setMemberModification($member_modification);

                $entityManager->persist($member_modification);
            }

            if ($form['MemberModificationCountry']->getData() == $member->getMemberCountry())
            {
                $member_modification->setMemberModificationCountry();
            }

            $entityManager->flush();

            return $this->render('Member/my_data.html.twig', array('member' => $member));
        }

        return $this->render('Member/my_data_update.html.twig', array('form' => $form->createView(), 'member' => $member));
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
        $member = $this->getUser()->getUserMember();

        $memberTools->setMember($member);

        return $this->render('Member/my_licence.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/mes_stages", name="my_stages")
     * @param MemberTools $memberTools
     * @return Response
     */
    public function myStages(MemberTools $memberTools)
    {
        $member = $this->getUser()->getUserMember();

        $memberTools->setMember($member);

        return $this->render('Member/my_stages.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/ma_candidature/{type<\d+>}", name="my_application")
     * @param Request $request
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    public function myApplication(Request $request, MemberTools $memberTools)
    {
        $member = $this->getUser()->getUserMember();

        $memberTools->setMember($member);

        $grade = $memberTools->getGrades()['exam']['grade'];

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_application', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

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
