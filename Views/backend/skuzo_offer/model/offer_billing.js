/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.OfferBilling', {

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
        { name:'company', type:'string' },
        { name:'department', type:'string' },
        { name:'email', type:'string' },
        { name:'street', type:'string' },
        { name:'streetNumber', type:'string' },
        { name:'zipCode', type:'string' },
        { name:'city', type:'string' },
        { name:'countryId', type:'int' },
        { name:'shopId', type:'int' },
        { name:'paymentId', type:'int' },
        {
            name: 'customerField',
            type:'string',
            convert: function(value, record) {
                return record.get('firstName')+'.'+ record.get('lastName')+' ['+ record.get('number')+']';
            }
        }
    ]


});

