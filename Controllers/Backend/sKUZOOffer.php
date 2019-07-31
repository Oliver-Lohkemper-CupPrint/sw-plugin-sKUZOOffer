<?php

use Shopware\CustomModels\Offer\Offer as Offer,
    Shopware\CustomModels\Offer\Detail as Detail,
    Shopware\CustomModels\Offer\Document\Document as Document,
    Doctrine\ORM\Query\Expr;

require_once __DIR__ . '/../../Components/CSRFWhitelistAware.php';
class Shopware_Controllers_Backend_sKUZOOffer extends Shopware_Controllers_Backend_Application implements \Shopware\Components\CSRFWhitelistAware {

    protected $model = 'Shopware\CustomModels\Offer\Offer';
    protected $alias = 'offer';

   /* protected function initAcl()
    {
        // read
        $this->addAclPermission('list', 'read', 'Insufficient Permissions');
    }*/

    /**
     * add actions to CSRF white list
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        return array(
            'openPDF'
        );
    }

    public function saveInternalCommentAction() {
        $params = $this->Request()->getParams();

        $offerNumber = $params['number'];
        if(empty($offerNumber)) {
            $this->View()->assign(array(
                'success' => false
            ));
            return false;
        }
        $offer = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer')->findOneBy(array('number'=>$offerNumber));
        if($offer instanceof \Shopware\CustomModels\Offer\Offer) {
            $offer->setInternalComment($params['internalComment']);
            try {
                $this->getManager()->persist($offer);
                $this->getManager()->flush();

                $this->View()->assign(array(
                    'success' => true,
                ));
                return true;
            } catch(Exception $e) {
            }
        }
        $this->View()->assign(array(
            'success' => false
        ));
        return false;
    }

    public function saveExternalCommentAction() {
        $params = $this->Request()->getParams();

        $offerNumber = $params['number'];
        if(empty($offerNumber)) {
            $this->View()->assign(array(
                'success' => false
            ));
            return false;
        }
        $offer = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer')->findOneBy(array('number'=>$offerNumber));
        if($offer instanceof \Shopware\CustomModels\Offer\Offer) {
            $offer->setCustomerComment($params['customerComment']);
            $offer->setComment($params['comment']);
            try {
                $this->getManager()->persist($offer);
                $this->getManager()->flush();

                $this->View()->assign(array(
                    'success' => true,
                ));
                return true;
            } catch(Exception $e) {
            }
        }
        $this->View()->assign(array(
            'success' => false
        ));
        return false;
    }

    public function getShopsAction()
    {
        $builder = Shopware()->Models()->createQueryBuilder();
        $shops = $builder->select(array('shop', 'locale', 'child'))
            ->from('Shopware\Models\Shop\Shop', 'shop')
            ->leftJoin('shop.locale', 'locale')
            ->leftJoin('shop.children', 'child')
            ->getQuery()
            ->getArrayResult();
        //get total result of the query
        $total = count($shops);

        foreach ($shops as $key => &$shop) {
            if ($shop['mainId'] == NULL) {
                $shop['name'] = $shop['name'] . " " . $shop['locale']['language'];
            }else{
                $mainShop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array('id' => $shop['mainId']));
                $shop['name'] = $mainShop->getName() . " " . $shop['locale']['language'];
            }
        }

        //return the data and total count
        $this->View()->assign(array('success' => true, 'data' => $shops, 'total' => $total));
    }

    public function getVariantsAction()
    {
        $builder = Shopware()->Models()->createQueryBuilder();

        $fields = array(
            'details.id',
            "CASE WHEN (details.additionalText!='') THEN CONCAT(articles.name,' - ',details.additionalText) ELSE articles.name END as name",
            'articles.description',
            'articles.active',
            'details.number as ordernumber',
            'articles.id as articleId',
            'details.inStock',
            'supplier.name as supplierName',
            'supplier.id as supplierId',
            'details.additionalText'
        );

        $builder->select($fields);
        $builder->from('Shopware\Models\Article\Detail', 'details');
        $builder->innerJoin('details.article', 'articles');
        $builder->innerJoin('articles.supplier', 'supplier');

        $filters = $this->Request()->getParam('filter', array());
        foreach ($filters as $filter) {
            if ($filter['property'] === 'free') {
                $builder->andWhere(
                    $builder->expr()->orX(
                        'details.number LIKE :free',
                        'articles.name LIKE :free',
                        'supplier.name LIKE :free'
                    )
                );
                $builder->setParameter(':free', $filter['value']);
            } else {
                $builder->addFilter($filter);
            }
        }

        $properties = $this->prepareVariantParam($this->Request()->getParam('sort', array()), $fields);
        foreach ($properties as $property) {
            $builder->addOrderBy($property['property'], $property['direction']);
        }

        $builder->setFirstResult($this->Request()->getParam('start'))
            ->setMaxResults($this->Request()->getParam('limit'));

        $result = $builder->getQuery()->getArrayResult();
        $total = count($result);

//        $result = $this->addAdditionalTextForVariant($result);

        $this->View()->assign(array('success' => true, 'data' => $result, 'total' => $total));
    }

    private function prepareVariantParam($properties, $fields)
    {
        //maps the fields to the correct table
        foreach ($properties as $key => $property) {
            foreach ($fields as $field) {
                $asStr = ' as ';
                $dotPos = strpos($field, '.');
                $asPos = strpos($field, $asStr, true);

                if ($asPos) {
                    $fieldName = substr($field, $asPos + strlen($asStr));
                } else {
                    $fieldName = substr($field, $dotPos + 1);
                }

                if ($fieldName == $property['property']) {
                    $properties[$key]['property'] = $field;
                }
            }
        }

        return $properties;
    }

    public function getConfigAction(){
        // get config emailPreview
        $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
        $emailPreview = $config->emailPreview;

        $instance = Shopware()->Plugins()->Backend()->sKUZOOffer();
        $ePost = $instance->checkForEPost();

        $showPurchasePrice = $config->showPurchasePrice;

        $this->View()->assign(array('success' => true, 'emailPreview' => $emailPreview, 'ePost' => $ePost, 'showPurchasePrice' => $showPurchasePrice));
    }

     /**
     * This function returns all available tax records of shop.
     */
    public function getTaxAction()
    {
        $builder = Shopware()->Models()->createQueryBuilder();
        $tax = $builder->select(array('tax'))
            ->from('Shopware\Models\Tax\Tax', 'tax')
            ->getQuery()
            ->getArrayResult();

        $this->View()->assign(array('success' => true, 'data' => $tax));
    }

    /**
     * This function returns all available offer state records of shop.
     */
    public function getOfferStatesAction()
    {
        $addIgnore = $this->Request()->getParam('addIgnore', null);
        $builder = Shopware()->Models()->createQueryBuilder();
        $state = $builder->select(array('state'))
            ->from('Shopware\CustomModels\Offer\States', 'state')
            ->getQuery()
            ->getArrayResult();

        if(isset($addIgnore) && !empty($addIgnore)) {
            $state = array_merge(array(array("id"=>-1,"description"=>"Ignore")),$state);
        }

        $this->View()->assign(array('success' => true, 'data' => $state));
    }

