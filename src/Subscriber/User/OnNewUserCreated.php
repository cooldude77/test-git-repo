<?php

namespace App\Subscriber\User;

use App\Event\User\UserCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class OnNewUserCreated implements EventSubscriberInterface
{

    public function __construct(private MailerInterface $mailer, private LoggerInterface $logger)
    {


    }

    public static function getSubscribedEvents(): array
    {
        return [UserCreatedEvent::USER_CREATED, ['sendEmail', 100]];
    }

    /** @noinspection PhpUnused */
    public function sendEmail(): void
    {
        $email = (new Email())
            ->from('sign_up@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->critical($e);
        }
    }
}