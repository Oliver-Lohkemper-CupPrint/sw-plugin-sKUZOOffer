/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Shop', {
    extend:'Shopware.data.Model',

    fields:[
		{ name:'id', type:'int' },
        { name:'mainId', type:'int' },
        { name:'default', type:'boolean' },
        { name:'localeId', type:'int' },
        { name:'categoryId', type:'int' },
        { name:'name', type:'string' }
    ]
});

