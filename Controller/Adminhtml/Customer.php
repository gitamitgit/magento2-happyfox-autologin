<?php

namespace A2bizz\Happyfox\Controller\Adminhtml;

abstract class Customer extends \Magento\Backend\App\Action {

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    )
    {
        parent::__construct($context);
    }
}
