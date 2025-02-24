<?php

declare(strict_types=1);

namespace App\Model\Orm\Enums;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class SexTypeEnum extends Type
{
    public const SEX_TYPE_ENUM = 'sexTypeEnum';
    public const MALE_ENUM = 'Sameček',
                 FEMALE_ENUM = 'Samička',
                 UNKNOWN_ENUM = 'Nevíme',
                 HERMAPHRODITE_ENUM = 'Hermafrodit';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): ?string
    {
        return "ENUM('" . implode("', '", self::getSexTypes()) . "')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        return $value; // no need for conversion here, we're storing the value as is
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value; // no need for conversion here, we're storing the value as is
    }

    public function getName(): string
    {
        return self::SEX_TYPE_ENUM;
    }

    public static function getSexTypes(): array
    {
        return [
            'MALE_ENUM' => self::MALE_ENUM,
            'FEMALE_ENUM' => self::FEMALE_ENUM,
            'UNKNOWN_ENUM' => self::UNKNOWN_ENUM,
            'HERMAPHRODITE_ENUM' => self::HERMAPHRODITE_ENUM,
        ];
    }

    public static function getSexTypesForm(): array
    {
        return [
            self::MALE_ENUM => self::MALE_ENUM,
            self::FEMALE_ENUM => self::FEMALE_ENUM,
            self::UNKNOWN_ENUM => self::UNKNOWN_ENUM,
            self::HERMAPHRODITE_ENUM => self::HERMAPHRODITE_ENUM,
        ];
    }
}
