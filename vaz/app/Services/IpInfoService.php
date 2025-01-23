<?php

declare(strict_types=1);

namespace App\Services;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

class IpInfoService
{
    private Cache $cache;

    public function __construct(string $cacheDir)
    {
        $storage = new FileStorage($cacheDir);
        $this->cache = new Cache($storage);
    }

    public function getIpInfo(string $ip): array // dodáme IP4 nebo IP6 adresu a dostaneme info používá zadarmo verzi s limitem spolu s tím se vytváří klíč ne kešování informací data se ukládají do TEMP takže je dobré je nesmazat ;-)
    {
        $cacheKey = 'ip-info-' . md5($ip);
        $ipInfo = $this->cache->load($cacheKey);

        if ($ipInfo === null) {
            $response = file_get_contents("https://ipinfo.io/{$ip}/json");
            $ipInfo = json_decode($response, true);
            $this->cache->save($cacheKey, $ipInfo);
            $ipInfo['cached'] = false; //tady nám to řekne jestli ůdaj pochází z cache nebo jestli ze zdroje

        } else {
            $ipInfo['cached'] = true;
        }
        $ipInfo['cacheKey'] = $cacheKey; //ještě si pošleme klíč kdyby jsme potřebovali něky s ním pracovat
        return $ipInfo;
    }
}
