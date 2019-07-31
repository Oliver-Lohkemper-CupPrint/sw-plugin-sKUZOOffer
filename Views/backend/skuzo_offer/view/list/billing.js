/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Billing', {
extend:'Ext.panel.Panel',
alias:'widget.offer-listing-billing-panel',
autoScroll:true,
    snippets:{
        title:'{s name=filter/title}Billing Address{/s}',
        clientNumber: '{s name=filter/clientNumber}Client Number{/s}',
        empty: '{s name=filter/empty}Display all{/s}',
        customerField: '{s name=filter/customerField}Customer{/s}',
        firstName: '{s name=filter/firstName}FirstName{/s}',
        lastName: '{s name=filter/lastName}LastName{/s}',
        number: '{s name=filter/number}Number{/s}',
        email: '{s name=filter/email}E-mail{/s}',
        zip: '{s name=filter/zip}Zip{/s}',
        city: '{s name=filter/city}City{/s}',
        country: '{s name=filter/country}Land{/s}',
        company: '{s name=filter/company}Company{/s}',
        street: '{s name=filter/street}Street{/s}',
        streetNumber: '{s name=filter/streetNumber}StreetNumber{/s}',
        amount: '{s name=filter/amount}Amount{/s}',
        shop: '{s name=filter/shop}Shop{/s}',
        payment: '{s name=filter/payment}Payment{/s}',
        dispatch: '{s name=filter/dispatch}Dispatch{/s}',
        shippingCost: '{s name=filter/shippingCost}Shipping{/s}',
        shippingTax: '{s name=filter/shippingTax}Tax{/s}',
        documentFormat: '{s name=filter/documentFormat}Create DiscountDoc{/s}',
        errorCustomer:'{s name=filter/error/customer}Problem loading customerStore{/s}',
        emailPreview: '{s name=filter/emailPreview}Email Preview{/s}',
        emptyTax: '{s name=filter/emptyTax}Select Tax{/s}',
    },


    initComponent:function () {
        var me = this;
        me.items = [ me.createFieldContainer()];

        if(me.record)
        {
            if(me.record.get('customerId') !==0){
                me.customerId = me.record.get('customerId');
                me.getCustomerDetails()
            }
        }
        me.title = me.snippets.title;
        me.callParent(arguments);
    },

    createFieldContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            border: false,
            padding: 10,
            style: {
                background: '#f0f2f4'
            },
            items: [
                me.createFilterForm(),
            ]
        });
    },


    createFilterForm: function() {
        var me = this;
        me.filterForm = Ext.create('Ext.form.Panel', {
            border: false,
            cls: Ext.baseCSSPrefix + 'filter-form',
            defaults:{
                anchor:'98%',
                labelWidth:120,
                minWidth:250,
                style: 'box-shadow: none;',
                labelStyle: 'font-weight:700;'

            },
            items: [
                me.createCustomerField(),
                me.createFistNameField(),
                me.createLastNameField(),
                me.createNumberField(),
                me.createEmailField(),
                me.createCompanyField(),
                me.createStreetField(),
                me.createZipField(),
                me.createCityField(),
                me.createCountryField(),
                me.createAmountField(),
                me.createShopField(),
                me.createPaymentField(),
                me.createDispatchField(),
                me.createShippingCostField(),
                me.createShippingTaxField(),
                me.createDocumentFormatField(),
                me.createEmailPreviewField()

        ]
        });
        return me.filterForm;
    },

    createCustomerField: function() {
        var me = this;

        me.combo = Ext.create('Ext.form.field.ComboBox', {
            name: 'customerField',
            enableKeyEvents:true,
            store: Ext.create('Shopware.apps.sKUZOOffer.store.Customer', { pageSize: 7 }),
            valueField: 'customerId',
            pageSize: 7,
            queryMode: 'remote',
            displayField: 'customerField',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.customerField,
            validSelected: false,
            oldValue: '',
            renderTo: Ext.getBody(),
            listeners: {
                change: function(field, value) {
                    field.store.load({
                        params:{
                            query: value  //me.customerRecord.get('firstName')
                        }});
                }
            }
        });
        /*me.combo.on('change',function(field, value) {
            field.store = field.store.load({
                params:{
                    query: value  //me.customerRecord.get('firstName')
                }});
        });*/


        me.combo.on('select', function(comboo, record,index) {
            var shippingForm = me.up().items.items[1].shippingForm;
            shippingForm.getForm().loadRecord(record[0]);
            me.filterForm.getForm().loadRecord(record[0]);
            me.customerId = record[0].get('customerId');
            comboo.validSelected = true;
            comboo.oldValue = comboo.getValue();
        });

        me.combo.on('focus',  function(combo, newValue, oldValue, opts) {
            combo.validSelected = false;
        });

        me.combo.on('blur', function(combo) {
            if(!combo.validSelected) {
                combo.setValue(combo.oldValue);
            }
        });

        return me.combo;
    },

    createFistNameField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            name: 'firstName',
            fieldLabel: me.snippets.firstName
        });
    },
    createLastNameField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'lastName',
            fieldLabel: me.snippets.lastName
        });
    },

    createNumberField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'number',
            fieldLabel: me.snippets.number
        });
    },

    createEmailField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'email',
            fieldLabel: me.snippets.email
        });
    },

    createCompanyField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'company',
            fieldLabel: me.snippets.company
        });
    },

    createStreetField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'street',
            fieldLabel: me.snippets.street
        });
    },

    createZipField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'zipCode',
            fieldLabel: me.snippets.zip
        });
    },

    createCityField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'city',
            fieldLabel: me.snippets.city
        });
    },
    createCountryField: function() {
        var me = this;
        me.countryStore = Ext.create('Shopware.store.Country');
        me.countryStore.load();

        return Ext.create('Ext.form.field.ComboBox', {
            name: 'countryId',
            queryMode: 'remote',
            store: me.countryStore,
            valueField: 'id',
            displayField: 'name',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.country,
            editable: false
        });
    },
    createAmountField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'amount',
            fieldLabel: me.snippets.amount,
            disabled: true

        });
    },

    createShopField: function() {
        var me = this;
        me.shopStore = Ext.create('Shopware.apps.sKUZOOffer.store.Shop');
        me.shopStore.load();
        me.shopCombo = Ext.create('Ext.form.field.ComboBox', {
            name: 'shopId',
            store: me.shopStore,
            valueField: 'id',
            pageSize: 7,
            queryMode: 'remote',
            displayField: 'name',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.shop,
            editable: false
        });
        me.shopCombo.on('collapse', function(combo) {
            me.shopId = combo.displayTplData[0].id;
            me.fireEvent('refreshPositions', me.shopId)
        });
        return me.shopCombo;
    },

    createDocumentFormatField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Checkbox', {
            name: 'documentFormat',
            inputValue: 1,
            uncheckedValue: 0,
            fieldLabel: me.snippets.documentFormat
        });
    },

    createEmailPreviewField: function() {
        var me = this

        return Ext.create('Ext.form.field.Checkbox', {
            name: 'emailPreview',
            inputValue: 1,
            uncheckedValue: 0,
            value: me.mailPreview,
            fieldLabel: me.snippets.emailPreview
        });
    },

    createPaymentField: function() {
        var me = this;
        me.paymentStore = Ext.create('Shopware.store.Payment', {
            pageSize: 7
        });
        me.paymentStore.load();
        return Ext.create('Ext.form.field.ComboBox', {
            name: 'paymentId',
            pageSize: 7,
            queryMode: 'remote',
            store: me.paymentStore,
            valueField: 'id',
            displayField: 'description',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.payment,
            editable: false
        });
    },

    createDispatchField: function() {
        var me = this;
        me.dispatchStore = Ext.create('Shopware.store.Dispatch', {
            pageSize: 7
        });
        me.dispatchStore.load();
        return Ext.create('Ext.form.field.ComboBox', {
            name: 'dispatchId',
            pageSize: 7,
            queryMode: 'remote',
            store: me.dispatchStore,
            valueField: 'id',
            displayField: 'name',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.dispatch,
            editable: false
        });
    },

    createShippingCostField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Number', {
            name: 'invoiceShipping',
            fieldLabel: me.snippets.shippingCost,
            minValue: 0,
            decimalPrecision: 2
        });
    },

    createShippingTaxField: function() {
        var me = this;
        me.taxStore = Ext.create('Shopware.store.Tax', {
            pageSize: 7
        });
        me.taxStore.load();
        return Ext.create('Ext.form.field.ComboBox', {
            name: 'tax',
            pageSize: 7,
            queryMode: 'remote',
            store: me.taxStore,
            valueField: 'tax',
            displayField: 'name',
            emptyText: me.snippets.emptyTax,
            fieldLabel: me.snippets.shippingTax,
            editable: true,
            listeners: {
                change: function(field, value) {
                    field.setValue((!value) ? '0' : value);
                }
            }
        });
    },


        getCustomerDetails: function() {
        var me = this;
            
        if(me.offerId !=0){
            me.filterForm.items.items[0].setValue(me.record.raw.offerBilling.firstName+'.'+me.record.raw.offerBilling.lastName+' ['+me.record.raw.offerBilling.number+']');
            me.filterForm.items.items[1].setValue(me.record.raw.offerBilling.firstName);
            me.filterForm.items.items[2].setValue(me.record.raw.offerBilling.lastName);
            me.filterForm.items.items[3].setValue(me.record.raw.offerBilling.number);
            me.filterForm.items.items[4].setValue(me.record.raw.customer.email);
            me.filterForm.items.items[5].setValue(me.record.raw.offerBilling.company);
            me.filterForm.items.items[6].setValue(me.record.raw.offerBilling.street);
            me.filterForm.items.items[7].setValue(me.record.raw.offerBilling.zipCode);
            me.filterForm.items.items[8].setValue(me.record.raw.offerBilling.city);
            me.filterForm.items.items[9].setValue(me.record.raw.offerBilling.countryId);
            me.filterForm.items.items[10].setValue(me.record.get('discountAmount'));
            me.filterForm.items.items[11].setValue(me.record.get('languageIso'));
            if(me.record.raw.payment != null) {
                me.filterForm.items.items[12].setValue(me.record.raw.payment.id);
            }
            if(me.record.raw.dispatch != null) {
                me.filterForm.items.items[13].setValue(me.record.raw.dispatch.id);
            }
            me.filterForm.items.items[14].setValue(me.record.get('invoiceShipping'));
            me.filterForm.items.items[15].setValue(me.record.raw.offerBilling.shippingTax);
        }else{
            var customerStore = Ext.create('Shopware.apps.sKUZOOffer.store.Customer');
            customerStore.load({
                params:{
                    query: me.customerId  //me.customerRecord.get('firstName')
                },
                callback: function(records, operation, success) {
                    if (success == true) {
                        Ext.each(records, function (record) {
                            if(record.get('customerId') == me.customerId /*me.customerRecord.get('id')*/){
                               me.filterForm.items.items[0].setValue(record.get('firstName')+'.'+record.get('lastName')+' ['+record.get('number')+']');
                               me.filterForm.items.items[1].setValue(record.get('firstName'));
                               me.filterForm.items.items[2].setValue(record.get('lastName'));
                               me.filterForm.items.items[3].setValue(record.get('number'));
                               me.filterForm.items.items[4].setValue(record.get('email'));
                               me.filterForm.items.items[5].setValue(record.get('company'));
                               me.filterForm.items.items[6].setValue(record.get('street'));
                               me.filterForm.items.items[7].setValue(record.get('zipCode'));
                               me.filterForm.items.items[8].setValue(record.get('city'));
                               me.filterForm.items.items[9].setValue(record.get('countryId'));
                               me.filterForm.items.items[11].setValue(record.get('languageIso'));
                               me.filterForm.items.items[12].setValue(record.get('paymentId'));
                               me.filterForm.items.items[13].setValue(record.get('dispatchId'));
                           }
                        });
                    } else {
                        Ext.Msg.alert(me.snippets.title, me.snippets.errorCustomer);
                    }
                }
            });
        }
    }
});


