<?php

declare(strict_types=1);

namespace App\Infrastructure\Task;

final class SendEmailTask implements AsyncTask
{
    private string $to;
    private string $subject;
    private string $template;
    /** @var mixed[] */
    private array $templateData;

    /**
     * @param mixed[] $templateData
     */
    public function __construct(string $to, string $subject, string $template, array $templateData)
    {
        $this->to           = $to;
        $this->subject      = $subject;
        $this->template     = $template;
        $this->templateData = $templateData;
    }

    public function getTo() : string
    {
        return $this->to;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    /**
     * @return mixed[]
     */
    public function getTemplateData() : array
    {
        return $this->templateData;
    }
}
