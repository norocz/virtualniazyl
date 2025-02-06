<?php
declare(strict_types=1);

namespace App\Services;

use Random\RandomException;

class CollectionKeyService
{
    /**
     * @throws RandomException
     */

    /**
     * Generuje unikátní klíč pro adopci.
     *
     * @param int $azylId id Azylu
     * @param int $collectionId Id sbírky
     * @return integer
     * @throws RandomException
     */

    public function createCollectionKey(int $azylId, int $collectionId): int
    {
        $padLanght = 10 - strlen(strval($azylId.$collectionId));
        $pad_str = random_int(0,9);
        return intval(str_pad(strval($azylId.$collectionId),$padLanght, strval($pad_str), STR_PAD_RIGHT));
    }



}