<?php

namespace App\Components\Messenger;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Users;
use DateTimeImmutable;

class ChatControl extends Control
{
    private EntityManagerInterface $entityManager;
    private Users $currentUser; // Uživatel, který je přihlášen
    private ?Users $selectedContact = null; // Aktuálně vybraný kontakt

    public function __construct(EntityManagerInterface $entityManager, Users $currentUser)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $currentUser;
    }

    // Vykreslení komponenty
    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/ChatControl.latte');

        // Načteme seznam kontaktů
        $this->template->contacts = $this->getContacts();

        // Zprávy mezi přihlášeným uživatelem a vybraným kontaktem
        $this->template->messages = $this->selectedContact ? $this->getMessages($this->selectedContact) : [];

        // Odesílací formulář
        $this->template->selectedContact = $this->selectedContact;
        $this->template->render();
    }

    // Načtení kontaktů, kteří si psali s přihlášeným uživatelem
    private function getContacts(): array
    {
        $dql = 'SELECT DISTINCT u FROM App\Model\Orm\Entity\Users u
                JOIN App\Model\Orm\Entity\Messages m
                WITH (m.sender = u OR m.receiver = u)
                WHERE u != :currentUser';

        $query = $this->entityManager->createQuery($dql)
            ->setParameter('currentUser', $this->currentUser);

        return $query->getResult();
    }

    // Načtení zpráv pro vybraný kontakt
    private function getMessages(Users $contact): array
    {
        return $this->entityManager->getRepository(Messages::class)
            ->createQueryBuilder('m')
            ->where('(m.sender = :currentUser AND m.receiver = :contact) OR (m.sender = :contact AND m.receiver = :currentUser)')
            ->setParameters([
                'currentUser' => $this->currentUser,
                'contact' => $contact,
            ])
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Zobrazení zpráv s vybraným kontaktem
    public function handleChat(int $contactId): void
    {
        $this->selectedContact = $this->entityManager->getRepository(Users::class)->find($contactId);
        if (!$this->selectedContact) {
            $this->error('Contact not found');
        }

        $this->redrawControl('chat-detail');
    }

    // Odeslání nové zprávy
    public function handleSendMessage(string $messageContent): void
    {
        if ($this->selectedContact) {
            $message = new Messages();
            $message->setSender($this->currentUser);
            $message->setReceiver($this->selectedContact);
            $message->setMessage($messageContent);
            $message->setCreatedAt(new DateTimeImmutable());
            $message->setReaded(false);
            $message->setType('text'); // nebo jiný typ podle typu zprávy

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->redrawControl('chat-detail');
        }
    }

    // Formulář pro vytvoření nového chatu
    protected function createComponentNewChatForm(): Form
    {
        $form = new Form();
        $form->addText('userName', 'UserName:')
            ->setRequired('Please enter a UserName');

        $form->addSubmit('startChat', 'Start Chat');
        $form->onSuccess[] = [$this, 'startNewChat'];
        return $form;
    }

    // Zahájení nového chatu zadáním uživatelského jména
    public function startNewChat(Form $form, $values): void
    {
        $contact = $this->entityManager->getRepository(Users::class)
            ->findOneBy(['userName' => $values->userName]);

        if ($contact && $contact !== $this->currentUser) {
            $this->selectedContact = $contact;
            $this->flashMessage("Chat started with {$contact->userName}", 'alert-success');
            $this->redrawControl('chat-detail');
        } else {
            $this->flashMessage('User not found or invalid.', 'alert-warning');
        }
    }
}
