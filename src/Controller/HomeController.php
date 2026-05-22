<?php

namespace App\Controller;

use App\DI\Container;
use App\Enum\GenderEnum;
use App\Model\Database;
use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Service\PasswordHasher;
use DateTime;
use PDO;
use Tracy\Debugger;

class HomeController extends BaseController
{
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
