<?php
declare(strict_types=1);

namespace App\Services;


class AdoptionKeyService
{
    protected $key;


    /**
     * Generuje unikátní klíč pro adopci.
     *
     * @param int $userId ID uživatele
     * @param int $animalId Email uživatele
     * @param int $azylId Uživatelské jméno
     * @return string Náhodně generovaná adresa
     */

    public function createKey(int $userId, int $animalId, int $azylId) : void
    {
        //vytvořím si klíč aby adopce nejela jen podle ID klíč lze vnitřně zreplikovat je to směs SHA z id zvířete, azylu a uživatele co chce adoptovat

        $this->key = strval(sha1($userId.$animalId.$azylId));

    }

    public function getKey() : string
    {
        return $this->key;
    }




}




