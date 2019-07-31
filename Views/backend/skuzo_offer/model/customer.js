/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Customer', {

    /**
     * Extends the standard Ext Model
     * @string
     */
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

        { name:'id', type:'int' },
        { name:'customerId', type:'int' },
        { name:'number', type:'string' },
        { name:'firstName', type:'string' },
        { name:'lastName', type:'string' },
        { name:'firstLogin', type:'date' },
        { name:'customerGroup', type:'string' },
        { name:'company', type:'string' },
        { name:'department', type:'string' },
        { name:'email', type:'string' },
        { name:'street', type:'string' },
        { name:'streetNumber', type:'string' },
        { name:'zipCode', type:'string' },
        { name:'city', type:'string' },
        { name:'countryId', type:'int' },
        { name:'orderCount', type:'int' },
        { name:'amount', type:'float' },
        { name:'shopId', type:'int' },
        { name:'paymentId', type:'int' },
        { name:'dispatchId', type:'int' },
        {
            name: 'customerField',
            type:'string',
            convert: function(value, record) {
                return record.get('firstName')+'.'+ record.get('lastName')+' ['+ record.get('number')+']';
            }
        },
        { name:'sfirstName', type:'string' },
        { name:'slastName', type:'string' },
        { name:'scompany', type:'string' },
        { name:'sstreet', type:'string' },
        { name:'szipCode', type:'string' },
        { name:'scity', type:'string' },
        { name:'scountryId', type:'int' }
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
            read:'{url controller="sKUZOOffer" action="getCustomer"}'
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

