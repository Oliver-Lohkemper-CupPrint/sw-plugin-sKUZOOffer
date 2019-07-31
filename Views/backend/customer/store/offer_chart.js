/**
 *
 */

Ext.define('Shopware.apps.Customer.store.OfferChart', {
    /**
     * Extend for the standard ExtJS 4
     * @string
     */
    extend:'Ext.data.Store',
    /**
     * Disable auto loading
     * @boolean
     */
    autoLoad:false,
    /**
     * To upload all selected items in one request
     * @boolean
     */
    batch:true,
    /**
     * Define the used model for this store
     * @string
     */
    model:'Shopware.apps.Customer.model.OfferChart'
});

