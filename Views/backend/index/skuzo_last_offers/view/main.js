/**
 *
 */
//{namespace name=backend/widget/labels}
Ext.define('Shopware.apps.Index.sKUZOLastOffersWidget.view.Main', {
    /**
     * Extend the base widget view
     */
    extend: 'Shopware.apps.Index.view.widgets.Base',

    /**
     * Set alias so the widget can be identified per widget name
     */
    alias: 'widget.skuzo-last-offers',

    /**
     * Set the south handle so the widget height can be resized.
     */
    resizable: {
        handles: 's'
    },

    /**
     * Minimum / Default height of the widget
     */
    minHeight: 250,

    /**
     * Maximum height that the widget can have
     */
    maxHeight: 600,

    /**
     * Contains all snippets for the view component
     * @object
     */
    snippets:{
        columns: {
            number:'{s name=column/number}OfferNr{/s}',
            invoiceAmount:'{s name=column/amount}Amount{/s}',
            offerTime:'{s name=column/offer_time}Offer Time{/s}',
            customer:'{s name=column/customer}Customer{/s}',
            state:'{s name=column/state}State{/s}',
            openCustomer: '{s name=column/open_customer}Open customer{/s}',
            detail: '{s name=column/detail}Show details{/s}',
       },
        states: {
            state1: '{s name=state/open}open{/s}',
            state2: '{s name=state/processed}Processed{/s}',
            state3: '{s name=state/sent}Gesendet{/s}',
            state4: '{s name=state/accepted}Accepted{/s}',
            state5: '{s name=state/confirmed}confirmed{/s}'
        }
    },

    /**
     * Initializes the widget.
     * Creates the offer store and the Grid for showing the newest registrations.
     * Adds a refresh button to the header to manually refresh the grid.
     *
     * @public
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.offerStore = Ext.create('Shopware.apps.Index.sKUZOLastOffersWidget.store.Offers').load();

        me.items = [
            me.createOfferGrid()
        ];

        me.tools = [{
            type: 'refresh',
            scope: me,
            handler: me.refreshView
        }];


        me.callParent(arguments);
    },

    /**
     * Creates the main Widget grid and its columns
     *
     * @returns { Ext.grid.Panel }
     */
    createOfferGrid: function() {
        var me = this;

        return Ext.create('Ext.grid.Panel', {
            border: 0,
            store: me.offerStore,
            columns: me.createColumns(),
            bbar: {
                xtype: 'pagingtoolbar',
                displayInfo: true,
                store: me.offerStore
            }
        });
    },

    /**
     * Helper method which creates the columns for the
     * grid panel in this widget.
     *
     * @return { Array } - generated columns
     */
    createColumns: function() {
	var me = this;
        return [{
            dataIndex: 'offerTime',
            header: me.snippets.columns.offerTime,
            flex: 1,
            renderer: Ext.util.Format.dateRenderer('d.m.Y')
        },  {
            dataIndex: 'number',
            header: me.snippets.columns.number,
            flex: 1
        },{
            dataIndex: 'customerId',
            header: me.snippets.columns.customer,
            flex: 1,
	    renderer: me.customerColumn
        }, {
            dataIndex: 'invoiceAmount',
            header: me.snippets.columns.invoiceAmount,
            flex: 1
        }, {
            header: me.snippets.columns.state,
            dataIndex: 'status',
            flex:1,
            renderer: me.statusColumn
        },{
            xtype: 'actioncolumn',
            width: 50,
            items: [{
                iconCls:'sprite-user--arrow',
                tooltip: me.snippets.columns.openCustomer,
                handler: function(view, rowIndex, colIndex, item, event, record) {
                    openNewModule('Shopware.apps.Customer', {
                        action: 'detail',
                        params: {
                            customerId: ~~(record.get('id'))
                        }
                    });
                }
            },
	    {
                iconCls:'sprite-pencil',
                tooltip: me.snippets.columns.detail,
                handler: function(view, rowIndex, colIndex, item, event, record) {
                    me.createOfferWindow=Ext.create('Shopware.apps.sKUZOOffer.view.list.Window',{
                        offerId: record.get('id'),
                        record: record,
                        taxStore: Ext.create('Shopware.apps.sKUZOOffer.store.Tax').load(),
                        positionStore: record.getPositions()
                    }).show();
                }
            }]
        }]
    },

    /**
     * Refresh the offer store if its available
     */
    refreshView: function() {
        var me = this;

        if(!me.offerStore) {
            return;
        }

        me.offerStore.reload();
    },

    customerColumn: function(value, metaData, record, colIndex, store, view) {
         var billing;

        if (record instanceof Ext.data.Model) {
            billing = record.raw.billing;
            if (billing) {
                return billing.firstName +' '+ billing.lastName;
            }
        } else {
            return value;
        }
    },

    statusColumn: function(value, metaData, record, colIndex, store, view) {
    var me = this,
        states;

        states = record.raw.states;
        if (states) {
            return states.description;
        } else {
            return value;
        }
    }


});