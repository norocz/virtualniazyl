<?php
declare(strict_types=1);

namespace App\Services;

use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\ConversationsRepository;
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
    private AzylRepository $azylRepository;
    private ConversationsRepository $conversationsRepository;
    public function __construct(MessagesRepository $messagesRepository,
                                UsersRepository $usersRepository,
                                UserAddressService $userAddressService,
                                AzylRepository $azylRepository,
                                ConversationsRepository $conversationsRepository,)
    {
        $this->messagesRepository = $messagesRepository;
        $this->usersRepository = $usersRepository;
        $this->userAddressService = $userAddressService;
        $this->azylRepository = $azylRepository;
        $this->conversationsRepository = $conversationsRepository;
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
        if ($presenter->getName() === 'Azyl')
        {
            $azyl = $this->azylRepository->findOneById($presenter->getUser()->getIdentity()->getData()['Azyl']->getId());
            $conversation = $this->conversationsRepository->findOneById($values->address);
            $conversation->setLastMessage(new DateTimeImmutable());
            $this->conversationsRepository->save($conversation);

            $message = new Messages();
            $message->setConversation($conversation);
            $message->setMessage($values->message);
            $message->setAzyl($azyl);
            $message->setCreatedAt(new DateTimeImmutable());
            $message->setReaded(false);
            $message->setType(MessageTypeEnum::TOUSER_TYPE);
            $message->setAdoption($conversation->getAdoption());
            $this->messagesRepository->save($message);

            if ($presenter->isAjax()) {
                $presenter->redrawControl('messages');
            } else {
                $chat = "?do=chat";
                $url = $presenter->link('this', $values->address) . $chat;
                $presenter->redirectUrl($url);
            }

        }
        else
        {
            $user = $this->usersRepository->findOneById($presenter->getUser()->getId());
            $conversation = $this->conversationsRepository->findOneById($values->address);
            $conversation->setLastMessage(new DateTimeImmutable());
            $this->conversationsRepository->save($conversation);

            $message = new Messages();
            $message->setConversation($conversation);
            $message->setMessage($values->message);
            $message->setUser($user);
            $message->setCreatedAt(new DateTimeImmutable());
            $message->setReaded(false);
            $message->setType(MessageTypeEnum::TOUSER_TYPE);
            $this->messagesRepository->save($message);

            if ($presenter->isAjax()) {
                $presenter->redrawControl('messages');
            } else {
                $chat = "?do=chat";
                $url = $presenter->link('this', $values->address) . $chat;
                $presenter->redirectUrl($url);
            }
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

    public function UpdateAzylMessages(): void
    {
        $azyls = $this->azylRepository->fetchAll();
        foreach ($azyls as $azyl) {
            if (is_null($azyl->getMessageAddress())) {
                $messageAddress = $this->userAddressService->generateCommunicationAddress($azyl->getId(), $azyl->getEmail(), $azyl->getAzylName());
                $azyl->setMessageAddress($messageAddress);
                $this->azylRepository->addUser($azyl);

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
        $messages = $this->messagesRepository->findBytConversationMessages($id);
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
