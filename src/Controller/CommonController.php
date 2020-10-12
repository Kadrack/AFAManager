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

use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class CommonController extends AbstractController
{
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
     * @param UserTools $userTools
     * @return Response
     */
    public function myAccess(Request $request, UserTools $userTools)
    {
        $form = $this->createForm(UserType::class, $this->getUser(), array('form' => 'my_access', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->changePassword($form->getData(), $form['Password']->getData());

            return $this->redirectToRoute('common_index');
        }

        return $this->render('Common/my_access.html.twig', array('form' => $form->createView()));
    }

}
