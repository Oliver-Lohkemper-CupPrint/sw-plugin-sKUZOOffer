/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
    me.mainWindow = me.getView('main.Window').create({
             offerStore: me.getStore('Offer').load({

             })
             //billingStore : me.getStore('Billing').load()

        }).show();

        me.callParent(arguments);
    }

});
