/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.sKUZOOffer',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main','Offer' ],

    models: ['Offer','Position', 'Tax', 'OfferBilling', 'OfferShipping', 'Receipt', 'Configuration', 'Customer', 'States', 'Mail', 'Shop'{if $swVersion4},'Variant'{/if}],

    views: [
        'main.Window',
        'list.Offer',
        'list.CreateOfferWindow',
        'list.Position',
        'list.Sidebar',
        'list.Billing',
        'list.Shipping',
        'list.Document',
        'list.Window',
        'list.Communication',
        'mails.Window',
        'mails.Form'
    ],
    stores: ['Offer','Position', 'Tax', 'Configuration', 'Customer', 'States', 'Shop'{if $swVersion4},'Variant'{/if}],


    launch: function() {
        return this.getController('Main').mainWindow;
    }
});