<?php
declare(strict_types=1);

namespace App\Model\Orm\Enums;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ActionTypeEnum extends Type
{
    public const ACTION_TYPE_ENUM = 'actionTypeEnum';
    public const START_ADOPTION = 'Start adopce',
                 END_ADOPTION = 'Konec adopce',
                 BREAK_ADOPTION = 'Přerušení adopce',
                 CONTACT_ADOPTION = 'Kontakt',
                 PHONE_CALL_ADOPTION = 'Telefonát',
                 PERSONAL_VISIT_ADOPTION = 'Osobní kontakt',
                 VERIFICATION_ADOPTION = 'Ověření',
                 POSITIVE_ADOPTION_END = 'Adopce se zdařila',
                 NEGATIVE_ADOPTION_END = 'Adopce se nezdařila';

    public function getName(): string
    {
        return self::ACTION_TYPE_ENUM;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): ?string
    {
        $actions = [
            self::START_ADOPTION, //modra
            self::END_ADOPTION,   //zluta
            self::BREAK_ADOPTION,   //zluta
            self::CONTACT_ADOPTION,
            self::PHONE_CALL_ADOPTION,
            self::PERSONAL_VISIT_ADOPTION,
            self::VERIFICATION_ADOPTION,
            self::POSITIVE_ADOPTION_END,  //zelena
            self::NEGATIVE_ADOPTION_END   //cervena
        ];
        $quotedActions = array_map(fn($action) => $platform->quoteStringLiteral($action), $actions);
        return 'ENUM(' . implode(', ', $quotedActions) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, self::getActionTypes())) {
            throw new \InvalidArgumentException("Invalid Action Type");
        }
        return $value;
    }

    public static function getActionTypes(): array
    {
        return [
            self::START_ADOPTION,
            self::END_ADOPTION,
            self::BREAK_ADOPTION,
            self::CONTACT_ADOPTION,
            self::PHONE_CALL_ADOPTION,
            self::PERSONAL_VISIT_ADOPTION,
            self::VERIFICATION_ADOPTION,
            self::POSITIVE_ADOPTION_END,
            self::NEGATIVE_ADOPTION_END
        ];
    }

    public static function getActionTypesForm(): array
    {
        return [
            self::START_ADOPTION => self::START_ADOPTION,
            self::END_ADOPTION => self::END_ADOPTION,
            self::BREAK_ADOPTION => self::BREAK_ADOPTION,
            self::CONTACT_ADOPTION => self::CONTACT_ADOPTION,
            self::PHONE_CALL_ADOPTION => self::PHONE_CALL_ADOPTION,
            self::PERSONAL_VISIT_ADOPTION => self::PERSONAL_VISIT_ADOPTION,
            self::VERIFICATION_ADOPTION => self::VERIFICATION_ADOPTION,
            self::POSITIVE_ADOPTION_END => self::POSITIVE_ADOPTION_END,
            self::NEGATIVE_ADOPTION_END => self::NEGATIVE_ADOPTION_END
        ];
    }
}