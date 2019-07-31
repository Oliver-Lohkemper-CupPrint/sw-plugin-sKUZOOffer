/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Configuration', {

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
        {
            name: 'offerId', type:'int' },
        // todo dateFields needs type 'date', but if used the dates in the created documents are formatted improperly
        {
            name: 'offerTime' },
        {
            name: 'deliveryDate' },
        {
            name: 'displayDate' },
        {
            name: 'documentType', type:'int' },
        {
            name: 'documentFormat' },
        {
            name: 'mediaId' }
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
            create:'{url controller="sKUZOOffer" action="createDocument" targetField=documents}'
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

