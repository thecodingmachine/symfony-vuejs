<?php

declare(strict_types=1);

namespace App\Infrastructure\Task;

final class SendEmailTask implements AsyncTask
{
    private string $domain;
    private string $locale;
    private string $to;
    private string $template;
    /** @var mixed[] */
    private array $templateData;

    /**
     * @param mixed[] $templateData
     */
    public function __construct(
        string $domain,
        string $locale,
        string $to,
        string $template,
        array $templateData
    ) {
        $this->domain       = $domain;
        $this->locale       = $locale;
        $this->to           = $to;
        $this->template     = $template;
        $this->templateData = $templateData;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return mixed[]
     */
    public function getTemplateData(): array
    {
        return $this->templateData;
    }
}
