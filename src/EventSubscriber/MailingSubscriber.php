<?php

namespace App\EventSubscriber;

use App\Entity\User;
use APP\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{

    public function __construct(private MailerInterface $mailer) {
        
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;
        $mail = (new TemplatedEmail())
            ->to($data->service)
            ->from($data->email)
            ->htmlTemplate('emails/contact.html.twig')
            ->subject('demande de contat')
            ->context(['data' => $data]);
            $this->mailer->send($mail);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if(!$user instanceof User) {
            return;
        }
        $mail = (new Email())
            ->to($user->getEmail())
            ->subject('Login')
            ->text('Hello ' . $user->getUsername() . ' welcome to our website');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onLogin'
        ];
    }
}
