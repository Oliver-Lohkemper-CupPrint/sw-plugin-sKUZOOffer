<?php
use Doctrine\ORM\Query\Expr;
class Shopware_Controllers_Frontend_sKUZOOffer extends Enlight_Controller_Action {

    /**
     * Reference to sAdmin object (core/class/sAdmin.php)
     *
     * @var sAdmin
     */
    protected $admin;

    /**
     * Reference to sBasket object (core/class/sBasket.php)
     *
     * @var sBasket
     */
    protected $basket;

    /**
     * Reference to Shopware session object (Shopware()->Session)
     *
     * @var Zend_Session_Namespace
     */
    protected $session;

    protected $db;
    private $moduleManager;
    private $eventManager;
    /**
     * This function Init method that get called automatically.
     *
     * Set class properties
     */
    public function init()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->basket = Shopware()->Modules()->Basket();
        $this->session = Shopware()->Session();
        $this->db = Shopware()->Db();
        $this->moduleManager = Shopware()->Modules();
        $this->eventManager = Shopware()->Events();
    }


    /**
     * This function  is Pre dispatch method.
     */
    public function preDispatch()
    {
        $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);

        $this->View()->sUserLoggedIn = $this->admin->sCheckUser();
        $this->View()->sUserData = $this->getUserData();
    }

    /**
     * This function  Save basket to session.
     */
    public function postDispatch()
    {
        $this->session->sBasketCurrency = Shopware()->Shop()->getCurrency()->getId();
        $this->session->sBasketQuantity = $this->basket->sCountBasket();
        $amount = $this->basket->sGetAmount();
        $this->session->sBasketAmount = empty($amount) ? 0 : array_shift($amount);
    }

    /**
     * This function Forward to cart or confirm action depending on user state.
     */
    public function indexAction() {
        if($this->Request()->getParam('controller', false)=="sKUZOOffer") {
            return $this->forward('offers');
        }

        if ($this->basket->sCountBasket()<1 || empty($this->View()->sUserLoggedIn)) {
            $this->forward('cart');
        } else {
            $this->forward('confirm');
        }
    }


    public function calculateShippingCostsAction() {
        if ($this->Request()->getPost('sCountry')) {
            $this->session['sCountry'] = (int) $this->Request()->getPost('sCountry');
            $this->session["sState"] = 0;
            $this->session["sArea"] = Shopware()->Db()->fetchOne("
            SELECT areaID FROM s_core_countries WHERE id = ?
            ",array($this->session['sCountry']));
        }

        if ($this->Request()->getPost('sPayment')) {
            $this->session['sPaymentID'] = (int) $this->Request()->getPost('sPayment');
        }

        if ($this->Request()->getPost('sDispatch')) {
            $this->session['sDispatch'] = (int) $this->Request()->getPost('sDispatch');
        }

        if ($this->Request()->getPost('sState')) {
            $this->session['sState'] = (int) $this->Request()->getPost('sState');
        }

        $this->forward($this->Request()->getParam('sTargetAction', 'index'));
    }

    /**
     * Mostly equivalent to cartAction
     * Get user- basket- and payment-data for view assignment
     * Create temporary entry in s_order table
     * Check some conditions (minimum charge)
     *
     * @return void
     */
    public function confirmAction()
    {
        if (empty($this->View()->sUserLoggedIn)) {
            return $this->forward('login', 'account', null, array('sTarget'=>'sKUZOOffer', 'sTargetAction'=>'confirm'));
        } elseif ($this->basket->sCountBasket() < 1) {
            return $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'checkout',
                'action' => 'cart'
            ));
        }

        $this->View()->sCountry = $this->getSelectedCountry();
        $this->View()->sState = $this->getSelectedState();
        $this->View()->sPayment = $this->getSelectedPayment();
        $this->View()->sUserData["payment"] = $this->View()->sPayment;

        $this->View()->sDispatch = $this->getSelectedDispatch();
        $this->View()->sPayments = $this->getPayments();
        $this->View()->sDispatches = $this->getDispatches();

        $this->View()->sBasket = $this->getBasket();

        $this->View()->sLaststock = $this->basket->sCheckBasketQuantities();
        $this->View()->sShippingcosts = $this->View()->sBasket['sShippingcosts'];
        $this->View()->sShippingcostsDifference = $this->View()->sBasket['sShippingcostsDifference'];
        $this->View()->sAmount = $this->View()->sBasket['sAmount'];
        $this->View()->sAmountWithTax = $this->View()->sBasket['sAmountWithTax'];
        $this->View()->sAmountTax = $this->View()->sBasket['sAmountTax'];
        $this->View()->sAmountNet = $this->View()->sBasket['AmountNetNumeric'];

        $this->View()->sPremiums = $this->getPremiums();

        $this->View()->sNewsletter = isset($this->session['sNewsletter']) ? $this->session['sNewsletter'] : null;
        $this->View()->sComment = isset($this->session['sComment']) ? $this->session['sComment'] : null;

        $this->View()->sShowEsdNote = $this->getEsdNote();

        $this->View()->sDispatchNoOrder = $this->getDispatchNoOrder();
        if(!$this->View()->sDispatch)
        {
            $this->View()->sDispatchNoOrder = true;
        }
        $this->View()->sRegisterFinished = !empty($this->session['sRegisterFinished']);

        $this->saveTemporaryOrder();

        if ($this->getMinimumCharge()) {
            return $this->forward('cart');
        }

        if($this->View()->sBasket['sAmount'] < Shopware()->Config()->inquiryvalue)
        {
            return $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'checkout',
                'action' => 'cart',
                'amountError' => true
            ));
        }

        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")) {
            $activeBillingAddressId = $this->session->offsetGet('checkoutBillingAddressId', null);
            if(empty($activeBillingAddressId)) {
                $activeBillingAddressId = $this->View()->sUserData['additional']['user']['default_billing_address_id'];
            }

            $activeShippingAddressId = $this->session->offsetGet('checkoutShippingAddressId', null);
            if(empty($activeShippingAddressId)) {
                $activeShippingAddressId = $this->View()->sUserData['additional']['user']['default_shipping_address_id'];
            }

            $this->View()->assign('activeBillingAddressId', $activeBillingAddressId);
            $this->View()->assign('activeShippingAddressId', $activeShippingAddressId);

            $this->View()->assign('invalidBillingAddress', !$this->isValidAddress($activeBillingAddressId));
            $this->View()->assign('invalidShippingAddress', !$this->isValidAddress($activeShippingAddressId));

            $billingAddress = Shopware()->Models()->find('Shopware\Models\Customer\Address',$activeBillingAddressId);
            $this->get('shopware_account.address_service')->setDefaultBillingAddress($billingAddress);
            $shippingAddress = Shopware()->Models()->find('Shopware\Models\Customer\Address',$activeShippingAddressId);
            $this->get('shopware_account.address_service')->setDefaultShippingAddress($shippingAddress);

            $userData = $this->View()->sUserData;
            $userAddressData = Shopware()->Modules()->Admin()->sGetUserData();
            $userData['billingaddress'] = $userAddressData['billingaddress'];
            $userData['shippingaddress'] = $userAddressData['shippingaddress'];
            $this->View()->sUserData = $userData;
        }

        $this->session['sOrderVariables'] = new ArrayObject($this->View()->getAssign(), ArrayObject::ARRAY_AS_PROPS);
        $this->View()->sKUZOOffer = true;
        $this->View()->sTargetAction = 'confirm';
    }

    /**
     * Validates the given address id with current shop configuration
     *
     * @param $addressId
     * @return bool
     */
    private function isValidAddress($addressId)
    {
        $address = Shopware()->Models()->find('Shopware\Models\Customer\Address',$addressId);
        return $this->get('shopware_account.address_validator')->isValid($address);
    }


    private function getCountryById($countryId) {
        $builder = Shopware()->Models()->createQueryBuilder();
        $query = $builder->select(array('c'))
            ->from('Shopware\Models\Country\Country', 'c')
            ->where('c.id = :countryId')
            ->setParameter('countryId',$countryId)
            ->getQuery();

        return $query->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }
    /**
     * Called from confirmAction View
     * Customers requests to finish current order
     * Check if all conditions match and save order
     *
     * @return void
     */
    public function finishAction() {
        if ($this->Request()->getParam('sUniqueID') && !empty($this->session['sOrderVariables'])) {
            $sql = '
                SELECT transactionID as sTransactionumber, ordernumber as sOrderNumber
                FROM s_order
                WHERE temporaryID=? AND userID=?
            ';

            $order = Shopware()->Db()->fetchRow($sql, array($this->Request()->getParam('sUniqueID'), Shopware()->Session()->sUserId));
            if (!empty($order)) {
                $this->View()->assign($order);
                if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                    $orderVariables = $this->session['sOrderVariables']->getArrayCopy();

                    $userData = $this->View()->sUserData;
                    $userData["billingaddress"]['country'] = $this->getCountryById($userData["billingaddress"]["countryID"]);
                    $userData["shippingaddress"]['country'] = $this->getCountryById($userData["shippingaddress"]["countryID"]);
                    $this->View()->sUserData = $userData;

                    $orderVariables['sAddresses']['billing'] = $this->View()->sUserData["billingaddress"];
                    $orderVariables['sAddresses']['shipping'] = $this->View()->sUserData["shippingaddress"];
                    $orderVariables['sAddresses']['equal'] = $this->areAddressesEqual($orderVariables['sAddresses']['billing'], $orderVariables['sAddresses']['shipping']);

                    $this->View()->assign($orderVariables);
                } else {
                    $this->View()->assign($this->session['sOrderVariables']->getArrayCopy());
                }
                return;
            }
        }

        if (empty($this->session['sOrderVariables'])||$this->getMinimumCharge()||$this->getEsdNote()||$this->getDispatchNoOrder()) {
            return $this->forward('confirm');
        }

        $checkQuantities = $this->basket->sCheckBasketQuantities();
        if (!empty($checkQuantities['hideBasket'])) {
            return $this->forward('confirm');
        }

        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
            $orderVariables = $this->session['sOrderVariables']->getArrayCopy();

            $userData = $this->View()->sUserData;
            $userData["billingaddress"]['country'] = $this->getCountryById($userData["billingaddress"]["countryID"]);
            $userData["shippingaddress"]['country'] = $this->getCountryById($userData["shippingaddress"]["countryID"]);
            $this->View()->sUserData = $userData;

            $orderVariables['sAddresses']['billing'] = $this->View()->sUserData["billingaddress"];
            $orderVariables['sAddresses']['shipping'] = $this->View()->sUserData["shippingaddress"];
            $orderVariables['sAddresses']['equal'] = $this->areAddressesEqual($orderVariables['sAddresses']['billing'], $orderVariables['sAddresses']['shipping']);
            $this->View()->assign($orderVariables);
        } else {
            $this->View()->assign($this->session['sOrderVariables']->getArrayCopy());
        }

        if ($this->basket->sCountBasket()>0
            && empty($this->View()->sUserData['additional']['payment']['embediframe'])) {
            if ($this->Request()->getParam('sNewsletter')!==null) {
                $this->session['sNewsletter'] = $this->Request()->getParam('sNewsletter') ? true : false;
            }
            //if ($this->Request()->getParam('sComment')!==null) {
                $this->session['sComment'] = trim(strip_tags($this->Request()->getParam('sComment')));
            //}
            if (!empty($this->session['sNewsletter'])) {
                $this->admin->sUpdateNewsletter(true, $this->admin->sGetUserMailById(), true);
            }
            $orderVariables = $this->saveOffer();
        }
        $this->View()->assign('sKUZOOffer', true);

        $this->View()->assign($orderVariables->getArrayCopy());
    }

    /**
     * @param array $addressA
     * @param array $addressB
     * @return bool
     */
    private function areAddressesEqual(array $addressA, array $addressB)
    {
        $unset = ['id', 'customernumber', 'phone', 'ustid'];
        foreach ($unset as $key) {
            unset($addressA[$key], $addressB[$key]);
        }

        return count(array_diff($addressA, $addressB)) == 0;
    }

    /**
     * Get complete user-data as an array to use in view
     *
     * @return array
     */
    public function getUserData()
    {
        $system = Shopware()->System();
        $userData = $this->admin->sGetUserData();
        if (!empty($userData['additional']['countryShipping'])) {
            $sTaxFree = false;
            if (!empty($userData['additional']['countryShipping']['taxfree'])) {
                $sTaxFree = true;
            } elseif (
                !empty($userData['additional']['countryShipping']['taxfree_ustid'])
                && !empty($userData['billingaddress']['ustid'])
                && $userData['additional']['country']['id'] == $userData['additional']['countryShipping']['id']
            ) {
                $sTaxFree = true;
            }

            $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow("
                SELECT * FROM s_core_customergroups
                WHERE groupkey = ?
            ", array($system->sUSERGROUP));

            if (!empty($sTaxFree)) {
                $system->sUSERGROUPDATA['tax'] = 0;
                $system->sCONFIG['sARTICLESOUTPUTNETTO'] = 1; //Old template
                Shopware()->Session()->sUserGroupData = $system->sUSERGROUPDATA;
                $userData['additional']['charge_vat'] = false;
                $userData['additional']['show_net'] = false;
                Shopware()->Session()->sOutputNet = true;
            } else {
                $userData['additional']['charge_vat'] = true;
                $userData['additional']['show_net'] = empty($system->sUSERGROUPDATA['tax']);
                Shopware()->Session()->sOutputNet = empty($system->sUSERGROUPDATA['tax']);
            }
        }

        return $userData;
    }

    /**
     * Create temporary order in s_order_basket on confirm page
     * Used to track failed / aborted orders
     */
    public function saveTemporaryOrder()
    {
        $order = Shopware()->Modules()->Order();

        $order->sUserData = $this->View()->sUserData;
        $order->sComment = isset($this->session['sComment']) ? $this->session['sComment'] : '';
        $order->sBasketData = $this->View()->sBasket;
        $order->sAmount = $this->View()->sBasket['sAmount'];
        $order->sAmountWithTax = !empty($this->View()->sBasket['AmountWithTaxNumeric']) ? $this->View()->sBasket['AmountWithTaxNumeric'] : $this->View()->sBasket['AmountNumeric'];
        $order->sAmountNet = $this->View()->sBasket['AmountNetNumeric'];
        $order->sShippingcosts = $this->View()->sBasket['sShippingcosts'];
        $order->sShippingcostsNumeric = $this->View()->sBasket['sShippingcostsWithTax'];
        $order->sShippingcostsNumericNet = $this->View()->sBasket['sShippingcostsNet'];
        $order->bookingId = Shopware()->System()->_POST['sBooking'];
        $order->dispatchId = $this->session['sDispatch'];
        $order->sNet = $this->View()->sUserData['additional']['show_net'];

        $order->sDeleteTemporaryOrder();	// Delete previous temporary orders
        $order->sCreateTemporaryOrder();	// Create new temporary order
    }

    /**
     * This function saves offer from frontend.
     */
    public function saveOffer()
    {
        // Insert basic-data of the order
        $offerNumber = $this->getOfferNumber();
        $subshopId = 1;
        if($this->View()->sDispatch["id"]) {
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.1")){
                //$subshopId = Shopware()->Shop()->getId();
                //updated
                $shop = Shopware()->Shop();
                $mainShop = $shop->getMain() !== null ? $shop->getMain() : $shop;
                $subshopId = $mainShop->getId();
            } else {
                $shop = Shopware()->Shop();
                $subshopId = $this->admin->sSYSTEM->sSubShop["id"];
            }


            if($shop->getId())
                $language = $shop->getId();
            else
                $language =$subshopId;

            $factor = Shopware()->Shop()->getCurrency()->getFactor();
            $currency = Shopware()->Shop()->getCurrency()->getCurrency();

            // add internal comment for custom Products
            $internalComment = "";
            foreach ($this->View()->sBasket["content"] as $key => $basketRow) {
                if(!$basketRow['articleDetailId'] && $basketRow['articleID'] && $basketRow['articlename']){
                       foreach ($this->View()->sBasket["content"] as $skey => $sBasketRow) {
                            if($sBasketRow['id'] == $basketRow['articleID']){
                                $internalComment = $internalComment." ".$sBasketRow['articlename']." (".$sBasketRow['ordernumber'].")".": - ".$basketRow['articlename'];
                            }
                        }
                }
            }

            $basket = $this->View()->sBasket;

            if(isset($basket['sAmountWithTax'])) {
                // net
                //$price = round(str_replace(',', '.', $basket['AmountWithTax']), 2);
                $price = str_replace(',', '.', $basket['AmountWithTax']);
                $amount = round(floatval($basket["sAmountWithTax"]/$factor),2);
            } else {
                //$price = round(str_replace(',', '.', $basket['Amount']), 2);
                $price = str_replace(',', '.', $basket['Amount']);
                $amount = round(floatval($basket["sAmount"]/$factor),2);
            }
            //$shipping = round((floatval($basket['sShippingcostsWithTax'])/$factor),2);
            //$shippingNet = round((floatval($basket['sShippingcostsNet'])/$factor),2);
            //$amountNet = round(floatval($basket["AmountNetNumeric"]/$factor),2) - $shippingNet;

            $shipping = floatval($basket['sShippingcostsWithTax'])/$factor;
            $shippingNet = floatval($basket['sShippingcostsNet'])/$factor;
            $amountNet = floatval($basket["AmountNetNumeric"]/$factor) - $shippingNet;

            //$price = ( floatval($this->View()->sBasket["sAmountWithTax"])-floatval($this->View()->sBasket['sShippingcostsWithTax']) )/$factor;
            $sql = "
            INSERT INTO s_offer (
                offerNumber,offertime, invoice_amount, discount_amount, discount_amount_net,invoice_amount_net,
                invoice_shipping,invoice_shipping_net, userID, paymentID,dispatchID,currency,
                subshopID,status,customerComment,internalComment,active,`language`) VALUES (".$offerNumber.",
                now(),
                ".$price.",
                ".$price.",
                ".$amountNet.",
                ".$amountNet.",
                ".$shipping.",
                ".$shippingNet.",
                ".$this->View()->sUserData["additional"]["user"]["id"].",
                ".$this->View()->sUserData["additional"]["user"]["paymentID"].",
                 ".$this->View()->sDispatch["id"].",
                '".$currency."',
                $subshopId,
                1,
                '".$this->session['sComment']."',
                '".$internalComment."',
                1,
                $language
            )
            ";

            try {
                $affectedRows = Shopware()->Db()->executeUpdate($sql);
                $offerID = Shopware()->Db()->lastInsertId();
            } catch (Exception $e) {
                throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} :" . $e->getMessage(), 0, $e);
            }
            if (!$affectedRows || !$offerID) {
                throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} : No rows affected or no offer id created.", 0);
            }

            $position = 0;
            foreach ($this->View()->sBasket["content"] as $key => $basketRow) {
                $position++;
                if($basketRow["articleDetailId"]||(isset($basketRow["customProductMode"])&&\ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManagerInterface::MODE_OPTION)||(isset($basketRow["customProductMode"])&&\ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManagerInterface::MODE_VALUE)){
                    $basketRow = $this->formatBasketRow($basketRow);


                    $customCount = count($basketRow['customizing']['values']);
                    $netprice = floatval($basketRow["netprice"]);

                    for ($x = 0; $x <= $customCount; $x++) {
                        $basketRow = $this->View()->sBasket["content"][$key+$x];
                        $basketRow = $this->formatBasketRow($basketRow);
                        if(!$basketRow["articleDetailId"]){
                            $basketRow["articleDetailId"] = $this->View()->sBasket["content"][$key]["articleID"];
                            if($basketRow["customProductMode"]==\ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManagerInterface::MODE_OPTION) {
                                $basketRow["articleDetailId"] = -2;
                            }
                            if($basketRow["customProductMode"]==\ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManagerInterface::MODE_VALUE) {
                                $basketRow["articleDetailId"] = -3;
                            }
                            $numberOfCustomPackage = $this->View()->sBasket["content"][$key]["quantity"];
                        }else{
                            $numberOfCustomPackage = 1;
                        }

                        $basketRow["netprice"] = $netprice;
/*
                        if(isset($basketRow["custom_product_prices"]) && !empty($basketRow["custom_product_prices"])) {
                            $basketRow["netprice"] = $basketRow["custom_product_prices"]["customProduct"]/(100+$basketRow["tax_rate"])*100;
                        }
*/
                        $basketRow["netprice"] = $basketRow["netprice"]/$factor;


                        for ($y = 1; $y <= $numberOfCustomPackage; $y++) {
                            if($basketRow['modus'] == 4){
                                $basketRow['quantityId'] = $y;
                            }
                            //$priceWithTax = $basketRow["priceNumeric"] + $basketRow['tax'];

                            $sql = "
                        INSERT INTO s_offer_details
                            (offerID,
                            articleDetailsID,
                            taxID,
                            tax_rate,
                            offerNumber,
                            articleoffernumber,
                            originalPrice,
                            price,
                            quantity,
                            name,
                            modus,
                            quantityID,
                            swagCustomProductsMode,
                            swagCustomProductsConfigurationHash                            
                           )
                            VALUES (
                            $offerID,
                            {$basketRow["articleDetailId"]},
                             {$basketRow["taxID"]},
                            {$basketRow["tax_rate"]},
                            '$offerNumber',
                            '{$basketRow["ordernumber"]}',
                            {$basketRow["netprice"]},
                            {$basketRow["netprice"]},
                            {$basketRow["quantity"]},
                            '" . addslashes($basketRow["articlename"]) . "',
                            {$basketRow["modus"]},
                            {$basketRow["quantityId"]},
                            '$basketRow[customProductMode]',
                            '".$basketRow[customProductHash]."'
                            )";

                            try {
                                $affectedRows = Shopware()->Db()->executeUpdate($sql);
                            } catch (Exception $e) {
                                throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} :" . $e->getMessage(), 0, $e);
                            }
                            if (!$affectedRows) {
                                throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} : No rows affected.", 0);
                            }
                        }
                    }
                }
            } // For every article in basket

            // Save Billing and Shipping-Address to retrace in future
            $this->saveOfferBillingAddress($this->View()->sUserData["billingaddress"], $basketRow["tax_rate"], $offerID);
            $this->saveOfferShippingAddress($this->View()->sUserData["shippingaddress"], $offerID);
            $net=0;
			if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
                $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($this->View()->sUserData["additional"]["user"]["id"]);
                if($customer instanceof \Shopware\Models\Customer\Customer) {
                    if ($customer->getGroup()->getTax()==0) {
                        $net = "1";
                    }
                }
			    $shippingCost = floatval($shipping);
                if($net==1) {
                    $shippingCost = floatval($shippingNet);
                }
            	$this->sendMailToAdmin($offerNumber,$shippingCost,$this->View()->sBasket["sAmountWithTax"],$amountNet+$shippingNet);
			}
            $order = Shopware()->Modules()->Order();
            $order->sDeleteTemporaryOrder();
            $this->sDeleteBasketOrder();
        }else{
            $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'sKUZOOffer',
                'action' => 'confirm',
            ));
        }
            if (Shopware()->Session()->offsetExists('sOrderVariables')) {
                $variables = Shopware()->Session()->offsetGet('sOrderVariables');
                unset(Shopware()->Session()->sOrderVariables);

            }
            return $variables;


    }

    /**
     * This function delete basket temperory orders after asking offer from frontend.
     */
    public function sDeleteBasketOrder()
    {
        $sessionId = Shopware()->Session()->get('sessionId');

        if (empty($sessionId)) return;

        $deleteWholeOrder = Shopware()->Db()->fetchAll("
        SELECT * FROM s_order_basket WHERE sessionID = ?
        ",array($sessionId));

        foreach ($deleteWholeOrder as $orderDelete) {
            Shopware()->Db()->executeUpdate("
            DELETE FROM s_order_basket WHERE id = ?
            ",array($orderDelete["id"]));
        }
    }

    /**
     * This function saves or updates billing address for offer while asking for offer from frontend.
     *
     * @param $address
     * @param $tax_rate
     * @param $id
     * @return int
     * @throws Enlight_Exception
     */
    public function saveOfferBillingAddress($address, $tax_rate, $id)
    {
        if($address['stateID'] == null)
        {
            $address['stateID']='0';
        }
        $checkForExistingDocument = Shopware()->Db()->fetchRow("SELECT id FROM s_offer_billingaddress WHERE offerID = ?",array($id));

        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
            $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($address["userID"]);
            if($customer instanceof \Shopware\Models\Customer\Customer) {
                $address['customernumber'] = $customer->getNumber();
            }
        }

        if (!empty($checkForExistingDocument["id"])) {

            $sql = "
        UPDATE s_offer_billingaddress SET
            userID = ?,
            customernumber = ?,
            company = ?,
            department = ?,
            salutation = ?,
            firstname = ?,
            lastname = ?,
            street = ?,
            zipcode = ?,
            city = ?,
            phone = ?,
            fax = ?,
            countryID = ?,
            stateID = ?,
            ustid = ?,
            shipping_tax = ?
             WHERE offerID = ?
        ";

			$street = $address["street"];
            $fax = $address["fax"];
            $phone = $address["phone"];
            $ustid = $address["ustid"];
            $department = $address["department"];
            $company = $address["company"];
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$street = $address["street"]." ".$address["streetnumber"];
			}
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                $fax = "";
                if(!isset($phone) || empty($phone)) {
                    $phone = "";
                }
                if(!isset($ustid) || empty($ustid)) {
                    $ustid = "";
                }
                if(!isset($department) || empty($department)) {
                    $department = "";
                }
                if(!isset($company) || empty($company)) {
                    $company = "";
                }
            }
            $array = array(
                $address["userID"],
                $address["customernumber"],
                $company,
                $department,
                $address["salutation"],
                $address["firstname"],
                $address["lastname"],
                $street,
                $address["zipcode"],
                $address["city"],
                $phone,
                $fax,
                $address["countryID"],
                $address["stateID"],
                $ustid,
                $tax_rate,
                $id
            );
        }
        else{
            $sql = "
        INSERT INTO s_offer_billingaddress
        (
            userID,
            offerID,
            customernumber,
            company,
            department,
            salutation,
            firstname,
            lastname,
            street,
            zipcode,
            city,
            phone,
            fax,
            countryID,
            stateID,
            ustid,
            shipping_tax
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
            )
        ";
			$street = $address["street"];
            $fax = $address["fax"];
            $phone = $address["phone"];
            $ustid = $address["ustid"];
            $department = $address["department"];
            $company = $address["company"];
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$street = $address["street"]." ".$address["streetnumber"];
			}
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                $fax = "";
                if(!isset($phone) || empty($phone)) {
                    $phone = "";
                }
                if(!isset($ustid) || empty($ustid)) {
                    $ustid = "";
                }
                if(!isset($department) || empty($department)) {
                    $department = "";
                }
                if(!isset($company) || empty($company)) {
                    $company = "";
                }
            }

            $array = array(
                $address["userID"],
                $id,
                $address["customernumber"],
                $company,
                $department,
                $address["salutation"],
                $address["firstname"],
                $address["lastname"],
                $street,
                $address["zipcode"],
                $address["city"],
                $phone,
                $fax,
                $address["countryID"],
                $address["stateID"],
                $ustid,
                $tax_rate
            );

        }
        $result = Shopware()->Db()->executeUpdate($sql,$array);
        if (!$result) {
            throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} : No row affected in s_offer_billingaddress.", 0);
        }
        return $result;
    }


    /**
     * This function saves or updates shipping address for offer while asking for offer from frontend.
     *
     * @param $address
     * @param $id
     * @return int
     * @throws Enlight_Exception
     */
    public function saveOfferShippingAddress($address, $id)
    {
        if($address['stateID'] == null)
        {
            $address['stateID']='0';
        }
        $checkForExistingDocument = Shopware()->Db()->fetchRow("SELECT id FROM s_offer_shippingaddress WHERE offerID = ?",array($id));

        if (!empty($checkForExistingDocument["id"])) {

            $sql = "
        UPDATE s_offer_shippingaddress SET
            userID = ?,
            company = ?,
            department = ?,
            salutation = ?,
            firstname = ?,
            lastname = ?,
            street = ?,
            zipcode = ?,
            city = ?,
            countryID = ?,
            stateID = ?
             WHERE offerID = ?
        ";
			$street = $address["street"];
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$street = $address["street"]." ".$address["streetnumber"];
			}
            $department = $address["department"];
            $company = $address["company"];
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                if(!isset($department) || empty($department)) {
                    $department = "";
                }
                if(!isset($company) || empty($company)) {
                    $company = "";
                }
            }

            $array = array(
                $address["userID"],
                $company,
                $department,
                $address["salutation"],
                $address["firstname"],
                $address["lastname"],
                $street,
                $address["zipcode"],
                $address["city"],
                $address["countryID"],
                $address["stateID"],
                $id
            );
        }
        else{
            $sql = "
        INSERT INTO s_offer_shippingaddress
        (
            userID,
            offerID,
            company,
            department,
            salutation,
            firstname,
            lastname,
            street,
            zipcode,
            city,
            countryID,
            stateID
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
        ";
			
			$street = $address["street"];
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$street = $address["street"]." ".$address["streetnumber"];
			}
            $department = $address["department"];
            $company = $address["company"];
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                if(!isset($department) || empty($department)) {
                    $department = "";
                }
                if(!isset($company) || empty($company)) {
                    $company = "";
                }
            }
            $array = array(
                $address["userID"],
                $id,
                $company,
                $department,
                $address["salutation"],
                $address["firstname"],
                $address["lastname"],
                $street,
                $address["zipcode"],
                $address["city"],
                $address["countryID"],
                $address["stateID"]
            );

        }
        $result = Shopware()->Db()->executeUpdate($sql,$array);
        if (!$result) {
            throw new Enlight_Exception("Shopware Offer Fatal-Error {$_SERVER["HTTP_HOST"]} : No row affected in s_offer_shippingaddress.", 0);
        }
        return $result;
    }

    /**
     * This function returns offerNumber for new offer created from frontend.
     *
     * @return int|string
     */
    public function getOfferNumber()
    {
        $number = Shopware()->Db()->fetchOne(
            "/*NO LIMIT*/ SELECT number FROM s_order_number WHERE name='offer' FOR UPDATE"
        );
        Shopware()->Db()->executeUpdate(
            "UPDATE s_order_number SET number = number + 1 WHERE name='offer'"
        );
        $number += 1;
        return $number;
    }

    /**
     * This function returns formated basket data.
     *
     * @param $basketRow
     * @return mixed
     */
    private function formatBasketRow($basketRow)
    {
        $basketRow["articlename"] = str_replace("<br />","\n",$basketRow["articlename"]);
        $basketRow["articlename"] = html_entity_decode($basketRow["articlename"]);
        $basketRow["articlename"] = strip_tags($basketRow["articlename"]);
        $basketRow["articlename"] = Shopware()->Modules()->Articles()->sOptimizeText(
            $basketRow["articlename"]
        );
        /*if(!$basketRow["articleDetailId"]){
            $basketRow["articleDetailId"] =$basketRow["articleID"];
        }*/
        if (!$basketRow["price"]) $basketRow["price"] = "0,00";
        if (!$basketRow["esdarticle"]) $basketRow["esdarticle"] = "0";
        if (!$basketRow["modus"]) $basketRow["modus"] = "0";
        if (!$basketRow["taxID"]) $basketRow["taxID"] = "0";
        if(!$basketRow['quantityId']){
            $basketRow['quantityId'] = 0;
        }
        return $basketRow;
    }

    /**
     * Return complete basket data to view
     * Basket items / Shippingcosts / Amounts / Tax-Rates
     *
     * @return array
     */
    public function getBasket()
    {
        $shippingcosts = $this->getShippingCosts();
        $basket = $this->basket->sGetBasket();

        $basket['sShippingcostsWithTax'] = $shippingcosts['brutto'];
        $basket['sShippingcostsNet'] = $shippingcosts['netto'];
        $basket['sShippingcostsTax'] = $shippingcosts['tax'];

        if (!empty($shippingcosts['brutto'])) {
            $basket['AmountNetNumeric'] += $shippingcosts['netto'];
            $basket['AmountNumeric'] += $shippingcosts['brutto'];
            $basket['sShippingcostsDifference'] = $shippingcosts['difference']['float'];
        }
        if (!empty($basket['AmountWithTaxNumeric'])) {
            $basket['AmountWithTaxNumeric'] += $shippingcosts['brutto'];
        }
        if ((!Shopware()->System()->sUSERGROUPDATA['tax'] && Shopware()->System()->sUSERGROUPDATA['id'])) {
            $basket['sTaxRates'] = $this->getTaxRates($basket);

            $basket['sShippingcosts'] = $shippingcosts['netto'];
            $basket['sAmount'] = round($basket['AmountNetNumeric'], 2);
            $basket['sAmountTax'] = round($basket['AmountWithTaxNumeric'] - $basket['AmountNetNumeric'], 2);
            $basket['sAmountWithTax'] = round($basket['AmountWithTaxNumeric'], 2);

        } else {
            $basket['sTaxRates'] = $this->getTaxRates($basket);

            $basket['sShippingcosts'] = $shippingcosts['brutto'];
            $basket['sAmount'] = $basket['AmountNumeric'];

            $basket['sAmountTax'] = round($basket['AmountNumeric'] - $basket['AmountNetNumeric'], 2);
        }
        return $basket;
    }


    /**
     * Return complete offer basket data to view
     * Basket items / Shippingcosts / Amounts / Tax-Rates
     * @param \Shopware\CustomModels\Offer\Offer $offer
     * @return mixed
     */

    public function getOfferBasket(\Shopware\CustomModels\Offer\Offer $offer, $addTax=true)
    {
        $DiscountAmountNet = 0;
        $offerDetails = Shopware()->Models()->toArray($offer->getDetails());
        $basket['Quantity'] = count($offerDetails);
        $basket['sCurrencyName'] = $offer->getCurrency();
        $basket['sCurrencyId'] = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency')->findOneBy(array('currency' => $offer->getCurrency()));
        foreach ($offerDetails as &$offerDetail) {
            $offerDetail['netPrice'] = $offerDetail['price'];
            $offerDetail['netprice'] = $offerDetail['netPrice'];
            $offerDetail['price'] = $this->getPriceWithTax($offerDetail['price'],$offerDetail['taxRate'], $addTax);
            $offerDetail['tax'] = $this->moneyFormat($offerDetail['price']-$offerDetail['netprice'],'');
            $offerDetail['priceNumeric'] = $offerDetail['price'];

            $offerDetail['articlename'] = $offerDetail['articleName'];
            $offerDetail['ordernumber'] = $offerDetail['articleNumber'];
            $offerDetail['amount'] = $offerDetail['price'] * $offerDetail['quantity'];
            $offerDetail['amount'] = $this->moneyFormat($offerDetail['amount'],'');
            $offerDetail['amountnet'] = $offerDetail['netprice'] * $offerDetail['quantity'];
            $offerDetail['priceNumeric'] = $offerDetail['price'];
            $offerDetail['tax_rate'] = $offerDetail['taxRate'];
            $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $offerDetail['articleDetailsId']));
            if($articleDetail instanceof \Shopware\Models\Article\Detail) {
                if (Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")) {
                    $articleId = $articleDetail->getArticleId();
                } else {
                    $articleId = $articleDetail->getArticle()->getId();
                }
                $offerDetail['shippingfree'] = $articleId;
                $offerDetail['modus'] = $articleDetail->getArticle()->getMode();
                if(!isset($offerDetail['modus']) || empty($offerDetail['modus'])) {
                    $offerDetail['modus'] = $offerDetail['mode'];
                }
                if(!isset($offerDetail['modus']) || empty($offerDetail['modus'])) {
                    $offerDetail['modus'] = 0;
                }
                $offerDetail['articleID'] = $articleId;
                $offerDetail['mainDetailId'] = $articleDetail->getArticle()->getMainDetail()->getId();
                $offerDetail['articleDetailsId'] = $articleDetail->getId();
                $offerDetail['minpurchase'] = $articleDetail->getMinPurchase();
                $offerDetail['maxpurchase'] = $articleDetail->getMaxPurchase();
                $offerDetail['instock'] = $articleDetail->getInStock();
                $offerDetail['stockmin'] = $articleDetail->getStockMin();
                $offerDetail['packuinit'] = $articleDetail->getPackUnit();
                $offerDetail['taxId'] = $articleDetail->getArticle()->getTax()->getId();

                $offerDetail['image'] = Shopware()->Modules()->Articles()->sGetArticlePictures($articleId);
            } else {
                $offerDetail['articleID'] = 0;
                $offerDetail['modus'] = $offerDetail['mode'];
            }
            $tempArticle = $this->moduleManager->Articles()->sGetProductByOrdernumber($offerDetail['articleNumber']);

            if (empty($tempArticle)) {
                $offerDetail["additional_details"] = array("properties" => array());
            } else {
                $offerDetail['additional_details'] = $tempArticle;
                $properties = '';
                foreach ($offerDetail['additional_details']['sProperties'] as $property) {
                    $properties .= $property['name'] . ':&nbsp;' . $property['value'] . ',&nbsp;';
                }
                $offerDetail['additional_details']['properties'] = substr($properties, 0, -7);
            }

            $this->db->insert(
                's_order_basket',
                array(
                    'sessionID' => $this->session->get('sessionId'),
                    'articlename' => trim($offerDetail["articleName"] . " " . $offerDetail["additionaltext"]),
                    'articleID' => $offerDetail['articleID'],
                    'ordernumber' => $offerDetail['ordernumber'],
                    'quantity' => $offerDetail['quantity'],
                    'price' => $offerDetail['price'],
                    'netprice' => $offerDetail['price']*$offerDetail['quantity'],
                    'tax_rate' => $offerDetail['tax_rate'],
                    'datum' => new Zend_Date(),
                    'modus' => $offerDetail['modus'],
                    'currencyFactor' => 1
                )
            );

            $basketID = Shopware()->Db()->lastInsertId();
            if(isset($offerDetail['swagCustomProductsConfigurationHash']) && !empty($offerDetail['swagCustomProductsConfigurationHash'])) {
                $attributeData = array(
                    'swag_custom_products_configuration_hash' => $offerDetail['swagCustomProductsConfigurationHash'],
                    'swag_custom_products_once_price' => $offerDetail['netPrice'],
                    'swag_custom_products_mode' => $offerDetail['swagCustomProductsMode']
                );

                Shopware()->Container()->get('shopware_attribute.data_persister')->persist($attributeData, 's_order_basket_attributes', $basketID);
/*
                $this->db->insert('s_order_basket_attributes', array(
                        'basketID' => $basketID,
                        'swag_custom_products_configuration_hash' => $offerDetail['swagCustomProductsConfigurationHash'],
                        'swag_custom_products_once_price' => $offerDetail['netPrice'],
                        'swag_custom_products_mode' => $offerDetail['swagCustomProductsMode']
                    ));
*/
            }
            $offerDetail['id'] = $basketID;
            $DiscountAmountNet += $offerDetail['netPrice']*$offerDetail['quantity'];
        }

        $basket['content'] = $offerDetails;
        $shippingTax = $offer->getOfferBilling()->getShippingTax();
        $basket['sShippingcostsWithTax'] = $offer->getInvoiceShipping();
        $basket['sShippingcostsNet'] = $offer->getInvoiceShippingNet();
        $basket['sShippingcostsTax'] = $shippingTax;

        //if (!empty($basket['sShippingcostsWithTax'])) {
            $basket['AmountNumeric'] = $offer->getDiscountAmount() + $offer->getInvoiceShipping();
            $basket['AmountNetNumeric'] = $DiscountAmountNet + $offer->getInvoiceShippingNet();
        //}
        if (!empty($basket['AmountWithTaxNumeric'])) {
            $basket['AmountWithTaxNumeric'] += $offer->getInvoiceShipping();
        }

        $Taxs = Shopware()->Models()->getRepository( 'Shopware\Models\Tax\Tax');
        $Taxs = $Taxs->findAll();
        foreach ($Taxs as $tax) {
            $taxCost[(string)$tax->getTax()] = 0;
        }
        $totalTax = 0;
        foreach ($offerDetails as &$position) {
            foreach ($Taxs as $tax) {
                if($position['taxRate']== $tax->getTax()){
                    $positionTax = $this->getTaxAmount($position['netPrice'],$position['taxRate'])*$position['quantity'];
                    $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + $positionTax;
                    $totalTax = $totalTax + $positionTax;
                }
            }
        }
        foreach ($Taxs as $tax) {
            if($shippingTax == $tax->getTax()){
                $shippingTaxAmount = $offer->getInvoiceShipping() - $offer->getInvoiceShippingNet();
                $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + ($shippingTaxAmount);
                $totalTax = $totalTax + $shippingTaxAmount;
            }
            if($taxCost[(string)$tax->getTax()] != 0){
                $taxRates[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()];
            }
        }

        $basket['sTaxRates'] = $taxRates;

        $basket['sShippingcosts'] = $offer->getInvoiceShipping();
        $basket['sAmountTax'] = $basket['AmountNumeric']-$basket['AmountNetNumeric'];
        //$basket['sAmountTax'] = $offer->getDiscountAmount() - $DiscountAmountNet;//round($offer->getInvoiceAmountNet() - $offer->getDiscountAmount(), 2);
        $basket['sAmount'] = round($basket['AmountNumeric'],2);
        //$basket['sAmount'] = round($DiscountAmountNet, 2);
        $basket['sAmountWithTax'] = null;
        //TODO check if net offer
        //$basket['sAmountWithTax'] = round($offer->getDiscountAmount()+$offer->getInvoiceShipping(), 2);
        //without shipping costs
        $basket['Amount'] = $this->moneyFormat($offer->getDiscountAmount(),'');
        //without shipping costs
        $basket['AmountNet'] = $this->moneyFormat($DiscountAmountNet,'');
        return $basket;
    }

    private function moneyFormat($value, $currency="EUR", $replaceComma=true) {
        if($replaceComma) {
            return trim(str_replace(".", ",", sprintf('%02.2f ' . $currency, $value)));
        } else {
            return trim(sprintf('%02.2f ' . $currency, $value));
        }
    }

    /**
     * This function return tax amount
     *
     * @param $price
     * @param $tax
     * @return float
     */
    public function  getTaxAmount($price, $tax)
    {
        return ($price * $tax )/100;
    }


    /**
     * This function returns tax rates for all basket positions
     *
     * @param unknown_type $basket array returned from this->getBasket
     * @return array
     */
    public function getTaxRates($basket)
    {
        $result = array();

        if (!empty($basket['sShippingcostsTax'])) {
            $basket['sShippingcostsTax'] = number_format(floatval($basket['sShippingcostsTax']),2);

            $result[$basket['sShippingcostsTax']] = $basket['sShippingcostsWithTax']-$basket['sShippingcostsNet'];
            if (empty($result[$basket['sShippingcostsTax']])) unset($result[$basket['sShippingcostsTax']]);
        } elseif ($basket['sShippingcostsWithTax']) {
            $result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')),2)] = $basket['sShippingcostsWithTax']-$basket['sShippingcostsNet'];
            if (empty($result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')),2)])) unset($result[number_format(floatval(Shopware()->Config()->get('sTAXSHIPPING')),2)]);
        }


        if (empty($basket['content'])) {
            ksort($result, SORT_NUMERIC);
            return $result;
        }

        foreach ($basket['content'] as $item) {

            if (!empty($item["tax_rate"])) {

            } elseif (!empty($item['taxPercent'])) {
                $item['tax_rate'] = $item["taxPercent"];
            } elseif ($item['modus'] == 2) {
                // Ticket 4842 - dynamic tax-rates
                $resultVoucherTaxMode = Shopware()->Db()->fetchOne(
                    "SELECT taxconfig FROM s_emarketing_vouchers WHERE ordercode=?
                ", array($item["ordernumber"]));
                // Old behaviour
                if (empty($resultVoucherTaxMode) || $resultVoucherTaxMode == "default") {
                    $tax = Shopware()->Config()->get('sVOUCHERTAX');
                } elseif ($resultVoucherTaxMode == "auto") {
                    // Automatically determinate tax
                    $tax = $this->basket->getMaxTax();
                } elseif ($resultVoucherTaxMode == "none") {
                    // No tax
                    $tax = "0";
                } elseif (intval($resultVoucherTaxMode)) {
                    // Fix defined tax
                    $tax = Shopware()->Db()->fetchOne("
                    SELECT tax FROM s_core_tax WHERE id = ?
                    ", array($resultVoucherTaxMode));
                }
                $item['tax_rate'] = $tax;
            } else {
                // Ticket 4842 - dynamic tax-rates
                $taxAutoMode = Shopware()->Config()->get('sTAXAUTOMODE');
                if (!empty($taxAutoMode)) {
                    $tax = $this->basket->getMaxTax();
                } else {
                    $tax = Shopware()->Config()->get('sDISCOUNTTAX');
                }
                $item['tax_rate'] = $tax;
            }

            if (empty($item['tax_rate']) || empty($item["tax"])) continue; // Ignore 0 % tax

            $taxKey = number_format(floatval($item['tax_rate']), 2);

            $result[$taxKey] += str_replace(',', '.', $item['tax']);
        }

        ksort($result, SORT_NUMERIC);

        return $result;
    }

    /**
     * This function get configured minimum charge to check in order processing
     *
     * @return bool
     */
    public function getMinimumCharge()
    {
        return $this->basket->sCheckMinimumCharge();
    }

    /**
     * This function check if order is possible under current conditions (dispatch)
     *
     * @return bool
     */
    public function getDispatchNoOrder()
    {
        return !empty(Shopware()->Config()->PremiumShippingNoOrder) && (empty($this->session['sDispatch']) || empty($this->session['sCountry']));
    }

    /**
     * This function get all premium products that are configured and available for this order
     *
     * @return array
     */
    public function getPremiums()
    {
        $sql = 'SELECT `id` FROM `s_order_basket` WHERE `sessionID`=? AND `modus`=1';
        $result = Shopware()->Db()->fetchOne($sql, array(Shopware()->SessionID()));
        if(!empty($result)) return array();
        return Shopware()->Modules()->Marketing()->sGetPremiums();
    }

    /**
     * This function check if any electronically distribution product is in basket
     *
     * @return boolean
     */
    public function getEsdNote()
    {
        $payment = empty($this->View()->sUserData['additional']['payment']) ? $this->session['sOrderVariables']['sUserData']['additional']['payment'] : $this->View()->sUserData['additional']['payment'];
        return $this->basket->sCheckForESD() && !$payment['esdactive'];
    }

    /**
     * This function returns array of country list.
     *
     * @return array
     */
    public function getCountryList()
    {
        return $this->admin->sGetCountryList();
    }

    /**
     * This function get all dispatches available in selected country from sAdmin object
     *
     * @return array list of dispatches
     */
    public function getDispatches()
    {
        $country = $this->getSelectedCountry();
        $state = $this->getSelectedState();
        if (empty($country)) {
            return false;
        }
        $stateId = !empty($state['id']) ? $state['id'] : null;
        return $this->admin->sGetPremiumDispatches($country['id'], null, $stateId);
    }

    /**
     * This function returns all available payment methods from sAdmin object
     *
     * @return array list of payment methods
     */
    public function getPayments()
    {
        return $this->admin->sGetPaymentMeans();
    }

    /**
     * This function get current selected country - if no country is selected, choose first one from list
     * of available countries
     *
     * @return array with country information
     */
    public function getSelectedCountry()
    {
        if (!empty($this->View()->sUserData['additional']['countryShipping'])) {
            $this->session['sCountry'] = (int) $this->View()->sUserData['additional']['countryShipping']['id'];
            $this->session['sArea'] = (int) $this->View()->sUserData['additional']['countryShipping']['areaID'];

            return $this->View()->sUserData['additional']['countryShipping'];
        }
        $countries = $this->getCountryList();
        if (empty($countries)) {
            unset($this->session['sCountry']);
            return false;
        }
        $country = reset($countries);
        $this->session['sCountry'] = (int) $country['id'];
        $this->session['sArea'] = (int) $country['areaID'];
        $this->View()->sUserData['additional']['countryShipping'] = $country;
        return $country;
    }

    /**
     * This function get current selected country - if no country is selected, choose first one from list
     * of available countries
     *
     * @return array with country information
     */
    public function getSelectedState()
    {
        if (!empty($this->View()->sUserData['additional']['stateShipping'])) {
            $this->session['sState'] = (int) $this->View()->sUserData['additional']['stateShipping']['id'];
            return $this->View()->sUserData['additional']['stateShipping'];
        }
        return array("id" => $this->session['sState']);
    }

    /**
     * This function get selected payment or do payment mean selection automatically
     *
     * @return array
     */
    public function getSelectedPayment()
    {
        if (!empty($this->View()->sUserData['additional']['payment'])) {
            $payment = $this->View()->sUserData['additional']['payment'];
        } elseif (!empty($this->session['sPaymentID'])) {
            $payment = $this->admin->sGetPaymentMeanById($this->session['sPaymentID'], $this->View()->sUserData);
        }

        $paymentClass = $this->admin->sInitiatePaymentClass($payment);
        if ($payment && $paymentClass instanceof \ShopwarePlugin\PaymentMethods\Components\BasePaymentMethod) {
            $data = $paymentClass->getCurrentPaymentDataAsArray(Shopware()->Session()->sUserId);
            if (!empty($data)) {
                $payment['data'] = $data;
            }
        }

        if (!empty($payment)) {
            return $payment;
        }
        $payments = $this->getPayments();
        if (empty($payments)) {
            unset($this->session['sPaymentID']);
            return false;
        }
        $payment = reset($payments);
        $this->session['sPaymentID'] = (int) $payment['id'];
        return $payment;
    }

    /**
     * This function get selected dispatch or select a default dispatch
     *
     * @return boolean|array
     */
    public function getSelectedDispatch()
    {
        if (empty($this->session['sCountry'])) {
            return false;
        }

        $dispatches = $this->admin->sGetPremiumDispatches($this->session['sCountry'], null, $this->session['sState']);
        if (empty($dispatches)) {
            unset($this->session['sDispatch']);
            return false;
        }

        foreach ($dispatches as $dispatch) {
            if ($dispatch['id'] == $this->session['sDispatch']) {
                return $dispatch;
            }
        }
        $dispatch = reset($dispatches);
        $this->session['sDispatch'] = (int) $dispatch['id'];
        return $dispatch;
    }

    /**
     * This function returns shipping costs.
     *
     * @return array|false
     */
    public function getShippingCosts()
    {
        $country = $this->getSelectedCountry();
        $payment = $this->getSelectedPayment();
        if (empty($country) || empty($payment)) {
            return array('brutto'=>0, 'netto'=>0);
        }
        $shippingcosts = $this->admin->sGetPremiumShippingcosts($country);
        return empty($shippingcosts) ? array('brutto'=>0, 'netto'=>0) : $shippingcosts;
    }

    /**
     * This action fires when use asks for offer.
     */
    public function offersAction()
    {
        $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
        $this->View()->directPayment = $config->paymentOption;
        $destinationPage = (int)$this->Request()->sPage;
        $orderData = $this->sGetOpenOfferData($destinationPage);
        $this->View()->sOpenOrders = $orderData["orderData"];
        $this->View()->sNumberPages = $orderData["numberOfPages"];
        $this->View()->sPages = $orderData["pages"];

        //this has to be assigned here because the config method in smarty can't handle array structures
        $this->View()->sDownloadAvailablePaymentStatus = Shopware()->Config()->get('downloadAvailablePaymentStatus');
    }


    /**
     * This function returns basket data.
     *
     * @param int $destinationPage
     * @param int $perPage
     * @return mixed
     */
    public function sGetOpenOfferData($destinationPage = 1, $perPage = 10)
    {
        $shop = Shopware()->Shop();
        $mainShop = $shop->getMain() !== null ? $shop->getMain() : $shop;

        $destinationPage = !empty($destinationPage) ? $destinationPage : 1;
        $limitStart = Shopware()->Db()->quote(($destinationPage - 1) * $perPage);
        $limitEnd = Shopware()->Db()->quote($perPage);

        $this->checkSession();
        $user = $this->admin->sGetUserData();

        $sql = "
            SELECT *
            FROM s_offer
            WHERE userID = ? AND status != 1 AND active = 1
            AND subshopID = ?
            ORDER BY offertime DESC
            LIMIT $limitStart, $limitEnd
        ";
        $getOrders = $this->db->fetchAll(
            $sql,
            array(
                $user['additional']['user']['id'],
                $mainShop->getId()
            )
        );
        $foundOrdersCount = (int)Shopware()->Db()->fetchOne('SELECT count(id) From s_offer WHERE status != 1 AND active = 1');

        $user = $this->getUserData();

        foreach ($getOrders as $orderKey => $orderValue) {

            $sql = "
            SELECT *
            FROM s_offer_documents
            WHERE offerID = ? ";
            $documents = $this->db->fetchAll($sql,array($getOrders[$orderKey]["id"]));
            $getOrders[$orderKey]["documents"] = $documents;

            $sql = "
            SELECT *
            FROM s_offer_details
            WHERE offerID = ? ";
            $positions = $this->db->fetchAll($sql,array($getOrders[$orderKey]["id"]));
            $offerId = $getOrders[$orderKey]["id"];
            $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offerId);
            $offerBillingArray = Shopware()->Models()->toArray($offer->getOfferBilling());
            $shippingTax = $offerBillingArray['shippingTax'];

            // getting available tax percentages to calculating tax
            $Taxs = Shopware()->Models()->getRepository( 'Shopware\Models\Tax\Tax');
            $Taxs = $Taxs->findAll();
            foreach ($Taxs as $tax) {
                $taxCost[(string)$tax->getTax()] = 0;
            }
            $totalTax = 0;

            //get packUnit, purchaseUnit, referenceUnit and unitName of variant
            foreach ($positions as &$position) {
                if($position['modus'] != 4){
                    $variant = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $position['articleDetailsID']));
                    if($variant instanceof Shopware\Models\Article\Detail) {
                        if($variant->getUnit()) {
                            $variantUnitArray = Shopware()->Models()->toArray($variant->getUnit());
                            $position['unitName'] = $variantUnitArray['name'];
                        }
                        $position['netPrice'] = $position['price'];
                        $position['price'] = $this->getPriceWithTax($position['price'], $position['tax_rate']);
                        if($user['additional']['show_net']) {
                            $position['price'] = $position['netPrice'];
                        }

                        $position['referenceUnit'] = (int)$variant->getReferenceUnit();
                        $position['purchaseUnit'] = $variant->getPurchaseUnit();
                        $position['packUnit'] = $variant->getPackUnit();
                        $position['pricePerUnit'] = ($variant->getReferenceUnit() * $position['price']) / $position['purchaseUnit'];
                        $position['totalUnit'] = $position['quantity'] * $position['purchaseUnit'];

                        if($orderValue['status'] != 5){
                             // for custom products
                            $sql = "SELECT `group_id` FROM `s_plugin_customizing_articles` WHERE `article_id`=:articleId";
                            try {
								if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
		                            $isCustomProduct = Shopware()->Db()
		                                ->fetchCol($sql, array('articleId' => $variant->getArticleId()));
		                            if($isCustomProduct) {
		                                $position['customProduct'] = 1;
		                                $position['articleId'] = $variant->getArticleId();
		                                $position['customizing'] = array();
		                            }
								} else {
		                            $sql = "SELECT `group_id` FROM `s_plugin_customizing_articles` WHERE `article_id`=:articleId";
		                            $isCustomProduct = Shopware()->Db()
		                                ->fetchCol($sql, array('articleId' => $variant->getArticle()->getId()));
		                            if($isCustomProduct) {
		                                $position['customProduct'] = 1;
		                                $position['articleId'] = $variant->getArticle()->getId();
		                                $position['customizing'] = array();
		                            }
								}
                            } catch(Exception $e) {
                                $var = $e;
                            }
                        }

                    }
                }
                else {
                    foreach ($positions as $ck => $corePosition) {
                        if($corePosition['modus'] != 4){
                           if($corePosition['articleId'] == $position['articleDetailsID']){
                               array_push($positions[$ck]['customizing'], $position);
                           }
                        }
                    }
                }

                //calculation taxes for each position
                foreach ($Taxs as $tax) {
                    if ($position['tax_rate'] == $tax->getTax()) {
                        $positionTax = $this->getTaxAmount($position['netPrice'], $position['tax_rate']) * $position['quantity'];
                        $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + $positionTax;
                        $totalTax = $totalTax + $positionTax;
                    }
                }
            }
            $positionCount =0;
            $validPositionCount = 0;
            //check for validForAccept
            foreach ($positions as &$checkPosition) {
                if($checkPosition['customProduct']){
                    $positionCount++;
                    $checkCount = 0;
                    for($a=1; $a<=$checkPosition['quantity']; $a++){
                        foreach ($checkPosition['customizing'] as $customPosition) {
                            if($customPosition['quantityID'] == $a){
                                $checkCount++;
                                break;
                            }
                        }
                    }
                    if($checkCount == $checkPosition['quantity']){
                        $validPositionCount++;
                    }
                }
            }
           if($positionCount == $validPositionCount)
               $getOrders[$orderKey]['validForAccept']= true;
            else
                $getOrders[$orderKey]['validForAccept']= false;


            $taxRates = Array();
            foreach ($Taxs as $tax) {
                if ($shippingTax == $tax->getTax()) {
                    $shippingTaxAmount = $offer->getInvoiceShipping() - $offer->getInvoiceShippingNet();
                    $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + ($shippingTaxAmount);
                    $totalTax = $totalTax + $shippingTaxAmount;
                }
                if($taxCost[(string)$tax->getTax()] != 0){
                    $taxRates[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()];
                }
            }
            $getOrders[$orderKey]["details"] = $positions;
            $getOrders[$orderKey]["invoiceAmountNumeric"] = $getOrders[$orderKey]["invoice_amount"];
            $getOrders[$orderKey]["invoice_amount"] = $this->moduleManager->Articles()
                ->sFormatPrice($orderValue["invoice_amount"]);
            $getOrders[$orderKey]["invoiceShippingNumeric"] = $getOrders[$orderKey]["invoice_shipping"];
            $getOrders[$orderKey]["invoice_shipping"] = $this->moduleManager->Articles()
                ->sFormatPrice($orderValue["invoice_shipping"]);
            $getOrders[$orderKey]["dispatch"] = Shopware()->Modules()->Admin()->sGetPremiumDispatch($orderValue['dispatchID']);
            $getOrders[$orderKey]["payment"] = Shopware()->Modules()->Admin()->sGetPaymentMeanById($orderValue['paymentID']);
            $getOrders[$orderKey]["shippingTax"] = $shippingTax;
            $getOrders[$orderKey]["priceWithoutTax"] = $getOrders[$orderKey]["invoice_amount_net"] - $totalTax;
            $getOrders[$orderKey]["taxRate"] = $taxRates;
            $getOrders[$orderKey]["showNet"] = $user['additional']['show_net'];
        }

        $orderData["orderData"] = $getOrders;

        // Make Array with page structure to render in template
        $numberOfPages = ceil($foundOrdersCount / $limitEnd);
        $orderData["numberOfPages"] = $numberOfPages;
        $orderData["pages"] = $this->getPagerStructure($destinationPage, $numberOfPages);
        return $orderData;
    }

    /**
     * This function returns invoice amount without tax
     *
     * @param $price
     * @param $tax
     * @return float
     */
    public function  getPriceWithoutTax($price, $tax)
    {
        return ($price * 100 )/(100 + $tax);
    }

    /**
     * This function returns invoice amount with added tax
     *
     * @param $price
     * @param $tax
     * @return float
     */
    public function  getPriceWithTax($price, $tax, $addTax=true)
    {
        if(!$addTax) {
            return $price;
        }
        return $price*((100+$tax)/100);
    }

    /**
     * Calculates and returns the pager structure for the frontend
     *
     * @param int $destinationPage
     * @param int $numberOfPages
     * @param array $additionalParams
     * @return array
     */
    public function getPagerStructure($destinationPage, $numberOfPages, $additionalParams = array())
    {
        $destinationPage = !empty($destinationPage) ? $destinationPage : 1;
        $pagesStructure = array();
        $baseFile = Shopware()->Config()->get('sBASEFILE');
        if ($numberOfPages > 1) {
            for ($i = 1; $i <= $numberOfPages; $i++) {
                $pagesStructure["numbers"][$i]["markup"] = ($i == $destinationPage);
                $pagesStructure["numbers"][$i]["value"] = $i;
                $pagesStructure["numbers"][$i]["link"] = $baseFile . $this->moduleManager->Core()->sBuildLink(
                        $additionalParams + array("sPage" => $i),
                        false
                    );
            }
            // Previous page
            if ($destinationPage != 1) {
                $pagesStructure["previous"] = $baseFile . $this->moduleManager->Core()->sBuildLink(
                        $additionalParams + array("sPage" => $destinationPage - 1),
                        false
                    );
            } else {
                $pagesStructure["previous"] = null;
            }
            // Next page
            if ($destinationPage != $numberOfPages) {
                $pagesStructure["next"] = $baseFile . $this->moduleManager->Core()->sBuildLink(
                        $additionalParams + array("sPage" => $destinationPage + 1),
                        false
                    );
            } else {
                $pagesStructure["next"] = null;
            }
        }
        return $pagesStructure;
    }

    /**
     * This function separates the street and streetnumber.
     */
    private function seperateStreetAndNumber($subject) {
        // Find a match and store it in $result.
        if ( preg_match('/([^\d]+)\s?(.+)/i', $subject, $result) ) {
            return $result;
        }
        return $subject;
    }

    public function prepareBasket(){

        if (empty($this->View()->sUserLoggedIn)) {
            return $this->forward('login', 'account', null, array('sTarget'=>'sKUZOOffer'));
        }
        $offerId = $this->Request()->getParam('offerId');
        $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offerId);

        $billingAddress = Shopware()->Models()->toArray($offer->getOfferBilling());

		$street = $billingAddress["street"];
		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
		    $streetAndNumber = $this->seperateStreetAndNumber(str_replace(',', '', $billingAddress["street"]));
		    if(is_array($streetAndNumber)) {
		        $street = trim($streetAndNumber[1]);
		        $streetnumber = trim($streetAndNumber[2]);
		    } else {
		        $street = $streetAndNumber;
		        $streetnumber = "";
		    }
		}

        //reframing billingAddress's name for sending mail
        $billingAddress['firstname'] = $billingAddress['firstName'];
        $billingAddress['lastname'] = $billingAddress['lastName'];
        $billingAddress['street'] = $street;
		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
        	$billingAddress['streetnumber'] = $streetnumber;
		}
        $billingAddress['customernumber'] = $billingAddress['number'];;
        $billingAddress['zipcode'] = $billingAddress['zipCode'];
        $billingAddress['userID'] = $billingAddress['customerId'];
        $billingAddress['countryID'] = $billingAddress['countryId'];
        $billingAddress['stateID'] = $billingAddress['stateId'];
        $billingAddress['customerBillingId'] = $billingAddress['customerId'];
        if(!$billingAddress['ustid']){
            $billingAddress['ustid'] = 0;
        }

        $shippingAddress = Shopware()->Models()->toArray($offer->getOfferShipping());

		$street = $shippingAddress["street"];
		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
		    $streetAndNumber = $this->seperateStreetAndNumber(str_replace(',', '', $shippingAddress["street"]));
		    if(is_array($streetAndNumber)) {
		        $street = trim($streetAndNumber[1]);
		        $streetnumber = trim($streetAndNumber[2]);
		    } else {
		        $street = $streetAndNumber;
		        $streetnumber = "";
		    }
		}

        //reframing shippingAddress's name for sending mail
        $shippingAddress['firstname'] = $shippingAddress['firstName'];
        $shippingAddress['lastname'] = $shippingAddress['lastName'];
        $shippingAddress['street'] = $street;
		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
            $shippingAddress['streetnumber'] = $streetnumber;
		}
        $shippingAddress['customernumber'] = $shippingAddress['number'];;
        $shippingAddress['zipcode'] = $shippingAddress['zipCode'];
        $shippingAddress['userID'] = $shippingAddress['customerId'];
        $shippingAddress['countryID'] = $shippingAddress['countryId'];
        $shippingAddress['stateID'] = $shippingAddress['stateId'];
        $shippingAddress['customerShippingId'] = $shippingAddress['customerId'];

        $this->View()->sPayment = Shopware()->Models()->toArray($offer->getPayment());
        $this->View()->sUserData["payment"] = $this->View()->sPayment;
        $userData = $this->View()->sUserData;
        $userData["additional"]["payment"] = $this->View()->sPayment;
        $userData["additional"]["user"]["paymentID"] = $userData["additional"]["payment"]["id"];
        $userData["billingaddress"] = $billingAddress;
        $userData["shippingaddress"] = $shippingAddress;

        $this->View()->sUserData = $userData;
        $this->View()->sDispatch = Shopware()->Models()->toArray($offer->getDispatch());

        $addTax = true;
        $customerGroup = Shopware()->Models()->getRepository('Shopware\Models\Customer\Group')->findOneBy(array('key' => $userData["additional"]["user"]["customergroup"]));
        if($customerGroup instanceof \Shopware\Models\Customer\Group) {
            $addTax = $customerGroup->getTax();
        }

        $this->View()->sBasket = $this->getOfferBasket($offer, $addTax);

        $this->View()->sLaststock = $this->basket->sCheckBasketQuantities();
        $this->View()->sShippingcosts = $this->View()->sBasket['sShippingcosts'];
        $this->View()->sShippingcostsDifference = $this->View()->sBasket['sShippingcostsDifference'];
        $this->View()->sAmount = $this->View()->sBasket['sAmount'];
        $this->View()->sAmountWithTax = $this->View()->sBasket['sAmountWithTax'];
        $this->View()->sAmountTax = $this->View()->sBasket['sAmountTax'];
        $this->View()->sAmountNet = $this->View()->sBasket['AmountNetNumeric'];
        $this->View()->sRegisterFinished = !empty($this->session['sRegisterFinished']);
        $this->saveTemporaryOrder();

        Shopware()->Session()->sDispatch = $offer->getDispatchId();
        $this->session['sOrderVariables'] = new ArrayObject($this->View()->getAssign(), ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * If any external payment mean chooses by customer
     * Forward to payment page after order submitting
     */
    public function paymentAction()
    {
        if($this->Request()->getParam('confimOffer',false)) {
            return $this->redirect(array(
                'controller' => 'sKUZOOffer',
                'action' => 'finish'
            ));
        }

        try{
            $agbChecked = $this->Request()->getParam('sAGB', false);
            $offerCheckAGB = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->offerCheckAGB;
            if(!$agbChecked && (isset($offerCheckAGB) && !empty($offerCheckAGB))) {
                return $this->forward('offers');
            }

            $this->prepareBasket();
            if (empty($this->session['sOrderVariables'])) {
                return $this->forward('offers');
            }

            $offerId = $this->Request()->getParam('offerId');
            Shopware()->Session()->offerId = $offerId;

            if (empty($this->View()->sPayment['embediframe'])
                && empty($this->View()->sPayment['action'])) {
                $this->redirect(array(
                    'controller' => 'checkout',
                    'action' => 'finish',
                    'sAGB' => 1,
                    'offerAcception' => 1
                ));

            } else {
                $this->redirect(array(
                    'controller' => 'checkout',
                    'action' => 'payment',
                    'sAGB' => 1
                ));
            }

        } catch (Exception $e) {
            $order = Shopware()->Modules()->Order();
            $order->sDeleteTemporaryOrder();
            $this->sDeleteBasketOrder();
            throw new Enlight_Exception("Error making Order:" . $e->getMessage(), 0, $e);
        }

    }

    /**
     * This action fires when customers accept offer presented by shop owner.
     */
    public function acceptOfferAction()
    {
        $this->checkSession();
        $offerId = $this->Request()->getParam("offerId");

        // updating status of Offer
        $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offerId);
        $offer->setStatus(4);
        try {
            Shopware()->Models()->persist($offer);
            Shopware()->Models()->flush();
        } catch(Exception $e) {
        }

        $this->redirect(array(
            'module' => 'frontend',
            'controller' => 'sKUZOOffer',
            'action' => 'offers'
        ));

    }

    /*
     *  Add custom Info
     */
    public function editCustomProductAction(){
        $articleId = $this->Request()->getParam("articleId");
        $offerId = $this->Request()->getParam("offerId");
        $quantityId = $this->Request()->getParam("quantityId",null);

        if($articleId && $offerId){
            $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'detail',
                'sArticle' => $articleId,
                'custom' => '1',
                'offerId' => $offerId,
                'quantityId' => $quantityId
            ));
        }else{
            $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'sKUZOOffer',
                'action' => 'offers',
                'erroCustomProduct' => true
            ));
        }

    }

    public function saveCustomDetailAction(){
        $offerId = $this->Request()->getParam("offerId", null);
        if ($offerId) {
            $offer = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer')->findOneBy(array(
                'id' => $offerId
            ));
            $articleId = $_SESSION['Shopware']['sLastArticle'];
            if($articleId){
                $articleResource = \Shopware\Components\Api\Manager::getResource('Article');
                $article = $articleResource->getOne($articleId);
                $articleDetaiId = $article['mainDetailId'];
                $internalComment = $article['name']." (".$article['mainDetail']['number'].")".":  ";;
            }
            $quantityId = $this->Request()->getParam("quantityId");
            if($quantityId){
                $customPositions = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Detail')->findBy(array(
                    'offerId' => $offerId,
                    'articleDetailsId' => $articleId,
                    'quantityId' => $quantityId
                ));
            }else{
                $customPositions = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Detail')->findBy(array(
                    'offerId' => $offerId,
                    'articleDetailsId' => $articleId,
                ));
            }
            if($customPositions) {
                foreach ($customPositions as $customPosition) {
                    if ($customPosition instanceof \Shopware\CustomModels\Offer\Detail) {
                        Shopware()->Models()->remove($customPosition);
                    }
                }
                Shopware()->Models()->flush();
            }


            $offerCustomizingValues = $this->Request()->getParam("customizingValues", null);
            if(!$offerCustomizingValues){
                $offerCustomizingValues = $_SESSION['Shopware']['customizingValues'][1];
            }
            foreach ($offerCustomizingValues as $ocKey => &$customizingValue){
                if(!is_array($customizingValue)){
                    $customizingValue = array($ocKey => $customizingValue);
                }
            }

            if($articleId){
                $sql = "SELECT `group_id` FROM `s_plugin_customizing_articles` WHERE `article_id`=:articleId";
                $isCustomProduct = Shopware()->Db()->fetchCol($sql, array('articleId' => $articleId));
            }
            if($isCustomProduct) {
                foreach ($isCustomProduct as $key => $customGroupId){
                    //check requrement
                    $requiredCustomProducts = Shopware()->Models()->getRepository('Shopware\CustomModels\Customizing\Option')->findBy(array(
                        'groupId' => $customGroupId,
                        'required'=> 1,
                        'active' =>1
                    ));
                    foreach ($requiredCustomProducts as $requiredCustomProduct){
                        foreach ($offerCustomizingValues as $ocKey => $customizingValue){
                           if($requiredCustomProduct->getId() == $ocKey){
                               $isRequiredValidated = true;
                               break;
                           }
                        }
                        if(!$isRequiredValidated){
                            return $this->redirect(array(
                                'module' => 'frontend',
                                'controller' => 'detail',
                                'sArticle' => $articleId,
                                'custom' => '1',
                                'offerId' => $offerId,
                                'quantityId' => $quantityId,
                                'requiredError' => true
                            ));
                        }
                    }

                    $customProducts = Shopware()->Models()->getRepository('Shopware\CustomModels\Customizing\Option')->findBy(array(
                        'groupId' => $customGroupId,
                    ));

                    foreach ($customProducts as $ckey => $customProduct) {
                        if($offerCustomizingValues[$ckey+1] != null){
                            foreach ($offerCustomizingValues[$ckey+1] as $offerCustomizingValue){
                                if(Shopware()->Models()->toArray($customProduct->getValues())){
                                    $optionsCustomizingValues = Shopware()->Models()->toArray($customProduct->getValues());
                                    foreach ($optionsCustomizingValues as $okey => $optionsCustomizingValue){
                                      if($optionsCustomizingValue['id'] == $offerCustomizingValue){
                                            $offerCustomizingValue = $optionsCustomizingValue['value'];
                                      }
                                    }
                                }

                                $offerCustomizingValue = $customProduct->getName().": ".$offerCustomizingValue;
                                try{

                                    if($customProduct->getNumber() && $customProduct->getName()){

                                        $internalComment = $internalComment." - ".$offerCustomizingValue;
                                        $offer->setInternalComment($internalComment);

                                        $position = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Detail')->findOneBy(array(
                                            'offerId' => $offerId,
                                            'articleDetailsId' => $articleDetaiId
                                        ));


                                        $chargeItem = Shopware()->Models()->getRepository('Shopware\CustomModels\Customizing\Charge\Item')->findOneBy(array(
                                            'number' => $customProduct->getNumber(),
                                            'name' => $customProduct->getName()
                                        ));
                                        $chargedPrice = 0;
                                        if($chargeItem){
                                            $percentageCharge = $chargeItem->getPercentage();
                                            $chargeItemId = $chargeItem->getId();
                                          /*  $chargeValue = Shopware()->Models()->getRepository('Shopware\CustomModels\Customizing\Charge\Value')->findAll(array(
                                                'item.id' => $chargeItemId
                                            ));*/
                                            $builder = Shopware()->Models()->createQueryBuilder();
                                            $chargeValueQuery = $builder->select(array('chargeValue'))
                                                ->from('Shopware\CustomModels\Customizing\Charge\Value', 'chargeValue')
                                                ->join('chargeValue.item','chargeItem')
                                                ->where('chargeItem.id = :id')
                                                ->setParameter('id', $chargeItemId)
                                                ->getQuery();
                                            $chargeValue = $chargeValueQuery->getArrayResult();

                                            $value = $chargeValue[0]['value'];

                                            if($percentageCharge)
                                                $chargedPrice = ($position->getOriginalPrice() * $value)/100;
                                            else
                                                $chargedPrice = $value;
                                        }
                                        if($quantityId == 0){
                                            for($q=1; $q<= $position->getQuantity(); $q++){
                                                $this->createNewCustomPosition($offerId, $articleId, $offer, $chargedPrice, $customProduct, $offerCustomizingValue, $q);
                                            }
                                        }else{
                                            $this->createNewCustomPosition($offerId, $articleId, $offer, $chargedPrice, $customProduct, $offerCustomizingValue, $quantityId);
                                        }
                                        Shopware()->Models()->flush();
                                    }


                                }catch(Exception $e){
                                    $this->redirect(array(
                                        'module' => 'frontend',
                                        'controller' => 'detail',
                                        'sArticle' => $articleId,
                                        'custom' => '1',
                                        'offerId' => $offerId
                                    ));
                                }
                            }
                        }

                    }
                }

                $offer->calculateInvoiceAmount();
                Shopware()->Models()->flush();

                $this->redirect(array(
                    'module' => 'frontend',
                    'controller' => 'sKUZOOffer',
                    'action' => 'offers'
                ));
            }else{
                $this->redirect(array(
                    'module' => 'frontend',
                    'controller' => 'sKUZOOffer',
                    'action' => 'offers'
                ));
            }
        }
    }

    public function createNewCustomPosition($offerId, $articleId, $offer, $chargedPrice, $customProduct, $offerCustomizingValue, $quantityId){
        $newPosition = new Shopware\CustomModels\Offer\Detail();
        $newPosition->setOfferId($offerId);
        $newPosition->setArticleDetailsId($articleId);
        $newPosition->setTaxId(0);
        $newPosition->setTaxRate(0);
        $newPosition->setNumber($offer->getNumber());
        $newPosition->setPrice($chargedPrice);
        $newPosition->setOriginalPrice($chargedPrice);
        $newPosition->setQuantity(1);
        $newPosition->setQuantityId($quantityId);
        $newPosition->setMode(4);
        $newPosition->setArticleNumber($customProduct->getNumber());
        $newPosition->setArticleName($offerCustomizingValue);
        Shopware()->Models()->persist($newPosition);
    }

    /**
     * This functions download offer Document from Frontend Customer Account.
     */
    public function downloadPdfAction() {
        $this->checkSession();
        $offerId = $this->Request()->getParam("offerId");
        $hash = $this->Request()->getParam("hash");
        $mediaId = $this->Request()->getParam("mediaId");

        $document = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Document\Document')->findOneBy(array('offerId' => $offerId, 'hash' => $hash));

        if(!$document instanceof \Shopware\CustomModels\Offer\Document\Document) {
            $document = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Document\Document')->findOneBy(array('offerId' => $offerId));
        }

        $isMedia = true;
        $docMediaId = $document->getMediaId();
        if(!isset($docMediaId) || empty($docMediaId) || $docMediaId==-1) {
            $isMedia = false;
        }

        try {
            $name = basename($document->getHash()) . '.pdf';
            $file = Shopware()->DocPath('files/documents') . $name;
            if(!file_exists($file)) {
                $this->View()->assign(array(
                    'success' => false,
                    'data' => $this->Request()->getParams(),
                    'message' => 'File not exist'
                ));
                return;
            }

            $docId = $document->getDocumentId();
            $response = $this->Response();
            $response->setHeader('Cache-Control', 'public');
            $response->setHeader('Content-Description', 'File Transfer');
            $response->setHeader('Content-disposition', 'attachment; filename='.($isMedia?'Angebot':$docId).".pdf" );
            $response->setHeader('Content-Type', 'application/pdf');
            $response->setHeader('Content-Transfer-Encoding', 'binary');
            $response->setHeader('Content-Length', filesize($file));
            echo file_get_contents($file);
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => $this->Request()->getParams(),
                'message' => $e->getMessage()
            ));
            return;
        }
        $this->forward('offers');
    }

    /**
     * This function checks session of customer.
     */
    public function checkSession()
    {
        if(!$this->admin->sCheckUser())
        {
            return $this->redirect(array(
                'module' => 'frontend',
                'controller' => 'account',
                'action' => 'login',
                'sTarget' => 'sKUZOOffer'
            ));
        }
    }

    /**
     * This action to handle selection of shipping and payment methods
     */
    public function shippingPaymentAction()
    {
        // Load payment options, select option and details
        $this->View()->sPayments = $this->getPayments();
        $this->View()->sFormData = array('payment' => $this->View()->sUserData['additional']['user']['paymentID']);
        $getPaymentDetails = $this->admin->sGetPaymentMeanById($this->View()->sFormData['payment']);

        $paymentClass = $this->admin->sInitiatePaymentClass($getPaymentDetails);
        if ($paymentClass instanceof \ShopwarePlugin\PaymentMethods\Components\BasePaymentMethod) {
            $data = $paymentClass->getCurrentPaymentDataAsArray(Shopware()->Session()->sUserId);
            if (!empty($data)) {
                $this->View()->sFormData += $data;
            }
        }
        if ($this->Request()->isPost()) {
            $values = $this->Request()->getPost();
            $values['payment'] = $this->Request()->getPost('payment');
            $values['isPost'] = true;
            $this->View()->sFormData = $values;
        }

        $this->View()->sBasket = $this->getBasket();

        // Load current and all shipping methods
        $this->View()->sDispatch = $this->getSelectedDispatch();
        $this->View()->sDispatches = $this->getDispatches($this->View()->sFormData['payment']);

        $this->View()->sLaststock = $this->basket->sCheckBasketQuantities();
        $this->View()->sShippingcosts = $this->View()->sBasket['sShippingcosts'];
        $this->View()->sShippingcostsDifference = $this->View()->sBasket['sShippingcostsDifference'];
        $this->View()->sAmount = $this->View()->sBasket['sAmount'];
        $this->View()->sAmountWithTax = $this->View()->sBasket['sAmountWithTax'];
        $this->View()->sAmountTax = $this->View()->sBasket['sAmountTax'];
        $this->View()->sAmountNet = $this->View()->sBasket['AmountNetNumeric'];
        $this->View()->sRegisterFinished = !empty($this->session['sRegisterFinished']);
        $this->View()->sTargetAction = 'shippingPayment';
        $this->View()->sKUZOOffer = true;
        if ($this->Request()->getParam('isXHR')) {
            return $this->View()->loadTemplate('frontend/s_k_u_z_o_offer/shipping_payment_core.tpl');
        }
    }

    /**
     * This action to simultaneously save shipping and payment details
     */
    public function saveShippingPaymentAction()
    {
        if (!$this->Request()->isPost()) {
            return $this->forward('shippingPayment');
        }

        // Load data from request
        $dispatch = $this->Request()->getPost('sDispatch');
        $payment = $this->Request()->getPost('payment');

        // If request is ajax, we skip the validation, because the user is still editing
        if ($this->Request()->getParam('isXHR')) {
            // Save payment and shipping method data.
            $this->admin->sUpdatePayment($payment);
            $this->setDispatch($dispatch, $payment);

            return $this->forward('shippingPayment');
        }

        $sErrorFlag = array();
        $sErrorMessages = array();

        if (is_null($dispatch) && ($this->getDispatches($payment) || Shopware()->Config()->get('premiumshippingnoorder'))) {
            $sErrorFlag['sDispatch'] = true;
            $sErrorMessages[] = Shopware()->Snippets()->getNamespace('frontend/checkout/error_messages')
                ->get('ShippingPaymentSelectShipping', 'Please select a shipping method');
        }
        if (is_null($payment)) {
            $sErrorFlag['payment'] = true;
            $sErrorMessages[] = Shopware()->Snippets()->getNamespace('frontend/checkout/error_messages')
                ->get('ShippingPaymentSelectPayment', 'Please select a payment method');
        }

        // If any basic info is missing, return error messages
        if (!empty($sErrorFlag) || !empty($sErrorMessages)) {
            $this->View()->assign('sErrorFlag', $sErrorFlag);
            $this->View()->assign('sErrorMessages', $sErrorMessages);
            return $this->forward('shippingPayment');
        }

        // Validate the payment details
        Shopware()->Modules()->Admin()->sSYSTEM->_POST['sPayment'] = $payment;
        $checkData = $this->admin->sValidateStep3();

        // Problem with the payment details, return error
        if (!empty($checkData['checkPayment']['sErrorMessages']) || empty($checkData['sProcessed'])) {
            $this->View()->assign('sErrorFlag', $checkData['checkPayment']['sErrorFlag']);
            $this->View()->assign('sErrorMessages', $checkData['checkPayment']['sErrorMessages']);
            return $this->forward('shippingPayment');
        }

        // Save payment method details db
        if ($checkData['sPaymentObject'] instanceof \ShopwarePlugin\PaymentMethods\Components\BasePaymentMethod) {
            $checkData['sPaymentObject']->savePaymentData(Shopware()->Session()->sUserId, $this->Request());
        }

        // Save the payment info
        $previousPayment = Shopware()->Modules()->Admin()->sGetUserData();
        $previousPayment = $previousPayment['additional']['user']['paymentID'];

        $previousPayment = $this->admin->sGetPaymentMeanById($previousPayment);
        if ($previousPayment['paymentTable']) {
            Shopware()->Db()->delete(
                $previousPayment['paymentTable'],
                array('userID = ?' => Shopware()->Session()->sUserId)
            );
        }

        // Save payment and shipping method data.
        $this->admin->sUpdatePayment($payment);
        $this->setDispatch($dispatch, $payment);

        $this->redirect(array(
            'controller' => $this->Request()->getParam('sTarget', 'sKUZOOffer'),
            'action' => $this->Request()->getParam('sTargetAction', 'confirm')
        ));
    }

    /**
     * This function set the provided dispatch method
     *
     * @param $dispatchId ID of the dispatch method to set
     * @param int|null $paymentId Payment id to validate
     * @return int set dispatch method id
     */
    public function setDispatch($dispatchId, $paymentId = null)
    {
        $supportedDispatches = $this->getDispatches($paymentId);

        // Iterate over supported dispatches, look for the provided one
        foreach ($supportedDispatches as $dispatch) {
            if ($dispatch['id'] == $dispatchId) {
                $this->session['sDispatch'] = $dispatchId;
                return $dispatchId;
            }
        }

        $defaultDispatch = array_shift($supportedDispatches);
        $this->session['sDispatch'] = $defaultDispatch['id'];
        return $this->session['sDispatch'];
    }

    /**
     * Delete an article from cart -
     * @param sDelete = id from s_basket identifying the product to delete
     * Forward to cart / confirmation page after success
     */
    public function deleteArticleAction()
    {
        if ($this->Request()->getParam('sDelete')) {
            $this->basket->sDeleteArticle($this->Request()->getParam('sDelete'));
        }

        $this->forward($this->Request()->getParam('sTargetAction', 'index'));
    }



    /**
     * Change quantity of a certain product
     * @param sArticle = The article to update
     * @param sQuantity = new quantity
     * Forward to cart / confirm view after success
     */
    public function changeQuantityAction()
    {
        if ($this->Request()->getParam('sArticle') && $this->Request()->getParam('sQuantity')) {
            $this->View()->sBasketInfo = $this->basket->sUpdateArticle($this->Request()->getParam('sArticle'), $this->Request()->getParam('sQuantity'));
        }
        $this->forward($this->Request()->getParam('sTargetAction', 'index'));
    }

    public function sendMailToAdmin($offerNumber, $shippingCost, $amount, $amountNet){
//adding all offer details to variable for sending mail
        $details = $this->getOrderDetailsForMail(
            $this->View()->sBasket["content"]
        );

        $variables = array(
            "sOrderDetails"=>$details,
            "billingaddress"=>$this->View()->sUserData["billingaddress"],
            "shippingaddress"=>$this->View()->sUserData["shippingaddress"],
            "additional"=>$this->View()->sUserData["additional"],
            "sShippingCosts"=>$shippingCost,
            "sAmount"=>$amount,
            "sAmountNet"=>$amountNet,
            "sTaxRates"   => $this->View()->sBasket["sTaxRates"],
            "ordernumber"=>$offerNumber,
            "sOrderDay" => date("d.m.Y"),
            "sOrderTime" => date("H:i"),
            "customerComment" => $this->session['sComment']
        );

        if ($this->View()->sDispatch["id"]) {
            $variables["sDispatch"] = $this->View()->sDispatch;
        }
        else{
            $variables["sDispatch"] = 0;
        }

        $confirmMailDeliveryFailed = false;
        try {
            //Shopware()->Modules()->Order()->sendMail($variables);
            $this->sendOfferMail($variables);
        } catch (\Exception $e) {
            $confirmMailDeliveryFailed = true;
            $email = $this->View()->sUserData['additional']['user']['email'];
            $this->logOrderMailException($e, $offerNumber, $email);
        }

    }


    /**
     * send order confirmation mail
     * @access public
     */
    public function logOrderMailException($e, $offerNumber, $email) {
        /**
         * @var $logger Shopware\Components\Logger
         */
        $logger = $this->get('pluginlogger');
        $logger->error("ERROR MailSend: OfferNumber: ".$offerNumber." MailAddress: ".$email." Stackrrace: ".$e->getTraceAsString());
    }

    /**
     * send order confirmation mail
     * @access public
     */
    public function sendOfferMail($variables)
    {

        $shopContext = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();

        $context = array(
            'sOrderDetails' => $variables["sOrderDetails"],

            'billingaddress'  => $variables["billingaddress"],
            'shippingaddress' => $variables["shippingaddress"],
            'additional'      => $variables["additional"],

            'sTaxRates'      => $variables["sTaxRates"],
            'sShippingCosts' => $variables["sShippingCosts"],
            'sAmount'        => $variables["sAmount"],
            'sAmountNet'     => $variables["sAmountNet"],

            'sOrderNumber' => $variables["ordernumber"],
            'sOrderDay'    => $variables["sOrderDay"],
            'sOrderTime'   => $variables["sOrderTime"],
            'sComment'     => $variables["customerComment"],

            'attributes'     => $variables["attributes"],
            'sCurrency'    => $this->admin->sSYSTEM->sCurrency["currency"],

            'sLanguage'    => $shopContext->getShop()->getId(),

            'sSubShop'     => $shopContext->getShop()->getId(),

            'sEsd'    => $variables["sEsd"],
            'sNet'    => $this->admin->sNet
        );

        // Support for individual payment means with custom-tables
        if ($variables["additional"]["payment"]["table"]) {
            $paymentTable = $this->db->fetchRow("
                  SELECT * FROM {$variables["additional"]["payment"]["table"]}
                  WHERE userID=?",
                array($variables["additional"]["user"]["id"])
            );
            $context["sPaymentTable"] = $paymentTable ? : array();
        } else {
            $context["sPaymentTable"] = array();
        }

        if ($variables["sDispatch"]) {
            $context['sDispatch'] = $variables["sDispatch"];
        }

        $mail = null;

        if (!($mail instanceof \Zend_Mail)) {
            $mail = Shopware()->TemplateMail()->createMail('sOfferMail', $context);
        }

        $mail->addTo($mail->getFrom());
        if (!($mail instanceof \Zend_Mail)) {
            return;
        }
        $mail->send();

    }

    /**
     * Small helper function which iterates all basket rows
     * and formats the article name and order number.
     * This function is used for the order status mail.
     *
     * @param $basketRows
     * @return array
     */
    private function getOrderDetailsForMail($basketRows)
    {
        $details = array();
        foreach ($basketRows as $content) {
            $content["articlename"] = trim(html_entity_decode($content["articlename"]));
            $content["articlename"] = str_replace(array("<br />", "<br>"), "\n", $content["articlename"]);
            $content["articlename"] = str_replace("&euro;", "�", $content["articlename"]);
            $content["articlename"] = trim($content["articlename"]);

            while (strpos($content["articlename"], "\n\n")!==false) {
                $content["articlename"] = str_replace("\n\n", "\n", $content["articlename"]);
            }

            $content["ordernumber"] = trim(html_entity_decode($content["ordernumber"]));

            $details[] = $content;
        }
        return $details;
    }

}