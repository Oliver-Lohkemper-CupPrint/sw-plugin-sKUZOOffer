/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.store.Offer', {
    extend:'Shopware.store.Listing',
    /**
     * Auto load the store after the component is initialized
     * @boolean
     */
    autoLoad:false,
    /**
     * Enable remote sort.
     * @boolean
     */
    remoteSort:true,
    /**
     * Enable remote filtering
     * @boolean
     */
    remoteFilter:true,
    /**
     * Amount of data loaded at once
     * @integer
     */
    pageSize:20,
    /**
     * to upload all selected items in one request
     * @boolean
     */
    batch:true,
    /**
     * Define the used model for this store
     * @string
     */
    configure: function() {
        return {
            controller: 'sKUZOOffer'
        };
    },
    model:'Shopware.apps.sKUZOOffer.model.Offer'
})