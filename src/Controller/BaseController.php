<?php

namespace App\Controller;

use App\DI\Container;
use App\Model\Database;
use App\View\TemplateRenderer;

abstract class BaseController
{
    // Společný základ controllerů: DI, DB, šablony a základní webové utility.
    protected Container $di;
    protected Database $database;

    protected TemplateRenderer $template;

    public function __construct(Container $container)
    {
        $this->di = $container;
        $this->database = $this->di->getService('database');
        $this->template = new TemplateRenderer('template/');

        $this->template->setParams(
            [
                'isUserLoggedIn' => $this->isUserLoggedIn(),
                'controller' => $this,
            ]
        );
    }

    public function isUserLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    protected function requireLogin(string $redirectTo = 'prihlaseni'): void
    {
        if ($this->isUserLoggedIn()) {
            return;
        }

        $this->addFlashMessage('Nejprve se přihlaste.', 'warning');
        $this->redirect($redirectTo);
    }

    protected function redirect(string $target = "", array $params = []): void
    {
        $baseUrl = $this->di->getConfig('app.base_url');

        // http://localhost?first=1&second=2&third=true.......
        $qs = "";

        if (!empty($params)) {
            $separator = str_contains($target, '?') ? '&' : '?';
            $qs = $separator . http_build_query($params);
        }

        $url = $baseUrl . $target . $qs;

        header('Location: ' . $url);

        exit;
    }

    protected function addFlashMessage(string $message, string $type = 'success'): void
    {
        $_SESSION["flashMessage"][$type][] = $message;
    }

    protected function getFlashMessages(): array
    {
        $messages = $_SESSION['flashMessage'] ?? [];
        $_SESSION['flashMessage'] = [];

        return $messages;
    }
}
