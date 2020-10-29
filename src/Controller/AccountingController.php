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
 * @Route("/comptabilite", name="accounting_")
 *
 * @IsGranted("ROLE_BANK")
 */
class AccountingController extends AbstractController
{
    /**
     * @Route("/rechercher_membres", name="search_members")
     * @param Request $request
     * @return Response
     */
    public function searchMembers(Request $request)
    {
        $form = $this->createForm(AccountingType::class, null, array('form' => 'search_members', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchMembers($form->get('Search')->getData());

            return $this->render('Accounting/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
        }

        return $this->render('Accounting/Member/search.html.twig', array('form' => $form->createView(), 'results' => isset($results) ? $results : null));
    }

    /**
     * @Route("/donnees_contact/{member<\d+>}", name="member_contact_data")
     * @param Member $member
     * @return Response
     */
    public function memberContactData(Member $member)
    {
        return $this->render('Accounting/Member/contact_data.html.twig', array('member' => $member));
    }
}
