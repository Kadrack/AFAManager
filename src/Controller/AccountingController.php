<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\Member;

use App\Entity\MemberLicence;
use App\Entity\MemberPrintout;
use App\Form\AccountingType;

use DateTime;

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

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response
     * @throws \Exception
     */
    #[Route('/validation-paiement-licence', name:'paymentLicenceValidation')]
    public function paymentLicenceValidation(Request $request): Response
    {
        $form = $this->createForm(AccountingType::class, new MemberLicence(), array('form' => 'paymentLicenceValidation', 'data_class' => MemberLicence::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && is_null($this->getDoctrine()->getRepository(MemberLicence::class)->findOneBy(['member_licence' => $form->get('LicenceNumber')->getData(), 'member_licence_status' => array(2, 3)])))
        {
            $members = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => explode(',', $form->get('LicenceNumber')->getData())]);

            $isNew = false;

            foreach ($members as $member)
            {
                $entityManager = $this->getDoctrine()->getManager();

                $memberLicence = $this->getDoctrine()->getRepository(MemberLicence::class)->findOneBy(['member_licence' => $member->getMemberId(), 'member_licence_status' => 2]);

                if (is_null($memberLicence))
                {
                    $isNew = true;

                    $memberLicence = new MemberLicence();

                    $memberLicence->setMemberLicence($member);
                    $memberLicence->setMemberLicenceClub($member->getMemberActualClub());
                    $memberLicence->setMemberLicenceDeadline(new DateTime('+1 year ' . $member->getMemberLastLicence()->getMemberLicenceDeadline()->format('Y-m-d')));
                }
                else
                {
                    $memberLicence->setMemberLicenceStatus(1);

                    $oldLicence = $member->getMemberLastLicence();

                    $oldLicence->setMemberLicenceStatus(0);

                    $member->setMemberLastLicence($memberLicence);
                    $member->setMemberActualClub($memberLicence->getMemberLicenceClub());

                    $stamp = new MemberPrintout();

                    $stamp->setMemberPrintoutLicence($memberLicence);
                    $stamp->setMemberPrintoutCreation(new DateTime(('today')));

                    $entityManager->persist($stamp);
                }

                $memberLicence->setMemberLicenceUpdate(new DateTime('today'));
                $memberLicence->setMemberLicenceStatus($isNew ? 2 : 1);
                $memberLicence->setMemberLicencePaymentDate($form->get('MemberLicencePaymentDate')->getData());
                $memberLicence->setMemberLicencePaymentValue($form->get('MemberLicencePaymentValue')->getData());

                !$isNew ?: $entityManager->persist($memberLicence);

                $entityManager->flush();
            }
        }

        $list = $this->getDoctrine()->getRepository(Member::class)->getAwaitingFormValidationMemberList();

        return $this->render('Accounting/Licence/licencePayment.html.twig', array('form' => $form->createView(), 'list' => $list));
    }

    /**
     * @param Request $request
     * @param MemberLicence $memberLicence
     * @return Response
     * @throws \Exception
     */
    #[Route('/validation-paiement-licence/{memberLicence<\d+>}', name:'paymentLicenceValidationUpdate')]
    public function paymentLicenceValidationUpdate(Request $request, MemberLicence $memberLicence): Response
    {
        if ($memberLicence->getMemberLicenceStatus() < 2)
        {
            return $this->redirectToRoute('accounting-paymentLicenceValidation');
        }

        $form = $this->createForm(AccountingType::class, $memberLicence, array('form' => 'paymentLicenceValidation', 'data_class' => MemberLicence::class));

        $form->get('LicenceNumber')->setData($memberLicence->getMemberLicence()->getMemberId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('LicenceNumber')->getData()]);

            $memberLicence->setMemberLicence($member);
            $memberLicence->setMemberLicenceStatus(2);
            $memberLicence->setMemberLicenceUpdate(new DateTime('today'));
            $memberLicence->setMemberLicenceClub($member->getMemberActualClub());
            $memberLicence->setMemberLicencePaymentDate($form->get('MemberLicencePaymentDate')->getData());
            $memberLicence->setMemberLicencePaymentValue($form->get('MemberLicencePaymentValue')->getData());
            $memberLicence->setMemberLicenceDeadline(new DateTime('+1 year ' . $member->getMemberLastLicence()->getMemberLicenceDeadline()->format('Y-m-d')));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('accounting-paymentLicenceValidation');
        }

        return $this->render('Accounting/Licence/licencePaymentUpdate.html.twig', array('form' => $form->createView()));
    }
}
