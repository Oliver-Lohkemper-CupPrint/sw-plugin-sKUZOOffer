<?php
/**
 * Created by PhpStorm.
 * User: jje
 * Date: 12/06/15
 * Time: 15:43
 */

class Shopware_Plugins_Backend_sKUZOOffer_Components_OfferDocument extends Shopware_Components_Document {

    /**
     * Object from Type CustomModel\Offer
     *
     * @var object CustomModel\Offer
     */
    public $_offer;
    public $_defaultPath = "";

    /**
     * This function initialize Documents.
     * @param int $offerId
     * @param int $documentID
     * @param array $offer
     * @param array $config
     * @return Enlight_Class
     * @throws Enlight_Exception
     */
    public static function initDocument($offerId, $documentID,$offer, array $config = array()) {
        //select own offer data
        if (empty($offerId)) {
            $config["_preview"] = true;
        }

        $d = Enlight_Class::Instance('Shopware_Plugins_Backend_sKUZOOffer_Components_OfferDocument');//new Shopware_Components_Document();
        $d->setOffer($offer);
        $d->setConfig($config);
        $d->setDocumentId($documentID);

        //setting language
        $language = $d->getLanguage($d->_offer);
        if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.1")){
            $d->_compatibilityMode = false;
        }
        if (!empty($offerId) && $language) {
            //if (empty($d->_subshop["doc_template"])) $d->setTemplate($d->_defaultPath);
            $d->_subshop = Shopware()->Db()->fetchRow("
                SELECT
                    s.id,
                    m.document_template_id as doc_template_id,
                    m.template_id as template_id,
                    (SELECT CONCAT('templates/', template) FROM s_core_templates WHERE id = m.document_template_id) as doc_template,
                    (SELECT CONCAT('templates/', template) FROM s_core_templates WHERE id = m.template_id) as template,
                    s.id as isocode,
                    s.locale_id as locale
                FROM s_offer, s_core_shops s
                LEFT JOIN s_core_shops m
                    ON m.id=s.main_id
                    OR (s.main_id IS NULL AND m.id=s.id)
                WHERE s.id = ?
                AND s_offer.id = ?
                ",
                array($language,$offerId)
            );

            if (empty($d->_subshop["doc_template"])) {
                $d->setTemplate($d->_defaultPath);
            }

            if (empty($d->_subshop["id"])) {
                throw new Enlight_Exception("Could not load template path for order $offerId");
            }

        } else {
            $d->_subshop = Shopware()->Db()->fetchRow("
            SELECT s_core_multilanguage.id,doc_template, template, isocode,locale FROM s_core_multilanguage WHERE s_core_multilanguage.default = 1
            ");

            $d->setTemplate($d->_defaultPath);
            $d->_subshop["doc_template"] = $d->_defaultPath;
        }

        $d->setTranslationComponent();
        $d->initTemplateEngine();

        return $d;
    }

    public function getLanguage($offer){
        if($offer->getLanguageIso()){
            $language = $offer->getLanguageIso();
        }else{
            $customerDetail = Shopware()->Models()->toArray($offer->getCustomer());
            if($customerDetail['languageId']){
                $language = $customerDetail['languageId'];
            }else{
                $language = $offer->getShopId();
            }
        }

        return $language;
    }

    /**
     * This function render offer data for creating offer document
     *
     * @param string $_renderer
     * @throws Exception
     * @throws SmartyException
     */
    public function render($_renderer = "")
    {
        if (!empty($_renderer)) $this->_renderer = $_renderer;
        if ($this->_valuesAssigend == false) {
            $this->assignValues();
        }
        /**@var $template \Shopware\Models\Shop\Template*/
        if (!empty($this->_subshop['doc_template_id'])) {
            $template = Shopware()->Container()->get('models')->find('Shopware\Models\Shop\Template', $this->_subshop['doc_template_id']);

            if ($template->getVersion() >= 3) {
                $inheritance = Shopware()->Container()->get('theme_inheritance')->getTemplateDirectories($template);
                $this->_template->setTemplateDir($inheritance);
            }
        }
        $data = null;
        foreach ($inheritance as $tpath) {
            if(file_exists($tpath."/documents/".$this->_document["template"])){
                $data = $this->_template->fetch($tpath."/documents/".$this->_document["template"], $this->_view);
                break;
            }
        }

        if(!$data){
            if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.3")) {
                $this->_template->disableSecurity();
            }
            $data = $this->_template->fetch(dirname(__FILE__)."/../Views/documents/".$this->_document["template"],$this->_view);
            //          $data = $this->_template->fetch(dirname(__FILE__)."/".$this->_document["template"],$this->_view);
        }

