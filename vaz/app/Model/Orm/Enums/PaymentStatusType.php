<?php

namespace App\Doctrine\Type;

use App\Enum\PaymentStatusEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PaymentStatusType extends Type
{
    public const PAYMENT_STATUS = 'payment_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('expected', 'paired', 'sent', 'closed')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?PaymentStatusEnum
    {
        return $value !== null ? PaymentStatusEnum::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof PaymentStatusEnum ? $value->value : null;
    }

    public function getName(): string
    {
        return self::PAYMENT_STATUS;
    }
}
