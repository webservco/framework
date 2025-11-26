<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use Throwable;
use WebServCo\Framework\AbstractLibrary;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\OutputInterface;

use function is_file;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;
use function sprintf;

final class HtmlOutput extends AbstractLibrary implements
    OutputInterface
{
    private string $path;
    private string $template;

    public function setPath(string $path): bool
    {
        $this->path = $path;

        return true;
    }

    public function setTemplate(string $template): bool
    {
        $this->template = $template;

        return true;
    }

    public function render(): string
    {
        ob_start();
        try {
            $templatePath = "{$this->path}{$this->template}.php";
            if (!is_file($templatePath)) {
                throw new ApplicationException(
                    sprintf('Template file not found: %s.', $templatePath),
                );
            }
            include $templatePath;
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return (string) $output;
    }
}
