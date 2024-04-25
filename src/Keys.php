<?php

namespace Tapbuy\DataScrubber;

class Keys
{
    private $keys;
    private $url;
    private $file;

    /**
     * Fetch keys from the API
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->file = __DIR__ . '/../var/data-scrubbing-keys.json';
        $this->keys = [];
        $this->setKeys();
    }

    /**
     * Fetch keys from the API
     * @return void
     */
    public function fetchKeys(): void
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if($response !== false) {
            $json = json_decode($response, true);
            if ($json['success'] === true) {
                file_put_contents($this->file, json_encode($json['data']));
            } else {
                throw new \Exception('Failed to load keys');
            }
        } else {
            throw new \Exception('Failed to load keys');
        }

        curl_close($curl);
    }

    /**
     * Set the keys to anonymize
     * @return void
     */
    private function setKeys(): void
    {
        if (!file_exists($this->file) || !file_get_contents($this->file)) {
            $this->fetchKeys();
            $this->keys = json_decode(file_get_contents($this->file), true);
        } else {
            $this->keys = json_decode(file_get_contents($this->file), true);
        }
    }

    /**
     * Get the keys
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }
}
