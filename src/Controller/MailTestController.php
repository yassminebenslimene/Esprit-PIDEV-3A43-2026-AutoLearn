<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailTestController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function testMail(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('autolearn66@gmail.com')
                ->to('autolearn66@gmail.com') // Send to yourself
                ->subject('Test Email from AutoLearn')
                ->text('This is a test email to check if mailer works.')
                ->html('<h1>Test Email</h1><p>This is a test email to check if mailer works.</p>');

            $mailer->send($email);
            
            return new Response('Email sent successfully! Check your inbox/spam.');
            
        } catch (\Exception $e) {
            return new Response('Error: ' . $e->getMessage());
        }
    }
}