<?php

namespace App\Service;

use App\Entity\Contact;
use Twig\Environment;

class Mailer
{
    protected $mailer;
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendMail(Contact $contact)
    {
        $email = (new \Swift_Message('Contact de zyno.fr'))
              ->setFrom('contact@zyno.fr')
              ->setTo('mura.loic@orange.fr')
              ->setReplyTo($contact->getEmail())
              ->setSubject('Contact de zyno.fr')
              ->setBody($this->twig->render('email/contact.html.twig', [
                  'contact' => $contact
              ]), 'text/html');

        $this->mailer->send($email);
    }
}