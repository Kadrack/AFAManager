<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\Mail;
use App\Entity\User;

use App\Form\MailType;
use App\Form\UserType;

use App\Service\UserTools;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mime\Email;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('', name:'common-')]
class CommonController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name:'index')]
    public function index(): Response
    {
        return $this->render('Common/index.html.twig');
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/changement-login', name:'changeLogin')]
    public function changeLogin(SessionInterface $session, Request $request, UserTools $userTools): RedirectResponse|Response
    {
        $session->set('duplicate', false);

        $form = $this->createForm(UserType::class, $this->getUser(), array('form' => 'changeLogin', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changeLogin($form->getData(), $form['Login']->getData()))
            {
                return $this->redirectToRoute('common-index');
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
     * @param SessionInterface $session
     * @param Request $request
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/changement-mot-de-passe', name:'changePassword')]
    public function changePassword(SessionInterface $session, Request $request, UserTools $userTools): RedirectResponse|Response
    {
        $session->set('passwordError', false);

        $form = $this->createForm(UserType::class, $this->getUser(), array('form' => 'changePassword', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changePassword($form->getData(), $form['Password1']->getData(), $form['Password2']->getData()))
            {
                return $this->redirectToRoute('common-index');
            }
            else
            {
                $session->set('passwordError', true);

                return $this->render('Common/change_password.html.twig', array('form' => $form->createView()));
            }
        }

        return $this->render('Common/change_password.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param \Symfony\Component\Mailer\MailerInterface $mailer
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/creer-email', name:'createMail')]
    public function createMail(MailerInterface $mailer, Request $request): Response
    {
        $email = new Mail();

        $form = $this->createForm(MailType::class, $email);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $mail = new Email();

            !$email->getMailPriority() ?: $mail->priority(Email::PRIORITY_HIGH);

            $mail->from('webmaster@aikido.be');
            $mail->replyTo('webmaster@aikido.be');
            $mail->to($email->getMailTo());

            is_null($email->getMailCc()) ?: $mail->cc($email->getMailCc());
            is_null($email->getMailBcc()) ?: $mail->bcc($email->getMailBcc());

            $mail->subject($email->getMailTitle());
            $mail->html($email->getMailBody());
            $mail->text($email->getMailBody());

            $mailer->send($mail);

            return $this->render('Common/Mail/create.html.twig', array('form' => $form->createView()));
        }

        return $this->render('Common/Mail/create.html.twig', array('form' => $form->createView()));
    }
}
