/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.store.Configuration', {
    /**
     * Extend for the standard ExtJS 4
     * @string
     */
    extend:'Ext.data.Store',
    /**
     * Enable batch processing
     */
    batch: true,
    /**
     * Define the used model for this store
     * @string
     */
    model:'Shopware.apps.sKUZOOffer.model.Configuration'
});


