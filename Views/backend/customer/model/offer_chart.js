/**
 *
 */

Ext.define('Shopware.apps.Customer.model.OfferChart', {

    /**
     * Extends the standard Ext Model
     * @string
     */
    extend:'Ext.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    fields:[
        { name:'amount', type:'float' },
        { name:'date', type:'date', dateFormat:'Y-m-d' }
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
            read:'{url controller="sKUZOOffer" action="getOfferChart"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader:{
            type:'json',
            root:'data'
        }
    }

});

