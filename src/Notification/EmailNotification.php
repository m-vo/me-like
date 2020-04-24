<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\MeLike\Notification;

use Mvo\MeLike\Endpoint\EndpointRegistry;
use Mvo\MeLike\Entity\Like;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailNotification
{
    private MailerInterface $mailer;
    private EndpointRegistry $registry;
    private TranslatorInterface $translator;

    private ?string $emailSubject;
    private ?string $emailFrom;

    public function __construct(MailerInterface $mailer, EndpointRegistry $registry, TranslatorInterface $translator, ?string $emailFrom)
    {
        $this->mailer = $mailer;
        $this->registry = $registry;
        $this->translator = $translator;

        $this->emailFrom = $emailFrom;
    }

    public function send(string $emailAddress, Like $like, string $confirmUrl): void
    {
        $additionalContext = $this->registry->getContext($like);

        $context = [
            'like' => $like,
            'url' => $confirmUrl,
            'data' => $additionalContext,
        ];

        $subject = $this->translator->trans(
            'email.subject',
            $additionalContext['email_subject'] ?? [],
            'MvoMeLikeBundle'
        );

        $email = (new TemplatedEmail())
            ->htmlTemplate('@MvoMeLike/Email/confirm_like.html.twig')
            ->from($this->emailFrom ?? 'mail@example.com')
            ->to($emailAddress)
            ->subject($subject)
            ->context($context);

        $this->mailer->send($email);
    }
}
