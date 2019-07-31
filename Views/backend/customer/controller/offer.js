/**
 *
 */

Ext.define('Shopware.apps.Customer.controller.Offer', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.Customer.controller.List',

    /**
     * List of classes that have to be loaded before instantiating this class.
     * @array
     */
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
        sendMail: {
            maskText:'{s name=message/send_mail/mask_text}E-Mail senden ...{/s}',
            successTitle: '{s name=message/send_mail/success_title}Successful{/s}',
            successMessage: '{s name=message/send_mail/success_message}E-Mail successfully send.{/s}',
            failureTitle: '{s name=message/send_mail/failure_title}Error{/s}',
            failureMessage: '{s name=message/send_mail/failure_message}E-Mail could not be send:{/s}'
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
        growlMessage: '{s name=growlMessage}Offer{/s}',
            singleDeleteTitle:'{s name=message/delete_single_title}Delete selected customer{/s}',
            singleDeleteMessage:'{s name=message/delete_single_content}Are you sure, you want to delete the selected customer: {/s}',
            multipleDeleteTitle:'{s name=message/delete_multiple_title}Delete selected customers{/s}',
            multipleDeleteMessage:'{s name=message/delete_multiple_content}There were marked [0] customers. Are you sure you want to delete all selected customers?{/s}',
            deleteSuccessTitle:'{s name=message/delete_success_message}Successfully{/s}',
            deleteSuccessMessage:'{s name=message/delete_success_title}Customer(s) has been removed{/s}',
            deleteErrorTitle:'{s name=message/delete_error_title}Failure{/s}',
            deleteErrorMessage:'{s name=message/delete_error_message}During deleting an error has occurred:{/s}'
    },

    requires: [ 'Shopware.apps.Customer.controller.List' ],

    refs:[
        { ref:'offerPositionGrid', selector:'offer-list-main-window offer-position-grid' },
        { ref:'offerBillingPanel', selector:'offer-listing-billing-panel' },
        { ref:'offerShippingPanel', selector:'offer-listing-shipping-panel' },
        { ref:'offerDocumentList', selector:'offer-document-list' },
        { ref:'offerPositionWindow', selector:'offer-position-panel' },
        { ref:'offerAddressSidebar', selector:'offer-address-sidebar' }
    ],
    init:function () {
        var me = this;
        me.control({
            'customer-list': {
                createOffer: me.onCreateOffer
            },
            'offer-position-grid': {
                openArticle: me.onOpenArticle,
                deletePosition: me.onDeletePosition,
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
                quantityChange: me.onQuantityChange
            },
            'offer-address-sidebar': {
                createDocument: me.onCreateDocument,
                saveOfferBilling: me.onSaveBillingShipping
            },
            'offer-document-list':{
                sendDocumentByMail: me.sendDocumentByMail
            },
            'customer-offer-list': {
                editOffer: me.onEditOffer,
                saveOrder: me.onSaveOrder,
                openOrderDetail: me.onOpenOrderDetail,
                searchOffers: me.onSearchOffers
            },
            'customer-detail-window datefield[name=fromOfferDate]':{
                change:me.onChangeFromDate
            },
            'customer-detail-window datefield[name=toOfferDate]':{
                change:me.onChangeToDate
            }
        });
        me.callParent(arguments);
    },

    /**
     * @param value
     * @return void
     */
    onChangeToDate:function (field, value) {
        var me = this;
        if ( Ext.typeOf(value) != 'date' ) {
            return;
        }

        var chart = field.up('window').down('customer-list-offer-chart'),
            store = chart.store;

        store.getProxy().extraParams = {
            customerID:store.getProxy().extraParams.customerID,
            fromDate:store.getProxy().extraParams.fromDate,
            toDate: me.getFormattedDate(value)
        };
        store.load();
    },

    /**
     * @param [Ext.form.Field.Date] - The date field which changed
     * @param [Ext.Date] - The new value
     * @return void
     */
    onChangeFromDate:function (field, value) {
        var me = this;
        if ( Ext.typeOf(value) != 'date' ) {
            return;
        }

        var chart = field.up('window').down('customer-list-offer-chart'),
            store = chart.store;

        store.getProxy().extraParams = {
            customerID:store.getProxy().extraParams.customerID,
            toDate:store.getProxy().extraParams.toDate,
            fromDate: me.getFormattedDate(value)
        };
        store.load();
    },

    /**
     * @public
     * @param [date] value - Date object
     * @param [string] sep - separator symbol
     * @return [string] formatted string
     */
    getFormattedDate: function(value, sep) {
        var month = value.getMonth(),
            year = value.getFullYear(),
            day = value.getDate();

        sep = sep || '-';
        return year + sep + (month + 1) + sep + day;
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
            me.createOfferWindow = me.getView('Shopware.apps.sKUZOOffer.view.list.Window').create({
                offerId: offerId,
                record: record,
                taxStore: me.getStore('Shopware.apps.sKUZOOffer.store.Tax').load(),
                {if $swVersion4}variantStore: me.getStore('Shopware.apps.sKUZOOffer.store.Variant').load(),{/if}
                positionStore: me.getStore('Shopware.apps.sKUZOOffer.model.Position')
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

    /**
     * Event listener method which is fired when the user insert a search string
     * into the search field which displayed on top of the offer list.
     * @param value
     */
    onSearchOffers: function(value,store) {
        var me = this,
            searchString = Ext.String.trim(value)

        //scroll the store to first page
        store.currentPage = 1;

        //If the search-value is empty, reset the filter
        if ( searchString.length === 0 ) {
            store.clearFilter();
        } else {
            //This won't reload the store
            store.filters.clear();
            //Loads the store with a special filter
            store.filter('free', searchString);
        }

        return true;
    },



    onCreateOffer: function(customerRecord) {
        var me = this;
        var record = Ext.create('Shopware.apps.sKUZOOffer.model.Offer');//grid.getSelectionModel().getSelection();

        record.set('customerId',customerRecord.get('id'));
        offerId = 0;
        //open the order listing window
        me.createOfferWindow = Ext.create('Shopware.apps.sKUZOOffer.view.list.Window',{
            taxStore:  me.getStore('Shopware.apps.sKUZOOffer.store.Tax').load(),
            {if $swVersion4}variantStore: me.getStore('Shopware.apps.sKUZOOffer.store.Variant').load(),{/if}
            positionStore: me.getStore('Shopware.apps.sKUZOOffer.store.Position'),
            offerId: offerId,
            record: record,
            customerRecord: customerRecord,
            mode: 'multi'
        });

        me.getOfferPositionWindow().offerPositionGrid.setDisabled(true);
        me.createOfferWindow.show();
    },

    onNewPosition: function(record, grid, editor) {
        var me = this;
        editor.cancelEdit();
        var position = Ext.create('Shopware.apps.sKUZOOffer.model.Position', {
            offerId: me.getOfferPositionWindow().offerId,
            quantity:1,
            scalePrice:1
        });

        grid.getStore().add(position);
        editor.startEdit(position, 0);
    },

    onUpdateDetailPage: function(offer, window) {
        var me = this;

        me.offerStore = me.getStore('Shopware.apps.sKUZOOffer.store.Offer').load();

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
        if(me.getStore('Shopware.apps.sKUZOOffer.store.Offer').load().getById(offerId)){
            positions = me.getStore('Shopware.apps.sKUZOOffer.store.Offer').load().getById(offerId).raw.details;
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
            shopId = me.getOfferBillingPanel().filterForm.items.items[10].getValue('shopId');
            paymentId  = me.getOfferBillingPanel().filterForm.items.items[11].getValue('paymentId');
            dispatchId  = me.getOfferBillingPanel().filterForm.items.items[12].getValue('dispatchId');
            invoiceShipping  = me.getOfferBillingPanel().filterForm.items.items[13].getValue('invoiceShipping');
            shippingTax  = me.getOfferBillingPanel().filterForm.items.items[14].getValue('tax');
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
                        me.getOfferBillingPanel().filterForm.items.items[9].setValue(rawData.invoiceAmount);
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
        else if(me.getOfferBillingPanel().filterForm.items.items[10].getValue('shopId')){
            shopId = me.getOfferBillingPanel().filterForm.items.items[10].getValue('shopId');
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

                columns[7].setValue(data.articlePrice);
                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[7].innerHTML = data.articlePrice;

                columns[8].setValue(data.articlePrice);

                columns[13].setValue(data.articleTax);

                columns[15].setValue(data.scalePrice);
            }
        });
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

        if(columns[15].getValue()=='1'){
            if(me.getOfferBillingPanel().record.get('shopId') && me.getOfferBillingPanel().record.get('customerId')){
                shopId = me.getOfferBillingPanel().record.get('shopId');
            }
            else if(me.getOfferBillingPanel().filterForm.items.items[10].getValue('shopId')){
                shopId = me.getOfferBillingPanel().filterForm.items.items[10].getValue('shopId');
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
                        discount = columns[10].getValue();
                    columns[7].setValue(price);
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[7].innerHTML = price;

                    columns[8].setValue(price - (price * discount) / 100);

                    columns[10].setValue(discount);
                    Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[10].innerHTML = discount;

                    columns[15].setValue(data.scalePrice);
                }
            });
        }
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
        if(me.getOfferBillingPanel().filterForm.items.items[15].value){
            docFormat = '1';
        }

        config.set('deliveryDate',new Date());
        config.set('displayDate', new Date());
        config.set('documentType', 0);
        config.set('offerId', me.getOfferPositionWindow().offerId);
        config.set('documentFormat', docFormat)

        store.add(config);
        store.sync({
            callback: function(batch, operation) {
                var rawData = batch.proxy.getReader().rawData;

                panel.setLoading(false);

                if ( rawData.success === true ) {

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
                    var offerStore = me.subApplication.getStore('Shopware.apps.sKUZOOffer.store.Offer');
                    offerStore.load();
                }
            }
        });
    },

    sendDocumentByMail:function(record) {
        var me = this,
            id = record.get('id'),
            documentId = record.get('documentId'),
            customerId = record.get('customerId');
        //offerId = offerId; //record.get('offerId');

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
    },

    onSaveBillingShipping: function() {
        var me = this;
        var billing = me.getOfferBillingPanel().filterForm.items.items;
        var shipping = me.getOfferShippingPanel().shippingForm.items.items;
        if(!me.getOfferBillingPanel().customerId){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectCustomer);
            return;
        }
        if(!billing[10].getValue('shopId')){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectShop);
            return;
        }
        if(!billing[11].getValue('paymentId')){
            Ext.Msg.alert(me.snippets.failureTitle, me.snippets.failure.selectPayment);
            return;
        }
        if(!billing[12].getValue('dispatchId')){
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
                shopId:  billing[10].getValue('shopId'),
                paymentId: billing[11].getValue('paymentId'),
                dispatchId: billing[12].getValue('dispatchId'),
                invoiceShipping: billing[13].getValue('invoiceShipping'),
                shippingTax: billing[14].getValue('tax'),
                customerId: me.getOfferBillingPanel().customerId,
                offerId: me.getOfferPositionWindow().offerId,
                sfirstName: shipping[0].getValue('firstName'),
                slastName: shipping[1].getValue('lastName'),
                scompany: shipping[2].getValue('company'),
                sstreet: shipping[3].getValue('street'),
                szipCode:  shipping[4].getValue('zipCode'),
                scity: shipping[5].getValue('city')
            },
            success: function(response) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                if(responsJSON.success == true) {
                    me.getOfferPositionWindow().offerId = responsJSON.data;
                    Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.successTitle, me.snippets.saveOffer.successMessage, me.snippets.growlMessage);
                    me.getOfferPositionWindow().offerPositionGrid.setDisabled(false);
                } else {
                    Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.failureTitle, me.snippets.saveOffer.failureMessage + " - " + responsJSON.message, me.snippets.growlMessage);
                    me.getOfferPositionWindow().offerId = responsJSON.data;
                }
            },
            failure: function(response, request) {
                var responsJSON = JSON.parse(response.responseText);
                Ext.getBody().unmask();
                Shopware.Notification.createGrowlMessage(me.snippets.saveOffer.failureTitle, me.snippets.saveOffer.failureMessage + " " + responsJSON.message, me.snippets.growlMessage);
                me.getOfferPositionWindow().offerId = responsJSON.data;
            }
        });
    }
});

