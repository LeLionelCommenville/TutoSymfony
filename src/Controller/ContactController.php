<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        
            if($form->isSubmitted() && $form->isValid()) {
                try {    
                    $mail = (new TemplatedEmail())
                        ->to($data->service)
                        ->from($data->email)
                        ->htmlTemplate('emails/contact.html.twig')
                        ->subject('demande de contat')
                        ->context(['data' => $data]);
                    $mailer->send($mail);
                } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre email');
            } 
            $this->addFlash('succes', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
