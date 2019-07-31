<?php

/**
 * Class Shopware_Plugins_Backend_sKUZOOffer_Bootstrap
 *
 * Article search works with only Shopware 5.0.2 or newer version
 * Some new Comment
 *
 */

use Shopware\Models\Media\Album as AlbumModel;

class Shopware_Plugins_Backend_sKUZOOffer_Bootstrap extends Shopware_Components_Plugin_Bootstrap {

    /**
     * constant for development mode
     */
    const development=false;

    // Set the new secureUninstall capability
    public function getCapabilities() {
        return array(
            'install' => true,
            'enable' => true,
            'update' => true,
            'secureUninstall' => true
        );
    }

    private function createNewMediaFolder() {
        /** @var \Shopware\Components\Model\ModelRepository $mediaAlbumRepository */
        $albumRepository = Shopware()->Models()->getRepository('Shopware\Models\Media\Album');

        $albumModel = $albumRepository->findOneBy(array(
            'name' => 'Externe Angebote'
        )) ;

        if(!$albumModel instanceof AlbumModel) {
            $albumModel = new AlbumModel();
            $albumModel->setName('Externe Angebote');
            $albumModel->setPosition(15);
            Shopware()->Models()->persist($albumModel);
            Shopware()->Models()->flush($albumModel);
        }
        return $albumModel->getId();
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function addJsFiles(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->Path() . 'Views/frontend/s_k_u_z_o_offer/src/js/jquery.skuzo_offer_add_article.js'
        ];

        return new \Doctrine\Common\Collections\ArrayCollection($jsFiles);
    }

    /**
     * This function install the plugin.
     *
     * @return array
     * @throws Enlight_Exception
     */
    public function install(){

        // Subscribe the needed event for js merge and compression
        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Javascript',
            'addJsFiles',
            99
        );

        //Backend Controller
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_sKUZOOffer',
            'getBackendController'
        );

        //Frontend Controller
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_sKUZOOffer',
            'getFrontendController'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_OfferDocument',
            'onInitResourceOfferDocument'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_OfferHelper',
            'onInitResourceOfferHelper'
        );

        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::getInquiryLink::replace',
            'onReplaceFrontendCheckoutGetInquiryLink'
        );

        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::addArticleAction::replace',
            'onReplaceFrontendCheckoutAddArticleAction'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend_Checkout',
            'onPostDispatchFrontendCheckout'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend_Detail',
            'onPostDispatchFrontendDetail'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend',
            'onPostDispatchBackend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Frontend',
            'onPostDispatchFrontendAccount'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Widgets',
            'onPostDispatchWidgets'
        );

        $this->subscribeEvent(
            'Theme_Compiler_Collect_Plugin_Less',
            'onCollectLessFiles'
        );