    /**
     * This function returns all available customers with associated shipping and billing Addresses of shop.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getCustomerAction()
    {
        $limit = $this->Request()->getParam('limit', 20);
        $offset = $this->Request()->getParam('start', 0);
        $searchQuery = $this->Request()->getParam('query', 0);
        $expr = Shopware()->Models()->getExpressionBuilder();
        $builder = Shopware()->Models()->createQueryBuilder();


        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
            $query = $builder->select(array(
                'billing.firstname AS firstName',
                'billing.lastname AS lastName',
                'billing.street',
                'billing.zipcode AS zipCode',
                'billing.city',
                'billing.countryId',
                'billing.company',
                'user.number',
                'user.id AS customerId'
            ))->from('Shopware\Models\Customer\Customer', 'user')
                ->join('user.defaultBillingAddress','billing')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->andWhere($expr->like('billing.firstname', '?1'))
                ->orWhere($expr->like('billing.lastname', '?1'))
                ->orWhere($expr->like('billing.company', '?1'))
                ->orWhere($expr->like('user.number', '?1'))
                ->orWhere($expr->like('user.email', '?1'))
                ->orWhere($expr->like('user.id', '?1'))
                ->setParameter(1,$searchQuery . '%')
                ->getQuery();
        } else {
            $query = $builder->select(array('billing'))->from('Shopware\Models\Customer\Billing', 'billing')
                ->join('billing.customer', 'user')->setFirstResult($offset)->setMaxResults($limit)
                ->andWhere($expr->like('billing.firstName', '?1'))->orWhere($expr->like('billing.lastName', '?1'))
                ->orWhere($expr->like('billing.company', '?1'))->orWhere($expr->like('billing.number', '?1'))
                ->orWhere($expr->like('user.email', '?1'))->orWhere($expr->like('user.id', '?1'))
                ->setParameter(1, $searchQuery . '%')->getQuery();
        }
        $query->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $paginator = $this->getModelManager()->createPaginator($query);
        $total = $paginator->count();

        $billing = $query->getArrayResult();
        foreach ($billing as $key => $bill) {
            $customerId = $bill['customerId'];
            $customer = $this->getManager()->find(
                'Shopware\Models\Customer\Customer',
                $customerId
            );
            if (!$customer instanceof Shopware\Models\Customer\Customer) {
                continue;
            }
            $billing[$key]['email'] = $customer->getEmail();
            $billing[$key]['paymentId'] = $customer->getPaymentId();
            $billing[$key]['shopId'] = $customer->getShop()->getId();
            $billing[$key]['dispatchId'] = '9'; //TODO find active and plausible dispatch
            if (!$billing[$key]['paymentId']) {
                $billing[$key]['paymentId'] = '1';
            }
            if (!$billing[$key]['shopId']) {
                $billing[$key]['shopId'] = '1';
            }
            if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")) {
                $billing[$key]['street'] = $billing[$key]['street']." ".$billing[$key]['streetNumber'];
            }

            //for shipping address from original customer
            $shipping = $customer->getDefaultShippingAddress();

            if ($shipping instanceof Shopware\Models\Customer\Address) {
                $billing[$key]['sfirstName'] = $shipping->getFirstName();
                $billing[$key]['slastName'] = $shipping->getLastName();
                $billing[$key]['scompany'] = $shipping->getCompany();
                $billing[$key]['szipCode'] = $shipping->getZipCode();
                $billing[$key]['sstreet'] = $shipping->getStreet();
                if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")) {
                    $billing[$key]['sstreet'] = $shipping->getStreet()." ".$shipping->getStreetNumber();
                }
                $billing[$key]['scity'] = $shipping->getCity();
                $billing[$key]['scountryId'] = $shipping->getCountry()->getId();
            }
        }

        $this->View()->assign(array('success' => true, 'data' => $billing, 'total' => $total));
    }

    /**
     * This function returns original article price with tax.
     *
     * @throws Enlight_Exception
     */
    public function getArticlePriceAction()
    {
        $quantity = $this->Request()->getParam('quantity');
        $discount = $this->Request()->getParam('discount');

        $shopId = $this->Request()->getParam('shopId');
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
            'id' => $shopId
        ));

        $customerId = $this->Request()->getParam('customerId');
        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array(
            'id' => $customerId
        ));

        $articleDetailsId = $this->Request()->getParam('articleDetailsId');
        $articleId = $this->Request()->getParam('articleId');

        $minPurchase = 1;
        $priceByQuantity = false;
        if($articleDetailsId){

            $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $articleDetailsId));
            if($articleDetail instanceof \Shopware\Models\Article\Detail){
                $minPurchase = $articleDetail->getMinPurchase();
            }


            if(!$quantity) {
                $quantity = $minPurchase;
            }

            $articlePriceRepo = Shopware()->Models()->getRepository('Shopware\Models\Article\Price');
            if($customer->getGroupKey()){
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId, 'customerGroupKey' => $customer->getGroupKey()));
            }

            if(!is_array($articlePrice)  || count($articlePrice) == 0) {
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId, 'customerGroupKey' => $shop->getCustomerGroup()->getKey()));
            }

            if(!is_array($articlePrice) || count($articlePrice) == 0) {
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId));
            }
            if($quantity && count($articlePrice)>1){
                $priceByQuantity = true;
                foreach ($articlePrice as $key => $aPrice) {
                    if($aPrice->getFrom() <= $quantity && ($aPrice->getTO() >= $quantity || $aPrice->getTO() == 'beliebig' )){
                        $articlePrice = $articlePrice[$key];
                        break;
                    }
                }
                $scalePrice = 1;
            }else{
                if(count($articlePrice)>1)
                    $scalePrice = 1;
                else
                    $scalePrice = 0;

                $articlePrice = $articlePrice[0];
            }
        }

        if(!$articleId){
            $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $articleDetailsId));
			if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
	            $articleId = $articleDetail->getArticleId();
			} else {
				$articleId = $articleDetail->getArticle()->getId();
			}
        }


        $articleRepo = Shopware()->Models()->getRepository('Shopware\Models\Article\Article');
        /** @var \Shopware\Models\Article\Article $article */
        $article = $articleRepo->findOneBy(array('id' => $articleId));

        $currency = Shopware()->Models()->toArray($shop->getCurrency());

        //if($customer->getGroup()->getTax()){
        if($article->getTax()->getId()){
            $articlePriceWithTax = $this->calculatePrice($articlePrice->getPrice(),$article->getTax()->getTax())*$currency['factor'];
            $purchasePrice = 0;
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->find($articleDetailsId);
                if($articleDetail instanceof \Shopware\Models\Article\Detail) {
                    $purchasePrice = $articleDetail->getPurchasePrice();
                }
            }
            else {
                $purchasePrice = $articlePrice->getBasePrice();
            }
            $purchasePriceWithTax = $this->calculatePrice($purchasePrice,$article->getTax()->getTax())*$currency['factor'];
            $taxId = $article->getTax()->getId();
        }else{
            $articlePriceWithTax =$articlePrice->getPrice()*$currency['factor'];
            $purchasePrice = 0;
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->find($articleDetailsId);
                if($articleDetail instanceof \Shopware\Models\Article\Detail) {
                    $purchasePrice = $articleDetail->getPurchasePrice();
                } else {
                    $purchasePrice = $articlePrice->getBasePrice();
                }
            }
            $purchasePriceWithTax = $purchasePrice*$currency['factor'];
            $taxId = 0;
        }

        $this->View()->assign(
            array(
                'success' => true,
                'articlePrice' => $articlePriceWithTax,
                'articleTax' => $taxId,
                'currency' => $currency['currency'],
                'scalePrice' => $scalePrice,
                'purchasePrice' => $purchasePriceWithTax,
                'minPurchase' => $minPurchase,
                'originalNetPrice' => $articlePrice->getPrice(),
                'priceByQuantity' => $priceByQuantity
            )
        );
    }

    private function calculatePrice($netPrice, $tax) {
            return ($netPrice * ($tax + 100) / 100);
    }

    /**
     * This function saves offer Billing Address.
     */
    public function saveOfferBillingAction()
    {
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shop = $shopRepo->findOneBy(array('id' => $this->Request()->getParam('shopId')));
        if($shop->getMain()){
            $shopId = $shop->getMain()->getId();
            $language = $shop->getId();
        }else{
            $shopId = $this->Request()->getParam('shopId');
            $language = $shopId;
        }
        if($shop){
            $currency = Shopware()->Models()->toArray($shop->getCurrency());
        }else{
            $currency['factor']=1;
        }

        try {
            $address["customerId"] = $this->Request()->getParam('customerId');
            $address["firstName"] = $this->Request()->getParam('firstName');
            $address["lastName"] = $this->Request()->getParam('lastName');
            $address["zipCode"] = $this->Request()->getParam('zipCode');
            $address["city"] = $this->Request()->getParam('city');
            $address["countryId"] = $this->Request()->getParam('countryId');
            $address["number"] = $this->Request()->getParam('number');
            $address["company"] = $this->Request()->getParam('company');
            $address["street"] = $this->Request()->getParam('street');
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
	            $address["street"] = $this->Request()->getParam('street')." ".$this->Request()->getParam('streetNumber');
			}
            $address["shippingTax"] = $this->Request()->getParam('shippingTax');

            $shippingAddress["customerId"] = $this->Request()->getParam('customerId');
            $shippingAddress["firstName"] = $this->Request()->getParam('sfirstName');
            $shippingAddress["lastName"] = $this->Request()->getParam('slastName');
            $shippingAddress["zipCode"] = $this->Request()->getParam('szipCode');
            $shippingAddress["city"] = $this->Request()->getParam('scity');
            $shippingAddress["countryId"] = $this->Request()->getParam('scountryId');
            $shippingAddress["company"] = $this->Request()->getParam('scompany');
            $shippingAddress["street"] = $this->Request()->getParam('sstreet');
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$shippingAddress["street"] = $this->Request()->getParam('sstreet')." ".$this->Request()->getParam('sstreetNumber');
			}
            $id = $this->Request()->getParam('offerId');
            if (!$id) {
                $offer = new Offer();
                $zendDate = new Zend_Date();
                $offer->setOfferTime($zendDate->get("YYYY-MM-dd HH:mm:ss"));
                $offer->setNumber($this->getOfferNumber());
                $offer->setCustomerId($address["customerId"]);
                $offer->setShopId($shopId);
                $offer->setLanguageIso($language);
                $offer->setPaymentId($this->Request()->getParam('paymentId'));
                $offer->setDispatchId($this->Request()->getParam('dispatchId'));
                $offer->setStatus('1');
                $offer->setCurrency($shop->getCurrency()->getCurrency());
                $offer->setInvoiceAmount('0');
                $offer->setDiscountAmount('0');
                $offer->setDiscountAmountNet('0');
                $offer->setInvoiceAmountNet('0');
                $offer->setInvoiceShippingNet($this->shippingWithoutTax($this->Request()->getParam('invoiceShipping')/$currency['factor'], $this->Request()->getParam('shippingTax')));
                $offer->setInvoiceShipping($this->Request()->getParam('invoiceShipping')/$currency['factor']);
                $this->getManager()->persist($offer);
                $this->getManager()->flush();
                $id = $offer->getId();
            } else {
                $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer', $id);
                if ($shopId){
                    $offer->setShopId($shopId);
                    $offer->setLanguageIso($language);
                }

                if ($this->Request()->getParam('paymentId'))
                    $offer->setPaymentId($this->Request()->getParam('paymentId'));
                if ($this->Request()->getParam('dispatchId'))
                    $offer->setDispatchId($this->Request()->getParam('dispatchId'));

                    $invoiceShipping = $this->Request()->getParam('invoiceShipping')/$currency['factor'];
                    $invoiceShippingNet = $this->shippingWithoutTax($invoiceShipping, $this->Request()->getParam('shippingTax'));
                    $offer->setInvoiceShippingNet($invoiceShippingNet);
                    $offer->setInvoiceShipping($invoiceShipping);
                    $offer->calculateInvoiceAmount();
                    //$offer->setDiscountAmountNet($offer->getDiscountAmount() + $invoiceShippingNet);
                    //$offer->setInvoiceAmountNet($offer->getDiscountAmount() + $invoiceShipping);

                if($address["customerId"]){
                    $offer->setCustomerId($address["customerId"]);
                }
                $this->getManager()->persist($offer);
                $this->getManager()->flush();
            }

            if ($address["customerId"]) {
                if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                    $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($address["customerId"]);
                    $customer = $user->getDefaultBillingAddress();
                } else {
                    $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Billing')
                        ->findOneBy(array('customerId' => $address["customerId"]));
                }
                $address["department"] = $customer->getDepartment();
                $address["salutation"] = $customer->getSalutation();
                $address["phone"] = $customer->getPhone();
                if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")) {
                    $address["fax"] = $customer->getFax();
                }
                $address["vatId"] = $customer->getVatId();

                $shippingAddress["department"] = $customer->getDepartment();
                $shippingAddress["salutation"] = $customer->getSalutation();
            }

            $this->saveOfferBillingAddress($address, $id);
            $this->saveOfferShippingAddress($shippingAddress, $id);
            $this->View()->assign(array(
                'success' => true,
                'data' => $id,
                'discountAmount'=>$offer->getDiscountAmount()

            ));
        } catch (Exception $e) {

            $this->View()->assign(array(
                'success' => false,
                'data' => $id,
                'message' => $e->getMessage()
            ));
            return;
        }
    }

    /**
     * This function returns shipping price without tax.
     *
     * @param $shippingNet
     * @param $tax
     * @return float
     */
    public function  shippingWithoutTax($shippingNet, $tax)
    {
        return round(($shippingNet *100 )/(100 + $tax), 2);
    }

    /**
     * This function delete position of offer.
     */
    public function deletePositionAction()
    {
        $namespace = Shopware()->Snippets()->getNamespace('backend/offer');
        //$positionId = $this->Request()->getParam('id');
        $positions = $this->Request()->getParam('positions', array(array('id' => $this->Request()->getParam('id'))));

        //check if any positions is passed.
        if (empty($positions)) {
            $this->View()->assign(array(
                    'success' => false,
                    'data' => $this->Request()->getParams(),
                    'message' => $namespace->get('no_order_passed', 'No orders passed'))
            );
            return;
        }

        //if no order id passed it isn't possible to update the order amount, so we will cancel the position deletion here.
        $offerId = $this->Request()->getParam('offerId', null);

        if (empty($offerId)) {
            $this->View()->assign(array(
                    'success' => false,
                    'data' => $this->Request()->getParams(),
                    'message' => $namespace->get('no_order_id_passed', 'No valid order id passed.'))
            );
            return;
        }

        try {
            foreach ($positions as $position) {
                if (empty($position['id'])) {
                    continue;
                }
                $model = Shopware()->Models()->find('Shopware\CustomModels\Offer\Detail', $position['id']);

                //check if the model was founded.
                if ($model instanceof \Shopware\CustomModels\Offer\Detail) {
                    Shopware()->Models()->remove($model);
                }
            }
            //after each model has been removed to executes the doctrine flush.
            Shopware()->Models()->flush();
            $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',    $offerId);
            $offer->calculateInvoiceAmount();
            Shopware()->Models()->flush();

            $data = $this->Request()->getParams();
            $this->View()->assign(array(
                'success' => true,
                'data' => $data,
                'invoiceAmount' =>$offer->getInvoiceAmount(),
                'discountAmount' =>$offer->getDiscountAmount()
            ));
        } catch (\Doctrine\ORM\ORMException $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => $this->Request()->getParams(),
                'message' => $e->getMessage()
            ));
            return;
        }
    }

    /**
     * This function returns new offerNumber for created new offer.
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

    public function getOriginalPriceByQuantity(){

        $quantity = $this->Request()->getParam('quantity');
        $customerId = $this->Request()->getParam('customerId');
        /** @var \Shopware\Models\Customer\Customer $customer */
        $customer = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->findOneBy(array(
            'id' => $customerId
        ));

        $shopId = $this->Request()->getParam('shopId');
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
            'id' => $shopId
        ));

        $articleDetailsId = $this->Request()->getParam('articleDetailsId');
        $articleId = $this->Request()->getParam('articleId');

        if($articleDetailsId){
            if(!$quantity) {
                $quantity = 1;
            }

            $articlePriceRepo = Shopware()->Models()->getRepository('Shopware\Models\Article\Price');
            if($customer->getGroupKey()){
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId, 'customerGroupKey' => $customer->getGroupKey()));
            }

            if(!is_array($articlePrice)  || count($articlePrice) == 0) {
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId, 'customerGroupKey' => $shop->getCustomerGroup()->getKey()));
            }

            if(!is_array($articlePrice) || count($articlePrice) == 0) {
                $articlePrice = $articlePriceRepo->findBy(array('articleDetailsId' => $articleDetailsId));
            }
            if($quantity && count($articlePrice)>1){
                foreach ($articlePrice as $key => $aPrice) {
                    if($aPrice->getFrom() <= $quantity && ($aPrice->getTO() >= $quantity || $aPrice->getTO() == 'beliebig' )){
                        $articlePrice = $articlePrice[$key];
                        break;
                    }
                }
            }else{
                $articlePrice = $articlePrice[0];
            }
        }

        if(!$articleId){
            $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $articleDetailsId));
            if(!$articleDetail instanceof \Shopware\Models\Article\Detail) {
                return $this->Request()->getParam('originalNetPrice');
            }
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
                $articleId = $articleDetail->getArticleId();
            } else {
                $articleId = $articleDetail->getArticle()->getId();
            }
        }


        $articleRepo = Shopware()->Models()->getRepository('Shopware\Models\Article\Article');
        /** @var \Shopware\Models\Article\Article $article */
