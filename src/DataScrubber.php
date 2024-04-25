<?php

namespace Tapbuy\DataScrubber;

class DataScrubber
{
    private $keys;
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->fetchKeys();
    }

    private function fetchKeys()
    {
        
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if($response !== false) {
            $json = json_decode($response, true);
            if ($json['success'] === true) {
                $this->keys = $json['data'];
            } else {
                throw new \Exception('Failed to load keys');
            }
        } else {
            throw new \Exception('Failed to load keys');
        }

        curl_close($curl);
    }

    public function anonymizeObject(object|array $data): object|array
    {
        return $this->anonymize($data);
    }

    private function anonymize($data)
    {
        if (is_object($data)) {
            $anonymizedData = new \stdClass();
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $anonymizedData->$key = $this->anonymize($value);
                } else {
                    if (in_array(strtolower($key), $this->keys)) {
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
            return $result;
        } else {
            return $data;
        }
    }

    private function anonymizeValue($value)
    {
        if (is_string($value)) {
            return str_repeat('*', strlen($value));
        } elseif (is_numeric($value)) {
            return $this->anonymizeNumeric($value);
        }
        return $value;
    }

    private function anonymizeNumeric($value)
    {
        $length = strlen((string)$value);
        $anonymizedNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $anonymizedNumber .= rand(0, 9);
        }
        return is_float($value) ? (float)$anonymizedNumber : (int)$anonymizedNumber;
    }
}