        if ($this->_renderer == "html" || !$this->_renderer) {
            echo $data;

        } elseif ($this->_renderer == "pdf"){
            $mpdfConfig = Shopware()->Container()->getParameter('shopware.mpdf.defaultConfig');

            $mpdfConfig['margin_left'] = $this->_document["left"];
            $mpdfConfig['margin_right'] = $this->_document["right"];
            $mpdfConfig['margin_top'] = $this->_document["top"];
            $mpdfConfig['margin_bottom'] = $this->_document["bottom"];

            if ($this->_preview == true || !$this->_documentHash) {
                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->WriteHTML($data);
                $mpdf->Output();
                exit;
            } else {
                $path = Shopware()->OldPath()."files/documents"."/".$this->_documentHash.".pdf";
                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->WriteHTML($data);
                $mpdf->Output($path,"F");

            }
        }
    }

    /**
     * This function save document to database
     */
    protected function saveDocument()
    {
        if ($this->_preview==true) return;

        $bid = $this->_config["bid"];
        if (!empty($bid)) {
            $this->_documentBid = $bid;
        }
        if (empty($bid)) $bid = 0;

        // Check if this kind of document already exists
        $typID = $this->_typID;
        $checkForExistingDocument = Shopware()->Db()->fetchRow("
        SELECT ID,docID,hash FROM s_offer_documents WHERE userID = ? AND offerID = ? AND type = ? AND (mediaId IS NULL OR mediaId=-1) 
        ",array($this->_offer->getCustomerId(),$this->_offer->getId(),$typID));

        if (!empty($checkForExistingDocument["ID"])) {
            // Document already exist. Update date and amount!
            $update = "
            UPDATE `s_offer_documents` SET `date` = now(),`amount` = ?
            WHERE `type` = ? AND userID = ? AND offerID = ? LIMIT 1
            ";
            $amount = $this->_config["netto"] == true ? round($this->_offer->getInvoiceAmountNet(),2) : round($this->_offer->getInvoiceAmount(),2);
            if ($typID == 4) {
                $amount *= -1;
            }
            $update = Shopware()->Db()->query($update,array(
                $amount,
                $typID,
                $this->_offer->getCustomerId(),
                $this->_offer->getId()
            ));

            $rowID = $checkForExistingDocument["ID"];
            $bid = $checkForExistingDocument["docID"];
            $hash = $checkForExistingDocument["hash"];


        } else {
            // Create new document
            $hash = md5(uniqid(rand()));
            $amount = $this->_config["netto"] == true ? round($this->_offer->getInvoiceAmountNet(),2) : round($this->_offer->getInvoiceAmount(),2);
            if ($typID == 4) {
                $amount *= -1;
            }
            $sql = "
            INSERT INTO s_offer_documents (`date`, `type`, `userID`, `offerID`, `amount`, `docID`,`hash`)
            VALUES ( NOW() , ? , ? , ?, ?, ?,?)
            ";
            $insert = Shopware()->Db()->query($sql,array(
                $typID,
                $this->_offer->getCustomerId(),
                $this->_offer->getId(),
                $amount,
                $bid,
                $hash
            ));
            $rowID = Shopware()->Db()->lastInsertId();
            // Update numberrange, except for cancellations
        }

        Shopware()->Models()->clear();
        // updating status of Offer
        $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$this->_offer->getId());
        $offer->setStatus(2);
        try {
            Shopware()->Models()->persist($offer);
            Shopware()->Models()->flush();
        } catch(Exception $e) {
            $var = $e;
        }

        $this->_documentID = $bid;
        $this->_documentRowID = $rowID;
        $this->_documentHash = $hash;
    }


    /**
     * This function assigns values from offer object to document configuration
     */
    public function assignValues()
    {
        $this->loadConfiguration4x();

        if (!$this->_preview) {
            $this->saveDocument();
        }

        return $this->assignValues4x();

    }

    /**
     * Assign configuration / data to template, new templatebase
     */
    protected function assignValues4x()
    {
        if ($this->_preview == true) {
            $id = 12345;
        } else {
            $id = $this->_documentID;
        }

        $Document = $this->_document->getArrayCopy();
        if (empty($this->_config["date"])) {
            $this->_config["date"] = date("d.m.Y");
        }
        $Document = array_merge($Document,array("comment"=>$this->_config["docComment"],"id"=>$id,"bid"=>$this->_documentBid,"date"=>$this->_config["date"],"deliveryDate"=>$this->_config["delivery_date"],"netto"=>$this->_offer->offer->taxfree ? true : $this->_config["netto"],"nettoPositions"=>$this->_offer->offer->net));

        $this->_view->assign('Document',$Document);

        $customerShowTax = $this->_offer->getCustomer()->getGroup()->getTax();

        // Translate payment and dispatch depending on the offer's language
        // and replace the default payment/dispatch text
        $dispatchId = $this->_offer->getDispatchId();
        $paymentId  = $this->_offer->getPaymentId();

        $language = $this->getLanguage($this->_offer);

        $translationPayment = $this->translationComponent->read($language, 'config_payment', 1);
        $translationDispatch = $this->translationComponent->read($language, 'config_dispatch', 1);

        if (isset($translationPayment[$paymentId])) {
            if (isset($translationPayment[$paymentId]['description'])) {
                $this->_offer->getPayment()->setDescription($translationPayment[$paymentId]['description']);
            }
            if (isset($translationPayment[$paymentId]['additionalDescription'])) {
                $this->_offer->getPayment()->setAdditionalDescription($translationPayment[$paymentId]['additionalDescription']);
            }
        }

        if (isset($translationDispatch[$dispatchId])) {
            if (isset($translationDispatch[$dispatchId]['dispatch_name'])) {
                $this->_offer->getDispatch()->setName($translationDispatch[$dispatchId]['dispatch_name']);
            }
            if (isset($translationDispatch[$dispatchId]['dispatch_description'])) {
                $this->_offer->getDispatch()->setDescription($translationDispatch[$dispatchId]['dispatch_description']);
            }
        }

        $this->_view->assign('Offer',$this->_offer->toArray($this->_offer));
        $this->_view->assign('Containers',$this->_document->containers->getArrayCopy());

        $offer = clone $this->_offer;


        //currency conversion
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(array(
            'id' => $offer->getShopId()
        ));
        $currency = Shopware()->Models()->toArray($shop->getCurrency());
        if(!$currency['factor']){$currency['factor'] = 1;}

        $offer->setInvoiceAmount($offer->getInvoiceAmount() * $currency['factor']);
        $offer->setInvoiceAmountNet($offer->getInvoiceAmountNet() * $currency['factor']);
        $offer->setDiscountAmount($offer->getDiscountAmount() * $currency['factor']);
        $offer->setInvoiceShipping($offer->getInvoiceShipping() * $currency['factor']);
        $offer->setInvoiceShippingNet($offer->getInvoiceShippingNet() * $currency['factor']);

        $positions = $offer->getDetails()->toArray();
        $Document["comment"] = $offer->getComment();
        $Document["customerComment"] = $offer->getCustomerComment();
        $this->_view->assign('Document',$Document);

        $articleModule = Shopware()->Modules()->Articles();

        $offerBillArray = Shopware()->Models()->toArray($offer->getOfferBilling());
        $shippingTax = $offerBillArray['shippingTax'];
        $Taxs = Shopware()->Models()->getRepository( 'Shopware\Models\Tax\Tax');
        $Taxs = $Taxs->findAll();
        foreach ($Taxs as $tax) {
            $taxCost[(string)$tax->getTax()] = 0;
        }
        $totalTax = 0;
        $totalPriceWithoutTax = 0;
        $totalPriceWithTax = 0;
        $totalOriginalPriceWithoutTax = 0;
        $totalOriginalPriceWithTax = 0;
        foreach ($positions as &$position) {
            $position = $position->toArray($position);
            $position['meta'] = $articleModule->sGetPromotionById('fix', 0, (int) $position['articleId']);
            $position['price'] = $position['price'] * $currency['factor']; //multiplied with factory for currency change
            $totalPriceWithoutTax += $position['price'] * $position['quantity'];
            $totalPriceWithTax += ($position['price'] * $position['quantity']) + $this->getTaxAmount($position['price'] * $position['quantity'], $position['taxRate']);
            $position['originalPrice'] = $position['originalPrice'] * $currency['factor'];
            $totalOriginalPriceWithoutTax += $position['originalPrice'] * $position['quantity'];
            $totalOriginalPriceWithTax += ($position['originalPrice'] * $position['quantity']) + $this->getTaxAmount($position['originalPrice'] * $position['quantity'], $position['taxRate']);
            //get packUnit, purchaseUnit, referenceUnit and unitName of variant
            $variant = Shopware()->Models()->getRepository( 'Shopware\Models\Article\Detail')->findOneBy(array('id'=>$position['articleDetailsId']));
            /*if(!$variant){
                throw new Exception('Article is not exist');
            }*/
            if($variant) {
                //translate article name for different subshops
				if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
                	$translationArticle = $this->translationComponent->read($language, 'article', $variant->getArticleId());
				} else {
	                $translationArticle = $this->translationComponent->read($language, 'article', $variant->getArticle()->getId());
				}
                if($translationArticle && isset($translationArticle['name']) && !empty($translationArticle['name'])){
                    $position['articleName'] = $translationArticle['name'];
                }

                if ($variant->getUnit()) {
                    $variantUnitArray = Shopware()->Models()->toArray($variant->getUnit());
                    $position['unitName'] = $variantUnitArray['name'];
                }

                $position['referenceUnit'] = $variant->getReferenceUnit();
                $position['purchaseUnit'] = $variant->getPurchaseUnit();
                $position['packUnit'] = $variant->getPackUnit();
                $position['pricePerUnit'] = ($variant->getReferenceUnit() * $position['price']) / $position['purchaseUnit'];
                $position['totalUnit'] = $position['quantity'] * $position['purchaseUnit'];

                if ($variant->getAttribute()) {
                    $position['attribute'] = Shopware()->Models()->toArray($variant->getAttribute());
                }
            }
            foreach ($Taxs as $tax) {
                if($position['taxRate']== $tax->getTax()){
                    $positionTax = $this->getTaxAmount($position['price'] ,$position['taxRate'])*$position['quantity'];
                    $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + $positionTax;
                    $totalTax = $totalTax + $positionTax;
                }
            }
            //if not net prices show bruto prices
            if($customerShowTax) {
                $position['originalPrice'] += $this->getTaxAmount($position['originalPrice'], $position['taxRate']);
                $position['price'] += $this->getTaxAmount($position['price'], $position['taxRate']);
            }

            if(isset($position['swagCustomProductsConfigurationHash']) && !empty($position['swagCustomProductsConfigurationHash'])) {
                /** @var CustomProductsServiceInterface $customProductsService */
                $customProductsService = Shopware()->Container()->get('custom_products.service');
                $position['customConfig'] = $customProductsService->getOptionsFromHash($position['swagCustomProductsConfigurationHash']);
                $position['customMode'] = $position['swagCustomProductsMode'];
            }
        }
        $totalOriginalPriceWithoutTax += $offer->getInvoiceShippingNet();
        $totalOriginalPriceWithTax += $offer->getInvoiceShipping();
        $totalPriceWithoutTax += $offer->getInvoiceShippingNet();
        $totalPriceWithTax += $offer->getInvoiceShipping();

        foreach ($Taxs as $tax) {
            if ($shippingTax == $tax->getTax()) {
                $shippingTaxAmount = $offer->getInvoiceShipping() - $offer->getInvoiceShippingNet();
                $taxCost[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()] + ($shippingTaxAmount);
                $totalTax = $totalTax + $shippingTaxAmount;
            }
            if($taxCost[(string)$tax->getTax()] != 0 && $tax->getTax() != 0){
                $taxRates[(string)$tax->getTax()] = $taxCost[(string)$tax->getTax()];
            }
        }

        if ($this->_config["_previewForcePagebreak"]) {
            $positions = array_merge($positions,$positions);
        }

        $positions = array_chunk($positions,$this->_document["pagebreak"],true);
        $this->_view->assign('Pages',$positions);
        $this->_view->assign('Offer',$this->_offer->toArray($offer));

        $billingCountry = Shopware()->Models()->getRepository('Shopware\Models\Country\Country')->findOneBy(array(
            'id' => $offer->getOfferBilling()->getCountryId()
        ));

        $shippingCountry = Shopware()->Models()->getRepository('Shopware\Models\Country\Country')->findOneBy(array(
            'id' => $offer->getOfferShipping()->getCountryId()
        ));

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(o)->from('Shopware\CustomModels\Offer\Billing', 'o')->where('o.offerId = :offerID');
        $builder->setParameter('offerID', $offer->getId());
        $billing = $builder->getQuery()->getArrayResult();
        $billing[0]['priceWithoutTax'] = $offer->getInvoiceAmountNet() - $totalTax;
        $billing[0]['dispatch'] = $offer->getDispatch()->getName();
        $billing[0]['payment'] = $offer->getPayment()->getDescription();
        $billing[0]['country'] = $billingCountry->getIsoName();
        $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();

        $user = array(
            "totalPriceWithoutTax"=>$totalPriceWithoutTax,
            "totalPriceWithTax"=>$totalPriceWithTax,
            "totalOriginalPriceWithoutTax"=>$totalOriginalPriceWithoutTax,
            "totalOriginalPriceWithTax"=>$totalOriginalPriceWithTax,
            "taxCost"=>$taxCost,
            "shipping"=>$billing[0],
            "billing"=>$billing[0],
            "additional"=>array("countryShipping"=>$shippingCountry->getName(),"country"=>$billingCountry->getName(),"documentFormat"=>$this->_config['documentFormat'],"zeroShipping"=>$config->showShippingTaxEveryTime,"customerShowTax"=>$customerShowTax)
        );
        $this->_view->assign('User',$user);

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
     * Load template / document configuration (s_core_documents / s_core_documents_box)
     */
    protected function loadConfiguration4x()
    {
        $id = $this->_typID;

        $this->_document = new ArrayObject(
            Shopware()->Db()->fetchRow(
                "SELECT * FROM s_core_documents WHERE id = ?",
                array($id),
                \PDO::FETCH_ASSOC
            )
        );

        // Load Containers
        $containers = Shopware()->Db()->fetchAll(
            "SELECT * FROM s_core_documents_box WHERE documentID = ?",
            array($id),
            \PDO::FETCH_ASSOC
        );

        $language = $this->getLanguage($this->_offer);

        $translation = $this->translationComponent->read($language, 'documents', 1);
        $this->_document->containers = new ArrayObject();
        foreach ($containers as $key => $container) {
            if (!is_numeric($key)) {
                continue;
            }
            if (!empty($translation[$id][$container["name"]."_Value"])) {
                $containers[$key]["value"] = $translation[$id][$container["name"]."_Value"];
            }
            if (!empty($translation[$id][$container["name"]."_Style"])) {
                $containers[$key]["style"] = $translation[$id][$container["name"]."_Style"];
            }

            // parse smarty tags
            $containers[$key]['value'] = $this->_template->fetch('string:'.$containers[$key]['value']);

            $this->_document->containers->offsetSet($container["name"], $containers[$key]);
        }
    }

    /**
     * This function set template path
     */
    public function setTemplate($path)
    {
        if (!empty($path)) {
            $this->_subshop["doc_template"] = $path;
        }
    }

    /**
     * This function set renderer
     */
    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * This function set type of document (0,1,2,3) > s_core_documents
     */
    public function setDocumentId($id)
    {
        $this->_typID = $id;
    }


    /**
     * This function initiate smarty template engine
     */
    protected function initTemplateEngine()
    {
        $this->_template = clone Shopware()->Template();
        $this->_view = $this->_template->createData();

        $path = basename($this->_subshop["doc_template"]);

        $this->_template->setTemplateDir(array(
            'custom' => $path,
            'local' => '_local',
            'emotion' => '_default',
        ));

        $this->_template->setCompileId(str_replace('/', '_', $path).'_'.$this->_offer->getShopId());
    }

    /**
     * This function sets the translation component
     */
    protected function setTranslationComponent()
    {
        $this->translationComponent = new Shopware_Components_Translation();
    }

    /**
     * This function set object configuration from array
     */
    protected function setConfig(array $config)
    {
        $this->_config = $config;
        foreach ($config as $key => $v) {
            if (property_exists($this,$key)) {
                $this->$key = $v;
            }
        }
    }

    /**
     * This function set shop for offer Document
     *
     * @param $offer
     */
    protected function setOffer($offer)
    {
        $this->_offer = $offer;
        $repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');
        // "language" actually refers to a language-shop and not to a locale
        $language = $this->getLanguage($this->_offer);
        $shop = $repository->getActiveById($language);
        /*if (!empty($this->_order->order->currencyID)) {
            $repository = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency');
            $shop->setCurrency($repository->find($this->_order->order->currencyID));
        }*/
        $shop->registerResources(Shopware()->Bootstrap());
    }

}