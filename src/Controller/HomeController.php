<?php

namespace App\Controller;

class HomeController extends BaseController
{
    // Prezentační controller statických stránek (bez business logiky).
    public function index(): void
    {
        $this->template->render(
            'Home/index.phtml',
            ['activePage' => 'home']
        );
    }

    public function about(): void
    {
        $this->template->render(
            'Home/about.phtml',
            ['activePage' => 'about']
        );
    }

    public function developer(): void
    {
        $this->template->render(
            'Home/developer.phtml',
            ['activePage' => 'developer']
        );
    }

    public function contact(): void
    {
        $this->template->render(
            'Home/contact.phtml',
            ['activePage' => 'contact']
        );
    }
}
