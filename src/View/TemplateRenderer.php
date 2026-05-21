<?php

namespace App\View;

class TemplateRenderer
{
    // Jednoduchý renderer: složí data + šablonu + layout do výsledného HTML.
    public function __construct(
        private string $templatePath,
        protected array $globalParams = []
    ) {
    }

    public function render(
        string $template,
        array $params = [],
        string|null $layout = "layout.phtml",
    ): void
    {
        // zpristupnit promenne v sablone
        extract($this->globalParams);
        extract($params);

        if ($layout) {
            ob_start();
            include $this->templatePath . $template;
            $templateActionContent = ob_get_clean();

            include $this->templatePath . $layout;
        } else {
            include $this->templatePath . $template;
        }
    }

    public function setParams(array $params): void
    {
        $this->globalParams = array_merge($this->globalParams, $params);
    }
}
