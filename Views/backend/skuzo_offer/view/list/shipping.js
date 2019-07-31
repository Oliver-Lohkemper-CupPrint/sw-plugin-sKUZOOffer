/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Shipping', {
extend:'Ext.panel.Panel',
alias:'widget.offer-listing-shipping-panel',
    autoScroll:true,
    snippets:{
        title:'{s name=shipping/title}Shipping Address{/s}',
        empty: '{s name=shipping/empty}Display all{/s}',
        firstName: '{s name=shipping/firstName}FirstName{/s}',
        lastName: '{s name=shipping/lastName}LastName{/s}',
        zip: '{s name=shipping/zip}Zip{/s}',
        city: '{s name=shipping/city}City{/s}',
        country: '{s name=filter/country}Land{/s}',
        company: '{s name=shipping/company}Company{/s}',
        street: '{s name=shipping/street}Street{/s}',
        errorCustomer:'{s name=shipping/error/customer}Problem loading customerStore{/s}'
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
                me.createShippingForm()
            ]
        });
    },


    createShippingForm: function() {
        var me = this;
        me.shippingForm = Ext.create('Ext.form.Panel', {
            border: false,
            cls: Ext.baseCSSPrefix + 'shipping-form',
            defaults:{
                anchor:'98%',
                labelWidth:120,
                minWidth:250,
                style: 'box-shadow: none;',
                labelStyle: 'font-weight:700;'

            },
            items: [
                me.createFistNameField(),
                me.createLastNameField(),
                me.createCompanyField(),
                me.createStreetField(),
                me.createZipField(),
                me.createCityField(),
                me.createCountryField()
        ]
        });
        return me.shippingForm;
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

    createCityField: function() {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'city',
            fieldLabel: me.snippets.city
        });
    },

    
    getCustomerDetails: function() {

        var me = this;
        if(me.offerId !=0){
            me.shippingForm.items.items[0].setValue(me.record.raw.offerShipping.firstName);
            me.shippingForm.items.items[1].setValue(me.record.raw.offerShipping.lastName);
            me.shippingForm.items.items[2].setValue(me.record.raw.offerShipping.company);
            me.shippingForm.items.items[3].setValue(me.record.raw.offerShipping.street);
            me.shippingForm.items.items[4].setValue(me.record.raw.offerShipping.zipCode);
            me.shippingForm.items.items[5].setValue(me.record.raw.offerShipping.city);
            me.shippingForm.items.items[6].setValue(me.record.raw.offerShipping.countryId);
        }else{
            var customerStore = Ext.create('Shopware.apps.sKUZOOffer.store.Customer');
            customerStore.load({
                params:{
                query: me.customerId//me.customerRecord.get('firstName')
                },
                callback: function(records, operation, success) {
                    if (success == true) {
                        Ext.each(records, function (record) {
                            if(record.get('customerId') == me.customerId /*me.customerRecord.get('id')*/){
                                me.shippingForm.items.items[0].setValue(record.get('firstName'));
                                me.shippingForm.items.items[1].setValue(record.get('lastName'));
                                me.shippingForm.items.items[2].setValue(record.get('company'));
                                me.shippingForm.items.items[3].setValue(record.get('street'));
                                me.shippingForm.items.items[4].setValue(record.get('zipCode'));
                                me.shippingForm.items.items[5].setValue(record.get('city'));
                                me.shippingForm.items.items[6].setValue(record.get('countryId'));
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


