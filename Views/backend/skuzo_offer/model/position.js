/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Position', {
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
        { name: 'offerId', type:'int' },
        { name: 'articleDetailsId', type:'int' },
        { name: 'articleId', type:'int' },
        { name: 'articleNumber', type:'string' },
        { name: 'articleName', type:'string' },
        { name: 'quantity', type:'int' },
        { name: 'purchasePrice', type:'float' },
        { name: 'originalNetPrice', type:'float' },
        { name: 'originalPrice', type:'float' },
        { name: 'price', type:'float' },
        { name: 'taxId', type:'int' },
        { name: 'taxRate', type:'float' },
        { name: 'inStock', type:'int' },
        { name: 'percentage', type:'int' },
        { name: 'currency', type:'string' },
        { name: 'purchasePrice', type:'float' },
        { name: 'scalePrice', type:'int' },
        {
            name: 'total',
            type:'float',
            convert: function(value, record) {
                if (!Ext.isNumeric(record.get('price'))) {
                    return record.get('price');
                }
                return record.get('price') * record.get('quantity');
            }
        },
        {
            name: 'discount',
            type:'float',
            convert: function(value, record) {
                if (!Ext.isNumeric(record.get('price'))) {
                    return record.get('price');
                }
                var originalPrice = Math.round(record.get('originalPrice') * 100 ) / 100;
                var price = Math.round(record.get('price') * 100 ) / 100;
                return (originalPrice - price)*record.get('quantity');
            }
        },
        {
            name: 'percentage',
            type:'float',
            convert: function(value, record) {
                var originalPrice = Math.round(record.get('originalPrice') * 100 ) / 100;
                var price = Math.round(record.get('price') * 100 ) / 100;
                return 100-(100*price/originalPrice);

            }
        }


    ],

    /**
     * Configure the data communication
     * @object
     */
    proxy:{
        /**
         * Set proxy type to ajax
         * @string
         */
        type:'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api:{
            destroy:'{url controller="sKUZOOffer" action="deletePosition" targetField=positions}',
            create:'{url controller="sKUZOOffer" action="savePosition"}',
            update:'{url controller="sKUZOOffer" action="savePosition"}',
            read:'{url controller="sKUZOOffer" action="postionList"}',
        },

        /**
         * Configure the data reader
         * @object
         */
        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }


});