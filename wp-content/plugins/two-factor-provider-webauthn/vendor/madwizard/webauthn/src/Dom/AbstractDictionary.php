<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Dom;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Json\JsonConverter;

abstract class AbstractDictionary implements DictionaryInterface
{
    abstract public function getAsArray(): array;

    public function getJsonData(): array
    {
        return JsonConverter::encodeDictionary($this);
    }

    protected static function removeNullValues(array $map): array
    {
        return array_filter($map, static function ($value) {
            return $value !== null;
        });
    }
}
