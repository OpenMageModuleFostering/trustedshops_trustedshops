<?php

/**
 * @category  Trustedshops
 * @package   Trustedshops_Trustedshops
 * @author    Trusted Shops GmbH
 * @copyright 2016 Trusted Shops GmbH
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.trustedshops.de/
 */
class Trustedshops_Trustedshops_Block_Trustcard extends Trustedshops_Trustedshops_Block_Abstract
{
    /**
     * path to layout file
     *
     * @var string
     */
    protected $_template = 'trustedshops/trustcard.phtml';

    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * get the current order from the checkout session
     * used on the checkout success page
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $session = Mage::getSingleton('checkout/session');
            $lastOrder = $session->getLastRealOrder();
            if (empty($lastOrder)) {
                $lastOrder = $session->getLastOrderId();
                $lastOrder = Mage::getModel('sales/order')->load($lastOrder);
            }
            $this->_order = $lastOrder;
        }
        return $this->_order;
    }

    /**
     * check if we should collect order data
     * always true for standard mode
     *
     * @return bool
     */
    public function collectOrders()
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->isExpert()) {
            return $this->getConfig('collect_orders', 'trustbadge');
        }
        return true;
    }

    /**
     * check if we should collect product data
     *
     * @return bool
     */
    public function collectReviews()
    {
        if ($this->isExpert()) {
            if (!$this->getConfig('collect_orders', 'trustbadge')) {
                return false;
            }
            return $this->getConfig('expert_collect_reviews', 'product');
        }
        return $this->getConfig('collect_reviews', 'product');
    }

    /**
     * get the formatted order amount
     *
     * @return string
     */
    public function getOrderAmount()
    {
        return $this->getOrder()->getGrandTotal();
    }

    /**
     * get the product image url
     *
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return string
     */
    public function getProductImage($item)
    {
        return (string)Mage::helper('catalog/image')->init($item->getProduct(), 'image');
    }

    /**
     * get the product sku
     * for composite products get the parents sku
     *
     * @param Mage_Sales_Model_Order_Item $item
     *
     * @return string
     */
    public function getProductSku($item)
    {
        if ($item->getHasChildren()) {
            return $item->getProduct()->getSku();
        }
        return $item->getSku();
    }
}
