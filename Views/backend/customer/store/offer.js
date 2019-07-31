/**
 *
 */
Ext.define('Shopware.apps.Customer.store.Offer', {
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
     * Amount of data loaded at once
     * @integer
     */
    pageSize:10,
    /**
     * Enable remote sorting.
     * @boolean
     */
    remoteSort:true,
    /**
     * Enable remote filtering.
     * @boolean
     */
    remoteFilter:true,
    /**
     * Define the used model for this store
     * @string
     */
    model:'Shopware.apps.Customer.model.Offer'
});

