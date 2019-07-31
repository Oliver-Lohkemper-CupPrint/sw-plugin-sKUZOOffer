<?php

class Shopware_Plugins_Backend_sKUZOOffer_Components_OfferHelper extends Enlight_Class {

    /**
     * get instance of hermesapi
     *
     * @return Enlight_Class
     */
    static public function getInstance() {
        $instance = Enlight_Class::Instance('Shopware_Plugins_Backend_sKUZOOffer_Components_OfferHelper');
        return $instance;
    }

    /**
     * This function sends mail to given email address with Document attached.
     *
     * @param $type
     * @param $document
     * @param $mailaddresses
     * @param $offernumber
     * @return void|Zend_Mail
     * @throws Enlight_Exception
     */
    public function sendMail($type, $document, $mailaddresses, $offernumber) {

        if(empty($type) || empty($mailaddresses)) {
            return;
        }

        $offer = Shopware()->Models()->getRepository("Shopware\CustomModels\Offer\Offer")
            ->findOneBy(array("number" => $offernumber));

        //$downloadDetails = $this->getDownloadsDetails($order);
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->findOneBy(Array('id' => $offer->getLanguageIso()));
        $shop->registerResources(Shopware()->Bootstrap());

        $mail = Shopware()->TemplateMail()->createMail($type, array(
            "firstname" => $offer->getOfferBilling()->getFirstName(),
            "lastname" => $offer->getOfferBilling()->getLastName(),
            "offernumber" => $offernumber,
            "sOffer" => $offer
        ),$shop);
        foreach($mailaddresses as $mailaddress) {
            $mail->addTo($mailaddress);
        }

        $path = Shopware()->DocPath() . "files/documents/" . $document['hash'] . ".pdf";

        if(false === ($fileHandle = fopen($path, 'r'))) {
            throw new \Enlight_Exception('Could not load file: ' . $path);
        }

        $namespace = Shopware()->Snippets()->getNamespace('backend/sKUZOOffer/view/offer');
        if($namespace->get('type/name'))
            $document['name'] = $namespace->get('type/name');
        if($document['name'])
            $attachmentName = $document['name'].' {$invoicenumber}';
        else
            $attachmentName = $document['attachmentName'];

        if($document["docID"]==-1) {
            $document["docID"] = "";
        }

        if(empty($attachmentName)) $attachmentName = $document["docID"]; else {
            $attachmentName = str_replace('{$offernumber}', $offernumber, $attachmentName);
            $attachmentName = str_replace('{$deliverynumber}', $document["docID"], $attachmentName);
            $attachmentName = str_replace('{$invoicenumber}', $document["docID"], $attachmentName);
            $attachmentName = str_replace('{$cancellationnumber}', $document["docID"], $attachmentName);
            $attachmentName = str_replace('{$creditnumber}', $document["docID"], $attachmentName);
        }
        $mail->createAttachment($fileHandle, 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64, $attachmentName . ".pdf");

        return $mail;
    }

    /**
     * This function creates document for offer.
     *
     * @param $offerId
     * @param $documentType
     * @param $offer
     * @return Shopware_Plugins_Backend_sKUZOOffer_Components_OfferDocument
     */
    public function createDocument($offer, $documentType,$deliveryDate=null, $documentFormat=null, $taxFree=false) {
        if(!$offer instanceof \Shopware\CustomModels\Offer\Offer) {
            return false;
        }
        $renderer = "pdf"; // html / pdf

        if (!empty($deliveryDate)) {
            $deliveryDate = new \DateTime($deliveryDate);
            $deliveryDate = $deliveryDate->format('d.m.Y');
        }

        $zendDate = new Zend_Date();
        $displayDate = $zendDate->get( "YYYY-MM-dd HH:mm:ss" );
        if (!empty($displayDate)) {
            $displayDate = new \DateTime($displayDate);
            $displayDate = $displayDate->format('d.m.Y');
        }
        if (empty($documentFormat)) {
            $documentFormat = 0;
        }

        $document = Shopware_Plugins_Backend_sKUZOOffer_Components_OfferDocument::initDocument(
            $offer->getId(),
            $documentType,
            $offer,
            array(
                'netto'                   => (bool) $taxFree,
                'bid'                     => $offer->getNumber(),
                'date'                    => $displayDate,
                'delivery_date'           => $deliveryDate,
                'documentFormat'           => $documentFormat,
                // Don't show shipping costs on delivery note #SW-4303
                'shippingCostsAsPosition' => (int) $documentType !== 2,
                '_renderer'               => $renderer,
            )
        );

        $document->render();
        if ($renderer == "html") exit; // Debu//g-Mode
        return true;
    }
}