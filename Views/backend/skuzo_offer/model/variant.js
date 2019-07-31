/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Variant', {
    /**
     * Extends the standard Ext Model
     * @string
     */
    extend:'Shopware.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    fields: [
        { name: 'id',
            type: 'int' },
        { name: 'name',
            type: 'string' },
        { name: 'articleId',
            type: 'int' },
        { name: 'additionalText',
            type: 'string' },
        { name: 'supplierName',
            type: 'string'},
        { name: 'supplierId',
            type: 'int' },
        { name: 'ordernumber',
            type: 'string' },
        { name: 'inStock',
            type: 'string' },
        { name: 'active',
            type: 'int' },


        //mapping fields for ExtJS
        { name: 'number',
            type: 'string',
            mapping: 'ordernumber' },
    ]
});

