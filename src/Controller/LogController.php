<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use App\Service\PasswordHasher;

class LogController extends BaseController
{
    // Controller zajišťuje přihlášení/registraci; práci s DB deleguje na repository.
    public function in(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('messenger');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            if (empty($data['username']) || empty($data['password'])) {
                $this->addFlashMessage('Vyplňte jméno a heslo', 'error');
                $this->redirect("prihlaseni");
            }

            $username = $data['username'];
            $password = $data['password'];

            $userRepository = $this->di->createUserRepository();
            $user = $userRepository->findByUsername($username);

            if (!$user) {
                $this->addFlashMessage('Chybné přihlašovací údaje', 'error');
                $this->redirect("prihlaseni");
            }

            $passwordService = new PasswordHasher();
            if (!$passwordService->verify($password, $user->getPassword())) {
                $this->addFlashMessage('Chybné přihlašovací údaje', 'error');
                $this->redirect("prihlaseni");
            }

            // prihlasime uzivatele
            $_SESSION['user_id'] = $user->getUuid()->toString();
            $this->addFlashMessage('Přihlášení proběhlo úspěšně');
            $this->redirect('messenger');
        } else {
            $this->template->render(
                'Log/in.phtml',
                [
                    'flashMessages' => $this->getFlashMessages(),
                ],
                null,
            );
        }
    }

    public function out(): void
    {
        $_SESSION['user_id'] = null;

        $this->addFlashMessage('Odhlášeno', 'info');
        $this->redirect('prihlaseni');
    }

    public function register(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('messenger');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;

            $username = trim((string)($data['username'] ?? ''));
            $email = trim((string)($data['email'] ?? ''));
            $firstName = trim((string)($data['first_name'] ?? ''));
            $password = (string)($data['password'] ?? '');
            $passwordConfirm = (string)($data['password_confirm'] ?? '');

            if (
                $username === '' ||
                $email === '' ||
                $firstName === '' ||
                $password === '' ||
                $passwordConfirm === ''
            ) {
                $this->addFlashMessage('Vyplňte všechna povinná pole.', 'error');
                $this->redirect('registrace');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->addFlashMessage('Neplatný e-mail.', 'error');
                $this->redirect('registrace');
            }

            if (strlen($password) < 6) {
                $this->addFlashMessage('Heslo musí mít alespoň 6 znaků.', 'error');
                $this->redirect('registrace');
            }

            if ($password !== $passwordConfirm) {
                $this->addFlashMessage('Hesla se neshodují.', 'error');
                $this->redirect('registrace');
            }

            $userRepository = $this->di->createUserRepository();

            if ($userRepository->findByUsername($username)) {
                $this->addFlashMessage('Uživatelské jméno je již obsazené.', 'error');
                $this->redirect('registrace');
            }

            if ($userRepository->findByEmail($email)) {
                $this->addFlashMessage('E-mail je již použitý.', 'error');
                $this->redirect('registrace');
            }

            $user = new User($username, $email, $password, $firstName);
            $userRepository->save($user);

            $this->addFlashMessage('Registrace proběhla úspěšně. Nyní se přihlaste.', 'success');
            $this->redirect('prihlaseni');
        }

        $this->template->render(
            'Log/register.phtml',
            [
                'flashMessages' => $this->getFlashMessages(),
            ],
            null,
        );
    }
}
