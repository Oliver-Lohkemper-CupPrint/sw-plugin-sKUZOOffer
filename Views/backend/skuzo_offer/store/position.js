/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.store.Position', {
    extend:'Ext.data.Store',
    autoLoad : false,
    remoteSort:true,
    remoteFilter:true,
    pageSize:20,

    model: 'Shopware.apps.sKUZOOffer.model.Position',
   /* listeners: {
        beforeload: function(store, operation) {

            operation.params = {
                positionId: store.positionId
            };
        }
    },*/
    proxy:{

        type:'ajax',

        api:{
            read : '{url controller="sKUZOOffer" action="positionList" }'
        },

        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }
})