<?php

namespace App\Services;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto as DefuseCrypto;

class Crypto
{
    /**
     * Encryption key
     *
     * @var Key
     */
    private $key;

    /**
     * Path to the file holding the encryption key
     *
     * @param string $keyPath
     */
    private $keyPath;

    /**
     * Initialize the service
     *
     * @param string $key
     */
    public function __construct(string $keyPath)
    {
        $this->keyPath = $keyPath;
    }

    /**
     * Boot the service. Creates a key if doesn't exist.
     *
     * @return void
     */
    public function boot()
    {
        if(!\file_exists($this->keyPath)) {
            // create key
            $this->key = Key::createNewRandomKey();
            \file_put_contents($this->keyPath, $this->key->saveToAsciiSafeString());
        } else {
            // load existing key
            $key = \file_get_contents($this->keyPath);
            $this->key = Key::loadFromAsciiSafeString($key);
        }
    }

    /**
     * Encrypt a string
     *
     * @param string $contents
     * @return string
     */
    public function encrypt(string $contents): string
    {
        if(!$this->key)
            throw new \RuntimeException('No key loaded. Please run boot() first');

        return DefuseCrypto::encrypt($contents, $this->key);
    }

    /**
     * Decrypt an encrypted string
     *
     * @param string $encrypted
     * @return string
     */
    public function decrypt(string $encrypted): string
    {
        if(!$this->key)
            throw new \RuntimeException('No key loaded. Please run boot() first');

        return DefuseCrypto::decrypt($encrypted, $this->key);
    }
}