//        $article = $articleRepo->findOneBy(array('id' => $articleId));

        $currency = Shopware()->Models()->toArray($shop->getCurrency());

        if(!isset($articlePrice) || empty($articlePrice)) {
            return $this->Request()->getParam('originalNetPrice');
        }
        $articleOriginalPriceWithoutTax =$articlePrice->getPrice()*$currency['factor'];
        return $articleOriginalPriceWithoutTax;
    }

    public function includeTax($amount, $taxRate){
        return ($amount * ($taxRate + 100) / 100);
    }

    public function excludeTax($amount, $taxRate){
        return ($amount / ($taxRate + 100) * 100);
    }

    /**
     * This function saves and updates position.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function savePositionAction()
    {
        $id = $this->Request()->getParam('id');
        $offerId = $this->Request()->getParam('offerId');
        $shopRepo = Shopware()->Models()->getRepository( 'Shopware\Models\Shop\Shop');
        $shop = $shopRepo->findOneBy(array('id' => $this->Request()->getParam('shopId')));
        $currency = Shopware()->Models()->toArray($shop->getCurrency());

        $originalPriceWithoutTax = $this->getOriginalPriceByQuantity($this->Request()->getParams());

        if ($this->Request()->getParam('taxId')){
            $tax = Shopware()->Models()->find('Shopware\Models\Tax\Tax', $this->Request()->getParam('taxId'));
            $taxRate = $tax->getTax();
        } elseif($this->Request()->getParam('taxRate')) {
            $taxRate = $this->Request()->getParam('taxRate');
        } else {
            $taxRate = 19;
        }
        $priceWithoutTax = $this->excludeTax($this->Request()->getParam('price'),$taxRate);
        $priceWithTax = $this->Request()->getParam('price');

        $originalPriceWithTax = $this->includeTax($originalPriceWithoutTax,$taxRate);

        if (empty($offerId)) {

            $originalPriceWithoutTax = $originalPriceWithoutTax/$currency['factor'];
            $customerId = $this->Request()->getParam('customerId');
            $shopId = $this->Request()->getParam('shopId');
            $paymentId = $this->Request()->getParam('paymentId');
            $dispatchId = $this->Request()->getParam('dispatchId');
            $priceWithoutTax = $priceWithoutTax/$currency['factor'];
            $quantity = $this->Request()->getParam('quantity');
            $invoiceShipping = $this->Request()->getParam('invoiceShipping')/$currency['factor'];
            $invoiceShippingNet = $this->shippingWithoutTax($invoiceShipping, $this->Request()->getParam('shippingTax'));
            $invoiceNet = $priceWithoutTax*$quantity;

            $offer = new Offer();
            $zendDate = new Zend_Date();
            $offer->setOfferTime($zendDate->get( "YYYY-MM-dd HH:mm:ss" ));
            $offer->setNumber($this->getOfferNumber());
            $offer->setCustomerId($customerId);
            $offer->setShopId($shopId);
            $offer->setPaymentId($paymentId);
            $offer->setDispatchId($dispatchId);
            $offer->setStatus('1');
            $offer->setCurrency($currency['currency']);
            // BaseAmount OR invoice_amount
            $offer->setInvoiceAmount($originalPriceWithTax*$quantity);
            // Amount OR discount_amount
            $offer->setDiscountAmount($priceWithTax*$quantity);

            $offer->setDiscountAmountNet($invoiceNet + $invoiceShippingNet);
            $offer->setInvoiceAmountNet(($priceWithoutTax*$quantity)+$invoiceShipping);
            $offer->setInvoiceShippingNet($invoiceShippingNet);
            $offer->setInvoiceShipping($invoiceShipping);
            $this->getManager()->persist($offer);
            $this->getManager()->flush();
        } else {
            $offer = $this->getManager()->find(
                'Shopware\CustomModels\Offer\Offer',
                $offerId
            );
        }
        try {
            //check if the passed position data is a new position or an existing position.
            if (empty($id)) {
                $position = new Detail();
                $this->getManager()->persist($position);
            } else {
                $position = $this->getManager()->find(
                    'Shopware\CustomModels\Offer\Detail',
                    $id
                );
            }
            $data = $this->Request()->getParams();
            $data = $this->getPositionAssociatedData($data);
                /*if($data['shopId']){
                    $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                        'id' => $data['shopId']
                    ));
                }*/
                $currency = Shopware()->Models()->toArray($shop->getCurrency());
                $data['originalPrice'] = $originalPriceWithoutTax / $currency['factor'];
                $data['price'] = $priceWithoutTax / $currency['factor'];


            if ($data === null) {
                $this->View()->assign(array(
                    'success' => false,
                    'data' => array(),
                    'message' => 'The articlenumber "' . $this->Request()->getParam('articleNumber', '') . '" is not valid'
                ));
                return;
            }

            $position->fromArray($data);
            $position->setOffer($offer);
            $this->getManager()->flush();

            $data['id'] = $position->getId();
            $articleRepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail');
            $article = $articleRepository->findOneBy(array('number' => $position->getArticleNumber()));
            if ($article instanceof \Shopware\Models\Article\Detail) {
                $data['inStock'] = $article->getInStock();
            }

            if($offerId){
            $offer->calculateInvoiceAmount();
            Shopware()->Models()->flush();
            $invoiceAmountNet = $offer->getInvoiceAmountNet();
            }
            if ($position->getOffer() instanceof \Shopware\CustomModels\Offer\Offer) {
                $invoiceAmountNet = $position->getOffer()->getInvoiceAmountNet();
            }
            if($data['purchasePrice'])
            $data['purchasePrice'] = $data['purchasePrice'] * $currency['factor'];

            $data['originalPrice'] = $originalPriceWithTax / $currency['factor'];
            $data['price'] = $priceWithTax/ $currency['factor'];
            $this->View()->assign(array(
                'success' => true,
                'data' => $data,
                'invoiceAmount' => $invoiceAmountNet,
                'offerId'=>$offer->getId(),
                'positionId'=>$position->getId()
            ));
            return;
        } catch (\Doctrine\ORM\ORMException $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => array(),
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * This function returns associated data for position.
     *
     * @param $data
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    private function getPositionAssociatedData($data)
    {
        //checks if the tax id for the position is passed and search for the assigned tax model
        if (!empty($data['taxId'])) {
            $tax = Shopware()->Models()->find('Shopware\Models\Tax\Tax', $data['taxId']);
            if ($tax instanceof \Shopware\Models\Tax\Tax) {
                $data['tax'] = $tax;
                $data['taxRate'] = $tax->getTax();
            }
        } else {
            unset($data['tax']);
        }
        return $data;
    }

    /**
     * This function returns all offer records with associated data.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function listAction(){

        $filter = $this->Request()->getParam('filter', null);
        $sort = $this->Request()->getParam('sort', null);
        $page = $this->Request()->getParam('page', 1);
        $start = $this->Request()->getParam('start', 0);
        $limit = $this->Request()->getParam('limit', 20);
        if (empty($sort)) {
            $sort = array(array('property' => 'o.offerTime', 'direction' => 'DESC'));
        } else {
            $sort[0]['property'] = 'o.' . $sort[0]['property'];
        }
        $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer');
        $builder = $repository->getListQuery($filter, $sort);

        $builder = $builder->setFirstResult($start)
            ->setMaxResults($limit);

        $query = $builder->getQuery();
        $query->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $paginator = $this->getModelManager()->createPaginator($query);
        $total = $paginator->count();

        $data = $paginator->getIterator()->getArrayCopy();
        if(!empty($data)) {
            $offers = $this->processOffersData($data);
        }

        // sends the out put to the view of the HBox
        $this->View()->assign(array(
            'success' => true,
            'data'    => $offers,
            'total' => $total
        ));
    }

    public function processOffersData($data){
        foreach ($data as $key =>$offer) {
            if($offer['shopId']){
                $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                    'id' => $offer['shopId']
                ));
            }
            $currency = Shopware()->Models()->toArray($shop->getCurrency());
            $data[$key]['invoiceAmount'] = $offer['invoiceAmount'] * $currency['factor'];
            $data[$key]['invoiceAmountNet'] = $offer['invoiceAmountNet'] * $currency['factor'];
            $data[$key]['discountAmount'] = $offer['discountAmount'] * $currency['factor'];
            $data[$key]['invoiceShipping'] = $offer['invoiceShipping'] * $currency['factor'];
            $data[$key]['currency'] = $currency['currency'];
            foreach ($offer["details"] as $detailKey => &$offerDetail) {
                $articleRepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail');
                $articleDetail = $articleRepository->findOneBy(array('number' => $offerDetail["articleNumber"]));
                if ($articleDetail instanceof \Shopware\Models\Article\Detail) {
                    $data[$key]['details'][$detailKey]['inStock'] = $articleDetail->getInStock();
					if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
                    	$data[$key]['details'][$detailKey]['articleId'] = $articleDetail->getarticleId();
					} else {
	                    $data[$key]['details'][$detailKey]['articleId'] = $articleDetail->getArticle()->getId();
					}
                    //purchase price
                    $articlePrice = $articleDetail->getPrices();

                    if($offerDetail['quantity'] && count($articlePrice)>1){
                        foreach ($articlePrice as $offer_key => $aPrice) {
                            if($articlePrice instanceof \Shopware\Models\Article\Price) {
                                if(($aPrice->getFrom() <= $offerDetail['quantity'] && ($aPrice->getTO() >= $offerDetail['quantity'] || $aPrice->getTO() == 'beliebig')) && $aPrice->getCustomerGroup()
                                        ->getKey() == $data[0]['customer']['groupKey']
                                ) {
                                    $articlePrice = $articlePrice[$offer_key];
                                    break;
                                }
                            }
                        }
                    }else{
                        $articlePrice = $articlePrice[0];
                    }

                    if($articlePrice){
                        $purchasePrice = 0;
                        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                            $purchasePrice = $articleDetail->getPurchasePrice();
                        }
                        else {
//                            $purchasePrice = $articlePrice->getBasePrice();
                        }
                        if($offerDetail['taxRate'])
                            $data[$key]['details'][$detailKey]['purchasePrice'] = $this->calculatePrice($purchasePrice, $offerDetail['taxRate'])*$currency['factor'];
                        else
                            $data[$key]['details'][$detailKey]['purchasePrice'] = $purchasePrice * $currency['factor'];
                    }
                }
                $data[$key]['details'][$detailKey]['originalNetPrice'] = $offerDetail['originalPrice'] * $currency['factor'];
                $data[$key]['details'][$detailKey]['originalPrice'] = $this->includeTax($offerDetail['originalPrice'], $offerDetail['taxRate']) * $currency['factor'];
                $data[$key]['details'][$detailKey]['price'] = $this->includeTax($offerDetail['price'], $offerDetail['taxRate']) * $currency['factor'];
                $data[$key]['details'][$detailKey]['currency'] = $currency['currency'];
                //$data[$key]['details'][$detailKey]['currency'] = $offer['currency'];
            }
            if($offer['orderId']){
                $order = $this->getManager()->find(
                    'Shopware\Models\Order\Order',
                    $offer['orderId']
                );
                if($order){
                    $data[$key]['orderNumber'] = $order->getNumber();
                }

            }
        }
        return $data;
    }

    /**
     * This function get all offer data and creates document.
     */
    public function createDocumentAction() {
        Shopware()->Template()->clearAllCache();
        //Shopware()->Template()->config_read_hidden = true;

        try {
            $offerId =  $this->Request()->getParam('offerId', null);
            $mediaId =  $this->Request()->getParam('mediaId', null);
            $doc = Shopware()->Models()->getRepository('Shopware\Models\Document\Document')->findOneBy(array('template' => 'offerBill.tpl'));
            $documentType = $doc->getId();
            $repository = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer');
            $builder = $repository->getListQuery(array(array('property' => 'o.id', 'value' => $offerId)), null,null,null);
            $query = $builder->getQuery();
            $offers = $query->getResult();
            if((empty($mediaId) || $mediaId==-1) && !empty($offerId) && !empty($documentType)) {
                $offerHelper = Shopware()->OfferHelper();
                $offerHelper->createDocument($offers[0], $documentType, $this->Request()->getParam('deliveryDate', null), $this->Request()->getParam('documentFormat', null), $this->Request()->getParam('taxFree', false));
            } else {
                $document =  Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Document\Document')->findOneBy(array('offerId'=>$offerId, 'documentId'=>-1));
                if(!$document instanceof \Shopware\CustomModels\Offer\Document\Document) {
                    $document = new Document();
                }
                $hash = $this->copyMediaToDocuments($mediaId);
                $document->setDate(new DateTime());
                $document->setTypeId($documentType);
                $document->setCustomerId($offers[0]->getCustomerId());
                $document->setOfferId($offerId);
                $document->setAmount(round($offers[0]->getInvoiceAmount(),2));
                $document->setDocumentId(-1);
                $document->setHash($hash);
                $document->setMediaId($mediaId);
                $offers[0]->setStatus(2);

                try {
                    $this->getManager()->persist($document);
                    $this->getManager()->persist($offers[0]);
                    $this->getManager()->flush();

                } catch(Exception $e) {
                    $var = $e;
                }
            }

            $offerRecord = $query->getArrayResult();

            $this->View()->assign(array(
                'success' => true,
                'data'    => $offerRecord,
                'message' => 'Document created successfully'
            ));

        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => $this->Request()->getParams(),
                'message' => $e->getMessage()
            ));
        }
    }

    private function copyMediaToDocuments($mediaId) {
        $mediaService = Shopware()->Container()->get('shopware_media.media_service');
        $media = Shopware()->Models()->getRepository('Shopware\Models\Media\Media')->find($mediaId);
        if($media instanceof \Shopware\Models\Media\Media) {
            $hash = md5(uniqid(rand()));
            $file = $mediaService->encode(Shopware()->DocPath() . $media->getPath());

            $name = basename($hash) . '.pdf';
            $destination = Shopware()->DocPath('files/documents') . $name;
            if(copy($file,$destination)) {
                return $hash;
            }
        }
        return null;
    }

    /**
     * This function gets all document data to show document in pdf formate.
     */
    public function openPdfAction() {
        try {
            $id = $this->Request()->getParam('id', null);
            $isMedia = ($this->Request()->getParam('isMedia', false)=="true")?true:false;
            $name = basename($id) . '.pdf';
            $file = Shopware()->DocPath('files/documents') . $name;
            if(!file_exists($file)) {
                $this->View()->assign(array(
                    'success' => false,
                    'data' => $this->Request()->getParams(),
                    'message' => 'File not exist'
                ));
                return;
            }

            // Disable Smarty rendering
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
            $this->Front()->Plugins()->Json()->setRenderer(false);

            $offerModel = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Document\Document')->findOneBy(array("hash"=>$this->Request()->getParam('id')));
            $offerModel = Shopware()->Models()->toArray($offerModel);
            $offerId = $offerModel["documentId"];

            $response = $this->Response();
            $response->setHeader('Cache-Control', 'public');
            $response->setHeader('Content-Description', 'File Transfer');
            $response->setHeader('Content-disposition', 'attachment; filename='.($isMedia?'Angebot':$offerId).".pdf" );
            $response->setHeader('Content-Type', 'application/pdf');
            $response->setHeader('Content-Transfer-Encoding', 'binary');
            $response->setHeader('Content-Length', filesize($file));
//            echo file_get_contents($file);

            $data = readfile($file);
            echo $data;
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => $this->Request()->getParams(),
                'message' => $e->getMessage()
            ));
            return;
        }
        //removes the global PostDispatch Event to prevent assignments to the view that destroyed the pdf
        Enlight_Application::Instance()->Events()->removeListener(new Enlight_Event_EventHandler('Enlight_Controller_Action_PostDispatch',''));
    }



    public function getMailDetailsAction(){
        try{
            $id = $this->Request()->getParam("id",null);
            $customerId = $this->Request()->getParam("customerId",null);
            $offerId = $this->Request()->getParam("offerId",null);

            if(empty($id)) {
                $this->View()->assign(array(
                    'success' => false,
                    'message' => "Document Not Found!"
                ));
                return false;
            }

            if(empty($customerId)) {
                $this->View()->assign(array(
                    'success' => false,
                    'message' => "Customer Not Found!"
                ));
                return false;
            }

            $mail = $this->sendMailFromBackend($id,$customerId,$offerId);
            if ($mail instanceof Enlight_Components_Mail) {
                $customerMail = array(
                    'mail' => $mail,
                    'data' => array(
                        'error' => false,
                        'content' => $mail->getPlainBodyText(),
                        'subject' => $mail->getPlainSubject(),
                        'to' => implode(', ', $mail->getTo()),
                        'fromMail' => $mail->getFrom(),
                        'fromName' => $mail->getFromName(),
                        'sent' => false,
                        'offerId' => $offerId,
                        'customerId' => $customerId,
                        'id' => $id
                    )
                );
            } else {
                $customerMail = array();
            }
            if (!empty($customerMail)) {
                $data['mail'] = $customerMail['data'];
            } else {
                $data['mail'] = null;
            }

            $this->View()->assign(array(
                'success' => true,
                'data' => $data
            ));
        } catch (\Doctrine\ORM\ORMException $e) {
            $this->View()->assign(array(
                'success' => false,
                'data' => array(),
                'message' => $e->getMessage()
            ));
        }
    }

    /**
     * @return array
     */
    public function sendMailAction()
    {
        $data = $this->Request()->getParams();

        if (empty($data)) {
            $this->View()->assign(array(
                    'success' => false,
                    'data' => $data,
                    'message' => 'no_data_passed'
            ));
            return;
        }

        try {
            //$mail = clone Shopware()->Mail();
            $mail = $this->sendMailFromBackend($data['id'],$data['customerId'],$data['offerId'],true);
            $mail->clearRecipients();
            $mail->clearSubject();
            $mail->setSubject($this->Request()->getParam('subject', ''));
//            $mail->clearDefaultTransport();
            $mail->setBodyText($this->Request()->getParam('content', ''));
//            $mail->setBodyHtml(null);
            $mail->clearFrom();
            $mail->setFrom($this->Request()->getParam('fromMail', ''), $this->Request()->getParam('fromName', ''));
            $mail->clearReplyTo();
            $mail->addTo($this->Request()->getParam('to', ''));
            $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
            if($config->ownerMailAddress)
            $mail->addBcc($config->ownerMailAddress);

            $mail->send();

            $this->View()->assign(array(
                'success' => true,
                'data' => $data
            ));
            return;
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'message' => $e->getMessage(),
                'data' => $data
            ));
            return;
        }
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function sendDocumentByMailAction() {
        //$documentId = $this->Request()->getParam("documentId",null);
        $id = $this->Request()->getParam("id",null);
        $customerId = $this->Request()->getParam("customerId",null);
        $offerId = $this->Request()->getParam("offerId",null);

        if(empty($id)) {
            $this->View()->assign(array(
                'success' => false,
                'message' => "Document Not Found!"
            ));
            return false;
        }

        if(empty($customerId)) {
            $this->View()->assign(array(
                'success' => false,
                'message' => "Customer Not Found!"
            ));
            return false;
        }
        $mail = $this->sendMailFromBackend($id,$customerId,$offerId);
        $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
        if($config->ownerMailAddress)
        $mail->addBcc($config->ownerMailAddress);
        $return = $mail->send();

        if(is_array($return) && $return["success"]==false) {
            $this->View()->assign($return);
        } else {
            // updating status of Offer
            $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offerId);
            $offer->setStatus(3);
            try {
                Shopware()->Models()->persist($offer);
                Shopware()->Models()->flush();
            } catch(Exception $e) {

            }

            $this->View()->assign(array(
                'success' => true,
                'message' => "Document Sent Successfully"
            ));
        }
    }

    /**
     * This function prepare mail for offer.
     *
     * @param $id
     * @param $customerId
     * @param $offerId
     * @return bool|Zend_Mail
     * @throws Enlight_Exception
     */
    public function sendMailFromBackend($id, $customerId, $offerId, $plain=false) {
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(o)->from('Shopware\CustomModels\Offer\Offer', 'o')->where('o.id = :id');
        $builder->setParameter('id', $offerId);
        $offers = $builder->getQuery()->getArrayResult();

        if(count($offers) != 1) {
            return false;
        }

        $offer = $offers[0];

        $document = Shopware()->Db()->fetchRow("SELECT * FROM s_offer_documents WHERE id = ?", array($id));
        $sql = "SELECT id FROM s_core_documents WHERE template = ?";
        $docID = Shopware()->Db()->fetchOne($sql, array('offerBill.tpl'));

        /*$repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        $shopId = $offer['subshopID'];
        $shop = $repository->getActiveById($shopId);
        $shop->registerResources(Shopware()->Bootstrap());*/


        switch($document["type"]) {
            case $docID:
                $mailType = "sOfferDocuments";
                $attachmentName = 'offer ' . '{$invoicenumber}';
                break;

            default:
                return false;
                break;
        }

        $document['attachmentName'] = $attachmentName;

        //fetch userData
        $customerResource = \Shopware\Components\Api\Manager::getResource('Customer');
        $customer = $customerResource->getOne($customerId);

        $offerHelper = Shopware()->OfferHelper();
        Shopware()->Models()->clear();

        $mailModel = $this->getModelManager()->getRepository('Shopware\Models\Mail\Mail')
            ->findOneBy(array('name' => $mailType));

        if($plain) {
            $originalIsHtml = $mailModel->isHtml();
            $mailModel->setIsHtml(!$plain);
        }

        $mail = $offerHelper->sendMail($mailModel, $document, array($customer['email']), $offer['number']);

        if($plain) {
            $mailModel->setIsHtml($originalIsHtml);
            Shopware()->Models()->flush($mailModel);
        }
        return $mail;
    }

    /**
     * This function fires when offer is being coverted to order and it saves offer data to order.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function saveOrderAction()
    {
        $offerId = $this->Request()->getParam("offerId",null);
        $offer = $this->getManager()->find(
            'Shopware\CustomModels\Offer\Offer',
            $offerId
        );
        if($offer->getShopId()){
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                'id' => $offer->getShopId()
            ));
            $shop->registerResources(Shopware()->Bootstrap());
            $currency = Shopware()->Models()->toArray($shop->getCurrency());
        }

        try {
            // Insert basic-data of the order
            $orderNumber = Shopware()->Modules()->Order()->sGetOrderNumber();
            $net = "0";
            $taxfree = "0";
            $customer = $offer->getCustomer();

            if ($customer->getGroup()->getTax()==0) {
                $net = "1";
            }

            if($offer->getLanguageIso()){
                $language = $offer->getLanguageIso();
            }else{
                $language = $offer->getShopId();
            }

            $sql = "
            INSERT INTO s_order (
                ordernumber, userID, invoice_amount,invoice_amount_net,invoice_shipping,invoice_shipping_net, ordertime, status,cleared, paymentID, transactionID, comment, customercomment, internalcomment,
                net,taxfree, partnerID,temporaryID,referer,trackingcode,language,dispatchID,currency,currencyFactor,subshopID,remote_addr
                )VALUES ('" . $orderNumber . "'," . $offer->getCustomerId() . "," . ($offer->getDiscountAmount()+$offer->getInvoiceShipping())*$currency['factor'] . "," . $offer->getDiscountAmountNet()*$currency['factor'] . ", " . $offer->getInvoiceShipping()*$currency['factor'] . ", " . $offer->getInvoiceShippingNet()*$currency['factor'] . ",now(), 0,
                17, " . $offer->getPaymentId() . ",'','" .$offer->getComment(). "' ,'" .$offer->getCustomerComment(). "' ,'" .$offer->getInternalComment(). "', $net, $taxfree,'', '', '','','" . $language . "','" . $offer->getDispatchId() . "','" . $offer->getCurrency() . "','" . '1' . "','" . $offer->getShopId() . "',  ''
            )";

            Shopware()->Db()->query($sql);
            $orderID = Shopware()->Db()->lastInsertId();

            $sql = "INSERT INTO s_order_attributes (orderID) VALUES (?)";
            Shopware()->Db()->query($sql,array($orderID));

            $position = 0;

            $offerDetails = array();

            foreach ($offer->getDetails()->toArray() as $key => $basketRow) {
                $offerDetails[] = Shopware()->Models()->toArray($basketRow);
                $articleDetail = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail')->findOneBy(array('id' => $basketRow->getArticleDetailsId()));
                if($articleDetail){
					if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
                    	$articleId = $articleDetail->getArticleId();
					} else {
	                    $articleId = $articleDetail->getArticle()->getId();
					}
                    $offerDetails[$key]['articleId'] = $articleId;
                }

                $position++;
                if (!$basketRow->getPrice())
                    $basketRow->setPrice(0);

                if($net=="1") {
                    $basketRowPrice = $basketRow->getPrice()*$currency['factor'];
                } else {
                    $basketRowPrice = $basketRow->getPrice()*$currency['factor']*((100+$basketRow->getTaxRate())/100);
                }

                $sql = "
            INSERT INTO s_order_details
                (orderID,
                ordernumber,
                articleID,
                articleordernumber,
                price,
                quantity,
                name,
                status,
                releasedate,
                modus,
                esdarticle,
                taxID,
                tax_rate,
                config
                )
                VALUES (
                $orderID,
                " . $orderNumber . ",
                " . $articleId . ",
                '" . $basketRow->getArticleNumber() . "',
                " . $basketRowPrice . ",
                " . $basketRow->getQuantity() . ",
                '" . $basketRow->getArticleName() . "',
                '0',
                '0000-00-00',
                " . $basketRow->getMode() . ",
                '0',
                " . $basketRow->getTaxId() . ",
                " . $basketRow->getTaxRate() . ",
                ''
            )";
                Shopware()->Db()->query($sql);
                $orderDetailID = Shopware()->Db()->lastInsertId();

                $sql = "INSERT INTO s_order_details_attributes (detailID) VALUES (?)";
                Shopware()->Db()->query($sql,array($orderDetailID));


                //add custom products mode and hash
                $customProductHash = $basketRow->getSwagCustomProductsConfigurationHash();
                $customProductMode = $basketRow->getSwagCustomProductsMode();
                if(isset($customProductHash) && !empty($customProductHash)) {
                    $orderDetailsAttributesId = Shopware()->Db()->lastInsertId();

                    $sql = "UPDATE s_order_details_attributes SET swag_custom_products_mode=?, swag_custom_products_configuration_hash=? WHERE id=?";
                    Shopware()->Db()->query($sql,array($customProductMode, $customProductHash, $orderDetailsAttributesId));
                }

                //update quantity of article
                $this->refreshOrderedVariant(
                    $basketRow->getArticleNumber(),
                    $basketRow->getQuantity()
                );
            }

            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select(o)->from('Shopware\CustomModels\Offer\Billing', 'o')->where('o.offerId = :offerId');
            $builder->setParameter('offerId', $offer->getId());
            $billing = $builder->getQuery()->getArrayResult();


            if (!empty($billing)) {
                // Save Billing-Address
                $this->saveBillingAddress($billing[0], $orderID);
            }

            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select(o)->from('Shopware\CustomModels\Offer\Shipping', 'o')->where('o.offerId = :offerId');
            $builder->setParameter('offerId', $offer->getId());
            $shipping = $builder->getQuery()->getArrayResult();
            if (empty($shipping)) {
                $shipping = $billing;
            }
            if (!empty($shipping)) {
                // Save Shipping-Address
                $this->saveShippingAddress($shipping[0], $orderID);
            }

            $shopId = $offer->getShopId();
            /** @var \Shopware\Models\Shop\Shop $shop */
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                'id' => $shopId
            ));
            //$shop->registerResources(Shopware()->Bootstrap());

            //reframing offerDetail's name for sending mail
            foreach ($offerDetails as &$offerDetail) {
                $offerDetail['articlename'] = $offerDetail['articleName'];
                $offerDetail['ordernumber'] = $offerDetail['articleNumber'];
                $offerDetail['amount'] = $offerDetail['price'] * $offerDetail['quantity'] * $currency['factor'];
                if($net!="1") {
                    $offerDetail['amount'] = $this->calculatePrice($offerDetail['amount'],$offerDetail['taxRate']);
                    $offerDetail['price'] = $this->calculatePrice($offerDetail['price'],$offerDetail['taxRate']);
                }
                $offerDetail['amount'] = $this->moneyFormat($offerDetail['amount'],'');
                $offerDetail['price'] = $this->moneyFormat($offerDetail['price'],'');
                $offerDetail['tax_rate'] = $offerDetail['taxRate'];
                $offerDetail['orderDetailId'] = $offerDetail['id'];
                $offerDetail['image'] = Shopware()->Modules()->Articles()->sGetArticlePictures($offerDetail['articleId']);
            }

            //reframing billingAddress's name for sending mail
            $billing[0]['firstname'] = $billing[0]['firstName'];
            $billing[0]['lastname'] = $billing[0]['lastName'];
            $billing[0]['customernumber'] = $billing[0]['number'];;
            $billing[0]['zipcode'] = $billing[0]['zipCode'];

            if ($billing[0]['countryId']){
                $country = Shopware()->Models()->getRepository('Shopware\Models\Country\Country')->findOneBy(array(
                    'id' => $billing[0]['countryId']
                ));
            }
            $country = Shopware()->Models()->toArray($country);

            //reframing shippingAddress's name for sending mail
            $shipping[0]['firstname'] = $shipping[0]['firstName'];
            $shipping[0]['lastname'] = $shipping[0]['lastName'];
            $shipping[0]['customernumber'] = $shipping[0]['number'];;
            $shipping[0]['zipcode'] = $shipping[0]['zipCode'];

            if ($shipping[0]['countryId']){
                $countryShipping = Shopware()->Models()->getRepository('Shopware\Models\Country\Country')->findOneBy(array(
                    'id' => $shipping[0]['countryId']
                ));
            }
            $countryShipping = Shopware()->Models()->toArray($countryShipping);
            $country['countryname'] = $country['name'];
            $countryShipping['countryname'] = $countryShipping['name'];

            //adding all offer details to variable for sending mail
            $variables['sOrderDetails'] = $offerDetails;
            $variables['billingaddress'] = $billing[0];
            $variables['shippingaddress'] = $shipping[0];
            $variables['additional']['country'] = $country;
            $variables['additional']['countryShipping'] = $countryShipping;
            $variables['additional']['user'] = Shopware()->Models()->toArray($offer->getCustomer());
            $variables['additional']['payment'] = Shopware()->Models()->toArray($offer->getPayment());
            $variables['sDispatch'] = Shopware()->Models()->toArray($offer->getDispatch());
            $shippingCost = $offer->getInvoiceShipping();
            if($net=="1") {
                $shippingCost = $offer->getInvoiceShippingNet();
            }
            $variables['sShippingCosts'] = $this->moneyFormat($shippingCost);
            $variables['sAmountNet'] = $this->moneyFormat($offer->getDiscountAmountNet()*$currency['factor']+$offer->getInvoiceShippingNet());
            $variables['sAmount'] = $this->moneyFormat(($offer->getDiscountAmount()*$currency['factor'])+$offer->getInvoiceShipping());
            $variables['ordernumber'] = $orderNumber;
            $zendDate = new Zend_Date();
            $variables['sOrderDay'] = $zendDate->get( "YYYY-MM-dd" );
            $variables['sOrderTime'] = $zendDate->get( "HH:mm:ss" );
            $variables['sCurrency'] = $currency['currency']; //$currency['symbol'];

            Shopware()->Modules()->Order()->sUserData = array(
                'additional' => array(
                    'user' => array(
                        'email' => $offer->getCustomer()->getEmail()
                    )
                )
            ) ;

            //adding offer document to order document grid
            $sql = "
            INSERT INTO s_order_documents (`date`, `type`, `userID`, `orderID`, `amount`, `docID`,`hash`)
            VALUES ( NOW() , ? , ? , ?, ?, ?,?)
            ";
            $offerDocumentsArray = $offer->getDocuments()->toArray();
            $doc = $offerDocumentsArray[0];
            $result = Shopware()->Db()->query($sql,array(
                $doc->getTypeId(),
                $offer->getCustomerId(),
                $orderID,
                $offer->getDiscountAmount()*$currency['factor'],
                $offer->getNumber(),
                $doc->getHash()
            ));

            if(!empty($result)) {
                $documentID = Shopware()->Db()->lastInsertId();

                $sql = "INSERT INTO s_order_documents_attributes (documentID) VALUES (?)";
                Shopware()->Db()->query($sql, array($documentID));
            }

            // updating status of Offer
            $offer->setOrderId($orderID);
            $offer->setStatus(5);

            //Shopware()->Modules()->Order()->sendMail($variables);
            $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
            if($config->backendOrderMail==1) {
                $this->sendMail($variables);
            }

            try {
                Shopware()->Models()->persist($offer);
                Shopware()->Models()->flush();
            } catch(Exception $e) {

            }

            $this->View()->assign(array(
                'success' => true,
                'data'    => $offer
            ));
        } catch (Exception $e) {
                $this->View()->assign(array(
                'success' => false,
                'data' => $this->Request()->getParams(),
                'message' => $e->getMessage()
        ));
    }
    }

    private function moneyFormat($value, $currency="EUR") {
        return trim(str_replace(".",",",sprintf('%02.2f '.$currency,$value)));
    }

    /**
     * send order confirmation mail
     * @access public
     */
    public function sendMail($variables)
    {
        $context = array(
            'sOrderDetails' => $variables["sOrderDetails"],

            'billingaddress'  => $variables["billingaddress"],
            'shippingaddress' => $variables["shippingaddress"],
            'additional'      => $variables["additional"],

            'sCurrency'      => $variables["sCurrency"],
            'sTaxRates'      => $variables["sTaxRates"],
            'sShippingCosts' => $variables["sShippingCosts"],
            'sAmount'        => $variables["sAmount"],
            'sAmountNet'     => $variables["sAmountNet"],

            'sOrderNumber' => $variables["ordernumber"],
            'sOrderDay'    => $variables["sOrderDay"],
            'sOrderTime'   => $variables["sOrderTime"],
            'sComment'     => $variables["sComment"],

            'attributes'     => $variables["attributes"],
        );

        $mail = null;
        if (!($mail instanceof \Zend_Mail)) {
            $mail = Shopware()->TemplateMail()->createMail('sORDER', $context);
        }

        $mail->addTo($variables["additional"]["user"]["email"]);

        if (!($mail instanceof \Zend_Mail)) {
            return;
        }
        $mail->send();

    }


    /**
     * This function updates the data for an ordered variant.
     * The variant sales value will be increased by the passed quantity
     * and the variant stock value decreased by the passed quantity.
     *
     * @param string $orderNumber
     * @param int $quantity
     */
    private function refreshOrderedVariant($orderNumber, $quantity)
    {
        Shopware()->Db()->executeUpdate("
            UPDATE s_articles_details
            SET sales = sales + :quantity,
                instock = instock - :quantity
            WHERE ordernumber = :number",
            array(':quantity' => $quantity, ':number' => $orderNumber)
        );
    }

    /**
     * This function saves or updates billing address for offer.
     *
     * @param $address
     * @param $id
     * @return int
     */
    public function saveOfferBillingAddress($address,$id)
    {

        if($address['stateId'] == null)
        {
            $address['stateId']='0';
        }

        if(!isset($address["department"]) || empty($address["department"])) {
            $address["department"] = "";
        }
        if(!isset($address["phone"]) || empty($address["phone"])) {
            $address["phone"] = "";
        }
        if(!isset($address["fax"]) || empty($address["fax"])) {
            $address["fax"] = "";
        }
        if(!isset($address["vatId"]) || empty($address["vatId"])) {
            $address["vatId"] = "";
        }

        $checkForExistingDocument = Shopware()->Db()->fetchRow("SELECT id FROM s_offer_billingaddress WHERE offerID = ?",array($id));

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

            $array = array(
                $address["customerId"],
                $address["number"],
                $address["company"],
                $address["department"],
                $address["salutation"],
                $address["firstName"],
                $address["lastName"],
                $address["street"],
                $address["zipCode"],
                $address["city"],
                $address["phone"],
                $address["fax"],
                $address["countryId"],
                $address["stateId"],
                $address["vatId"],
                $address["shippingTax"],
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

        $array = array(
            $address["customerId"],
            $id,
            $address["number"],
            $address["company"],
            $address["department"],
            $address["salutation"],
            $address["firstName"],
            $address["lastName"],
            $address["street"],
            $address["zipCode"],
            $address["city"],
            $address["phone"],
            $address["fax"],
            $address["countryId"],
            $address["stateId"],
            $address["vatId"],
            $address["shippingTax"]
        );

        }
        $result = Shopware()->Db()->executeUpdate($sql,$array);
        return $result;
    }

    /**
     * This function saves or updates shipping address for offer.
     *
     * @param $address
     * @param $id
     * @return int
     */
    public function saveOfferShippingAddress($address,$id)
    {

        if($address['stateId'] == null)
        {
            $address['stateId']='0';
        }

        if(!isset($address["department"]) || empty($address["department"])) {
            $address["department"] = "";
        }
        if(!isset($address["phone"]) || empty($address["phone"])) {
            $address["phone"] = "";
        }
        if(!isset($address["fax"]) || empty($address["fax"])) {
            $address["fax"] = "";
        }
        if(!isset($address["vatId"]) || empty($address["vatId"])) {
            $address["vatId"] = "";
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

            $array = array(
                $address["customerId"],
                $address["company"],
                $address["department"],
                $address["salutation"],
                $address["firstName"],
                $address["lastName"],
                $address["street"],
                $address["zipCode"],
                $address["city"],
                $address["countryId"],
                $address["stateId"],
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

            $array = array(
                $address["customerId"],
                $id,
                $address["company"],
                $address["department"],
                $address["salutation"],
                $address["firstName"],
                $address["lastName"],
                $address["street"],
                $address["zipCode"],
                $address["city"],
                $address["countryId"],
                $address["stateId"]
            );

        }
        $result = Shopware()->Db()->executeUpdate($sql,$array);
        return $result;
    }


    /**
     * This function Save order billing address
     * @access public
     */
    public function saveBillingAddress($address,$id)
    {

        if($address['stateId'] == null)
        {
            $address['stateId']='0';
        }

        if(!isset($address["department"]) || empty($address["department"])) {
            $address["department"] = "";
        }
        if(!isset($address["phone"]) || empty($address["phone"])) {
            $address["phone"] = "";
        }
        if(!isset($address["fax"]) || empty($address["fax"])) {
            $address["fax"] = "";
        }
        if(!isset($address["vatId"]) || empty($address["vatId"])) {
            $address["vatId"] = "";
        }

        $sql = "
        INSERT INTO s_order_billingaddress
        (
            userID,
            orderID,
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
            ustid
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
            ?
            )
        ";

        $array = array(
            $address["customerId"],
            $id,
            $address["number"],
            $address["company"],
            $address["department"],
            $address["salutation"],
            $address["firstName"],
            $address["lastName"],
            $address["street"],
            $address["zipCode"],
            $address["city"],
            $address["phone"],
            $address["fax"],
            $address["countryId"],
            $address["stateId"],
            $address["vatId"]
        );

        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
            $sql = "
        INSERT INTO s_order_billingaddress
        (
            userID,
            orderID,
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
            countryID,
            stateID,
            ustid
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
            ?
            )
        ";

            $array = array(
                $address["customerId"],
                $id,
                $address["number"],
                $address["company"],
                $address["department"],
                $address["salutation"],
                $address["firstName"],
                $address["lastName"],
                $address["street"],
                $address["zipCode"],
                $address["city"],
                $address["phone"],
                $address["countryId"],
                $address["stateId"],
                $address["vatId"]
            );
        }

		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
		    $sql = "
		    INSERT INTO s_order_billingaddress
		    (
		        userID,
		        orderID,
		        customernumber,
		        company,
		        department,
		        salutation,
		        firstname,
		        lastname,
		        street,
				streetnumber,
		        zipcode,
		        city,
		        phone,
		        fax,
		        countryID,
		        stateID,
		        ustid
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

		    $streetAndNumber = $this->seperateStreetAndNumber(str_replace(',', '', $address["street"]));
		    if(is_array($streetAndNumber)) {
		        $street = trim($streetAndNumber[1]);
		        $streetnumber = trim($streetAndNumber[2]);
		    } else {
		        $street = $streetAndNumber;
		        $streetnumber = "";
		    }

			$array = array(
				$address["customerId"],
				$id,
				$address["number"],
				$address["company"],
				$address["department"],
				$address["salutation"],
				$address["firstName"],
				$address["lastName"],
				$street,
				$streetnumber,
				$address["zipCode"],
				$address["city"],
				$address["phone"],
				$address["fax"],
				$address["countryId"],
				$address["stateId"],
				$address["vatId"]
			);
		}

        $result = Shopware()->Db()->executeUpdate($sql,$array);

		if(!empty($result)) {
            $billingID = Shopware()->Db()->lastInsertId();

            $sql = "INSERT INTO s_order_billingaddress_attributes (billingID) VALUES (?)";
            Shopware()->Db()->query($sql, array($billingID));
        }

        return $result;
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

    /**
     * This function save order shipping address
     * @access public
     */
    public function saveShippingAddress($address,$id)
    {
        if($address['stateId'] == null)
        {
            $address['stateId']='0';
        }

        if(!isset($address["department"]) || empty($address["department"])) {
            $address["department"] = "";
        }
        if(!isset($address["phone"]) || empty($address["phone"])) {
            $address["phone"] = "";
        }
        if(!isset($address["fax"]) || empty($address["fax"])) {
            $address["fax"] = "";
        }
        if(!isset($address["vatId"]) || empty($address["vatId"])) {
            $address["vatId"] = "";
        }

        $sql = "
        INSERT INTO s_order_shippingaddress
        (
            userID,
            orderID,
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

        $array = array(
            $address["customerId"],
            $id,
            $address["company"],
            $address["department"],
            $address["salutation"],
            $address["firstName"],
            $address["lastName"],
            $address["street"],
            $address["zipCode"],
            $address["city"],
            $address["countryId"],
            $address["stateId"]
        );

		if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
		    $streetAndNumber = $this->seperateStreetAndNumber(str_replace(',', '', $address["street"]));
		    if(is_array($streetAndNumber)) {
		        $street = trim($streetAndNumber[1]);
		        $streetnumber = trim($streetAndNumber[2]);
		    } else {
		        $street = $streetAndNumber;
		        $streetnumber = "";
		    }

		    $sql = "
		    INSERT INTO s_order_shippingaddress
		    (
		        userID,
		        orderID,
		        company,
		        department,
		        salutation,
		        firstname,
		        lastname,
		        street,
		        streetnumber,
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
		        ?,
		        ?
		        )
		    ";

		    $array = array(
		        $address["customerId"],
		        $id,
		        $address["company"],
		        $address["department"],
		        $address["salutation"],
		        $address["firstName"],
		        $address["lastName"],
		        $street,
		        $streetnumber,
		        $address["zipCode"],
		        $address["city"],
		        $address["countryId"],
		        $address["stateId"]
		    );
		}

        $result = Shopware()->Db()->executeUpdate($sql,$array);

        if(!empty($result)) {
            $shippingID = Shopware()->Db()->lastInsertId();

            $sql = "INSERT INTO s_order_shippingaddress_attributes (shippingID) VALUES (?)";
            Shopware()->Db()->query($sql, array($shippingID));
        }

        return $result;
    }

    public function makeDiscountAction(){
        try{
            $id = $this->Request()->getParam('positionId');
            $offerId = $this->Request()->getParam('offerId');
            $discountPercentage = $this->Request()->getParam('discount');

            $position = $this->getManager()->find(
                'Shopware\CustomModels\Offer\Detail',
                $id
            );

            $originalPrice = $position->getOriginalPrice();
            //$originalPrice = $this->Request()->getParam('originalPrice');
            $price = ($originalPrice -($originalPrice * ($discountPercentage)/100));


            $position->setPrice($price);
            Shopware()->Models()->flush();

            $offer = $this->getManager()->find(
                'Shopware\CustomModels\Offer\Offer',
                $offerId
            );
            $offer->calculateInvoiceAmount();
            Shopware()->Models()->flush();
            $this->View()->assign(array(
                'success' => true,
                'offerId' => $offerId
            ));
        } catch (Exception $e) {

            $this->View()->assign(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
            return;
        }
    }

    public function positionListAction(){
        $id = $this->Request()->getParam('offerId');
        $shopId = $this->Request()->getParam('shopId','null');
        if($shopId){
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                'id' => $shopId
            ));
        }
        $expr = Shopware()->Models()->getExpressionBuilder();
        $builder = Shopware()->Models()->createQueryBuilder();
        $query = $builder->select(array('position'))
            ->from('Shopware\CustomModels\Offer\Detail', 'position')
            ->where('position.offerId = :id')
            ->setParameter('id',$id)
            ->getQuery();

        $positions = $query->getArrayResult();

        if($this->Request()->getParam('shopId')){
            $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
                'id' => $this->Request()->getParam('shopId')
            ));
        }
        $currency = Shopware()->Models()->toArray($shop->getCurrency());
        if(!empty($positions)) {
            foreach ($positions as $key =>$position) {
                    $articleRepository = Shopware()->Models()->getRepository('Shopware\Models\Article\Detail');
                    $articleDetail = $articleRepository->findOneBy(array('number' => $position["articleNumber"]));
                    if ($articleDetail instanceof \Shopware\Models\Article\Detail) {
                        $positions[$key]['inStock'] = $articleDetail->getInStock();
						if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
	                        $positions[$key]['articleId'] = $articleDetail->getarticleId();
						} else {
							$positions[$key]['articleId'] = $articleDetail->getArticle()->getId();
						}


                        //purchase price
                        if ($this->Request()->getParam('customerId')){
                            $customRepo = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer');
                            $customer = $customRepo->findOneBy(array('id' => $this->Request()->getParam('customerId')));
                        }

                        $articlePrice = $articleDetail->getPrices();
                        if($position['quantity'] && count($articlePrice)>1){
                              foreach ($articlePrice as $offer_key => $aPrice) {
                                 if(($aPrice->getFrom() <= $position['quantity'] && ($aPrice->getTO() >= $position['quantity'] || $aPrice->getTO() == 'beliebig' )) ){
                                     if ($customer){
                                         if ($aPrice->getCustomerGroup()->getKey() == $customer->getGroupKey()) {
                                             $articlePrice = $articlePrice[$offer_key];
                                             break;
                                         }
                                     }
                                     else{
                                         $articlePrice = $articlePrice[$offer_key];
                                         break;
                                     }
                                  }
                              }
                        }else{
                            $articlePrice = $articlePrice[0];
                        }

                        if($articlePrice){
                            $purchasePrice = 0;
                            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
                                $purchasePrice = $articleDetail->getPurchasePrice();
                            }
                            else {
                                $purchasePrice = $articlePrice->getBasePrice();
                            }
                            if($position['taxRate'])
                                $positions[$key]['purchasePrice'] = $this->calculatePrice($purchasePrice, $position['taxRate'])*$currency['factor'];
                            else
                                $positions[$key]['purchasePrice'] = $purchasePrice * $currency['factor'];
                        }

                    }

                $currency = Shopware()->Models()->toArray($shop->getCurrency());
                $positions[$key]['originalNetPrice'] = $positions[$key]['originalPrice'] * $currency['factor'];
                $positions[$key]['originalPrice'] = $this->includeTax($positions[$key]['originalPrice'], $position['taxRate']) * $currency['factor'];
                $positions[$key]['price'] = $this->includeTax($positions[$key]['price'], $position['taxRate']) * $currency['factor'];
                $positions[$key]['currency'] = $currency['currency'];
            }
        }
        $this->View()->assign(array('success' => true, 'data' => $positions));
    }

    public function getOfferChartAction()
    {
        try {
            if (!$this->_isAllowed('read', 'offer')) {
                /** @var $namespace Enlight_Components_Snippet_Namespace */
                $namespace = Shopware()->Snippets()->getNamespace('backend/customer');

                $this->View()->assign(array(
                        'success' => false,
                        'data' => $this->Request()->getParams(),
                        'message' => $namespace->get('no_order_rights', 'You do not have sufficient rights to view customer offers.'))
                );
                return;
            }

            //customer id passed?
            $customerId = $this->Request()->getParam('customerID');
            if ($customerId === null || $customerId === 0) {
                $this->View()->assign(array('success' => false, 'message' => 'No customer id passed'));
                return;
            }
            $offers = $this->getChartData($customerId);

            $this->View()->assign(array('success' => true, 'data' => $offers));
        } catch (Exception $e) {
            $this->View()->assign(array('success' => true, 'data' => array(), 'message' => $e->getMessage()));
        }
    }

    private function getChartData($customerId)
    {
        //if a from date passed, format it over the \DateTime object. Otherwise create a new date with today - 1 year
        $fromDate = $this->Request()->getParam('fromDate');
        if (empty($fromDate)) {
            $fromDate = new \DateTime();
            $fromDate->setDate($fromDate->format('Y') - 1, $fromDate->format('m'), $fromDate->format('d'));
        } else {
            $fromDate = new \DateTime($fromDate);
        }
        $fromDateFilter = $fromDate->format('Y-m-d');

        //if a to date passed, format it over the \DateTime object. Otherwise create a new date with today
        $toDate = $this->Request()->getParam('toDate');
        if (empty($toDate)) {
            $toDate = new \DateTime();
        } else {
            $toDate = new \DateTime($toDate);
        }
        $toDateFilter = $toDate->format('Y-m-d');

        $sql= "
            SELECT
                SUM(invoice_amount_net) as amount,
                DATE_FORMAT(offertime, '%Y-%m-01') as `date`
            FROM s_offer
            WHERE userID = ?
            AND s_offer.status NOT IN (-1, 4)
            AND offertime >= ?
            AND offertime <= ?
            GROUP by YEAR(offertime), MONTH(offertime)
        ";

        //select the offers from the database
        $offers = Shopware()->Db()->fetchAll($sql, array($customerId, $fromDateFilter, $toDateFilter));

        if (!empty($offers)) {
            $first = new \DateTime($offers[0]['date']);
            $last = new \DateTime($offers[count($offers)-1]['date']);

            //to display the whole time range the user inserted, check if the date of the first offer equals the fromDate parameter
            if ($fromDate->format('Y-m') !== $first->format('Y-m')) {
                //create a new dummy offer with amount 0 and the date the user inserted.
                $fromDate->setDate($fromDate->format('Y'), $fromDate->format('m'), 1);
                $emptyOffer = array('amount' => '0.00', 'date' => $fromDate->format('Y-m-d'));
                array_unshift($offers, $emptyOffer);
            }

            //to display the whole time range the user inserted, check if the date of the last offer equals the toDate parameter
            if ($toDate->format('Y-m') !== $last->format('Y-m')) {
                $toDate->setDate($toDate->format('Y'), $toDate->format('m'), 1);
                $offers[] = array('amount' => '0.00', 'date' => $toDate->format('Y-m-d'));
            }
        }
        return $offers;
    }

    /**
     * @return void.
     */
    public function getCustomerOffersAction()
    {
        try {
            if (!$this->_isAllowed('read', 'offer')) {
                /** @var $namespace Enlight_Components_Snippet_Namespace */
                $namespace = Shopware()->Snippets()->getNamespace('backend/customer');

                $this->View()->assign(array(
                        'success' => false,
                        'data' => $this->Request()->getParams(),
                        'message' => $namespace->get('no_offer_rights', 'You do not have sufficient rights to view customer offers.'))
                );
                return;
            }

            $customerId = $this->Request()->getParam('customerID');
            if ($customerId === null || $customerId === 0) {
                $this->View()->assign(array('success' => false, 'message' => 'No customer id passed'));
                return;
            }

            $defaultSort = array('0' => array('property' => 'o.offerTime', 'direction' => 'DESC'));

            $limit = $this->Request()->getParam('limit', 10);
            $offset = $this->Request()->getParam('start', 0);
            $sort = $this->Request()->getParam('sort', $defaultSort);
            $filter = $this->Request()->getParam('filter', null);

            $offerRepository = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Offer');
            $builder = $offerRepository->getListQuery($filter, $sort);
            $builder->andWhere('oc.id = ?2')
                    ->setParameter(2, $customerId);
            $query = $builder->getQuery();
            //returns the total count of the query
            $totalResult = $this->getManager()->getQueryCount($query);
            $data = $query->getArrayResult();
            if(!empty($data)) {
                $offers = $this->processOffersData($data);
            }
            // sends the out put to the view of the HBox
            $this->View()->assign(array(
                'success' => true,
                'data'    => $offers,
                'total' => $totalResult
            ));

        } catch (\Doctrine\ORM\ORMException $e) {
            $this->View()->assign(array('success' => false, 'data' => array(), 'message' => $e->getMessage()));
        }
    }

}