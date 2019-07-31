/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.store.States', {
    extend:'Ext.data.Store',

    autoLoad : false,
    remoteSort:true,
    remoteFilter:true,
    pageSize:20,


    model:'Shopware.apps.sKUZOOffer.model.States'
});

