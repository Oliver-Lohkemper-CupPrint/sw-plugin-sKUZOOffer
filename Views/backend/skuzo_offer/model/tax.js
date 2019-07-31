/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Tax', {

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
        { name: 'tax', type:'float' },
        { name: 'name', type:'string' }
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
         * Specific urls to call on CRUD action methods "create", "read", "update" and "destroy".
         * @object
         */
         url: '{url controller="sKUZOOffer" action="getTax"}',

        /**
         * The Ext.data.reader.Reader to use to decode the server's
         * response or data read from client. This can either be a Reader instance,
         * a config object or just a valid Reader type name (e.g. 'json', 'xml').
         * @object
         */
        reader:{
            type:'json',
            root:'data'
       }
    }

});

