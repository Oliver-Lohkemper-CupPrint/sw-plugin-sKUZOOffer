/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.controller.Offer', {

    /**
     * Defines that this component is a extJs controller extension
     * @string
     */
    extend:'Ext.app.Controller',

    /**
     * Contains all snippets for the component
     * @object
     */
    snippets: {
        successTitle:'{s name=message/save/success_title}Successful{/s}',
        failureTitle:'{s name=message/save/error_title}Error{/s}',
        failure: {
            selectCustomer: '{s name=message/save_billing_shipping/selectCustomer}Please select Customer.{/s}',
            selectShop: '{s name=message/save_billing_shipping/selectShop}Please select Shop.{/s}',
            selectPayment: '{s name=message/save_billing_shipping/selectPayment}Please select Payment Option.{/s}',
            selectDispatch: '{s name=message/save_billing_shipping/selectDispatch}Please select Dispatch Option.{/s}'
        },
        previewPost: {
            title:'{s name=message/preview_post_mail/title}E-POSTBRIEF{/s}',
            maskText:'{s name=message/preview_post_mail/preview_mask_text}"previewing E-POSTBRIEF..."{/s}',
            errorMessage: '{s name=message/preview_post_mail/sent_error_message}Preview Could not be generated{/s}',
            successMessage: '{s name=message/preview_post_mail/success_message}Preview generated Successfully{/s}',
            failureMessage: '{s name=message/preview_post_mail/failure_message}Preview Could not be generated{/s}'
        },
        sendPost: {
            title:'{s name=message/send_post_mail/title}E-POSTBRIEF{/s}',
            confirmTitle: '{s name=message/send_post_mail/confirm_title}Document : {/s}',
            confirmMessage: '{s name=message/send_post_mail/confirm_message}Do you want to send this document by Post-Mail?{/s}',
            maskText:'{s name=message/send_post_mail/send_mask_text}sending Post-Mail ...{/s}',
            errorMessage: '{s name=message/send_post_mail/sent_error_message}An error has occurred while sending the Post-Mail:{/s}',
            successMessage: '{s name=message/send_post_mail/success_message}Post-Mail sent Successfully{/s}',
            failureMessage: '{s name=message/send_post_mail/failure_message}Post-Mail could not be send:{/s}'
        },
        sendMail: {
            maskText:'{s name=message/send_mail/mask_text}E-Mail senden ...{/s}',
            errorTitle: '{s name=message/post_mail/sent_error_title}Email could not be sent.{/s}',
            errorMessage: '{s name=message/send_mail/sent_error_message}An error has occurred while sending the mail:{/s}',
            successTitle: '{s name=message/send_mail/success_title}Successful{/s}',
            successMessage: '{s name=message/send_mail/success_message}Email sent to customer [0]{/s}',
            failureTitle: '{s name=message/send_mail/failure_title}Error{/s}',
            failureMessage: '{s name=message/send_mail/failure_message}E-Mail could not be send:{/s}'
        },
	    document: {
            successTitle: '{s name=message/document/success_title}Successful{/s}',
            failureTitle: '{s name=message/document/failure_title}Error{/s}',
        },
        editOffer: {
            isConverted: '{s name=message/edit_offer/failure/is_converted}You can not edit this offer Because It has already converted to Order.{/s}',
            isAccepted: '{s name=message/edit_offer/failure/is_accepted}You can not edit this offer Because It has already accepted{/s}'
        },
        createOrder: {
            isOpen: '{s name=message/create_order/failure/is_open}You can not convert this offer Because It is still open.{/s}',
            isConverted: '{s name=message/create_order/failure/is_converted}You can not convert this offer Because It has already converted to Order.{/s}',
            successTitle: '{s name=message/create_order/success_title}Successful{/s}',
            successMessage: '{s name=message/create_order/success_message}Order created successfully.{/s}',
            failureTitle: '{s name=message/create_order/failure_title}Error{/s}',
            failureMessage: '{s name=message/create_order/failure_message}Order could not be created:{/s}'
        },
        changeStatus: {
            successMessage: '{s name=message/status/success}The status has been changed successfully{/s}',
            failureMessage: '{s name=message/status/failure}An error has occurred while changing the status.{/s}'
        },
        deleteOffer: {
            title: '{s name=delete_offer/title}Delete selected offer{/s}',
            message: '{s name=delete_offer/message}Are you sure you want to delete the selected offer: {/s}',
            successTitle:'{s name=delete_offer/success_message}Successful{/s}',
            successMessage:'{s name=delete_offer/success_title}Offer has been removed{/s}',
            failureTitle:'{s name=delete_offer/error_title}Failure{/s}',
            failureMessage:'{s name=delete_offer/error_message}An error has occurred while deleting:{/s}'
        },
        deletePosition: {
            title: '{s name=delete_position/title}Delete selected offer position{/s}',
            message: '{s name=delete_position/message}Are you sure you want to delete the selected offer: {/s}',
            successTitle:'{s name=delete_position/success_message}Successful{/s}',
            successMessage:'{s name=delete_position/success_title}Offer position has been removed{/s}',
            failureTitle:'{s name=delete_position/error_title}Error{/s}',
            failureMessage:'{s name=delete_position/error_message}An error has occurred while deleting:{/s}'
        },
        positions: {
            successMessage: '{s name=message/positions/success}The offer position has been saved successfully{/s}',
            failureMessage: '{s name=message/positions/failure}An error has occurred while saving the offer positions.{/s}'
        },
        delete: {
            title: '{s name=message/delete/title}Delete selected positions{/s}',
            message: '{s name=message/delete/message}There have been marked [0] positions. Are you sure you want to delete all selected positions?{/s}',
            successMessage: '{s name=message/delete/success}The offer position(s) has been removed successfully{/s}',
            failureMessage: '{s name=message/delete/failure}An error has occurred while deleting the offer position(s).{/s}'
        },
        saveOffer: {
            successTitle: '{s name=message/save_offer/success_title}Successful{/s}',
            successMessage: '{s name=message/save_offer/success_message}Offer successfully saved.{/s}',
            failureTitle: '{s name=message/save_offer/failure_title}Error{/s}',
            failureMessage: '{s name=message/save_offer/failure_message}Offer could not be saved:{/s}'
        },
        makeDiscount: {
            successTitle: '{s name=message/make_discount/success_title}Successful{/s}',
            successMessage: '{s name=message/make_discount/success_message}Discount made successfully.{/s}',
            failureTitle: '{s name=message/make_discount/failure_title}Error{/s}',
            failureMessage: '{s name=message/make_discount/failure_message}Discount could not be made:{/s}'
        },
        internalComment: {
            successMessage: '{s name=message/internal_comment/success}Internal comment has been saved successfully{/s}',
            failureMessage: '{s name=message/internal_comment/failure}An error has occurred while saving the internal comment.{/s}'
        },
        externalComment: {
            successMessage: '{s name=message/external_comment/success}External comment has been saved successfully{/s}',
            failureMessage: '{s name=message/external_comment/failure}An error has occurred while saving the external comment.{/s}'
        },
        growlMessage: '{s name=growlMessage}Offer{/s}'

    },

    /**
     * all references to get the elements by the applicable selector
     */
    refs:[
        { ref:'offerListGrid', selector:'offer-list-main-window offer-list' },
        { ref:'offerPositionGrid', selector:'offer-list-main-window offer-position-grid' },
        { ref:'offerBillingPanel', selector:'offer-listing-billing-panel' },
        { ref:'offerShippingPanel', selector:'offer-listing-shipping-panel' },
        { ref:'offerDocumentList', selector:'offer-document-list' },
        { ref:'offerPositionWindow', selector:'offer-position-panel' },
        { ref:'offerAddressSidebar', selector:'offer-address-sidebar' },
        { ref:'offerDetailWindow', selector:'offer-detail-window' },
        { ref:'mailWindow', selector:'offer-mail-window' }
    ],
    public:offerId=0,

    /**
     * A template method that is called when your application boots.
     * It is called before the Application's launch function is executed
     * so gives a hook point to run any code before your Viewport is created.
     *
     * @return void
     */
    init:function () {
        var me = this;
        me.emailPreview = 0;
        me.ePost = 0;
        me.showPurchasePrice = 0;
        me.getPluginConfig();
        me.control({
            'offer-list-main-window offer-list': {
                deleteOffer: me.onDeleteOffer,
                openCustomer: me.onOpenCustomer,
                createOffer: me.onCreateOffer,
                editOffer: me.onEditOffer,
                selectionchange: me.onSelectionChange,
                saveOffer: me.onSaveOffer,
                deleteMultiplePositions: me.onDeleteMultipleOffers,
                saveOrder: me.onSaveOrder,
                openOrderDetail: me.onOpenOrderDetail,
                searchOffers: me.onSearchOffers
            },
            'offer-position-grid': {
                openArticle: me.onOpenArticle,
                deletePosition: me.onDeletePosition
            },
            'offer-position-panel': {
                deletePosition: me.onDeletePosition,
                openArticle: me.onOpenArticle,
                newPosition: me.onNewPosition,
                beforeEdit: me.onBeforeEdit,
                savePosition: me.onSavePosition,
                cancelEdit: me.onCancelEdit,
                articleNumberSelect: me.onArticleSelect,
                articleNameSelect: me.onArticleSelect,
                deleteMultiplePositions: me.onDeleteMultiplePositions,
                updateForms: me.onUpdateDetailPage,
                makeDiscount: me.makeDiscountWindow,
                quantityChange: me.onQuantityChange
            },
            'offer-address-sidebar': {
                createDocument: me.onCreateDocument,
                saveOfferBilling: me.onSaveBillingShipping
            },
            'offer-document-list':{
                sendDocumentByMail: me.sendDocumentByMail,
                previewDocument: me.onPreviewDocument,
                sendDocument: me.onSendDocument
            },
            'offer-detail-window offer-communication-panel': {
                saveInternalComment: me.onSaveInternalComment,
                saveExternalComment: me.onSaveExternalComment
            },
            'offer-listing-billing-panel': {
                'refreshPositions': me.onRefreshPositions
            },
            'offer-mail-window offer-mail-form': {
                sendMail: me.onSendMail
            },
            'offer-address-sidebar mediafield': {
                selectMedia: me.onSelectMedia
            },
        });
        me.callParent(arguments);
    },

    /**
     * @event selectMedia
     * @param [object] mediaManager - Shopware.MediaManager.DropZone
     * @param [array] selected - Array of the selected Ext.data.Model's
     */
    onSelectMedia: function(mediaManager, selected) {
        if(selected<1) {
            Shopware.Notification.createGrowlMessage(me.snippets.document.failureTitle, 'Es wurde kein Dokument gewählt.' , me.snippets.growlMessage);
            return;
        }

        var store = Ext.create('Shopware.apps.sKUZOOffer.store.Configuration');
        var config = Ext.create('Shopware.apps.sKUZOOffer.model.Configuration');

        var docFormat = '-1',
            me = this;
        me.getOfferDetailWindow().setLoading(true);

        var offer = me.getOfferAddressSidebar().record;

        config.set('deliveryDate',new Date());
        config.set('displayDate', new Date());
        config.set('documentType', 0);
        config.set('offerId', me.getOfferPositionWindow().offerId);
        config.set('documentFormat', docFormat);
        config.set('mediaId', selected[0].getId());

        store.add(config);
        store.sync({
            callback: function(batch, operation) {
                var rawData = batch.proxy.getReader().rawData;
                me.getOfferDetailWindow().setLoading(false);

                if(rawData.success === true) {
                    Shopware.Notification.createGrowlMessage(me.snippets.document.successTitle, "Dokumentauswahl erfolgreich", me.snippets.growlMessage);
                    var data = rawData.data[0];
                    offer.set(data);

                    var documentStore = me.getOfferDocumentList().store;
                    var documents = offer.get('documents');

                    documentStore.removeAll();
                    Ext.each(documents, function(modelData){
                        var model = Ext.create('Shopware.apps.sKUZOOffer.model.Receipt', modelData),
                            typeModel = Ext.create('Shopware.apps.Base.model.DocType', modelData.type);

                        var typeStore = model.getDocType();
                        typeStore.add(typeModel);
                        model['getDocTypeStore'] = typeStore;
                        documentStore.add(model);
                    });

                    var offerStore = me.subApplication.getStore('Offer');
                    offerStore.load();
                }else{
                    Shopware.Notification.createGrowlMessage(me.snippets.document.failureTitle, "Dokumentauswahl nicht erfolgreich" , me.snippets.growlMessage);
                }
            }
        });
    },

    getPluginConfig:function(){
        var me = this;

        Ext.Ajax.request({
            url: '{url controller="sKUZOOffer" action="getConfig"}',
            method: 'POST',
            scope: me,
            success: function(response) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                if(responsJSON.success == true) {
                    if(responsJSON.emailPreview){
                        me.emailPreview =responsJSON.emailPreview;
                    }
                    if(responsJSON.ePost){
                        me.ePost =responsJSON.ePost;
                    }
                    if(responsJSON.showPurchasePrice){
                        me.showPurchasePrice =responsJSON.showPurchasePrice;
                    }
                }
            }
        })
    },

    onPreviewDocument:function(record) {
        var me = this,
            id = record.get('id'),
            documentId = record.get('documentId'),
            customerId = record.get('customerId'),
            offerId = record.get('offerId');

        Ext.getBody().mask('E-POSTBRIEF Vorschau generieren ...');

        Ext.override(Ext.data.proxy.Ajax, { timeout: 120000 });
        Ext.override(Ext.form.action.Action, { timeout: 120 });
        Ext.override(Ext.data.Connection, { timeout: 120000 });
        Ext.Ajax.timeout = 120000;

        Ext.Ajax.request({
            url: '{url controller="EPostBrief" action="previewDocument"}',
            method: 'POST',
            params: {
                id: id,
                documentId: documentId,
                customerId: customerId,
                orderId: offerId,
                plugin: "Offer"
            },
            success: function(response, request) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                if(responsJSON.success == true) {
                    var epostPreviewUrl = "{link file='../engine/Shopware/Plugins/Community/Backend/sKUZOePostBusiness/files/preview.pdf'}";
                    window.open(epostPreviewUrl,me.snippets.previewPost.maskText);
                    Shopware.Notification.createGrowlMessage(me.snippets.previewPost.title, me.snippets.previewPost.successMessage, me.snippets.growlMessage);
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.previewPost.title, me.snippets.previewPost.errorMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                }
            },
            failure: function() {
                Ext.getBody().unmask();
                Shopware.Notification.createGrowlMessage(me.snippets.previewPost.title, me.snippets.previewPost.failureMessage + " " + rawData.message, me.snippets.growlMessage);
            }
        });
    },

    onSendDocument:function(record) {
        var me = this,
            documentId = record.get('documentId');
        var title = me.snippets.sendPost.confirmTitle+' '+documentId; //"Soll das Dokument ("+documentId+") jetzt als E-POSTBRIEF versendet werden?";

        /*{if $ePostConfirmSend}*/
        Ext.MessageBox.confirm(title, me.snippets.sendPost.confirmMessage, function (response) {
            if ( response !== 'yes' ) {
                return;
            }
            me.sendDocument(record);
        });
        /*{else}*/
        me.sendDocument(record);
        /*{/if}*/

    },
    sendDocument:function(record) {
        var me = this,
            id = record.get('id'),
            documentId = record.get('documentId'),
            customerId = record.get('customerId'),
            offerId = record.get('offerId');

        Ext.getBody().mask(me.snippets.sendPost.maskText);

        Ext.override(Ext.data.proxy.Ajax, { timeout: 120000 });
        Ext.override(Ext.form.action.Action, { timeout: 120 });
        Ext.override(Ext.data.Connection, { timeout: 120000 });
        Ext.Ajax.timeout = 120000;

        Ext.Ajax.request({
            url: '{url controller="EPostBrief" action="sendDocument"}',
            method: 'POST',
            params: {
                id: id,
                documentId: documentId,
                customerId: customerId,
                orderId: offerId,
                plugin: "Offer"
            },
            success: function(response, request) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();

                if(responsJSON.success == true) {
                    var store = me.subApplication.getStore('Offer');
                    store.load();
                    Shopware.Notification.createGrowlMessage(me.snippets.sendPost.title, me.snippets.sendPost.successMessage, me.snippets.growlMessage);
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.sendPost.title, me.snippets.sendPost.errorMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                }
            },
            failure: function() {
                Ext.getBody().unmask();
                Shopware.Notification.createGrowlMessage(me.snippets.sendPost.title, me.snippets.sendPost.failureMessage + " " + rawData.message, me.snippets.growlMessage);
            }
        });
    },


    /**
     * @param { Ext.form.Panel } form
     */
    onSendMail: function(form) {
        var me = this,
            win = me.getMailWindow(),
            snippets = me.snippets.sendMail,
            mail = form.getRecord(),
            rawData,
            message;

        win.setLoading(true);

        form.getForm().updateRecord(mail);
        mail.set('plain', true);
        mail.setDirty();

        mail.save({
            callback: function(record, operation) {
                win.setLoading(false);

                rawData = record.getProxy().getReader().rawData;

                if (!operation.success) {
                    Shopware.Notification.createGrowlMessage(snippets.errorTitle, snippets.errorMessage + '<br>' + rawData.message, snippets.growlMessage);
                    return;
                }

                var store = me.subApplication.getStore('Offer');
                store.load();
                mail.set('set', true);
                message = Ext.String.format(snippets.successMessage, mail.get('to'));
                Shopware.Notification.createGrowlMessage(snippets.successTitle, message, snippets.growlMessage);

                win.destroy();
            }
        });
    },



    onCreateOffer: function(grid) {
        var me = this;
        me.getOfferListGrid().setLoading(true);
        var record = Ext.create('Shopware.apps.sKUZOOffer.model.Offer');//grid.getSelectionModel().getSelection();

        offerId = 0;
          //open the order listing window
            me.createOfferWindow =me.getView('list.Window').create({
                taxStore:  me.getStore('Tax').load(),
                {if $swVersion4}variantStore: me.getStore('Variant').load(),{/if}
                positionStore: me.getStore('Position'),
                offerId: offerId,
                record: record,
                mode: 'multi',
                emailPreview: me.emailPreview,
                ePost: me.ePost,
                showPurchasePrice: me.showPurchasePrice
            });

        me.getOfferPositionWindow().offerPositionGrid.setDisabled(true);
        me.getOfferListGrid().setLoading(false);
        me.createOfferWindow.show();
    },



    onEditOffer: function(record) {
        var me = this,
            customerId = record.data.customerId;
            offerId = record.data.id;

        if(record.get('status')==5) {
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.editOffer.isConverted);
            return;
        }
        if(record.get('status')==4)
        {
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.editOffer.isAccepted);
            return;
        }
        else{
        //open the order listing window
            me.createOfferWindow = me.getView('list.Window').create({
                offerId: offerId,
                record: record,
                taxStore: me.getStore('Tax').load(),
                {if $swVersion4}variantStore: me.getStore('Variant').load(),{/if}
                positionStore: me.getStore('Position'),
                emailPreview: me.emailPreview,
                ePost: me.ePost,
                showPurchasePrice: me.showPurchasePrice
            }).show();
        }
    },

    onSaveOrder: function(record) {

        var me = this;
        var offerStore = me.subApplication.getStore('Offer');

        if(record.get('status')==1)
        {
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.createOrder.isOpen);
            return;
        }
        Ext.Ajax.request({
            url: '{url controller="sKUZOOffer" action="saveOrder"}',
            method: 'POST',
            params: {
                offerId: record.get('id')
            },
            scope: me,
            success: function(response) {
                var responsJSON = JSON.parse(response.responseText);
                var windows = me.subApplication.windowManager.zIndexStack;
                Ext.getBody().unmask();
                if(responsJSON.success == true) {
                    Shopware.Notification.createGrowlMessage(me.snippets.createOrder.successTitle, me.snippets.createOrder.successMessage, me.snippets.growlMessage);
                    windows.forEach(function(window) {
                        if(window.$className === "Shopware.apps.sKUZOOffer.view.list.CreateOfferWindow"){
                            window.close();
                        }
                    });
                    offerStore.load();
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.createOrder.failureTitle, me.snippets.createOrder.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                }
            },
            failure: function(response) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                Shopware.Notification.createGrowlMessage(me.snippets.createOrder.failureTitle, me.snippets.createOrder.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
            }
        });

    },


    onNewPosition: function(record, grid, editor) {
        var me = this;
        editor.cancelEdit();
        var position = Ext.create('Shopware.apps.sKUZOOffer.model.Position', {
            offerId: me.getOfferPositionWindow().offerId,
            quantity:1,
            percentage:0,
            scalePrice:1
         });

        grid.getStore().add(position);
        editor.startEdit(position, 0);
    },

    onDeletePosition: function(position, store, options) {

        var me = this,
            offerPositionGrid = me.getOfferPositionGrid(),
            offer = me.subApplication.getStore('Offer').getById(position.get('offerId')),
            message = me.snippets.deletePosition.message + ' ' + offer.get('number'),
            title = me.snippets.deletePosition.title;

        // we do not just delete - we are polite and ask the user if he is sure.
        Ext.MessageBox.confirm(title, message, function (response) {
            if ( response !== 'yes' ) {
                return;
            }

            position.destroy({
                params: {
                    offerId: position.get('offerId')
                },
                callback: function(data, operation) {
                    var records = operation.getRecords(),
                        record = records[0],
                        rawData = record.getProxy().getReader().rawData;


                    if ( operation.success === true ) {
                        Shopware.Notification.createGrowlMessage(me.snippets.deletePosition.successTitle,me.snippets.deletePosition.successMessage, me.snippets.growlMessage);
                        store.remove(position);
                        me.subApplication.getStore('Offer').load();
                        offer.set('invoiceAmount', rawData.invoiceAmount);
                        offer.set('discountAmount', rawData.discountAmount);
                        me.getOfferBillingPanel().filterForm.items.items[10].setValue(rawData.discountAmount);
                        if (options !== Ext.undefined && Ext.isFunction(options.callback)) {
                            options.callback(offer);
                        }

                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.deletePosition.failureTitle, me.snippets.deletePosition.failureMessage + ' ' + rawData.message, me.snippets.growlMessage);
                    }
                }
            });
        });
    },

    onUpdateDetailPage: function(offer, window) {
        var me = this;
        me.offerStore = me.getOfferListGrid().store.load();
        var billing = me.getOfferBillingPanel().filterForm.items.items;
        if(billing){
            var shopId = billing[11].getValue('shopId');
        }
        if(!shopId)
        {
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectShop);
            return;
        }
        me.positionStore = me.getStore('Position').load({
            params:{
                customerId: me.getOfferBillingPanel().customerId,
                offerId : me.getOfferPositionWindow().offerId,
                shopId: shopId
            }
        });
        me.getOfferPositionWindow().positionGrid.reconfigure(me.getOfferPositionWindow().positionStore);
       //detail = window.down('create-offer-window');
       //detail.record = offer;
       //detail.loadRecord(offer);
    },

    onBeforeEdit: function(editor, e) {
        var me = this,
            columns = editor.editor.items.items,
            articleId = e.record.get('articleId');

        columns[2].setValue(e.record.get('articleNumber'));
        columns[3].setValue(e.record.get('articleName'));

    },

    /**
     * Event will be fired when the user clicks the update button of the row editor.
     *
     * @param [Ext.grid.plugin.Editing] - The row editor
     * @param [object]  - An edit event with the following properties:
     *   grid - The grid this editor is on
     *   view - The grid view
     *   store - The grid store
     *   record - The record being edited
     *   row - The grid table row
     *   column - The grid Column defining the column that initiated the edit
     *   rowIdx - The row index that is being edited
     *   colIdx - The column index that initiated the edit
     *   cancel - Set this to true to cancel the edit or return false from your handler.
     */
    onSavePosition: function(editor, e, offer, options) {

        var me = this,
            customerId,shopId,paymentId,dispatchId,invoiceShipping,shippingTax, positions,count = 0;
        if(me.getStore('Offer').load().getById(offerId)){
            positions = me.getStore('Offer').load().getById(offerId).raw.details;
        //positions = me.offerStore.getById(offerId).raw.details;
        Ext.each(positions, function (position) {
            if(position['articleName'] == e.newValues.articleName && position['id'] != e.record.get('id')){
                count = count + 1;
            }
        });
        }
        if(count >= 1)
        {
            Ext.Msg.alert(me.snippets.failureTitle, ' Please Inseret new Article');
            return;
        }
        //to convert the float value. Without this the insert value "10,55" would be converted to "1055,00"
        e.record.set('price', e.newValues.price);

        //the article suggest search is not a form field so we have to set the value manually
        e.record.set('articleDetailsId', e.newValues.articleDetailsId);
        e.record.set('articleName', e.newValues.articleName);
        e.record.set('articleNumber', e.newValues.articleNumber);
        e.record.set('offerId', me.getOfferPositionWindow().offerId);
        e.record.set('originalPrice', e.newValues.originalPrice);
        e.record.set('currency', e.newValues.currency);
        //calculate the new total amount.
        if (Ext.isNumeric(e.newValues.price) && Ext.isNumeric(e.newValues.quantity)) {
            e.record.set('total', e.newValues.price * e.newValues.quantity);
            e.newValues.total = e.newValues.price * e.newValues.quantity;
        }

        if(me.getOfferBillingPanel().offerId !==0)
        {
            customerId = me.getOfferBillingPanel().record.get('customerId');
                shopId = me.getOfferBillingPanel().record.get('shopId');
            paymentId  = me.getOfferBillingPanel().record.get('paymentId');
            dispatchId  = me.getOfferBillingPanel().record.get('dispatchId');
            invoiceShipping  = me.getOfferBillingPanel().record.get('invoiceShipping');
            shippingTax  = me.getOfferBillingPanel().record.get('tax');
        }else{
            customerId = me.getOfferBillingPanel().customerId;
            shopId = me.getOfferBillingPanel().filterForm.items.items[11].getValue('shopId');
            paymentId  = me.getOfferBillingPanel().filterForm.items.items[12].getValue('paymentId');
            dispatchId  = me.getOfferBillingPanel().filterForm.items.items[13].getValue('dispatchId');
            invoiceShipping  = me.getOfferBillingPanel().filterForm.items.items[14].getValue('invoiceShipping');
            shippingTax  = me.getOfferBillingPanel().filterForm.items.items[15].getValue('tax');
        }
        if(customerId && shopId && paymentId){

        e.record.save({

            params:{
                customerId : customerId,
                shopId : shopId,
                paymentId : paymentId,
                dispatchId : dispatchId,
                invoiceShipping: invoiceShipping,
                shippingTax: shippingTax
            },
            callback:function (data, operation) {
                var records = operation.getRecords(),
                    record = records[0],
                    rawData = record.getProxy().getReader().rawData;

                if ( operation.success === true ) {
                      Shopware.Notification.createGrowlMessage(me.snippets.successTitle, me.snippets.positions.successMessage, me.snippets.growlMessage);
                     //offer.set('invoiceAmount', rawData.invoiceAmount);
                    me.getOfferBillingPanel().filterForm.items.items[10].setValue(rawData.invoiceAmount);
                    me.getOfferPositionWindow().offerId = rawData.offerId;
                    me.getOfferDocumentList().offerId = rawData.offerId;
                      e.record.set('id', rawData.positionId);

                    me.onSaveBillingShipping(customerId);
                    if (options !== Ext.undefined && Ext.isFunction(options.callback)) {
                        options.callback(offer);
                    }
                } else {
                   Shopware.Notification.createGrowlMessage(me.snippets.failureTitle, me.snippets.positions.failureMessage + '<br> ' + rawData.message, me.snippets.growlMessage);
                    e.store.remove(records);
                }
            }
        });
        }
        else
        {
            Ext.Msg.alert(me.snippets.failureTitle, ' Please Inseret CustomrId or ShopId or paymentId');
            return;
        }
    },

    /**
     * Event listener method which is fired when the user cancel the row editing in the position grid
     * on the detail page. If the edited record is a new position, the position will be removed.
     *
     * @param grid
     * @param eOpts
     */
    onCancelEdit: function(grid, eOpts) {
        var record = eOpts.record,
            store = eOpts.store;

        if (!(record instanceof Ext.data.Model) || !(store instanceof Ext.data.Store)) {
            return;
        }
        if (record.get('id') === 0) {
            store.remove(record);
        }
    },

    /**
     * Event will be fired when the user change quantity in the row editor
     * @param editor
     * @param value
     */
    onQuantityChange: function(editor, value) {
        var me = this,
            shopId,customerId,
            columns = editor.editor.items.items,
            updateButton = editor.editor.floatingButtons.items.items[0];

        //if(columns[15].getValue()=='1'){
            if(me.getOfferBillingPanel().record.get('shopId') && me.getOfferBillingPanel().record.get('customerId')){
                shopId = me.getOfferBillingPanel().record.get('shopId');
            }
            else if(me.getOfferBillingPanel().filterForm.items.items[11].getValue('shopId')){
                shopId = me.getOfferBillingPanel().filterForm.items.items[11].getValue('shopId');
            }
            else
            {
                Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectShop);
                return;
            }

            if(me.getOfferBillingPanel().record.get('shopId') && me.getOfferBillingPanel().record.get('customerId')){
                customerId = me.getOfferBillingPanel().record.get('customerId');
            }
            else if(me.getOfferBillingPanel().customerId){
                customerId = me.getOfferBillingPanel().customerId;
            }
            else{
                Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectCustomer);
                return;
            }

            updateButton.setDisabled(false);

            Ext.Ajax.request({
                url: '{url controller=sKUZOOffer action=getArticlePrice}',
                method: 'POST',
                params: {
                    articleDetailsId: columns[1].getValue(),
                    shopId: shopId,
                    customerId: customerId,
                    quantity:value,
                },
                success: function(response, operation) {
                    var data = Ext.JSON.decode(response.responseText);
                    var price = data.articlePrice,
                        discount = columns[12].getValue();

                    if (data.priceByQuantity == true){
                        var element = Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner')[16].lastChild;
                        var className = element.className.replace('x-hidden', '');
                        Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner')[16].lastChild.className = className;
                    }
                    columns[5].setValue(Math.round(data.purchasePrice * 100) / 100);
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[5].innerHTML = Math.round(data.purchasePrice * 100) / 100;

                    columns[6].setValue(Math.round(data.originalNetPrice * 100) / 100)
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[6].innerHTML = Math.round(data.originalNetPrice * 100) / 100;

                    var disc = (columns[7].getValue() - columns[8].getValue()) * columns[4].getValue();
                    disc = Math.round(disc * 100) / 100
                    columns[10].setValue(disc);
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[10].innerHTML = disc;

                    var new_total = columns[8].getValue() * columns[4].getValue()
                    new_total = Math.round(new_total * 100) / 100
                    columns[9].setValue(new_total);
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[9].innerHTML = new_total;

                    if (columns[7].getValue() != data.articlePrice){


                        columns[7].setValue(Math.round(data.articlePrice * 100) / 100);
                        Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[7].innerHTML = Math.round(data.articlePrice * 100) / 100;

                        var new_price = price - (price * discount) / 100;
                        new_price = Math.round(new_price * 100) / 100
                        columns[8].setValue(new_price);

                        var article_price = Math.round(data.articlePrice * 100) / 100;
                        var new_discount = (article_price - new_price) * columns[4].getValue();
                        new_discount = Math.round(new_discount * 100) / 100
                        columns[10].setValue(new_discount);
                        Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[10].innerHTML = new_discount;

                        var new_total = new_price * columns[4].getValue()
                        new_total = Math.round(new_total * 100) / 100
                        columns[9].setValue(new_total);
                        Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[9].innerHTML = new_total;
                    }

                    columns[15].setValue(data.scalePrice);
                }
            });
        //}
    },


    /**
     * Event will be fired when the user search for an article number in the row editor
     * and selects an article in the drop down menu.
     *
     * @param [object] editor - Ext.grid.plugin.RowEditing
     * @param [string] value - Value of the Ext.form.field.Trigger
     * @param [object] record - Selected record
     */
    onArticleSelect: function(editor, value, record) {
        var me = this,
            shopId,customerId,
            columns = editor.editor.items.items,
            updateButton = editor.editor.floatingButtons.items.items[0];

        if(me.getOfferBillingPanel().record.get('shopId') && me.getOfferBillingPanel().record.get('customerId')){
            shopId = me.getOfferBillingPanel().record.get('shopId');
        }
        else if(me.getOfferBillingPanel().filterForm.items.items[11].getValue('shopId')){
            shopId = me.getOfferBillingPanel().filterForm.items.items[11].getValue('shopId');
        }
        else
        {
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectShop);
            return;
        }

        if(me.getOfferBillingPanel().record.get('shopId') && me.getOfferBillingPanel().record.get('customerId')){
            customerId = me.getOfferBillingPanel().record.get('customerId');
        }
        else if(me.getOfferBillingPanel().customerId){
            customerId = me.getOfferBillingPanel().customerId;
        }
        else{
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectCustomer);
            return;
        }

        updateButton.setDisabled(false);
        columns[1].setValue(record.get('id'));
        columns[2].setValue(record.get('number'));
        columns[3].setValue(record.get('name'));
        columns[14].setValue(record.get('inStock'));
        Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[14].innerHTML = record.get('inStock');

        Ext.Ajax.request({
            url: '{url controller=sKUZOOffer action=getArticlePrice}',
            method: 'POST',
            params: {
                articleDetailsId: record.get('id'),
                articleId:record.get('articleId'),
                shopId: shopId,
                customerId: customerId
            },
            success: function(response, operation) {
                var data = Ext.JSON.decode(response.responseText);

                columns[4].setValue(data.minPurchase);

                columns[5].setValue(data.purchasePrice);
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[5].innerHTML= data.purchasePrice;

                columns[6].setValue(data.originalNetPrice)
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[6].innerHTML = data.originalNetPrice;

                columns[7].setValue(data.articlePrice);
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[7].innerHTML = data.articlePrice;

                columns[8].setValue(data.articlePrice);

                columns[11].setValue(data.currency);
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[11].innerHTML = data.currency;

                columns[9].setValue(data.articlePrice * data.minPurchase);
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[9].innerHTML = data.articlePrice * data.minPurchase;

                columns[13].setValue(data.articleTax);

                columns[15].setValue(data.scalePrice);

            }
        });
    },


    onSaveOffer: function(editor, event, store) {
        var me = this,
            record, rawData,
            record = store.getAt(event.rowIdx);
        if(record == null) {
            return;
        }

        record.save({
            callback:function (data, operation) {
                var records = operation.getRecords(),
                    record = records[0],
                    proxyReader = record.getProxy().getReader(),
                    rawData = proxyReader.rawData;

                if ( operation.success === true ) {
                    record.set('discountAmount', rawData.data.discountAmount);

                } else {
                    Ext.Msg.alert('Failure', ' Record is not updated successfully');
                }
            }
        });

    },

    onDeleteMultiplePositions: function(offer, grid, options) {
           var me = this, offerId,
            store = grid.getStore(),
            selectionModel = grid.getSelectionModel(),
            positions = selectionModel.getSelection(),
            message =  Ext.String.format(me.snippets.delete.message, positions.length);

        if (positions.length === 0) {
            return;
        } else {
            offerId = positions[0].get('offerId');
        }

        // we do not just delete - we are polite and ask the user if he is sure.
        Ext.MessageBox.confirm(me.snippets.delete.title, message, function (response) {
            if ( response !== 'yes' ) {
                return;
            }
            store.remove(positions);
            store.getProxy().extraParams = {
                offerId: offerId
            };
            store.sync({
                callback:function (batch, operation) {
                    var rawData = batch.proxy.getReader().rawData;

                    if ( rawData.success === true ) {
                        Shopware.Notification.createGrowlMessage(me.snippets.successTitle, me.snippets.delete.successMessage, me.snippets.growlMessage);

                        offer.set('invoiceAmount', rawData.data.invoiceAmount);
                        if (options !== Ext.undefined && Ext.isFunction(options.callback)) {
                            options.callback(offer);
                        }

                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.failureTitle, me.snippets.delete.failureMessage + '<br> ' + rawData.message, me.snippets.growlMessage)
                    }
                }
            });
        });
    },

    onDeleteOffer:function (record) {
        var me = this,
            store = me.subApplication.getStore('Offer'),
            message = me.snippets.deleteOffer.message + ' ' + record.get('number'),
            title = me.snippets.deleteOffer.title;

        // we do not just delete - we are polite and ask the user if he is sure.
        Ext.MessageBox.confirm(title, message, function (response) {
            if ( response !== 'yes' ) {
                return;
            }
            record.destroy({
                callback: function(data, operation) {
                    var records = operation.getRecords(),
                        record = records[0],
                        rawData = record.getProxy().getReader().rawData;

                    if ( operation.success === true ) {
                        Shopware.Notification.createGrowlMessage(me.snippets.deleteOffer.successTitle,me.snippets.deleteOffer.successMessage, me.snippets.growlMessage);
                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.deleteOffer.failureTitle, me.snippets.deleteOffer.failureMessage + ' ' + rawData.message, me.snippets.growlMessage);
                    }
                    store.load();
                }
            });
        });
    },

    onDeleteMultipleOffers: function() {

        var me = this,
            store = me.getOfferListGrid().getStore(),
            selectionModel = me.getOfferListGrid().getSelectionModel(),
            records = selectionModel.getSelection(),
            message =  Ext.String.format(me.snippets.delete.message, records.length);


        // we do not just delete - we are polite and ask the user if he is sure.
        Ext.MessageBox.confirm(me.snippets.delete.title, message, function (response) {
            if ( response !== 'yes' ) {
                return;
            }
            records.forEach(function(record) {
            record.destroy({
                callback: function(data, operation) {
                    var records = operation.getRecords(),
                        record = records[0],
                        rawData = record.getProxy().getReader().rawData;

                    if ( operation.success === true ) {
                        Shopware.Notification.createGrowlMessage(me.snippets.deleteOffer.successTitle,me.snippets.deleteOffer.successMessage, me.snippets.growlMessage);
                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.deleteOffer.failureTitle, me.snippets.deleteOffer.failureMessage + ' ' + rawData.message, me.snippets.growlMessage);
                    }
                    store.load();
                }
            });
            });
        });
    },

    onSelectionChange: function(selectionModel, selected, eOpts) {
        var me = this,
            positionGrid = me.getOfferPositionGrid(),
            positionStore = Ext.create('Ext.data.Store', { model: 'Shopware.apps.sKUZOOffer.model.Position' }),
            record = null;

        if (Ext.isArray(selected)) {
            record = selected[selected.length-1];
        } else {
            record = selected;
        }

        if (record instanceof Ext.data.Model && record.getPositions() instanceof Ext.data.Store) {
            positionStore = record.getPositions();
        }
        positionGrid.reconfigure(positionStore);
    },

    onOpenCustomer: function(record) {
        var me=this;
        var customerId = 1;
        if (record && record.get('customerId')) {
            customerId = record.get('customerId');
       }
        Shopware.app.Application.addSubApplication({
            name: 'Shopware.apps.Customer',
            action: 'detail',
            params: {
                customerId: customerId
            }
        });

    },

    onOpenOrderDetail: function(record){

        var me=this;
        if (record && record.get('orderId')) {
            Shopware.app.Application.addSubApplication({
                name: 'Shopware.apps.Order',
                params: {
                    orderId:record.get('orderId')
                }
            });
        }
    },


    onOpenArticle: function(record) {
        Shopware.app.Application.addSubApplication({
            name: 'Shopware.apps.Article',
            action: 'detail',
            params: {
                articleId: record.get('articleId')
            }
        });
    },

    onCreateDocument: function(offer, config, panel) {
        var store = Ext.create('Shopware.apps.sKUZOOffer.store.Configuration');
        var docFormat = '0',
            me = this;
         panel.setLoading(true);
        if(me.getOfferBillingPanel().filterForm.items.items[16].value){
            docFormat = '1';
        }

        config.set('deliveryDate',new Date());
        config.set('displayDate', new Date());
        config.set('documentType', 0);
        config.set('offerId', me.getOfferPositionWindow().offerId);
        config.set('documentFormat', docFormat);
        config.set('mediaId', null);

        store.add(config);
        store.sync({
            callback: function(batch, operation) {
                var rawData = batch.proxy.getReader().rawData;

                panel.setLoading(false);

                if ( rawData.success === true ) {
			Shopware.Notification.createGrowlMessage(me.snippets.document.successTitle, 			rawData.message, me.snippets.growlMessage);
                    var data = rawData.data[0];
                    offer.set(data);

                        var documentStore = me.getOfferDocumentList().store;
                        var documents = offer.get('documents');

                    documentStore.removeAll();
                    Ext.each(documents, function(modelData){
                        var model = Ext.create('Shopware.apps.sKUZOOffer.model.Receipt', modelData),
                            typeModel = Ext.create('Shopware.apps.Base.model.DocType', modelData.type);

                        var typeStore = model.getDocType();
                        typeStore.add(typeModel);
                        model['getDocTypeStore'] = typeStore;
                        documentStore.add(model);

                   });

                    var offerStore = me.subApplication.getStore('Offer');
                    offerStore.load();
                }else{	
			
        	        Shopware.Notification.createGrowlMessage(me.snippets.document.failureTitle, rawData.message , me.snippets.growlMessage);
		}
            }
        });
    },

    /**
     * Creates the batch window with a special mode, so only the mail panel will be displayed.
     *
     * @param mail
     */
    showOfferMail: function(mail) {
        var me = this;

        //open the offer listing window
        me.mailWindow = me.getView('mails.Window').create({
            mail: mail
        }).show();
    },

    sendDocumentByMail:function(record) {

        var me = this;
        var id = record.get('id'),
            documentId = record.get('documentId'),
            customerId = record.get('customerId'),
            emailPreview = me.getOfferBillingPanel().filterForm.items.items[17].getValue();
        if(emailPreview){
            Ext.Ajax.request({
                url: '{url controller="sKUZOOffer" action="getMailDetails"}',
                method: 'POST',
                params: {
                    id: id,
                    documentId: documentId,
                    customerId: customerId,
                    offerId: me.getOfferPositionWindow().offerId
                },
                success: function(response) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    if(responsJSON.success == true) {
                        var mail = Ext.create('Shopware.apps.sKUZOOffer.model.Mail', responsJSON.data.mail);
                        me.showOfferMail(mail);
                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.sendMail.failureTitle, me.snippets.sendMail.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                    }
                },
                failure: function(response) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    Shopware.Notification.createGrowlMessage(me.snippets.sendMail.failureTitle, me.snippets.sendMail.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
                }
            });
        }else{
            Ext.getBody().mask(me.snippets.sendMail.maskText);

            Ext.override(Ext.data.proxy.Ajax, { timeout: 120000 });
            Ext.override(Ext.form.action.Action, { timeout: 120 });
            Ext.override(Ext.data.Connection, { timeout: 120000 });
            Ext.Ajax.timeout = 120000;

            Ext.Ajax.request({
                url: '{url controller="sKUZOOffer" action="sendDocumentByMail"}',
                method: 'POST',
                params: {
                    id: id,
                    documentId: documentId,
                    customerId: customerId,
                    offerId: me.getOfferPositionWindow().offerId
                },
                success: function(response) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    if(responsJSON.success == true) {
                        Shopware.Notification.createGrowlMessage(me.snippets.sendMail.successTitle, me.snippets.sendMail.successMessage, me.snippets.growlMessage);
                        var store = me.subApplication.getStore('Offer');
                        store.load();
                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.sendMail.failureTitle, me.snippets.sendMail.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                    }
                },
                failure: function(response) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    Shopware.Notification.createGrowlMessage(me.snippets.sendMail.failureTitle, me.snippets.sendMail.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
                }
            });
        }
    },

    onSaveBillingShipping: function() {
        var me = this;
        var billing = me.getOfferBillingPanel().filterForm.items.items;
        var shipping = me.getOfferShippingPanel().shippingForm.items.items;
        if(!me.getOfferBillingPanel().customerId){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectCustomer);
            return;
        }
        if(!billing[11].getValue('shopId')){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectShop);
            return;
        }
        if(!billing[12].getValue('paymentId')){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectPayment);
            return;
        }
        if(!billing[13].getValue('dispatchId')){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectDispatch);
            return;
        }
        Ext.Ajax.request({
            url: '{url controller="sKUZOOffer" action="saveOfferBilling"}',
            method: 'POST',
            params: {
                firstName: billing[1].getValue('firstName'),
                lastName: billing[2].getValue('lastName'),
                number: billing[3].getValue('number'),
                email: billing[4].getValue('email'),
                company: billing[5].getValue('company'),
                street: billing[6].getValue('street'),
                zipCode:  billing[7].getValue('zipCode'),
                city: billing[8].getValue('city'),
                countryId: billing[9].getValue('countryId'),
                shopId:  billing[11].getValue('shopId'),
                paymentId: billing[12].getValue('paymentId'),
                dispatchId: billing[13].getValue('dispatchId'),
                invoiceShipping: billing[14].getValue('invoiceShipping'),
                shippingTax: billing[15].getValue('tax'),
                customerId: me.getOfferBillingPanel().customerId,
                offerId: me.getOfferPositionWindow().offerId,
                sfirstName: shipping[0].getValue('firstName'),
                slastName: shipping[1].getValue('lastName'),
                scompany: shipping[2].getValue('company'),
                sstreet: shipping[3].getValue('street'),
                szipCode:  shipping[4].getValue('zipCode'),
                scity: shipping[5].getValue('city'),
                scountryId: shipping[6].getValue('countryId')
            },
            success: function(response) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                if(responsJSON.success == true) {
                    me.getOfferPositionWindow().offerId = responsJSON.data;
                    Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.successTitle, me.snippets.saveOffer.successMessage, me.snippets.growlMessage);
                    var store = me.subApplication.getStore('Offer');
                    store.load();
                    me.getOfferPositionWindow().offerPositionGrid.setDisabled(false);
                    me.getOfferBillingPanel().filterForm.items.items[10].setValue(responsJSON.discountAmount)
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.failureTitle, me.snippets.saveOffer.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                    me.getOfferPositionWindow().offerId = responsJSON.data;
                    var store = me.subApplication.getStore('Offer');
                    store.load();
                    me.getOfferBillingPanel().filterForm.items.items[10].setValue(responsJSON.discountAmount)
                }
            },
            failure: function(response, request) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.failureTitle, me.snippets.saveOffer.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
                me.getOfferPositionWindow().offerId = responsJSON.data;
                var store = me.subApplication.getStore('Offer');
                store.load();
            }
        });
    },

    /**
     * Event listener method which is fired when the user insert a search string
     * into the search field which displayed on top of the offer list.
     * @param value
     */
    onSearchOffers: function(value) {
        var me = this,
            store = me.subApplication.getStore('Offer');

        if (store.filters.containsKey('free')) {
            store.filters.removeAtKey('free');
        }
        store.filters.add('free', Ext.create('Ext.util.Filter', { property: 'free', value: Ext.String.trim(value) }));

        //scroll the store to first page
        store.currentPage = 1;

        store.filter();
    },

    /**
     * Event listener method which is fired when the user wants to save the internal comment
     * which can be edit in the communication tab panel on the detail page.
     * @return void
     */
    onSaveInternalComment: function(record) {
        var me = this;

        me.saveOffer(record, me.snippets.internalComment.successMessage, me.snippets.internalComment.failureMessage, {
            'action':'saveInternalComment'
        });
    },
    onSaveExternalComment: function(record) {
        var me = this;

        me.saveOffer(record, me.snippets.externalComment.successMessage, me.snippets.externalComment.failureMessage, {
            'action':'saveExternalComment'
        });
    },

    saveOffer: function(offer, successMessage, errorMessage, options) {
        var me = this;
        offer.save({
            callback:function (data, operation) {
                var records = operation.getRecords(),
                    record = records[0],
                    rawData = record.getProxy().getReader().rawData;

                if ( operation.success === true ) {
                    Shopware.Notification.createGrowlMessage(me.snippets.successTitle, successMessage, me.snippets.growlMessage);
                    offer.set('discountAmount', rawData.data.discountAmount);

                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.failureTitle, errorMessage , me.snippets.growlMessage)
                }
                // reload the order list
                //me.offerStore.load();
            },
            params: options
        });
    },

    makeDiscountWindow: function(record, grid,discountValue) {
        var me = this,
            selectionModel = grid.getSelectionModel(),
            positions = selectionModel.getSelection();


        if (positions.length <= 0) return;
        Ext.each(positions, function(position) {
            Ext.Ajax.request({
                url: '{url controller=sKUZOOffer action="makeDiscount"}',
                method: 'POST',
                params: {
                    positionId: position.get('id'),
                    offerId: position.get('offerId'),
                    originalPrice: position.get('originalPrice'),
                    discount: discountValue
                },
                success: function (response) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    if(responsJSON.success == true) {
                        var store = me.subApplication.getStore('Offer');
                        store.load();
                        var billing = me.getOfferBillingPanel().filterForm.items.items;
                        me.positionStore = me.getStore('Position').load({
                            params:{
                                customerId: me.getOfferBillingPanel().customerId,
                                offerId : me.getOfferPositionWindow().offerId,
                                shopId: billing[11].getValue('shopId')
                            }
                        });
                        me.getOfferPositionWindow().positionGrid.reconfigure(me.getOfferPositionWindow().positionStore);
                        Shopware.Notification.createGrowlMessage(me.snippets.makeDiscount.successTitle, me.snippets.makeDiscount.successMessage, me.snippets.growlMessage);
                    } else {
                        Shopware.Notification.createGrowlMessage(me.snippets.makeDiscount.failureTitle, me.snippets.makeDiscount.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                    }
                },
                failure: function(response, request) {
                    var responsJSON = JSON.parse(response.responseText);
                    Ext.getBody().unmask();
                    Shopware.Notification.createGrowlMessage(me.snippets.makeDiscount.failureTitle, me.snippets.makeDiscount.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
                }
            });
        })
        /*var billing = me.getOfferBillingPanel().filterForm.items.items;
        me.positionStore = me.getStore('Position').load({
            params:{
                offerId : me.getOfferPositionWindow().offerId,
                shopId: billing[10].getValue('shopId')
            }
        });*/


       /* Ext.create('Shopware.window.Progress', {
            title: '{s name=progressWindow/makeDiscount/title}making Discount Window{/s}',
            cancelProcess: true,
            configure: function() {
                return {
                    tasks: [{
                        event: 'make-discount-process',
                        data: Ext.clone(positions),
                        text: '{s name=progressWindow/makeDiscount/text}Discounting{/s}'+' [0] of [1]'
                    }],

                    infoText: '<h2>'+'{s name=progressWindow/makeDiscount/infoText}This process may take several minutes to complete.{/s}'+'</h2>'
                }
            }
            }
        }).show();*/

    },

    onRefreshPositions: function(shopId){
        var me = this;
        me.positionStore = me.getStore('Position').load({
            params:{
                customerId: me.getOfferBillingPanel().customerId,
                offerId : me.getOfferPositionWindow().offerId,
                shopId: shopId
            }
        });
        me.getOfferPositionWindow().positionGrid.reconfigure(me.getOfferPositionWindow().positionStore);
    }


});
