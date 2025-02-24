<?php
declare(strict_types=1);

namespace App\Model\Orm\Enums;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AdoptionsTypeEnum extends Type
{
    public const ADOPTION_TYPE_ENUM = 'adoptionsTypeEnum';
    public const VIRTUAL_ADOPTION_TYPE = 'Virtuální adopce',
                 TEMP_ADOPTION_TYPE = 'Dočasná péče',
                 PREADOPT_ADOPTION_TYPE = 'Předadopce',
                 FULL_ADOPTION_TYPE = 'Plná adopce';

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     * @throws Exception
     */

    public function getSQLDeclaration(array $fieldDeclaration,
                                     AbstractPlatform $platform): ?string
    {
        $adoptionsTypes = self::getAdoptionsTypes();
        $quotedAdoptionsTypes = array_map(fn($adoptionsType) => $platform->quoteStringLiteral($adoptionsType), $adoptionsTypes);
        return 'ENUM(' . implode(', ', $quotedAdoptionsTypes) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        return $value;
    }

    public function getName(): string
    {
        return 'adoptionsTypeEnum';
    }

    public function getAdoptionsTypes(): array
    {
        return [
            self::VIRTUAL_ADOPTION_TYPE,
            self::PREADOPT_ADOPTION_TYPE,
            self::FULL_ADOPTION_TYPE,
            self::TEMP_ADOPTION_TYPE
        ];
    }

        public function getAdoptionsTypesForm(): array
    {
        return [
            self::VIRTUAL_ADOPTION_TYPE => self::VIRTUAL_ADOPTION_TYPE,
            self::PREADOPT_ADOPTION_TYPE => self::PREADOPT_ADOPTION_TYPE,
            self::FULL_ADOPTION_TYPE => self::FULL_ADOPTION_TYPE,
            self::TEMP_ADOPTION_TYPE => self::TEMP_ADOPTION_TYPE
        ];
    }
}