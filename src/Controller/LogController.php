<?php

declare(strict_types=1);

namespace App\Controller;

use App\DI\Container;
use App\Model\Repository\UserRepository;
use App\Service\PasswordHasher;
use Tracy\Debugger;

class LogController extends BaseController
{
    public function in()
    {
        // Heslo je "heslo"

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            if (empty($data['username']) || empty($data['password'])) {
                $this->addFlashMessage('Vyplňte jméno a heslo', 'error');
                $this->redirect("prihlaseni");

                exit();
            }

            $username = $data['username'];
            $password = $data['password'];

            $userRepository = new UserRepository($this->database);
            $user = $userRepository->findByUsername($username);

            if (!$user) {
                $this->addFlashMessage('Chybné přihlašovací údaje', 'error');
                $this->redirect("prihlaseni");

                exit();
            }

            $passwordService = new PasswordHasher();
            if (!$passwordService->verify($password, $user->getPassword())) {
                $this->addFlashMessage('Chybné přihlašovací údaje', 'error');
                $this->redirect("prihlaseni");

                exit();
            }

            // prihlasime uzivatele
            $_SESSION['user_id'] = $user->getUuid()->toString();
            $this->addFlashMessage('Přihlášení proběhlo úspěšně');
            $this->redirect();
        } else {
            $this->template->render(
                'Log/in.phtml',
                [
                    'flashMessages' => $this->getFlashMessages(),
                ],
                null,
            );

//            $html = <<<HTML
//<html lang="cs">
//        <head></head>
//        <body>
//            <form method="post">
//                <input type="text" name="username" placeholder="Uživatelské jméno">
//                <input type="password" name="password" placeholder="Heslo">
//                <input type="submit" name="login_submitted" value="Přihlásit">
//            </form>
//        </body>
//</html>
//
//HTML;

//            echo $html;
        }
    }

    public function out()
    {
        session_destroy();

        $this->addFlashMessage('Odhlášeno', 'info');
        $this->redirect();
    }

    public function register()
    {
        // registrace
    }
}
