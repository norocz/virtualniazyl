<?php

declare(strict_types=1);

use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

// Autoload a konfigurace
require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->addConfig(__DIR__ . '/../app/config/common.neon');
$container = $configurator->createContainer();

$db = $container->getByType(Explorer::class);

function generateAddress(int $userId, string $email, string $username): string {
    return sprintf('%d-%s-%s-%d', $userId, $email, $username, random_int(1000, 9999));
}

try {
    // Získání všech uživatelů
    $users = $db->table('users'); // Předpokládám, že tabulka se jmenuje 'users'

    foreach ($users as $user) {
        $address = generateAddress($user->id, $user->email, $user->username);
        $user->update(['address' => $address]); // Předpokládám, že sloupec se jmenuje 'address'
    }

    // Přiřazení adres stávajícím zprávám
    $messages = $db->table('messages'); // Předpokládám, že tabulka se jmenuje 'messages'

    foreach ($messages as $message) {
        $user = $users->get($message->user_id); // Předpokládám, že v message je 'user_id'

        if ($user) {
            $message->update(['address' => $user->address]); // Předpokládám, že sloupec se jmenuje 'address'
        }
    }

    echo "Migrace byla úspěšná.\n";
} catch (Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
}
