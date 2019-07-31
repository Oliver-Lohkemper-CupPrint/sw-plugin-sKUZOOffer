/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.Customer.view.action.Offer', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.Customer.view.list.List',

    /**
     * List of classes that have to be loaded before instantiating this class.
     * @array
     */

    requires: [ 'Shopware.apps.Customer.view.list.List' ],
    alias:'widget.create-offer-action',



    /**
     * Initializes the class override to provide additional functionality
     * like a new full page preview.
     *
     * @public
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.callOverridden(arguments);
    },

    registerEvents:function () {
        this.callParent(arguments);
        this.addEvents('createOffer');
    },

    getColumns: function () {
        var me = this;
        var grid = me.callOverridden(arguments);
        var gridAction = grid[grid.length-1];
        grid.width = 400;
        gridAction.width = 80;
        gridAction.items.push({
                iconCls:'sprite-plus-circle-frame',
                action:'createOffer',
                tooltip: '{s name=customer/createOffer}create offer{/s}',
                handler:function (view, rowIndex) {
                    var store = view.getStore(),
                        record = store.getAt(rowIndex);
                    me.fireEvent('createOffer', record);
                }
          });
        return grid;
    }
});