/*
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::saveOrder::after',
            'onAfterSaveOrder'
        );
*/
        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::saveOrder::replace',
            'onFrontendCheckoutSaveOrderReplace'
        );

        $this->subscribeEvent(
            'Shopware_Controllers_Frontend_Checkout::finishAction::before',
            'onBeforeSaveOrder'
        );

        $this->subscribeEvent(
            'Shopware_Components_Document::initTemplateEngine::after',
            'onAfterInitTemplateEngineFrontendDocument'
        );

        $this->subscribeEvent(
            'Shopware_CronJob_OfferRemember',
            'onRunCronOfferRemember'
        );
        $this->removeCronJobIfExist("Offer Remember");
        $this->createCronJob("Offer Remember", "OfferRemember", 1, true);

        $this->subscribeEvent(
            'Shopware_CronJob_OfferAutoGenerate',
            'onRunCronOfferAutoGenerate'
        );
        $this->removeCronJobIfExist("Angebotsgenerierung");
        $this->createCronJob("Angebotsgenerierung", "OfferAutoGenerate", 1, true);

        $this->subscribeEvent(
            'Shopware\CustomModels\Offer\Offer::preUpdate',
            'preUpdateOffer'
        );

        // extend article extjs module
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Customer',
            'onPostDispatchBackendCustomer'
        );

        // Adds a new widget to the database
        $this->removeWidgetIfExist('skuzo-last-offers');
        $this->createWidget('skuzo-last-offers');

        // Add path to backend controller
         $this->registerController('Backend', 'LastOffersWidget');

        // extend index extjs module
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Index',
            'onPostDispatchBackendIndex'
        );

        $logoURL = Enlight_Application::Instance()->Front()->Request()->getBaseURL().'/engine/Shopware/Plugins/Community/Backend/sKUZOOffer/plugin.png';
        $this->createMenuItem(array(
            'label' => 'Offer',
            'controller' => 'sKUZOOffer',
            'class' => '\" style=\"background: url('. $logoURL.') no-repeat scroll 0 0 transparent !important;',
//            'class' => 'sprite-application-block',
            'action' => 'Index',
            'active' => 1,
            'parent' => $this->Menu()->findOneBy(array('label' => 'Kunden'))
        ));

        try {
            Shopware()->Acl()->createResource('skuzooffer', array('read', 'create', 'update',
                                                                  'delete'), 'Offer', $this->getId());
        } catch(Exception $e) {
            //ignore
        }

        $this->updateSchema();
        $this->createMail();
        $this->createOfferMail();
        $this->createRememberMail();
        $this->createOfferStates();
        $this->createOfferNumber();
        $this->createDocument();
        $this->createForm();

        return array(
            'success' => true,
            'invalidateCache' => array('backend', 'theme')
        );
    }

    /**
     * Remove a widget if already existing
     *
     * @param $name
     */
    public function removeCronJobIfExist($name)
    {
        $connection = $this->get('dbal_connection');
        $result = $connection->fetchAll("select * from s_crontab where name='Offer Remember'");
        if ($result[0]){
            $connection->delete(
                's_crontab',
                [
                    'name'       => $name,
                ],
                [
                    'next' => 'datetime',
                    'end'  => 'datetime',
                ]
            );
        }
    }
    /**
     * Remove a widget if already existing
     *
     * @param $name
     */
    public function removeWidgetIfExist($name)
    {
        $this->registerCustomModels();
        $widget = Shopware()->Models()->getRepository('Shopware\Models\Widget\Widget')->findOneBy(array('name' => $name));
        if ($widget){
            Shopware()->Models()->remove($widget);
            Shopware()->Models()->flush();
        }
    }

    /**
     * This function adding less file for rendering.
     *
     * @return \Shopware\Components\Theme\LessDefinition
     */
    public function onCollectLessFiles()
    {
        return new \Shopware\Components\Theme\LessDefinition(
            [],
            [__DIR__ . '/Views/frontend/s_k_u_z_o_offer/src/less/all.less']
        );
    }

    /**
     * This function uninstall the plugin.
     *
     * @return bool
     */
    public function uninstall() {
        $this->secureUninstall();
        $this->registerCustomModels();
        if(self::development) {
            $em = $this->Application()->Models();
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
            //$this->removeOfferStates();

            $classes = array(
                $em->getClassMetadata('Shopware\CustomModels\Offer\Offer'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\History'),
//                $em->getClassMetadata('Shopware\CustomModels\Offer\Attribute'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\Detail'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\Document\Document'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\States'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\Billing'),
                $em->getClassMetadata('Shopware\CustomModels\Offer\Shipping')
            );
            Shopware()->Acl()->deleteResource('skuzooffer');
            $tool->dropSchema($classes);

            $this->removeWidgetIfExist('skuzo-last-offers');
            $this->removeOfferNumber();
            $this->removeDocument();
            $this->removeAcl();
        }

        return array(
            'success' => true,
            'invalidateCache' => array('backend', 'theme')
        );
    }

    // Remove only non-user related data.
    public function secureUninstall() {
        return true;
    }

    public function assertMinimumVersion($version) {
        return parent::assertMinimumVersion($version);
    }

    /**
     * This function updates the plugin
     *
     * @return boolean
     */
    public function update($oldVersion) {
        switch ($oldVersion) {
            case '1.0.0':
            case '1.0.1':
                $this->subscribeEvent(
                    'Shopware_Controllers_Frontend_Checkout::saveOrder::after',
                    'onAfterSaveOrder'
                );
                $this->subscribeEvent(
                    'Shopware_Controllers_Frontend_Checkout::finishAction::before',
                    'onBeforeSaveOrder'
                );
                $this->createForm();
            case '1.1.0':
            case '1.1.1':
            case '1.1.2':
            case '1.1.3':
            case '1.1.4':
            case '1.1.5':
            case '1.1.6':
                $this->subscribeEvent(
                    'Enlight_Controller_Action_PostDispatch_Frontend_Detail',
                    'onPostDispatchFrontendDetail'
                );
                $this->subscribeEvent(
                    'Shopware_Controllers_Frontend_Checkout::addArticleAction::replace',
                    'onReplaceFrontendCheckoutAddArticleAction'
                );

                $form = $this->Form();
                $form->setElement('checkbox', 'detailOfferButton', array(
                    'label' => 'Add Offer Button in Product Detail?',
                    'value' => 0,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
                $translations = array(
                    'en_GB' => array(
                        'detailOfferButton' => 'Add Offer Button in Product Detail?',
                    ),
                    'de_DE' => array(
                        'detailOfferButton' => 'Angebot-Anfordern-Button in Artikel-Detailseite?',
                    )
                );
                $this->translateForm($form, $translations);

                $this->updateSchema();
            case '1.1.7':
                $this->subscribeEvent(
                    'Enlight_Bootstrap_InitResource_OfferHelper',
                    'onInitResourceOfferHelper'
                );

                $this->subscribeEvent(
                    'Shopware\CustomModels\Offer\Offer::preUpdate',
                    'preUpdateOffer'
                );

                $this->subscribeEvent(
                    'Shopware_CronJob_OfferRemember',
                    'onRunCronOfferRemember'
                );
                $this->createCronJob("Offer Remember", "OfferRemember", 1, true);

                $this->createRememberMail();

                $sql = "ALTER TABLE s_offer ADD isSendRememberMail1 TINYINT NULL,  ADD isSendRememberMail2 TINYINT NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }

                $form = $this->Form();
                $form->setElement('select', 'rememberFromState', array(
                    'value' => -1,
                    'label' => 'From what state should be reminded?',
                    'store' => array(
                        array(-1, 'Do not remember - Nicht Erinnern'),
                        array(1, 'Open - Offen'),
                        array(2, 'Processed - Erstellt'),
                        array(3, 'Sent - Versendet')
                    ),
                    'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
                ));

                $form->setElement('text', 'rememberDays1', array('label'=>'1. reminder after x days','value'=>'7', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
                $form->setElement('text', 'rememberDays2', array('label'=>'2. reminder after x days','value'=>'14', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));

                $translations = array(
                    'en_GB' => array(
                        'rememberFromState' => 'From what state should be reminded?',
                        'rememberDays1' => '1. reminder after x days',
                        'rememberDays2' => '2. reminder after x days'
                    ),
                    'de_DE' => array(
                        'rememberFromState' => 'Ab welchem Status soll Erinnerung berechnet werden?',
                        'rememberDays1' => '1. Erinnerung nach x Tage',
                        'rememberDays2' => '2. Erinnerung nach x Tage'
                    )
                );
                $this->translateForm($form, $translations);

                $this->updateSchema();
            case '1.1.8':
                $this->createOfferMail();

                // extend article extjs module
                $this->subscribeEvent(
                    'Enlight_Controller_Action_PostDispatch_Backend_Customer',
                    'onPostDispatchBackendCustomer'
                );
                // Adds a new widget to the database
                $this->createWidget('skuzo-last-offers');

                // Add path to backend controller
                 $this->registerController('Backend', 'LastOffersWidget');

                // extend index extjs module
                $this->subscribeEvent(
                    'Enlight_Controller_Action_PostDispatch_Backend_Index',
                    'onPostDispatchBackendIndex'
                );

            case '1.1.9':
                $sql = "ALTER TABLE s_offer ADD comment LONGTEXT NULL,  ADD customercomment LONGTEXT NULL,  ADD internalcomment LONGTEXT NULL, ADD active TINYINT";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
                $sql = "UPDATE s_offer SET active=1";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.1.10':
                try {
                    $sql = "CREATE TABLE IF NOT EXISTS s_offer_history (id int(11) NOT NULL AUTO_INCREMENT,offerId int(11) NOT NULL,userId int(11) DEFAULT NULL,previousStatusId int(11) NOT NULL,statusId int(11) NOT NULL,comment longtext COLLATE utf8_unicode_ci NOT NULL,changeDate datetime NOT NULL,PRIMARY KEY (id), FOREIGN KEY (statusId) REFERENCES s_offer_states (id), FOREIGN KEY (offerId) REFERENCES s_offer (id), FOREIGN KEY (previousStatusId) REFERENCES s_offer_states (id))";
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.1.11':
            case '1.1.12':
                $form = $this->Form();
                $form->setElement('checkbox', 'showShippingTaxEveryTime', array(
                    'label' => 'show 0 shippingCost in offer Document',
                    'value' => 0,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
                $translations = array(
                    'en_GB' => array(
                        'showShippingTaxEveryTime' => 'show 0 shippingCost in offer Document',
                    ),
                    'de_DE' => array(
                        'showShippingTaxEveryTime' => 'Kostenfreien Versand in Angebotsdokument?',
                    )
                );
                $this->translateForm($form, $translations);
            case '1.1.13':
            case '1.1.14':
            case '1.1.15':
            case '1.1.16':
            case '1.1.17':
                $form = $this->Form();
                $form->setElement('checkbox', 'emailPreview', array('label' => 'want to see email preview', 'value' => 0, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
                $translations = array(
                    'en_GB' => array(
                        'emailPreview' => 'want to see email preview'
                    ),
                    'de_DE' => array(
                        'emailPreview' => 'Vorauswahl eMail-Vorschau bei Versand Angebotsdokument?'
                    )
                );
                $this->translateForm($form, $translations);
            case '1.1.18':
                $form = $this->Form();
                $form->setElement('text', 'ownerMailAddress', array('label' => 'Owner e-mail address', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
                $translations = array(
                    'en_GB' => array(
                        'ownerMailAddress' => 'Owner e-mail address'
                    ),
                    'de_DE' => array(
                        'ownerMailAddress' => 'Interne E-Mail Adresse'
                    )
                );
                $this->translateForm($form, $translations);
                $sql = "ALTER TABLE s_offer ADD discount_amount_net double NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.2.0':
            case '1.2.1':
                $sql = "ALTER TABLE s_offer_details ADD modus int(11) NOT NULL DEFAULT 0,  ADD quantityID int(11) NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.2.2':
                $form = $this->Form();
                $form->setElement('checkbox', 'showPurchasePrice', array(
                    'label' => 'show purchasePrice in Position List?',
                    'value' => 0,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
                $translations = array(
                    'en_GB' => array(
                        'showPurchasePrice' => 'show purchasePrice in Position List?'
                    ),
                    'de_DE' => array(
                        'showPurchasePrice' => 'Einkaufspreise in Liste der Angebotspositionen anzeigen?'
                    )
                );
                $this->translateForm($form, $translations);
                $sql = "ALTER TABLE s_offer ADD language VARCHAR(10) NOT NULL DEFAULT 1";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.2.3':
            case '1.2.4':
            case '1.2.5':
            case '1.2.6':
            case '1.2.7':
            case '1.2.8':
                $this->subscribeEvent(
                    'Enlight_Controller_Action_PostDispatch_Backend',
                    'onPostDispatchBackend'
                );
            case '1.2.9':
            case '1.2.10':
            case '1.2.11':
            case '1.2.12':
            case '1.2.13':
            case '1.2.14':
            case '1.2.15':
            case '1.2.16':
            case '1.2.17':
            case '1.2.18':
                Shopware()->Container()->get('shopware.cache_manager')->clearProxyCache();
                $sql = "ALTER TABLE s_offer_details ADD isConvertedToNet int(11) NULL;";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }

                $sql = "ALTER TABLE s_offer_details ADD swagCustomProductsMode int(11) NULL, ADD swagCustomProductsConfigurationHash VARCHAR(255) NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }

                $this->registerCustomModels();
                $details = Shopware()->Models()->getRepository('Shopware\CustomModels\Offer\Detail')->findBy(array("isConvertedToNet"=>null));
                if($details){
                    $sql = "CREATE TABLE s_offer_details_bak LIKE s_offer_details; INSERT s_offer_details_bak SELECT * FROM s_offer_details;";
                    try {
                        Shopware()->Db()->exec($sql);
                    } catch (Exception $e) {
                        $var = $e;
                    }

                    foreach($details as $offerDetail){
                        $offerDetail->setOriginalPrice($this->excludeTax($offerDetail->getOriginalPrice(),
                            $offerDetail->getTaxRate()));
                        $offerDetail->setPrice($this->excludeTax($offerDetail->getPrice(),
                            $offerDetail->getTaxRate()));
                        $offerDetail->setIsConvertedToNet(1);
                        Shopware()->Models()->persist($offerDetail);
                        Shopware()->Models()->flush();
                    }
                }
            case '1.2.14-dev':
            case '1.2.15-dev':
            case '1.2.16-dev':
            case '1.2.17-dev':
                $form = $this->Form();
                $form->setElement('checkbox', 'backendOrderMail', array('label' => 'Send order mail on backend order?', 'value' => 1, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
                $translations = array(
                    'en_GB' => array(
                        'backendOrderMail' => 'Send order mail on backend order?',
                    ),
                    'de_DE' => array(
                        'backendOrderMail' => 'E-Mail-Bestellbestätigung bei Bestellumwandlung im Backend versenden?',
                    )
                );
                $this->translateForm($form, $translations);
            case '1.2.18-dev':
                $this->subscribeEvent(
                    'Shopware_CronJob_OfferAutoGenerate',
                    'onRunCronOfferAutoGenerate'
                );
                $this->removeCronJobIfExist("Angebotsgenerierung");
                $this->createCronJob("Angebotsgenerierung", "OfferAutoGenerate", 1, true);

                $form = $this->Form();
                $form->setElement('checkbox', 'sendOfferAfterAutoGeneration', array(
                    'label' => 'Send offer by mail after auto generation?',
                    'value' => 0,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
                $translations = array(
                    'en_GB' => array(
                        'sendOfferAfterAutoGeneration' => 'Send offer by mail after auto generation?'
                    ),
                    'de_DE' => array(
                        'sendOfferAfterAutoGeneration' => 'Sende Angebot via Mail nach Autogenerierung?'
                    )
                );
                $this->translateForm($form, $translations);
            case '1.2.19-dev':
            case '1.2.20-dev':
            case '1.2.21-dev':
            case '1.2.22-dev':
            case '1.2.23-dev':
            case '1.2.24-dev':
            case '1.2.25-dev':
            case '1.2.26-dev':
                $this->subscribeEvent(
                    'Theme_Compiler_Collect_Plugin_Javascript',
                    'addJsFiles',
                    99
                );
                $sql = "ALTER TABLE s_offer_details ADD swagCustomProductsMode int(11) NULL, ADD swagCustomProductsConfigurationHash VARCHAR(255) NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.2.27-dev':
            case '1.2.28-dev':
            case '1.2.29-dev':
            case '1.2.30-dev':
            case '1.3.0':
                $this->Form()->setElement('text', 'offerAlbumId',
                    array(
                        'value' => $this->createNewMediaFolder(),
                        'hidden' => true
                    )
                );
                $sql = "ALTER TABLE s_offer_documents ADD mediaID int(11) NULL";
                try {
                    Shopware()->Db()->exec($sql);
                } catch (Exception $e) {
                    $var = $e;
                }
            case '1.3.1':
                $form = $this->Form();
                $form->setElement('checkbox', 'enableOfferUpload', array(
                    'label' => 'Add document upload function?',
                    'value' => 0,
                    'hidden' => true,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
                $form->setElement('checkbox', 'offerCheckAGB', array(
                    'label' => 'Add AGB checkbox?',
                    'value' => 0,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));

                $translations = array(
                    'en_GB' => array(
                        'enableOfferUpload' => 'Add document upload function?',
                        'offerCheckAGB' => 'Add AGB checkbox?'
                    ),
                    'de_DE' => array(
                        'enableOfferUpload' => 'Dokumentupload aktivieren?',
                        'offerCheckAGB' => 'AGB-Bestätigung aktivieren?'
                    )
                );
                $this->translateForm($form, $translations);
            case '1.3.2':
                $this->subscribeEvent(
                    'Enlight_Controller_Action_PostDispatchSecure_Widgets',
                    'onPostDispatchWidgets'
                );
            case '1.3.3':
/*
                $formElementEnableOfferUpload = $this->Form()->getElement('enableOfferUpload');
                $formElementEnableOfferUpload->setOptions(array(
                    'label' => 'Add document upload function?',
                    'value' => 0,
                    'hidden' => false,
                    'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
                ));
*/
            case '1.3.4':
            case '1.3.5':
                $this->subscribeEvent(
                    'Shopware_Controllers_Frontend_Checkout::saveOrder::replace',
                    'onFrontendCheckoutSaveOrderReplace'
                );
                $this->updateSchema(true);
            break;
            default:
                return false;
                break;
        }
        return array(
            'success' => true,
            'invalidateCache' => array('backend', 'config', 'theme', 'proxy')
        );
    }

    /**
     * This function is to exclude specified Tax rate from amount
     * @param $amount
     * @param $taxRate
     * @return float
     */
    private function excludeTax($amount, $taxRate){
        return ($amount / ($taxRate + 100) * 100);
    }


    /**
     * This function register customModels.
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function updateSchema($update=false)
    {
        $this->registerCustomModels();

        $em = $this->Application()->Models();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = array(
            $em->getClassMetadata('Shopware\CustomModels\Offer\Offer'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\Detail'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\Document\Document'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\States'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\Billing'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\Shipping'),
            $em->getClassMetadata('Shopware\CustomModels\Offer\History'),
//            $em->getClassMetadata('Shopware\CustomModels\Offer\Attribute'),
        );

        if(self::development) {
            try {
                $tool->dropSchema($classes);
            } catch(Exception $e) {
                //ignore
            }
        }

        if($update) {
            try {
                $tool->updateSchema($classes, true);
            } catch(Exception $e) {
                //ignore
            }
        } else {
            try {
                $tool->createSchema($classes);
            } catch(Exception $e) {
                //ignore
            }
        }
    }

    /**
     * This function removes privileges, roles and resources of plugin.
     */
    public function removeAcl()
    {
        $sql = "SELECT id FROM s_core_acl_resources WHERE name = ?";
        $resourceID = Shopware()->Db()->fetchOne($sql, array('sKUZOOffer'));

        $delete = 'DELETE FROM s_core_acl_resources WHERE id = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $delete = 'DELETE FROM s_core_acl_privileges WHERE resourceID = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $delete = 'DELETE FROM s_core_acl_roles WHERE resourceID = ?';
        Shopware()->Db()->query($delete, array($resourceID));
    }

    /**
     * This function set the paths for backend Controller.
     *
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public function getBackendController(Enlight_Event_EventArgs $args)
    {
        $this->registerCustomModels();

        // Add template and snippet directory
        $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'snippets/'
        );
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        return $this->Path() . '/Controllers/Backend/sKUZOOffer.php';
    }

    /**
     *  This function configure data for sending mail.
     *
     * @throws Enlight_Exception
     */
    private function createMail() {
        $sql = "SELECT id FROM s_core_config_mails WHERE name=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('sOfferDocuments'));
        if($templateID === false) {
            //insert email-template
            $sql = "INSERT INTO s_core_config_mails (name, frommail, fromname, subject, content, contentHTML, isHTML, attachment)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if(!Shopware()->Db()->query($sql, array(
                'sOfferDocuments',
                '{config name=mail}',
                '{config name=shopName}',
                'Ihr Angebot {$offernumber}',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, im Anhang finden Sie Ihr gewünschtes Angebot {$sOffer->getNumber()}',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, im Anhang finden Sie Ihr gewünschtes Angebot {$sOffer->getNumber()}',
                '1',
                ''
            ))
            ) {
                throw new Enlight_Exception("eMail-Template sOfferDocuments konnte nicht angelegt werden!");
            }
        }
    }

    /**
     *  This function configure data for sending mail.
     *
     * @throws Enlight_Exception
     */
    private function createRememberMail() {
        $sql = "SELECT id FROM s_core_config_mails WHERE name=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('sOfferRemember1'));
        if($templateID === false) {
            //insert email-template
            $sql = "INSERT INTO s_core_config_mails (name, frommail, fromname, subject, content, contentHTML, isHTML, attachment)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if(!Shopware()->Db()->query($sql, array(
                'sOfferRemember1',
                '{config name=mail}',
                '{config name=shopName}',
                'Ihr Angebot {$offernumber} wurde noch nicht bestätigt',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, Ihr Angebot wurde noch nicht aktzeptiert. Im Anhang finden Sie Ihr Angebot {$sOffer->getNumber()}',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, Ihr Angebot wurde noch nicht aktzeptiert. Im Anhang finden Sie Ihr Angebot {$sOffer->getNumber()}',
                '1',
                ''
            ))
            ) {
                throw new Enlight_Exception("eMail-Template sOfferRemember1 konnte nicht angelegt werden!");
            }
        }

        $sql = "SELECT id FROM s_core_config_mails WHERE name=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('sOfferRemember2'));
        if($templateID === false) {
            //insert email-template
            $sql = "INSERT INTO s_core_config_mails (name, frommail, fromname, subject, content, contentHTML, isHTML, attachment)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if(!Shopware()->Db()->query($sql, array(
                'sOfferRemember2',
                '{config name=mail}',
                '{config name=shopName}',
                'Ihr Angebot {$offernumber} wurde noch nicht bestätigt (2.Erinnerung)',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, Ihr Angebot wurde noch nicht aktzeptiert. Im Anhang finden Sie Ihr Angebot {$sOffer->getNumber()}',
                'Hallo {$sOffer->getOfferBilling()->getFirstName()} {$sOffer->getOfferBilling()->getLastName()}, Ihr Angebot wurde noch nicht aktzeptiert. Im Anhang finden Sie Ihr Angebot {$sOffer->getNumber()}',
                '1',
                ''
            ))
            ) {
                throw new Enlight_Exception("eMail-Template sOfferRemember2 konnte nicht angelegt werden!");
            }
        }
    }

    /**
     * This function returns the stateId by description
     *
     * @return int|string
     */
    public function getStateIdByDescription($description)
    {
        return Shopware()->Db()->fetchOne(
            "SELECT id FROM s_order_states WHERE description=?",
            array($description)
        );
    }

    /**
     * This function is called by cron and generates offers automaticaly
     *
     * @param Shopware_Components_Cron_CronJob $job
     */
    public function onRunCronOfferAutoGenerate(Shopware_Components_Cron_CronJob $job) {
        $this->registerCustomModels();

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(o)->from('Shopware\CustomModels\Offer\Offer', 'o')->where('o.status < 2');
        $offers = $builder->getQuery()->getResult();

        foreach($offers AS $offer) {
            $doc = Shopware()->Models()->getRepository('Shopware\Models\Document\Document')->findOneBy(array('template' => 'offerBill.tpl'));
            $documentType = $doc->getId();

            $offerHelper = Shopware()->OfferHelper();
            $documentCreated = $offerHelper->createDocument($offer, $documentType);

            $config = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config();
            $sendOfferAfterAutoGeneration = $config->sendOfferAfterAutoGeneration;
            if($documentCreated && $sendOfferAfterAutoGeneration) {
                //send offer by mail
                Shopware()->Models()->clear();
                $mailType = "sOfferDocuments";
                $mailModel = Shopware()->Models()->getRepository('Shopware\Models\Mail\Mail')
                    ->findOneBy(array('name' => $mailType));

                $document = Shopware()->Db()->fetchRow("SELECT * FROM `s_offer_documents` WHERE offerID=? AND type =? LIMIT 1", array($offer->getId(), $documentType));
                $attachmentName = 'Angebot {$invoicenumber}';
                $document['attachmentName'] = $attachmentName;

                $mailreturn = $offerHelper->sendMail($mailModel, $document, array($offer->getCustomer()->getEmail()), $offer->getNumber());
                $mailreturn->send();
            }
        }
    }

    /**
     * This function is called by cron and sends remember mails
     *
     * @param Shopware_Components_Cron_CronJob $job
     */
    public function onRunCronOfferRemember(Shopware_Components_Cron_CronJob $job) {
        //calculate remember date and send mail
        $rememberFromState = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->rememberFromState;

        if(!isset($rememberFromState) || empty($rememberFromState) || $rememberFromState==-1) {
            return;
        }


        $this->registerCustomModels();

        $rememberDays1 = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->rememberDays1;
        $offerHistoryEntries = $this->getRememberEntries(1,$rememberDays1, $rememberFromState);
        foreach($offerHistoryEntries as $offerHistoryEntry) {
            $this->sendRememberMail("sOfferRemember1",$offerHistoryEntry["offerId"]);
        }

        $rememberDays2 = Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->rememberDays2;
        $offerHistoryEntries = $this->getRememberEntries(1,$rememberDays2, $rememberFromState);
        foreach($offerHistoryEntries as $offerHistoryEntry) {
            $this->sendRememberMail("sOfferRemember2",$offerHistoryEntry["offerId"]);
        }
    }

    /**
     * This function sends the offer remember mails to the customer
     *
     * @param $type
     * @param $offerId
     */
    private function sendRememberMail($type, $offerId) {
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(o)->from('Shopware\CustomModels\Offer\Offer', 'o')->where('o.id = :id');
        $builder->setParameter('id', $offerId);
        $offers = $builder->getQuery()->getArrayResult();

        if(count($offers) != 1) {
            return false;
        }

        $offer = $offers[0];

        $sql = "SELECT id FROM s_core_documents WHERE template = ?";
        $docId = Shopware()->Db()->fetchOne($sql, array('offerBill.tpl'));
        $document = Shopware()->Db()->fetchRow("SELECT * FROM `s_offer_documents` WHERE offerID=? AND type =? LIMIT 1", array($offerId, $docId));

        switch($document["type"]) {
            case $docId:
                $mailType = $type;
                $attachmentName = 'Angebot {$invoicenumber}';
                break;

            default:
                return false;
                break;
        }

        $document['attachmentName'] = $attachmentName;

        //fetch userData
        $customerResource = \Shopware\Components\Api\Manager::getResource('Customer');
        $customer = $customerResource->getOne($offer["customerId"]);

        //set attribute, that remembermail is sendet for this offer
        $offerModel = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offer["id"]);
        if(!$offerModel instanceof \Shopware\CustomModels\Offer\Offer) {
            return false;
        }

        if($type=="sOfferRemember1") {
            $offerModel->setIsSendRememberMail1(1);
        }
        if($type=="sOfferRemember2") {
            $offerModel->setIsSendRememberMail2(1);
        }

        try {
            Shopware()->Models()->persist($offerModel);
            Shopware()->Models()->flush();
        } catch(Exception $e) {
            $var = $e;
        }

        $offerHelper = Shopware()->OfferHelper();

        $mailreturn = $offerHelper->sendMail($mailType, $document, array($customer['email']), $offer['number']);
        $mailreturn->send();

        return $mailreturn;
    }

    /**
     * This function gets values for offers a remember mail is necessary
     *
     * @param $rememberDays
     * @param $rememberStateId
     * @return array
     */
    private function getRememberEntries($number, $rememberDays, $rememberStateId) {
        $dateNow = new DateTime("now");
        $firstRememberDate = $dateNow->sub(new DateInterval('P' . $rememberDays . 'D'));

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select(array('history'))->from('Shopware\CustomModels\Offer\History', 'history')
            ->leftJoin('history.offer', 'offer')->where('offer.status < 4');

        if($number==1) {
            $builder->andWhere('offer.isSendRememberMail1 IS NULL');
        } elseif($number==2) {
            $builder->andWhere('offer.isSendRememberMail2 IS NULL');
        }
        $builder->andWhere('history.statusId = :stateId')
            ->andWhere('history.changeDate < :firstRememberDate');

        //TODO look if remember mail is sendet before

        $builder->setParameter('stateId', $rememberStateId);
        $builder->setParameter('firstRememberDate', $firstRememberDate);
        return $builder->getQuery()->getArrayResult();
    }

    /**
     *  This function creates states for offer.
     *
     * @throws Enlight_Exception
     */
    private function createOfferStates() {
            //insert offer States
            $sql = "INSERT INTO s_offer_states (description)
					VALUES (? )";

            if(!Shopware()->Db()->query($sql, array('Open')) ||
               !Shopware()->Db()->query($sql, array('Processed')) ||
               !Shopware()->Db()->query($sql, array('Sent')) || !Shopware()->Db()->query($sql, array('accepted')) ||
               !Shopware()->Db()->query($sql, array('Confirmed'))
            )
            {
            throw new Enlight_Exception("Error in inserting OfferStates!");
            }
    }

    /**
     *  This function register offerNumber which is used for offerNumber Range for Plugin.
     *
     * @throws Enlight_Exception
     */
    private function createOfferNumber() {
        $sql = "SELECT id FROM s_order_number WHERE name=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('offer'));
        if($templateID === false) {
            //insert offer States
            $sql = "INSERT INTO s_order_number (number, name, `desc`)
					VALUES (?, ?, ? )";

            if(!Shopware()->Db()->query($sql, array(7000,'offer','Angebote')))
            {
                throw new Enlight_Exception("Error in inserting OfferNumber!");
            }
        }
    }

    /**
     * This function register Offer Document template data which is used for creating offer Document.
     *
     * @throws Enlight_Exception
     */
    private function createDocument() {
        $sql = "SELECT id FROM s_core_documents WHERE template=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('offerBill.tpl'));
        if($templateID === false) {
            //insert offer States
            $sql = "INSERT INTO s_core_documents (name, template, numbers, `left`, `right`, top, bottom, pagebreak)
					VALUES (?, ?, ?, ?, ?, ?, ?, ? )";

            if(!Shopware()->Db()->query($sql, array('Angebot', 'offerBill.tpl', 'doc_0', 25, 10, 20, 20, 10)))
            {
                throw new Enlight_Exception("Error in inserting OfferNumber!");
            }

            $docId = Shopware()->Db()->lastInsertId();
            $sql_doc = "INSERT INTO s_core_documents_box (documentID, `name`, style, `value`)
					VALUES (?, ?, ?, ? )";

            if(!Shopware()->Db()->query($sql_doc, array($docId, 'Body', 'width:100%; font-family: Verdana, Arial, Helvetica, sans-serif; font-size:11px;', '')))
            {
                throw new Enlight_Exception("Error in inserting OfferBill Document styles!");
            }
            else
            {
                Shopware()->Db()->query($sql_doc, array( $docId, 'Logo', 'height: 20mm; width: 90mm; margin-bottom:5mm;', '<p><img src="http://www.shopware.de/logo/logo.png " alt="" /></p>'));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header_Recipient', '', ''));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header', 'height: 60mm;', ''));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header_Sender', '', '<p>Demo GmbH - Stra&szlig;e 3 - 00000 Musterstadt</p>'));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header_Box_Left', 'width: 120mm; height:60mm; float:left;', ''));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header_Box_Right', 'width: 45mm; height: 60mm; float:left; margin-top:-20px; margin-left:5px;', '<p><strong>Demo GmbH </strong><br /> Max Mustermann<br /> Stra&szlig;e 3<br /> 00000 Musterstadt<br /> Fon: 01234 / 56789<br /> Fax: 01234 /            56780<br />info@demo.de<br />www.demo.de</p>'));
                Shopware()->Db()->query($sql_doc, array( $docId, 'Header_Box_Bottom', 'font-size:14px; height: 10mm;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Content', 'height: 65mm; width: 170mm;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Td', 'white-space:nowrap; padding: 5px 0;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Td_Name', 'white-space:normal;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Td_Line', 'border-bottom: 1px solid #999; height: 0px;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Td_Head', 'border-bottom:1px solid #000;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Footer', 'width: 170mm; position:fixed; bottom:-20mm; height: 15mm;', '<table style="height: 90px;" border="0" width="100%"><tbody><tr valign="top"><td style="width: 25%;"><p><span style="font-size: xx-small;">Demo GmbH</span></p><p><span style="font-size: xx-small;">Steuer-Nr <br />UST-ID: <br />Finanzamt </span><span style="font-size: xx-small;">Musterstadt</span></p></td><td style="width: 25%;"><p><span style="font-size: xx-small;">Bankverbindung</span></p><p><span style="font-size: xx-small;">Sparkasse Musterstadt<br />BLZ: <br />Konto: </span></p><span style="font-size: xx-small;">aaaa<br /></span></td><td style="width: 25%;"><p><span style="font-size: xx-small;">AGB<br /></span></p><p><span style="font-size: xx-small;">Gerichtsstand ist Musterstadt<br />Erf&uuml;llungsort Musterstadt<br />Gelieferte Ware bleibt bis zur vollst&auml;ndigen Bezahlung unser Eigentum</span></p></td><td style="width: 25%;"><p><span style="font-size: xx-small;">Gesch&auml;ftsf&uuml;hrer</span></p><p><span style="font-size: xx-small;">Max Mustermann</span></p></td></tr></tbody></table>')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Content_Amount', 'margin-left:90mm;', '')) ;
                Shopware()->Db()->query($sql_doc, array( $docId, 'Content_Info', '', '<p>Die Ware bleibt bis zur vollst&auml;ndigen Bezahlung unser Eigentum</p>'));
            }
        }
    }


    /**
     *  This function remove offer Document template data.
     */
    public function removeDocument()
    {
        $sql = "SELECT id FROM s_core_documents WHERE template = ?";
        $resourceID = Shopware()->Db()->fetchOne($sql, array('offerBill.tpl'));

        $delete = 'DELETE FROM s_core_documents WHERE id = ?';
        Shopware()->Db()->query($delete, array($resourceID));

        $delete = 'DELETE FROM s_core_documents_box WHERE documentID = ?';
        Shopware()->Db()->query($delete, array($resourceID));
    }

    /**
     * This function remove offerNumber range of Plugin..
     */
    public function removeOfferNumber()
    {
        $sql = "SELECT id FROM s_order_number WHERE name = ?";
        $resourceID = Shopware()->Db()->fetchOne($sql, array('offer'));

        $delete = 'DELETE FROM s_order_number WHERE id = ?';
        Shopware()->Db()->query($delete, array($resourceID));
    }

    /**
     * This function removes offer States of Plugin.
     */
    public function removeOfferStates()
    {
        $sql = "SELECT id FROM s_core_states WHERE group = ?";
        $resourceID = Shopware()->Db()->fetchOne($sql, array('offer'));

        $delete = 'DELETE FROM s_core_states WHERE id = ?';
        Shopware()->Db()->query($delete, array($resourceID));
    }


    /**
     * this function returns the plugin version
     *
     * @return string
     */
    public function getVersion(){
        return "1.4.0";
    }

    /**
     * This function return the metadata.
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            "autor" => "kuzo media | Schenk & Schenk GbR",
            "copyright" => "Copyright © 2017, kuzo media | Schenk & Schenk GbR",
            "version" => $this->getVersion(),
            "label" => "Angebote",
            "source" => $this->getSource(),
            "description" => file_get_contents(dirname(__FILE__).'/description.txt'),
            "support" => "http://www.kuzo-media.de",
            "link" => "http://www.kuzo-media.de",
            "license" => "");
    }

    /**
     * This function register InitResource for creating offer Document.
     *
     * @param Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onInitResourceOfferDocument(Enlight_Event_EventArgs $args) {
        $args->getSubject()->_view->smarty->addTemplateDir($this->Path() . 'Views/');
        $instance = Shopware_Plugins_Backend_sKUZOOffer_Components_OfferDocument::getInstance();
        return $instance;
    }

    /**
     * This function register an helper component
     *
     * @param Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onInitResourceOfferHelper(Enlight_Event_EventArgs $args) {
        $instance = Shopware_Plugins_Backend_sKUZOOffer_Components_OfferHelper::getInstance();
        return $instance;
    }

    /**
     * This function set frontend controller path.
     *
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public function getFrontendController(Enlight_Event_EventArgs $args) {
        $this->registerCustomModels();
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
        return $this->Path(). '/Controllers/Frontend/sKUZOOffer.php';
    }

    /**
     * This function extends frontend templates while firing checkoutAction.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendCheckout(Enlight_Event_EventArgs $args) {
        $subject = $args->getSubject();
        /** @var Enlight_Controller_Request_Request $request */
        $request = $subject->Request();
        $response = $subject->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }

        if($request->getParam("sOpenOffer",false)==true) {
//            return $subject->redirect(array('controller' => 'sKUZOOffer', 'action' => 'confirm'));
            $view = $args->getSubject()->View();
            $view->redirectToOffer = true;
        }

        if($request->getActionName() == 'ajaxCart') {
            $view = $args->getSubject()->View();
            $view->sInquiry = $args->getSubject()->getInquiry();
            $view->sInquiryLink = $args->getSubject()->getInquiryLink();
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('frontend/s_k_u_z_o_offer/ajax_cart.tpl');
        }

        if($request->getActionName() == 'cart') {
            $view = $args->getSubject()->View();
            $amountError = $request->getParams();
            if($amountError['amountError'])
                $view->amountError = $amountError;
            else
                $view->amountError = false;
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('frontend/s_k_u_z_o_offer/cart.tpl');
        }

        if($request->getActionName() == 'finish') {
            $view = $args->getSubject()->View();
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('frontend/s_k_u_z_o_offer/checkout/finish.tpl');
        }
    }

    /**
     * This function extends frontend templates while firing accountAction.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendAccount(Enlight_Event_EventArgs $args) {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }

        if($request->getControllerName() == 'ticket' || $request->getControllerName() == 'address' || $request->getControllerName() == 'account' || $request->getControllerName() == 'note' || $request->getControllerName() == 'sKUZOOffer') {
            $view = $args->getSubject()->View();
            //Add our plugin template directory.
            $view->assign('sAction', $request->getActionName());
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->extendsTemplate('frontend/s_k_u_z_o_offer/account.tpl');
        }

        if(Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5.2")){
            $view = $args->getSubject()->View();
            $view->assign('minSwVersion52', true);
        }
    }

    /**
     * This function extends widgets
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchWidgets(Enlight_Event_EventArgs $args) {
        $request = $args->getSubject()->Request();
        $view = $args->getSubject()->View();
        $view->assign('sAction', $request->getActionName());
        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/s_k_u_z_o_offer/account.tpl');
    }

    public function onPostDispatchBackend(Enlight_Event_EventArgs $args) {
        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'backend') {
            return;
        }

        if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
            $view = $args->getSubject()->View();
            $view->assign('swVersion4', true);
        }
    }

    /**
     * This function extends frontend templates while firing detailAction.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendDetail(Enlight_Event_EventArgs $args) {

        /*if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->detailOfferButton) {
            return;
        }*/

        $request = $args->getSubject()->Request();
        $response = $args->getSubject()->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }

        if($request->getActionName() == 'index') {
            $view = $args->getSubject()->View();
            $view->addTemplateDir($this->Path() . 'Views/');
            $view->detailTemplate = $request->getParam("template");



            if($request->getParam('custom') && $request->getParam('offerId')){
                $view->extendsTemplate('frontend/s_k_u_z_o_offer/buy.tpl');
                $view->offerId = $request->getParam('offerId');
                $view->quantityId = $request->getParam('quantityId');
                $view->requiredError = $request->getParam('requiredError');
                $view->sKUZOOffer = true;
            }else if(Shopware()->Plugins()->Backend()->sKUZOOffer()->Config()->detailOfferButton){
                $view->extendsTemplate('frontend/s_k_u_z_o_offer/detail.tpl');
                $view->sKUZOOffer = true;
            }


        }
    }

    /**
     * This function replace url for checkout inquiry url.
     *
     * @param Enlight_Hook_HookArgs $args
     * @return mixed|string
     */
    public function onReplaceFrontendCheckoutGetInquiryLink(Enlight_Hook_HookArgs $args) {
        $url = Shopware()->Front()->Router()->assemble(array(
            'module' => 'frontend',
            'controller' => 'sKUZOOffer',
            'action' => 'confirm'
        ));
        $args->setReturn($url);
        return $url;
    }




    /**
     * This function replace the addArticleAction
     *
     * @param Enlight_Hook_HookArgs $args
     * @return mixed|string
     */
    public function onReplaceFrontendCheckoutAddArticleAction(Enlight_Hook_HookArgs $args) {
        $subject = $args->getSubject();
        $request = $subject->Request();
        //if sOpenOfferSet add article and jump to offer
        if($request->getParam("sOpenOffer",false)==true) {
            $ordernumber = $request->getParam('sAdd');
            $quantity = $request->getParam('sQuantity');
            $articleID = Shopware()->Modules()->Articles()->sGetArticleIdByOrderNumber($ordernumber);

            if (!empty($articleID)) {
                $insertID = Shopware()->Modules()->Basket()->sAddArticle($ordernumber, $quantity);
            }
            return $subject->redirect(array('controller' => 'sKUZOOffer', 'action' => 'confirm'));
        } else { //call parent
            $args->setReturn($args->getSubject()->executeParent($args->getMethod(), $args->getArgs()));
        }
    }


    /**
     * Creates and stores the payment config form.
     */
    private function createForm() {
        $form = $this->Form();

        $form->setElement('checkbox', 'paymentOption', array(
            'label' => 'Direct Payment Activate',
            'value' => 1,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $form->setElement('checkbox', 'detailOfferButton', array(
            'label' => 'Add Offer Button in Product Detail?',
            'value' => 0,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $form->setElement('checkbox', 'showShippingTaxEveryTime', array(
            'label' => 'show 0 shippingCost in offer Document',
            'value' => 0,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $form->setElement('checkbox', 'showPurchasePrice', array(
            'label' => 'show purchasePrice in Position List?',
            'value' => 0,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));


        $form->setElement('select', 'rememberFromState', array(
            'value' => -1,
            'label' => 'From what state should be reminded?',
            'store' => array(
                array(-1, 'Do not remember - Nicht Erinnern'),
                array(1, 'Open - Offen'),
                array(2, 'Processed - Erstellt'),
                array(3, 'Sent - Versendet')
            ),
            'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP
        ));

        $form->setElement('checkbox', 'sendOfferAfterAutoGeneration', array(
            'label' => 'Send offer by mail after auto generation?',
            'value' => 0,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $form->setElement('text', 'rememberDays1', array('label'=>'1. reminder after x days','value'=>'7', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
        $form->setElement('text', 'rememberDays2', array('label'=>'2. reminder after x days','value'=>'14', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
        $form->setElement('checkbox', 'emailPreview', array('label' => 'want to see email preview', 'value' => 0, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
        $form->setElement('text', 'ownerMailAddress', array('label' => 'Owner e-mail address', 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
        $form->setElement('checkbox', 'backendOrderMail', array('label' => 'Send order mail on backend order?', 'value' => 1, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));

        $form->setElement('text', 'offerAlbumId',
            array(
                'value' => $this->createNewMediaFolder(),
                'hidden' => true
            )
        );

        $form->setElement('checkbox', 'enableOfferUpload', array(
            'label' => 'Add document upload function?',
            'value' => 0,
            'hidden' => true,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $form->setElement('checkbox', 'offerCheckAGB', array(
            'label' => 'Add AGB checkbox?',
            'value' => 0,
            'scope' => \Shopware\Models\Config\Element::SCOPE_LOCALE
        ));

        $this->translateForm($form);
    }

    private function translateForm($form, $translations=null) {
        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        if(!isset($translations)||empty($translations)) {
            $translations = array(
                'en_GB' => array(
                    'paymentOption' => 'Direct Payment Activate',
                    'detailOfferButton' => 'Add Offer Button in Product Detail?',
                    'showShippingTaxEveryTime' => 'show 0 shippingCost in offer Document',
                    'rememberFromState' => 'From what state should be reminded?',
                    'rememberDays1' => '1. reminder after x days',
                    'rememberDays2' => '2. reminder after x days',
                    'emailPreview' => 'want to see email preview',
                    'ownerMailAddress' => 'Owner e-mail address',
                    'showPurchasePrice' => 'show purchasePrice in Position List?',
                    'backendOrderMail' => 'Send order mail on backend order?',
                    'sendOfferAfterAutoGeneration' => 'Send offer by mail after auto generation?',
                    'enableOfferUpload' => 'Add document upload function?',
                    'offerCheckAGB' => 'Add AGB checkbox?'
                ),
                'de_DE' => array(
                    'paymentOption' => 'Direkten Kauf aktivieren',
                    'detailOfferButton' => 'Angebot-Anfordern-Button in Artikel-Detailseite?',
                    'showShippingTaxEveryTime' => 'Kostenfreien Versand in Angebotsdokument?',
                    'rememberFromState' => 'Ab welchem Status soll Erinnerung berechnet werden?',
                    'rememberDays1' => '1. Erinnerung nach x Tage',
                    'rememberDays2' => '2. Erinnerung nach x Tage',
                    'emailPreview' => 'Vorauswahl eMail-Vorschau bei Versand Angebotsdokument?',
                    'ownerMailAddress' => 'Interne E-Mail Adresse',
                    'showPurchasePrice' => 'Einkaufspreise in Liste der Angebotspositionen anzeigen?',
                    'backendOrderMail' => 'E-Mail-Bestellbestätigung bei Bestellumwandlung im Backend versenden?',
                    'sendOfferAfterAutoGeneration' => 'Sende Angebot via Mail nach Autogenerierung?',
                    'enableOfferUpload' => 'Dokumentupload aktivieren?',
                    'offerCheckAGB' => 'AGB-Bestätigung aktivieren?'
                )
            );
        }

        //iterate the languages
        foreach($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array(
                'locale' => $locale
            ));

            //not found? continue with next language
            if($localeModel === null){
                continue;
            }

            //iterate all snippets of the current language
            foreach($snippets as $element => $snippet) {

                //get the form element by name
                $elementModel = $form->getElement($element);

                //not found? continue with next snippet
                if($elementModel === null) {
                    continue;
                }
                //create new translation model
                $translationModel = new \Shopware\Models\Config\ElementTranslation();
                $translationModel->setLabel($snippet);
                $translationModel->setLocale($localeModel);

                //add the translation to the form element
                $elementModel->addTranslation($translationModel);

                //done
            }
        }
    }

    public function onAfterSaveOrder(Enlight_Hook_HookArgs $args) {
    }

    public function onBeforeSaveOrder(Enlight_Hook_HookArgs $args) {
        $subject = $args->getSubject();

        if ($subject->Request()->getParam('sUniqueID') && !empty(Shopware()->Session()->sOrderVariables)) {
            $sql = '
                SELECT transactionID as sTransactionumber, ordernumber as sOrderNumber
                FROM s_order
                WHERE temporaryID=? AND userID=?
            ';

            $order = Shopware()->Db()->fetchRow($sql, array($subject->Request()->getParam('sUniqueID'), Shopware()->Session()->sUserId));
            if (!empty($order)) {
                $this->changeOfferStatus(Shopware()->Session()->offerId, $order['sOrderNumber']);
            }
        }

        if($subject->Request()->getParam('offerAcception', null)) {
            $_SERVER['REQUEST_METHOD']='POST';
        }
    }

    public function onFrontendCheckoutSaveOrderReplace(Enlight_Hook_HookArgs $args) {
        $subject = $args->getSubject();
        $view = $subject->View();
        $request = $subject->Request();

        $order = Shopware()->Modules()->Order();

        $order->sUserData = $view->sUserData;
        $comment = Shopware()->Session()->get('sComment');
        $order->sComment = isset($comment) ? $comment : '';
        $order->sBasketData = $view->sBasket;
        $order->sAmount = $view->sBasket['sAmount'];
        $order->sAmountWithTax = !empty($view->sBasket['AmountWithTaxNumeric']) ? $view->sBasket['AmountWithTaxNumeric'] : $view->sBasket['AmountNumeric'];
        $order->sAmountNet = $view->sBasket['AmountNetNumeric'];
        $order->sShippingcosts = $view->sBasket['sShippingcosts'];
        $order->sShippingcostsNumeric = $view->sBasket['sShippingcostsWithTax'];
        $order->sShippingcostsNumericNet = $view->sBasket['sShippingcostsNet'];
        $order->dispatchId = Shopware()->Session()->get('sDispatch');
        $order->sNet = !$view->sUserData['additional']['charge_vat'];
        $order->deviceType = $request->getDeviceType();

        $orderNumber =  $order->sSaveOrder();

        //update offer with status and order number and Order with offer document
        $this->changeOfferStatus(Shopware()->Session()->offerId, $orderNumber);

        return $orderNumber;
    }

    public function changeOfferStatus($offerId, $orderNumber){
        //update offer with status and order number and Order with offer document
        $this->registerCustomModels();

        if(empty($offerId))
            return;

        $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(array('number' => $orderNumber));
        $orderID= $order->getId();

        $offer = Shopware()->Models()->find('Shopware\CustomModels\Offer\Offer',$offerId);

        //adding offer document to order document grid
        $sql = "
            INSERT INTO s_order_documents (`date`, `type`, `userID`, `orderID`, `amount`, `docID`,`hash`)
            VALUES ( NOW() , ? , ? , ?, ?, ?,?)
            ";
        $documentArray = $offer->getDocuments()->toArray();
        $doc = $documentArray[0];
        Shopware()->Db()->query($sql,array(
            $doc->getTypeId(),
            $offer->getCustomerId(),
            $orderID,
            $offer->getDiscountAmount(),
            $offer->getNumber(),
            $doc->getHash()
        ));

        // updating orderId of Offer
        $offer->setOrderId($orderID);
        // updating status of Offer
        $offer->setStatus(5);
        try {
            Shopware()->Models()->persist($offer);
            Shopware()->Models()->flush();
        } catch(Exception $e) {

        }

    }

    /**
     * This function extends the view with the plugin's template path, also the lacking template variables for the dunning are set.
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onAfterInitTemplateEngineFrontendDocument(Enlight_Hook_HookArgs $arguments){
        $arguments->getSubject()->_view->smarty->addTemplateDir($this->Path() . 'View/');
    }

    /**
     *
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $arguments
     */
    public function preUpdateOffer($arguments=null) {
        if(!isset($arguments) || empty($arguments)) {
            return;
        }

        $this->registerCustomModels();
        $offer = $arguments->getEntity();

        if (!($offer instanceof \Shopware\CustomModels\Offer\Offer)) {
            return;
        }

        $changeSet = Shopware()->Models()->getUnitOfWork()->getEntityChangeSet($offer);
        $statusChange = false;
        if(isset($changeSet["status"])) {
            $oldStatus = $changeSet["status"][0];
            $newStatus = intval($changeSet["status"][1]);
            if($oldStatus!=$newStatus) {
                $statusChange = true;
            }
        }

        //offer status changed?
        if ($statusChange) {
            $historyData = array(
                'userId'      => null,
                'changeDate' => date('Y-m-d H:i:s'),
                'offerId'     => $offer->getId(),
            );

            $historyData['previousStatusId'] = $oldStatus;
            $historyData['statusId'] = $newStatus;

            try {
                $sql = "INSERT INTO s_offer_history (offerId, previousStatusId, statusId, comment, changeDate)
					VALUES (?, ?, ?, ?, ?)";
                Shopware()->Db()->query($sql, array(
                    $historyData['offerId'],
                    $historyData['previousStatusId'],
                    $historyData['statusId'],
                    '',
                    $historyData['changeDate']
                ));
            } catch(Exception $e) {
                $var = $e;
            }
        }

    }

    /**
     *  This function configure data for sending mail to Admin.
     *
     * @throws Enlight_Exception
     */
    private function createOfferMail() {
        $sql = "SELECT id FROM s_core_config_mails WHERE name=?";
        $templateID = Shopware()->Db()->fetchOne($sql, array('sOfferMail'));
        if($templateID === false) {
            //insert email-template
            $sql = "INSERT INTO s_core_config_mails (name, frommail, fromname, subject, content, contentHTML, isHTML, attachment)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if(!Shopware()->Db()->query($sql, array(
                'sOfferMail',
                '{config name=mail}',
                '{config name=shopName}',
                'Neue Angebotsanfrage {$offernumber}',
                file_get_contents(dirname(__FILE__).'/Views/mail/sOfferMail_plain.tpl'),
                file_get_contents(dirname(__FILE__).'/Views/mail/sOfferMail_html.tpl'),
                '1',
                ''
            ))
            ) {
                throw new Enlight_Exception("eMail-Template sOfferDocuments konnte nicht angelegt werden!");
            }
        }
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchBackendCustomer(Enlight_Event_EventArgs $args)
    {
        $args->getSubject()->View()->addTemplateDir(
            $this->Path() . 'Views/'
        );


        if ($args->getRequest()->getActionName() === 'index') {
            $args->getSubject()->View()->extendsTemplate(
                'backend/customer/customer_app.js'
            );
        }
    }


    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchBackendIndex(Enlight_Event_EventArgs $args)
    {
        $request = $args->getRequest();
        $view = $args->getSubject()->View();

        $view->addTemplateDir($this->Path() . 'Views/');

        // if the controller action name equals "index" we have to extend the backend offer application
        if ($request->getActionName() === 'index') {
            $view->extendsTemplate('backend/index/skuzo_last_offers/app.js');
			if(!Shopware()->Plugins()->Backend()->sKUZOOffer()->assertMinimumVersion("5")){
				$view->extendsTemplate('backend/index/form/field/article_search.js');
			}
        }
    }

    public function checkForEPost()
    {
        $ePost = 0;
        $requiredPlugins = array("sKUZOePostBusiness");
        if ($this->assertRequiredPluginsPresent($requiredPlugins)) {
            $ePost = 1;
        }
        return $ePost;
    }

}

