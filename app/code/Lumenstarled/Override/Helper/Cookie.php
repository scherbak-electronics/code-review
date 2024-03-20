<?php 
namespace Lumenstarled\Override\Helper;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
class Cookie{
        /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;
 
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;
 
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @var Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddressInstance;
 
    /**
     * [__construct ]
     *
     * @param CookieManagerInterface                    $cookieManager
     * @param CookieMetadataFactory                     $cookieMetadataFactory
     * @param SessionManagerInterface                   $sessionManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function getCookie($name)
    {
        return $this->_cookieManager->getCookie($name);
    }

    /**
     * @param string $value
     * @param int $duration
     * @return void
     */
    public function setPublicCookie($name, $value)
    {
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
           # ->setDuration($duration)
            ->setPath('/')
           ->setDomain($this->_sessionManager->getCookieDomain());
 
        $this->_cookieManager->setPublicCookie(
            $name,
            $value,
            $metadata
        );
    }
	
 	public function setPublicCookie1($name, $value)
    {
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
           # ->setDuration($duration)
            ->setPath('/')
			->setDomain($this->_sessionManager->getCookieDomain());
 
        $this->_cookieManager->setPublicCookie(
            $name,
            $value,
            $metadata
        );
    }
	public function delete($name){
        $this->_cookieManager->deleteCookie($name);
    }

    /**
     * @return void
     */
    /* public function delete()
    {
        $this->cookieManager->deleteCookie(
            self::COOKIE_NAME,
            $this->cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain());
        );
    } */
}