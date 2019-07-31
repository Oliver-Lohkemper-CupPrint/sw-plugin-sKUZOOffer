/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.Customer.view.offer.Offer', {

    extend:'Ext.grid.Panel',

    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.customer-offer-list',

    /**
     * Set css class
     * @string
     */
    cls:Ext.baseCSSPrefix + 'customer-offer-list',

    /**
     * The window uses a boffer layout, so we need to set
     * a region for the grid panel
     * @string
     */
    region:'center',

    /**
     * The view needs to be scrollable
     * @string
     */
    autoScroll:false,

    /**
     * Contains all snippets for the view component
     * @object
     */
    snippets:{
        columns: {
            number:'{s name=column/number}OfferNumber{/s}',
            orderNumber:'{s name=column/orderNumber}OrderNumber{/s}',
            invoiceAmount:'{s name=column/amount}Base Amount{/s}',
            discountAmount:'{s name=column/discount_amount}Amount{/s}',
            discount:'{s name=column/discount}Discount{/s}',
            shipping:'{s name=column/shipping}Shipping{/s}',
            total:'{s name=column/total}Total{/s}',
            offerTime:'{s name=column/offer_time}Offer Time{/s}',
            state:'{s name=column/state}State{/s}',
            active:'{s name=column/active}Active{/s}',
            currency:'{s name=column/currency}Currency{/s}',
            dispatchName:'{s name=column/dispatch_name}Shipping{/s}',
            shopName:'{s name=column/shop}Shop{/s}',
            customer:'{s name=column/customer}Customer{/s}',
            paymentName:'{s name=column/payment_name}Payment{/s}',
            detail: '{s name=column/detail}Show details{/s}',
            saveOrder: '{s name=column/saveOrder}Save Order{/s}',
            openOrder: '{s name=column/openOrder}Open Order{/s}'
        },
        externalComment: '{s name=external_comment}External comment{/s}',
        customerComment: '{s name=customer_comment}Customer comment{/s}',
        internalComment: '{s name=internal_comment}Internal comment{/s}',
        toolbar: {
            search: '{s name=toolbar/search}Search...{/s}',
        },
        paging: {
            pageSize: '{s name=paging_bar/page_size}Number of offers{/s}'
        },
        states: {
            state1: '{s name=state/open}open{/s}',
            state2: '{s name=state/processed}Processed{/s}',
            state3: '{s name=state/sent}Gesendet{/s}',
            state4: '{s name=state/accepted}Accepted{/s}',
            state5: '{s name=state/confirmed}confirmed{/s}'
        }
    },



    initComponent:function () {
        var me = this;

        me.store = me.offerStore;
        me.columns = me.getColumns();
        me.toolbar = me.getToolbar();
        me.pagingbar = me.getPagingBar();
        me.dockedItems = [ me.toolbar, me.pagingbar ];
        me.callParent(arguments);
    },


    /**
     * Creates the paging toolbar for the customer grid to allow
     * and store paging. The paging toolbar uses the same store as the Grid
     *
     * @return Ext.toolbar.Paging The paging toolbar for the customer grid
     */
    getPagingBar:function () {
        var me = this;

        var pageSize = Ext.create('Ext.form.field.ComboBox', {
            fieldLabel: me.snippets.paging.pageSize,
            labelWidth: 120,
            cls: Ext.baseCSSPrefix + 'page-size',
            queryMode: 'local',
            width: 180,
            listeners: {
                scope: me,
                select: me.onPageSizeChange
            },
            store: Ext.create('Ext.data.Store', {
                fields: [ 'value' ],
                data: [
                    { value: '10' },
                    { value: '20' },
                    { value: '30' },
                    { value: '40' },
                    { value: '50' }
                ]
            }),
            displayField: 'value',
            valueField: 'value'
        });
        pageSize.setValue(me.offerStore.pageSize);

        var pagingBar = Ext.create('Ext.toolbar.Paging', {
            store: me.offerStore,
            dock:'bottom',
            displayInfo:true
        });

        pagingBar.insert(pagingBar.items.length - 2, [ { xtype: 'tbspacer', width: 6 }, pageSize ]);

        return pagingBar;

    },

    /**
     * Event listener method which fires when the user selects
     * a entry in the "number of offers"-combo box.
     *
     * @event select
     * @param [object] combo - Ext.form.field.ComboBox
     * @param [array] records - Array of selected entries
     * @return void
     */
    onPageSizeChange: function(combo, records) {
        var record = records[0],
            me = this;

        me.offerStore.pageSize = record.get('value');
        me.offerStore.loadPage(1);
    },

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns:function () {
        var me = this;
        var columns = [
            {
                header: me.snippets.columns.offerTime,
                dataIndex: 'offerTime',
                flex:2,
                renderer:me.dateColumn
            },
            {
                header: me.snippets.columns.number,
                dataIndex: 'number',
                flex:1
            },
            {
                header: me.snippets.columns.total,
                dataIndex: 'invoiceAmountNet',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: me.snippets.columns.discount,
                dataIndex: 'discount',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')

            },
            {
                header: me.snippets.columns.currency,
                dataIndex: 'currency',
                flex:1
            },
            {
                header: me.snippets.columns.paymentName,
                dataIndex: 'paymentId',
                flex:2,
                renderer: me.paymentColumn
            },
            {
                header: me.snippets.columns.dispatchName,
                dataIndex: 'dispatchId',
                flex:2,
                renderer: me.dispatchColumn
            },
            {
                header: me.snippets.columns.shopName,
                dataIndex: 'shopId',
                flex:1,
                renderer: me.shopColumn
            },
            {
                header: me.snippets.columns.orderNumber,
                dataIndex: 'orderNumber',
                flex:1
            },
            {
                header: me.snippets.columns.active,
                dataIndex: 'active',
                flex:1,
                renderer: me.activeColumn,
                editor: {
                    xtype: 'checkbox',
                    inputValue: 1,
                    uncheckedValue: 0
                }
            },
            {
                header: me.snippets.columns.state,
                dataIndex: 'status',
                flex:1,
                renderer: me.statusColumn
            },
            me.createActionColumn()
        ];

        return columns;
    },

    createActionColumn: function() {
        var me = this;

        return Ext.create('Ext.grid.column.Action', {
            width:50,
            header: me.snippets.toolbar.action,
            items:[
                me.createEditOfferColumn(),
                me.createSaveOrderColumn(),
                me.createOpenOrderColumn()
            ]
        });
    },

    createEditOfferColumn: function () {
        var me = this;

        return {
            iconCls:'sprite-pencil',
            action:'editOffer',
            tooltip:me.snippets.columns.detail,

            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);

                me.fireEvent('editOffer', record);
            }
        }
    },


    createSaveOrderColumn: function () {
        var me = this;


        me.saveOrderButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-plus-circle-frame',
            action:'saveOrder',
            tooltip:me.snippets.columns.saveOrder,
            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);

                me.fireEvent('saveOrder', record);
            },
            getClass: function(v, meta, record) {
                if(record.get('status') == 5)
                    return 'x-hidden';
            }
        });


        return me.saveOrderButton;

    },

    createOpenOrderColumn: function () {
    var me = this;

        me.openOrderButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-sticky-notes-pin',
            action:'openOrderDetail',
            tooltip:me.snippets.columns.openOrder,
            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);

                me.fireEvent('openOrderDetail', record);
            },
            getClass: function(v, meta, record) {
                if(record.get('status') != 5)
                    return 'x-hidden';
            }
        });

    return me.openOrderButton;

    },



    /**
     * Creates the grid toolbar with the add and delete button
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar:function () {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            dock:'top',
            ui: 'shopware-ui',
            items:[
                '->',
                {
                    xtype:'textfield',
                    name:'searchfield',
                    cls:'searchfield',
                    width:175,
                    emptyText: me.snippets.toolbar.search,
                    enableKeyEvents:true,
                    checkChangeBuffer:500,
                    listeners: {
                        change: function(field, value) {
                            me.fireEvent('searchOffers', value,me.offerStore);
                        }
                    }
                },
                { xtype:'tbspacer', width:6 }
            ]
        });
    },

    dateColumn:function (value, metaData, record) {
        if ( value === Ext.undefined ) {
            return value;
        }
        return Ext.util.Format.date(value) + ' ' + Ext.util.Format.date(value, timeFormat);
    },
    paymentColumn: function(value, metaData, record) {
        var payment = null;
        if (record instanceof Ext.data.Model) {
            payment = record.raw.payment;
            if (payment) {
                return payment.description;
            }
        } else {
            return value;
        }
    },
    dispatchColumn: function(value, metaData, record) {
        var dispatch = null;
        if (record instanceof Ext.data.Model) {
            dispatch = record.raw.dispatch;
            if (dispatch) {
                return dispatch.name;
            }
        } else {
            return value;
        }
    },
    shopColumn: function(value, metaData, record) {
        var shop = null;
        if (record instanceof Ext.data.Model) {
            shop = record.raw.shop;
            if (shop) {
                return shop.name;
            }
        } else {
            return value;
        }
    },
    customerColumn: function(value, metaData, record, colIndex, store, view) {
        var me = this,
            name = '',
            billing = null;
            comments = [];

        if (typeof record.getBilling === 'function') {
            billing = record.getBilling();
        }

        if (billing instanceof Ext.data.Store && billing.first() instanceof Ext.data.Model) {
            billing = billing.first();
            if (billing.get('company').length > 0) {
                name = billing.get('company');
            } else {
                name = Ext.String.trim(billing.get('firstName') + ' ' + billing.get('lastName'));
            }
        }


        var tpl = new Ext.XTemplate(
            '<div class="sprite-balloon customer-column-icon" style=" width: 16px; height: 16px;">',
            '</div>',
            '<p class="customer-column-text" style="margin-left: 19px;margin-top: -16px;">' + name + '</p>'
        );

        if (record.get('customerComment').length > 0) {
            comments.push("<b>" + me.snippets.customerComment + "</b><br/>" + Ext.String.htmlEncode(record.get('customerComment')));
        }
        if (record.get('internalComment').length > 0) {
            comments.push("<b>" + me.snippets.internalComment + "</b><br/>" + Ext.String.htmlEncode(record.get('internalComment')));
        }
        if (record.get('comment').length > 0) {
            comments.push("<b>" + me.snippets.externalComment + "</b><br/>" + Ext.String.htmlEncode(record.get('comment')));
        }

        if (comments.length > 0) {
            metaData.tdAttr = 'data-qtip="' + comments.join('<br/><br/>') + '"';
            return tpl.html;
        } else {
            return name;
        }

    },
    statusColumn: function(value, metaData, record, colIndex, store, view) {
          var me = this,
             states;
        if (me.snippets.states) {
            name = 'state'+value;
            snippet = me.snippets.states[name];
        }
        if (Ext.isString(snippet) && snippet.length > 0) {
            return snippet;
        } else {
            states = record.raw.states;
            if (states) {
                return states.description;
            } else {
                return value;
            }
        }
    },
    /**
     * Column renderer for boolean columns in order to
     * @param value
     */
    activeColumn: function (value) {
        var checked = 'sprite-ui-check-box-uncheck';
        if (value == true) {
            checked = 'sprite-ui-check-box';
        }
        return '<span style="display:block; margin: 0 auto; height:15px; width:15px;" class="' + checked + '"></span>';
    },
    /**
     * snippets: {
        //{block name="backend/base/model/order_status/snippets"}{/block}
        state0: '{s name=cancelled}Cancelled{/s}',
        state1: '{s name=open}Open{/s}',
        state2: '{s name=in_process}In process{/s}',
        state3: '{s name=completed}Completed{/s}',
        state4: '{s name=partially_completed}Partially completed{/s}',
        state5: '{s name=cancelled_rejected}Cancelled/rejected{/s}',
        state6: '{s name=ready_for_delivery}Ready for delivery{/s}',
        state7: '{s name=partially_delivered}Partially delivered{/s}',
        state8: '{s name=completely_delivered}Completely delivered{/s}',
        state9: '{s name=clarification_required}Clarification required{/s}'
    },
     */

});
