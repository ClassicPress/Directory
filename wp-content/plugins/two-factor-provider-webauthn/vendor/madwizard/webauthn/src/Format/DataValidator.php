<?php

namespace WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Format;

use WildWolf\WordPress\TwoFactorWebAuthn\Vendor\MadWizard\WebAuthn\Exception\DataValidationException;
use function array_key_exists;
use function get_class;
use function gettype;

final class DataValidator
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param CborMap $data     CBOR map to valdiate
     * @param array   $types    Expected types in the CBOR map. Keys match with the keys from the map, the values
     *                          of this array are the expected types as strings. In case of objects this is the fully qualified classname, for
     *                          other types this can be any of the return values of PHP's gettype() function. When a type is prefixed with `?`
     *                          it is optional and not required to be in the data array.
     * @param bool    $complete Indicates whether the $types parameter completely covers the data. If any additional fields
     *                          are found in the $data map that are not in the $types array an exception is thrown. When false additional
     *                          fields are ignored.
     *
     * @throws DataValidationException
     *
     * @return void Returns nothing but only returns when the data is valid. Otherwise an exception is thrown.
     */
    public static function checkMap(CborMap $data, array $types, bool $complete = true): void
    {
        $data = $data->copy();
        foreach ($types as $key => $type) {
            $type = self::parseType($type, $key, $optional, $nullable);

            if ($data->has($key)) {
                self::validateDataKey($data->get($key), $key, $type, $nullable);
                $data->remove($key);
            } elseif (!$optional) {
                throw new DataValidationException(sprintf('Required key "%s" is missing in data.', $key));
            }
        }
        if ($complete && $data->count() !== 0) {
            throw new DataValidationException(sprintf('Unexpected fields in data (%s).', implode(', ', $data->getKeys())));
        }
    }

    /**
     * @param array $data     Data array to valdiate
     * @param array $types    Expected types in the data array. Keys match with the keys from the data array, the values
     *                        of this array are the expected types as strings. In case of objects this is the fully qualified classname, for
     *                        other types this can be any of the return values of PHP's gettype() function. When a type is prefixed with `?`
     *                        it is optional and not required to be in the data array.
     * @param bool  $complete Indicates whether the $types parameter completely covers the data. If any additional fields
     *                        are found in the $data array that are not in the $types array an exception is thrown. When false additional
     *                        fields are ignored.
     *
     * @throws DataValidationException
     *
     * @return void Returns nothing but only returns when the data is valid. Otherwise an exception is thrown.
     */
    public static function checkArray(array $data, array $types, bool $complete = true): void
    {
        foreach ($types as $key => $type) {
            $type = self::parseType($type, $key, $optional, $nullable);

            if (array_key_exists($key, $data)) {
                self::validateDataKey($data[$key], $key, $type, $nullable);
                unset($data[$key]);
            } elseif (!$optional) {
                throw new DataValidationException(sprintf('Required key "%s" is missing in data.', $key));
            }
        }
        if ($complete && \count($data) !== 0) {
            throw new DataValidationException(sprintf('Unexpected fields in data (%s).', implode(', ', array_keys($data))));
        }
    }

    /**
     * @param mixed $value
     * @param mixed $key
     *
     * @throws DataValidationException
     */
    private static function validateDataKey($value, $key, string $type, bool $nullable): void
    {
        if ($nullable && $value === null) {
            return;
        }

        $actualType = gettype($value);
        if ($actualType === 'object') {
            $actualType = get_class($value);
        }

        if ($actualType !== $type) {
            throw new DataValidationException(sprintf('Expecting key "%s" to have value of type "%s" but has type "%s".', (string) $key, $type, $actualType));
        }
    }

    /**
     * @param mixed $key
     */
    private static function parseType(string $type, $key, bool &$optional = null, bool &$nullable = null): string
    {
        $optional = false;
        $nullable = false;
        if ($type === '') {
            throw new DataValidationException(sprintf('Invalid type "%s" for key "%s".', $type, (string) $key));
        }

        if ($type[0] === '?') {
            $optional = true;
            $type = substr($type, 1);
        }
        if ($type[0] === ':') {
            $nullable = true;
            $type = substr($type, 1);
        }
        return $type;
    }
}
