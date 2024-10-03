<?php
declare(strict_types=1);

namespace App\Services;

use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\UsersRepository;
use DateTimeImmutable;
use Nette\Application\UI\Form;
use App\Model\Orm\Enums\MessageTypeEnum;
use Nette\Application\LinkGenerator;

class MessagesService
{
    private MessagesRepository $messagesRepository;
    private UsersRepository $usersRepository;
    private UserAddressService $userAddressService;
    public function __construct(MessagesRepository $messagesRepository, UsersRepository $usersRepository, UserAddressService $userAddressService)
    {
        $this->messagesRepository = $messagesRepository;
        $this->usersRepository = $usersRepository;
        $this->userAddressService = $userAddressService;
    }

    // Metoda pro načtení zpráv mezi dvěma uživateli
    public function getMessagesBetweenUsers(int $userId, int $otherUserId): array
    {
        // Příchozí zprávy - zprávy, které poslal otherUserId uživateli userId
        $incomingMessages = $this->messagesRepository->getMessagesBetween($otherUserId, $userId);

        // Odchozí zprávy - zprávy, které uživatel userId poslal uživateli otherUserId
        $outgoingMessages = $this->messagesRepository->getMessagesBetween($userId, $otherUserId);

        return [
            'incoming' => $incomingMessages,
            'outgoing' => $outgoingMessages
        ];
    }

    public function getUserContacts(int $userId): array
    {
        return $this->messagesRepository->getMessagesByReceiverId($userId);

    }

    public function createMessageForm($factory,$messageAddress): Form
    {
        $form = $factory->create();
        $form->setDefaults(['address' => $messageAddress]);
        $form->onSuccess[] = [$this, 'messagesFormSucceeded'];
        return $form;
    }

    public function messagesFormSucceeded(Form $form, \stdClass $values, $presenter): void
    {
        $message = new Messages();
        $senderUser = $this->usersRepository->getUserById($presenter->getUser()->getId());
        $receiverUser = $this->usersRepository->getUserByMessageAddress($values->address);
        $message->setSender($senderUser);
        $message->setSenderAddress($senderUser->getMessageAddress());
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setReceiver($receiverUser);
        $message->setReceiverAddress($receiverUser->getMessageAddress());
        $message->setMessage($values->message);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setReaded(false);
        $this->messagesRepository->save($message);

        if ($presenter->isAjax()) {
            $presenter->redrawControl('messages');
        } else {
            $chat = "?do=chat";
            $url = $presenter->link('User:messages', $receiverUser->getMessageAddress()) . $chat;
            $presenter->redirectUrl($url);
        }
    }

    public function UpdateMessages(): void
    {
        $users = $this->usersRepository->fetchAll();
        foreach ($users as $user) {
            if (is_null($user->getMessageAddress())) {
                $messageAddress = $this->userAddressService->generateCommunicationAddress($user->getId(), $user->getEmail(), $user->getUserName());
                $user->setMessageAddress($messageAddress);
                $this->usersRepository->addUser($user);

            }
        }

        $messages = $this->messagesRepository->findAll();

        foreach ($messages as $message) {

            $message->setSenderAddress($message->getSender()->getMessageAddress());
            $message->setReceiverAddress($message->getReceiver()->getMessageAddress());
            $this->messagesRepository->save($message);
        }
    }

    public function markMessagesAsRead(string $id)
    {
        $messages = $this->messagesRepository->getMessagesByReceiverAddress($id);
        foreach ($messages as $message)
        {
            $message->setReaded(true);
            $this->messagesRepository->save($message);
        }
    }

    public function deleteMessage($id,$presenter):bool
    {
        $message = $this->messagesRepository->getMessagesById($id);
        if($message && $message->getSenderAddress() === $presenter->getPresenter()->getUser()->getIdentity()->getData()['User']->getMessageAddress()) {

            $redirectId = $message->getSender()->getId();
            $message->setDeletedAt(new DateTimeImmutable());
            $this->messagesRepository->save($message);
            return true;
        } else {

            return false;
        }
    }
}
