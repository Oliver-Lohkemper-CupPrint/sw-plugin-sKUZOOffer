/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Receipt', {

    /**
     * Extends the standard Ext Model
     * @string
     */
    extend:'Ext.data.Model',

    /**
     * Unique identifier field
     * @string
     */
    idProperty:'id',

    /**
     * The fields used for this model
     * @array
     */
    fields:[

        { name: 'id', type:'int' },
        { name: 'date', type:'date' },
        { name: 'typeId', type:'int' },
        { name: 'customerId', type:'int' },
        { name: 'offerId', type:'int' },
        { name: 'amount', type:'float' },
        { name: 'documentId', type:'int' },
        { name: 'hash', type:'string' },
        { name: 'typeName', type:'string' },
        { name: 'mediaId', type:'string' }
    ],
    /**
     * @array
     */
    associations:[
           { type:'hasMany', model:'Shopware.apps.Base.model.DocType', name:'getDocType', associationKey:'type' }
    ]

});
