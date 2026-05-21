<?php

declare(strict_types=1);

namespace App\Controller;

class MessengerController extends BaseController
{
    // Controller koordinuje messenger use-cases; perzistenci nechává repository vrstvám.
    public function index(?string $conversationId = null): void
    {
        $this->requireLogin();

        $currentUserId = (string)$_SESSION['user_id'];

        $conversationRepository = $this->di->createConversationRepository();
        $messageRepository = $this->di->createMessageRepository();
        $userRepository = $this->di->createUserRepository();

        $conversations = $conversationRepository->findAllByUser($currentUserId);
        $contacts = $userRepository->findAllExcept($currentUserId);

        $selectedConversationId = $conversationId ?? (string)($_GET['c'] ?? '');
        if ($selectedConversationId === '' && !empty($conversations)) {
            $selectedConversationId = $conversations[0]->getUuid()->toString();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedConversationId !== '') {
            $messageText = trim((string)($_POST['message'] ?? ''));

            if ($messageText !== '') {
                $messageRepository->create($selectedConversationId, $currentUserId, $messageText);
            }

            $this->redirect('messenger/' . $selectedConversationId);
        }

        $allowedConversationIds = array_map(
            static fn($conversation) => $conversation->getUuid()->toString(),
            $conversations,
        );

        if ($selectedConversationId !== '' && !in_array($selectedConversationId, $allowedConversationIds, true)) {
            http_response_code(403);
            $this->template->render('Error/403.phtml', [], null);

            return;
        }

        $messages = [];
        if ($selectedConversationId !== '') {
            $messages = $messageRepository->findAllByConversation($selectedConversationId, 200);
        }

        $currentUser = $userRepository->findById($currentUserId);

        $this->template->render(
            'Messenger/index.phtml',
            [
                'flashMessages' => $this->getFlashMessages(),
                'currentUser' => $currentUser,
                'conversations' => $conversations,
                'contacts' => $contacts,
                'messages' => $messages,
                'selectedConversationId' => $selectedConversationId,
            ],
            null,
        );
    }

    public function createConversation(string $userId): void
    {
        $this->requireLogin();

        $currentUserId = (string)$_SESSION['user_id'];

        if ($currentUserId === $userId) {
            $this->addFlashMessage('Nemůžete založit konverzaci sami se sebou.', 'warning');
            $this->redirect('messenger');
        }

        $userRepository = $this->di->createUserRepository();
        $conversationRepository = $this->di->createConversationRepository();

        $targetUser = $userRepository->findById($userId);
        if (!$targetUser) {
            $this->addFlashMessage('Uživatel neexistuje.', 'error');
            $this->redirect('messenger');
        }

        $conversation = $conversationRepository->findDirectConversationBetweenUsers($currentUserId, $userId);
        if (!$conversation) {
            $conversation = $conversationRepository->createDirectConversation(
                $currentUserId,
                $userId,
                'Chat: ' . $targetUser->getUsername(),
            );
        }

        $this->redirect('messenger/' . $conversation->getUuid()->toString());
    }
}
