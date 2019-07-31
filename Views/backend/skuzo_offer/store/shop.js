/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.store.Shop', {
    extend: 'Ext.data.Store',

    model : 'Shopware.apps.sKUZOOffer.model.Shop',
    pageSize: 7,

    remoteSort: true,
    remoteFilter: true,

    proxy:{
        type:'ajax',
        url: '{url controller="sKUZOOffer" action=getShops}',
        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    },

    filters: [{
        property: 'main',
        value: null
    }]
});

