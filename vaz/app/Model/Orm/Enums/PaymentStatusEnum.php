<?php
declare(strict_types=1);

namespace App\Model\Orm\Enums;

enum PaymentStatusEnum: string
{
    case Expected = 'expected'; //platba vznikla vygenerováním QR kódu
    case Paired = 'paired';  //platba má protějšek na účtu
    case Sent = 'sent'; //platba přidána k odeslání
    case Closed = 'closed'; //platba je odeslána z účtu
}

//variabilní symbol platby si musí projít všemi stavy aby byl  brán jako konečný