<?php

namespace Tapbuy\DataScrubber;
use Tapbuy\DataScrubber\Keys;

class Anonymizer
{
    private $keys;
    private $url;

    /**
     * Fetch keys from the API
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->keys = [];
        $this->setKeys();
    }

    /**
     * Set the keys to anonymize
     * @return void
     */
    private function setKeys(): void
    {
        $keys = new Keys($this->url);
        $this->keys = $keys->getKeys();
    }

    /**
     * Anonymize an object or array recursively
     * @param object|array $data
     */
    public function anonymizeObject(object|array $data): object|array
    {
        return $this->anonymize($data);
    }

    /**
     * Anonymize a key in an object or array if matches the keys from the API
     * @param mixed $data
     */
    private function anonymize(mixed $data): mixed
    {
        if (is_object($data)) {
            $anonymizedData = new \stdClass();
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $anonymizedData->$key = $this->anonymize($value);
                } else {
                    if (in_array(strtolower(str_replace('[]', '', $key)), $this->keys)) {
                        $anonymizedData->$key = $this->anonymizeValue($value);
                    } else {
                        $anonymizedData->$key = $this->anonymize($value);
                    }
                }
            }
            return $anonymizedData;
        } elseif (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                if (is_array($value) && in_array(strtolower($key . '[]'), $this->keys)) {
                    $result[$key] = $this->anonymizeArray($value);
                } else {
                    if (is_object($value) || is_array($value)) {
                        $result[$key] = $this->anonymize($value);
                    } else {
                        if (in_array(strtolower($key), $this->keys)) {
                            $result[$key] = $this->anonymizeValue($value);
                        } else {
                            $result[$key] = $this->anonymize($value);
                        }
                    }
                }
            }
            return $result;
        } else {
            return $data;
        }
    }

    /**
     * Anonymize a string keeping the length and the type
     * @param mixed $value
     */
    private function anonymizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return str_repeat('*', strlen($value));
        } elseif (is_numeric($value)) {
            return $this->anonymizeNumeric($value);
        }
        return $value;
    }

    /**
     * Anonymize a numeric value keeping the length
     * @param int|float $value
     */
    private function anonymizeNumeric(int|float $value): int|float
    {
        $length = strlen((string)$value);
        $anonymizedNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $anonymizedNumber .= rand(0, 9);
        }
        return is_float($value) ? (float)$anonymizedNumber : (int)$anonymizedNumber;
    }

    /**
     * Anonymize an array
     * @param array $value
     */
    private function anonymizeArray(array $value): array
    {
        $anonymizedArray = [];
        foreach ($value as $item) {
            $anonymizedArray[] = $this->anonymizeValue($item);
        }
        return $anonymizedArray;
    }
}
