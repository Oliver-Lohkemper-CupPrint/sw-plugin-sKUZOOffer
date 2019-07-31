/**
 * Shopware LastOfferWidget Store
 *
 */

Ext.define('Shopware.apps.Index.sKUZOLastOffersWidget.store.Offers', {
    /**
     * Extends the default Ext Store
     * @string
     */
    extend: 'Ext.data.Store',

    model: 'Shopware.apps.Index.sKUZOLastOffersWidget.model.Offers',

    remoteSort: true,

    pageSize: 10,

    autoLoad: true,

    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        type: 'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        url: '{url controller="LastOffersWidget" action="getLastOffers"}',

        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});