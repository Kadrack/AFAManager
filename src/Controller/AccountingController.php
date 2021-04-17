<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\Member;

use App\Form\AccountingType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AccountingController
 * @package App\Controller
 *
 * @IsGranted("ROLE_BANK")
 */
#[Route('/comptabilite', name:'accounting-')]
class AccountingController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/rechercher-membres', name:'searchMembers')]
    public function searchMembers(Request $request): Response
    {
        $form = $this->createForm(AccountingType::class, null, array('form' => 'searchMembers', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchMembers($form->get('Search')->getData());

            return $this->render('Accounting/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
        }

        return $this->render('Accounting/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results ?? null));
    }

    /**
     * @param Member $member
     * @return Response
     */
    #[Route('/donnees-contact/{member<\d+>}', name:'memberContactData')]
    public function memberContactData(Member $member): Response
    {
        return $this->render('Accounting/Member/contact_data.html.twig', array('member' => $member));
    }
}
