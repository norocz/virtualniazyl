<?php

declare(strict_types=1);

namespace App\Model;

use SimpleXMLElement;

class VersionService
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getVersions(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $xml = simplexml_load_file($this->filePath);
        $versions = [];

        foreach ($xml->version as $version) {
            $versions[] = [
                'date' => (string) $version->date,
                'number' => (string) $version->number,
                'description' => (string) $version->description,
            ];
        }

        return $versions;
    }

    public function getLastVersion(): ?array
    {
        $versions = $this->getVersions();
        return $versions[0] ?? null;
    }

    public function addVersion(string $date, string $number, string $description): void
    {
        $xml = file_exists($this->filePath)
            ? simplexml_load_file($this->filePath)
            : new SimpleXMLElement('<versions/>');

        $version = $xml->addChild('version');
        $version->addChild('date', $date);
        $version->addChild('number', $number);
        $version->addChild('description', $description);

        $xml->asXML($this->filePath);
    }
}
