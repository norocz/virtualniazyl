<?php

namespace App\Services;

use App\Model\Orm\Entity\Users;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\UsersRepository;

class UserAddressService
{
    /**
     * Generuje unikátní adresu uživatele pro komunikaci.
     *
     * @param int $userId ID uživatele
     * @param string $email Email uživatele
     * @param string $username Uživatelské jméno
     * @return string Náhodně generovaná adresa
     */
    public function generateCommunicationAddress(int $userId, string $email, string $username): string
    {
        // Náhodné číslo (rozsah můžete přizpůsobit dle potřeby)
        $randomNumber = random_int(100000, 999999);

        // Kombinace dat pro hash
        $data = $userId . $email . $username . $randomNumber;

        // Vytvoření hashe pomocí SHA-256
        $hash = 'usr'.hash('sha256', $data);

        // Zkrácení hashe na prvních 40 znaků (volitelné)
        return substr($hash, 0, 40);
    }
}

class AzylAddressService
{
    /**
     * Generuje unikátní adresu uživatele pro komunikaci.
     *
     * @param int $userId ID uživatele
     * @param string $email Email uživatele
     * @param string $username Uživatelské jméno
     * @return string Náhodně generovaná adresa
     */
    public function generateCommunicationAddress(int $azylId, string $email, string $azylName): string
    {
        // Náhodné číslo (rozsah můžete přizpůsobit dle potřeby)
        $randomNumber = random_int(100000, 999999);

        // Kombinace dat pro hash
        $data = $azylId . $email . $azylName . $randomNumber;

        // Vytvoření hashe pomocí SHA-256
        $hash = 'azl'.hash('sha256', $data);

        // Zkrácení hashe na prvních 40 znaků (volitelné)
        return substr($hash, 0, 40);
    }

}
