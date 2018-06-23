<?php

namespace A2bizz\Happyfox\Controller\Adminhtml\Customer;

//use A2bizz\Happyfox\Helper\Data;
use Firebase\JWT\JWT;

class Autologin extends \A2bizz\Happyfox\Controller\Adminhtml\Customer {

    //protected $_resultPageFactory;
    protected $_authSession;

    protected $_jwtClass;

    protected $_scopeConfig;

    protected $rand1;
    protected $rand2;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Firebase\JWT\JWT $jwtClass
        //\Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        //$this->_resultPageFactory = $resultPageFactory;
        $this->_authSession = $authSession;
        $this->_jwtClass = $jwtClass;
        //$this->_scopeConfig = $scopeConfig;

        //Data::setPage('rev_addon');

        parent::__construct($context);
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('A2bizz_Happyfox::autologin');
    }


    /*
     * Admin Session get User Details
     *  return Admin User Session Object
     */
    private function getCurrentUser()
    {
        return $this->_authSession->getUser();
    }

    /**
     * Auto login
     *
     */

    public function execute() {
        $this->rand1 = '1000000000';
        $this->rand2 = '9999999999';

        $user = $this->getCurrentUser();

        $userId = $user->getUserId();
        if(isset($userId) && $userId > 0){
            //$token = Mage::getStoreConfig('happyfox/general/tocken');
            //$token = $this->_scopeConfig->getValue('happyfox/general/token');
            $token = 'fbcc37ae226e4736b853c75fa85c3bde';

            $payload = array('iat' => time(),
                'jti' => rand($this->rand1,(int) $this->rand2),
                'name' => $user->getFirstname() . ' ' . $user->getLastname(),
                'email' => $user->getEmail()
                //'store' => Mage::app()->getStore()->getName()
            );

            $signature_encoded = $this->_jwtClass->encode($payload, $token);

            //$domain = Mage::getStoreConfig('happyfox/general/subdomain');
            //$domain = $this->_scopeConfig->getValue('happyfox/general/subdomain');
            $domain = 'dermpro.happyfox.com';
            $url = 'https://' . $domain . '/jwt/?token='.$signature_encoded;

            $this->_redirect($url); return;

        } else {
            $message = __('Please Login first to generate Ticket !!!');
            $this->messageManager->addError( __($message) );

            $this->_redirect('admin/dashboard/index'); return;
        }
    }
}
