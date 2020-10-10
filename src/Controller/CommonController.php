<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\User;

use App\Entity\UserAuditTrail;
use App\Form\UserType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class CommonController extends AbstractController
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
     * @Route("/", name="common_index")
     */
    public function index()
    {
        return $this->render('Common/index.html.twig');
    }

    /**
     * @Route("/mon_acces", name="my_access")
     * @param Request $request
     * @return Response
     */
    public function myAccess(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user, array('form' => 'my_access', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form['Password']->getData()));

            $auditTrail = new UserAuditTrail();

            $auditTrail->setUserAuditTrailAction(4);
            $auditTrail->setUserAuditTrailUser($user);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($auditTrail);
            $entityManager->flush();

            return $this->redirectToRoute('common_index');
        }

        return $this->render('Common/my_access.html.twig', array('form' => $form->createView()));
    }

}
