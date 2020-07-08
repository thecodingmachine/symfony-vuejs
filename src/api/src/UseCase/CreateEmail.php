<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Domain\Model\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

use function strval;

class CreateEmail
{
    private TranslatorInterface $translator;
    protected ParameterBagInterface $parameters;

    public function __construct(
        TranslatorInterface $translator,
        ParameterBagInterface $parameters
    ) {
        $this->translator = $translator;
        $this->parameters = $parameters;
    }

    /**
     * @param array<string,string> $context
     */
    protected function create(User $user, string $subjectId, string $template, array $context): TemplatedEmail
    {
        $context['domain'] = 'emails';
        $context['locale'] = strval($user->getLocale());

        $translatedSubject = $this->translator
            ->trans(
                $subjectId,
                [],
                $context['domain'],
                $context['locale']
            );

        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName()))
            ->from(new Address($this->parameters->get('app.mail_from_address'), $this->parameters->get('app.mail_from_name')))
            ->subject($translatedSubject)
            ->htmlTemplate($template)
            ->context($context);

        // This header tells auto-repliers ("email holiday mode") to not
        // reply to this message because it's an automated email.
        $email->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

        return $email;
    }
}
