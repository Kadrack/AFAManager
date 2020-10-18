<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\User;

use App\Form\UserType;

use App\Service\UserTools;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("", name="common_")
 *
 * @IsGranted("ROLE_USER")
 */
class CommonController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Common/index.html.twig');
    }

    /**
     * @Route("/changement_login", name="change_login")
     * @param SessionInterface $session
     * @param Request $request
     * @param UserTools $userTools
     * @return Response
     */
    public function changeLogin(SessionInterface $session, Request $request, UserTools $userTools)
    {
        $session->set('duplicate', false);

        $form = $this->createForm(UserType::class, $this->getUser(), array('form' => 'change_login', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changeLogin($form->getData(), $form['Login']->getData()))
            {
                return $this->redirectToRoute('common_index');
            }
            else
            {
                $session->set('duplicate', true);

                return $this->render('Common/change_login.html.twig', array('form' => $form->createView()));
            }

        }

        return $this->render('Common/change_login.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/changement_mot_de_passe", name="change_password")
     * @param SessionInterface $session
     * @param Request $request
     * @param UserTools $userTools
     * @return Response
     */
    public function changePassword(SessionInterface $session, Request $request, UserTools $userTools)
    {
        $session->set('passwordError', false);

        $form = $this->createForm(UserType::class, $this->getUser(), array('form' => 'change_password', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changePassword($form->getData(), $form['Password1']->getData(), $form['Password2']->getData()))
            {
                return $this->redirectToRoute('common_index');
            }
            else
            {
                $session->set('passwordError', true);

                return $this->render('Common/change_password.html.twig', array('form' => $form->createView()));
            }
        }

        return $this->render('Common/change_password.html.twig', array('form' => $form->createView()));
    }
}
