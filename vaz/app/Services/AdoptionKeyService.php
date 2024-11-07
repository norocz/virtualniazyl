<?php
declare(strict_types=1);

namespace App\Services;

class AdoptionKeyService
{
    /**
     * Generuje unikátní klíč pro adopci.
     *
     * @param int $userId ID uživatele
     * @param int $animalId Email uživatele
     * @param int $azylId Uživatelské jméno
     * @return string Náhodně generovaná adresa
     */

    public function createKey(int $userId, int $animalId, int $azylId) : string
    {
        //vytvořím si klíč aby adopce nejela jen podle ID klíč lze vnitřně zreplikovat je to směs SHA z id zvířete, azylu a uživatele co chce adoptovat

        return strval(sha1($userId.$animalId.$azylId));

    }




}




