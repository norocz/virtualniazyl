<?php
declare(strict_types=1);

namespace App\Services;

use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\UsersRepository;
use BaconQrCode\Encoder\QrCode;
use DateTimeImmutable;
use Defr\QRPlatba\QRPlatba;
use Defr\QRPlatba\QRPlatbaException;
use Nette\Application\UI\Form;
use App\Model\Orm\Enums\MessageTypeEnum;
use Nette\Application\LinkGenerator;
use Nette\DI\Attributes\Inject;
use Nette\SmartObject;


class QrCodeService
{

    use SmartObject;

    #[Inject]
    private \Nette\DI\Container $container;
    private string $account;
    private string $specificSymbol;
    private string $curency;
    private int $amount;
    private string $message;
    private string $variableSymbol;

    public function __construct($account)
    {
        $this->account = $account;
    }
    public function setCurency($curency = 'CZK'):void
    {
        $this->curency = $curency;
    }

    public function setAmoutn(int $amount):void
    {
        $this->amount = $amount;
    }

    public function setMessage(string $message):void
    {
        $this->message = $message;
    }

    public function setVariableSymbol(string $variableSymbol):void
    {
        $this->variableSymbol = $variableSymbol;
    }

    public function setSpecificSymbol(string $specificSymbol):void
    {
        $this->specificSymbol = $specificSymbol;
    }

    private function removeDiacritics(string $text): string
    {
        $diacritics = [
            'á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e', 'í' => 'i',
            'ň' => 'n', 'ó' => 'o', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ú' => 'u',
            'ů' => 'u', 'ý' => 'y', 'ž' => 'z', 'ö' => 'o', 'ü' => 'u', 'ä' => 'a',
            'Á' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Ě' => 'E', 'Í' => 'I',
            'Ň' => 'N', 'Ó' => 'O', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ú' => 'U',
            'Ů' => 'U', 'Ý' => 'Y', 'Ž' => 'Z', 'Ö' => 'O', 'Ü' => 'U', 'Ä' => 'A'
        ];

        return strtr($text, $diacritics);
    }


    private function QrForCollection(): QRPlatba
    {

        $qrPlatba =New QRPlatba();
        $qrPlatba->setAccount($this->account);
        $qrPlatba ->setCurrency($this->curency);
        $qrPlatba ->setAmount($this->amount);
        $qrPlatba ->setMessage($this->removeDiacritics($this->message));
        $qrPlatba ->setVariableSymbol($this->variableSymbol);
        $qrPlatba ->setDueDate(new \DateTime('now'));

      return $qrPlatba;
    }

    /**
     * @throws QRPlatbaException
     */
    private function QrForVirtualAdoption(): QRPlatba
    {

        $qrPlatba =New QRPlatba();
        $qrPlatba->setAccount($this->account);
        $qrPlatba ->setCurrency($this->curency);
        $qrPlatba ->setAmount($this->amount);
        $qrPlatba ->setMessage($this->removeDiacritics($this->message));
        $qrPlatba ->setVariableSymbol($this->variableSymbol);
        $qrPlatba ->setSpecificSymbol($this->specificSymbol);
        $qrPlatba ->setDueDate(new \DateTime('now'));

        return $qrPlatba;
    }
/*
if (!is_null($azylProfil->getBankAccount())){
$qrPlatba =New QRPlatba();
$qrPlatba->setAccount($azylProfil->getBankAccount().'/'.$azylProfil->getBankCode())
->setMessage('Peníze pro '.$azylProfil->getAzylName())
->setVariableSymbol($azylProfil->getBankSpecificCode())
->setCurrency('CZK')
->setAmount((float)'101.11')
->setMessage('')
->setDueDate(new \DateTime('now'));
}

$this->getTemplate()->qrkodazyl = $qrPlatba->getQRCodeImage();
*/


}