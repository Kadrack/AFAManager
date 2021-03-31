<?php
// src/Service/Mailing.php
namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mime\Email;

/**
 * Class Tools
 * @package App\Service
 */
class Mailing
{
    private MailerInterface $mailer;

    /**
     * ClubTools constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return bool
     * @throws TransportExceptionInterface
     */
    public function sendEmail(): bool
    {
        $email = (new Email())
            ->from('afa-manager@aikido.be')
            ->to('frederic.buchon@aikido.be')
            //->cc('')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Test Symfony Mailer!')
            ->text('Voilà un mail envoyé avec Symfony')
            ->html('<p>Voilà un mail envoyé avec Symfony</p>');

        $this->mailer->send($email);

        return true;
    }
}
