/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Mail', {

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
		{ name: 'id', type:'int' },
        { name: 'offerId', type:'int' },
        { name: 'customerId', type:'int' },
        { name: 'content', type:'string' },
        { name: 'subject', type:'string' },
        { name: 'to', type:'string' },
        { name: 'fromMail', type:'string' },
        { name: 'fromName', type:'string' },
        { name: 'sent', type:'boolean' },
        { name: 'plain', type:'boolean' }
    ],

    /**
    * Configure the data communication
    * @object
    */
    proxy: {
        /**
         * Set proxy type to ajax
         * @string
         */
        type: 'ajax',

        /**
         * Specific urls to call on CRUD action methods "create", "read", "update" and "destroy".
         * @object
         */
        api: {
            create: '{url controller="sKUZOOffer" action="sendMail"}',
            update: '{url controller="sKUZOOffer" action="sendMail"}'
        }
    }

});

