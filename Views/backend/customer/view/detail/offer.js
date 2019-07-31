
/**
 * Shopware UI - Customer list detail page
 *
 * This component represents the window for the detail page of a customer record.
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.Customer.view.detail.Offer', {
    override: 'Shopware.apps.Customer.view.detail.Window',

    requires: [ 'Shopware.apps.Customer.view.detail.Window' ],
    alias:'widget.create-offer-tab',


    layout:'fit',

    alias:'widget.customer-detail-window',

    width:1020,

    height:'90%',

    footerButton:false,

    border:false,

    cls:Ext.baseCSSPrefix + 'customer-detail-window',

    stateful:true,

    stateId:'shopware-customer-detail-window',

    snippets:{
        titleCreate:'{s namespace=backend/customer/view/detail name=window/create_title}Customer administration - Create a customer{/s}',
        titleEdit:'{s namespace=backend/customer/view/detail name=window/edit_title}Customer account:{/s}',
        cancel:'{s namespace=backend/customer/view/detail name=window/cancel}Cancel{/s}',
        save:'{s namespace=backend/customer/view/detail name=window/save}Save{/s}',
        dataTab:'{s namespace=backend/customer/view/detail name=window/data_tab}Data{/s}',
        orderTab:'{s namespace=backend/customer/view/detail name=window/order_tab}Orders{/s}',
        offerTab:'{s name=customerDeatils/window/offer_tab}Offers{/s}',
        from:'{s namespace=backend/customer/view/detail name=customerDeatils/window/from_date}From{/s}',
        to:'{s namespace=backend/customer/view/detail name=customerDeatils/window/to_date}To{/s}',

        addressTab:'{s namespace=backend/customer/view/detail name=window/address_tab}Addresses{/s}',
        field_title: '{s namespace=backend/customer/view/detail name=base/field_title}Title{/s}',
        salutation: {
            label: '{s namespace=backend/customer/view/detail name=base/salutation}Salutation{/s}'
        },
        firstname: '{s namespace=backend/customer/view/detail name=base/firstname}Firstname{/s}',
        lastname: '{s namespace=backend/customer/view/detail name=base/lastname}Lastname{/s}',
        birthday: '{s namespace=backend/customer/view/detail name=base/birthday}Birthday{/s}'
    },


     getTabs:function () {
        var me = this;

         var result = me.callParent(arguments);
             result.push(me.createOfferTab());

         return result;
     },


    createOfferTab:function () {
        var me = this,
            gridStore = Ext.create('Shopware.apps.Customer.store.Offer'),
            chartStore = Ext.create('Shopware.apps.Customer.store.OfferChart');

        gridStore.getProxy().extraParams = { customerID:me.record.data.id };
        chartStore.getProxy().extraParams = { customerID:me.record.data.id };

        me.offerGrid = Ext.create('Shopware.apps.Customer.view.offer.Offer', {
            flex: 1,
            offerStore: gridStore.load()
        })

        me.offerToolbar = me.createOfferToolbar();

        return Ext.create('Ext.container.Container', {
            layout: {
                type: 'vbox',
                align : 'stretch'
            },
            defaults: { flex: 1 },
            title: me.snippets.offerTab,
            items: [{
                xtype: 'panel',
                unstyled: true,
                layout: 'border',
                items: [{
                    xtype:'customer-list-offer-chart',
                    region: 'center',
                    store:chartStore.load()
                }],
                dockedItems: [ me.offerToolbar ]
            }, me.offerGrid ]
        });
    },


    createOfferToolbar:function () {
        var me = this,
            today = new Date();

        me.fromDateField = Ext.create('Ext.form.field.Date', {
            labelWidth:45,
            name:'fromOfferDate',
            fieldLabel:me.snippets.from,
            value:new Date(today.getFullYear() - 1, today.getMonth(), today.getDate())
        });

        me.toDateField = Ext.create('Ext.form.field.Date', {
            labelWidth:45,
            name:'toOfferDate',
            fieldLabel:me.snippets.to,
            value:today
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            ui:'shopware-ui',
            padding: '10 0 5',
            cls: Ext.baseCSSPrefix + 'offer-chart-toolbar',
            items:[
                { xtype:'tbspacer', width:6 },
                me.fromDateField,
                { xtype:'tbspacer', width:12 },
                me.toDateField
            ]
        });
    }

});

