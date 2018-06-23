<?php
namespace A2bizz\Happyfox\Observer;

use Magento\Framework\Event\ObserverInterface;

class PreAutologinObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_authSession;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    )
    {
        $this->_authSession = $authSession;
        $this->redirect = $redirect;
    }

    /*
     * Admin Session get User Details
     *  return Admin User Session Object
     */
    private function getCurrentUser()
    {
        return $this->_authSession->getUser();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $user = $this->getCurrentUser();
        //echo "Amit Dwivedi<pre>";print_r($user->getData());exit;
        $userId = $user->getUserId();

        $url = 'https://dermpro.happyfox.com/api/1.1/json/users/';
        $api_key = "fca46b135d7a46deb37cc0fb98af3190";
        $auth_code = "b3a98a02f69c45feac0b10ba21b06309";

        //$userRoleData = $this->getRoleData($userId);
        //$storeId = $userRoleData['website_id'];

        $data = array('name'=> $user->getFirstname() . ' ' . $user->getLastname(),
            'email' => $user->getEmail(),
            'c-cf-4'=> 1//Mage::getStoreConfig('happyfox/general/storeoptionid', $storeId)
        );

        $headers = array("Content-Type:application/json");
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_USERPWD => $api_key . ":" . $auth_code
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo '<pre>';print_r($result);
        if(!curl_errno($ch))
        {
            $info = curl_getinfo($ch);
            echo $info['http_code'];
            echo json_decode($result,true);
            if($info['http_code'] == 500){
                error_log(var_dump($info));
            }else{
                echo json_decode($result,true);
            }
            if ($info['http_code'] == 200) {
                $errmsg = "File uploaded successfully";
            }
        } else {
            $errmsg = curl_error($ch);
        }
        curl_close($ch);
        echo $errmsg;
    }
}
?>