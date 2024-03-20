<?php
/**
 * Copyright © Pronko Consulting
 * See LICENSE for license details.
 */

namespace Pronko\Elavon\Gateway\Helper;

use Pronko\Elavon\Spi\ConfigInterface;
use Magento\Framework\Encryption\Encryptor;

/**
 * Class HashEncryptor
 * @package     Pronko\Elavon\Gateway\Helper
 */
class HashEncryptor
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var int
     */
    private $hashType;

    /**
     * HashEncryptor constructor.
     * @param ConfigInterface $config
     * @param Encryptor $encryptor
     * @param int $hashType
     */
    public function __construct(
        ConfigInterface $config,
        Encryptor $encryptor,
        $hashType = Encryptor::HASH_VERSION_MD5
    ) {
        $this->config = $config;
        $this->encryptor = $encryptor;
        $this->hashType = $hashType;
    }

    /**
     * @param array $data
     * @return string
     */
    public function encrypt(array $data)
    {
        $hashString = implode('.', $data);
        $hashString = $this->encryptor->hash($hashString, $this->hashType);

        $hashString = sprintf("%s.%s", $hashString, $this->config->getSecret());

        return $this->encryptor->hash($hashString, $this->hashType);
    }
}
