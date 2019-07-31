/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.States', {

    /**
     * Extends the standard Ext Model
     * @string
     */
    extend:'Ext.data.Model',
    idProperty:'id',
    /**
     * The fields used for this model
     * @array
     */
    fields:[
	    { name: 'id', type:'int' },
        {
            name: 'description',
            type: 'string'
        }
    ]


});

