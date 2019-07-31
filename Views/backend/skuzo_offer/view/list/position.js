/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Position', {
    alternateClassName: 'Shopware.offer.position.grid',
    extend:'Ext.grid.Panel',
    alias:  'widget.offer-position-grid',
    minHeight: 120,
    autoScroll:true,
    cls: Ext.baseCSSPrefix + 'offer-position-grid',
    flex: 1,

    snippets: {
        articleNumber: '{s name=column/article_number}Article number{/s}',
        articleName: '{s name=column/article_name}Article name{/s}',
        quantity: '{s name=column/quantity}Quantity{/s}',
        total: '{s name=column/total}Total{/s}',
        discount: '{s name=column/discount}Discount{/s}',
        tax: '{s name=column/tax}Tax{/s}',
        inStock: '{s name=column/in_stock}Stock{/s}',
        originalNetPrice: '{s name=column/originalNetPrice}Net Original{/s}',
        originalPrice: '{s name=column/originalPrice}Gross Original{/s}',
        price: '{s name=column/price}Gross Price{/s}',
        currency:'{s name=column/currency}Currency{/s}',
        discountPercentage: '{s name=column/discountPercent}Percent{/s}',
        openArticle: '{s name=column/open_article}Show article{/s}',
        deletePosition: '{s name=column/delete_position}Delete offer position{/s}',
        priceByQuantity: "{s name=column/priceByQuantity}Article's price change by quantity range{/s}"
    },

    viewConfig: {
        enableTextSelection: true
    },

    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.callParent(arguments);
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
                dataIndex: 'articleDetailsId',
                hidden: true
            },
            {
                header: me.snippets.articleNumber,
                dataIndex: 'articleNumber',
                flex:1.5
            },
            {
                header: me.snippets.articleName,
                dataIndex: 'articleName',
                flex:2
            },
            {
                header: me.snippets.quantity,
                dataIndex: 'quantity',
                flex:1
            },
            {
                header: me.snippets.originalPrice,
                dataIndex: 'originalPrice',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: me.snippets.price,
                dataIndex: 'price',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: me.snippets.total,
                dataIndex: 'total',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: me.snippets.discount,
                dataIndex: 'discount',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: me.snippets.tax,
                dataIndex: 'taxId',
                flex:1,
                renderer: me.taxColumn

            },
            {
                header: me.snippets.inStock,
                dataIndex: 'inStock',
                flex:1
            },
            {
                /**
                 * Special column type which provides
                 * clickable icons in each row
                 */
                xtype:'actioncolumn',
                width:40,
                items:[
                    {
                        iconCls:'sprite-inbox',
                        action:'openArticle',
                        tooltip: me.snippets.openArticle,
                        /**
                         * Add button handler to fire the openCustomer event which is handled
                         * in the list controller.
                         */
                        handler:function (view, rowIndex, colIndex, item) {
                            var store = view.getStore(),
                                record = store.getAt(rowIndex);

                            me.fireEvent('openArticle', record);
                        },
                        getClass: function(value, metadata, record) {
                            if (!record.get('articleId'))  {
                                return 'x-hidden';
                            }
                        }
                    }
                ]
            }
        ];

        return columns;
    },
    taxColumn: function(value, metaData, rowRecord) {

        // SW-3289 If we have no valid taxId, return the taxRate
        if(rowRecord.get('taxRate')!= null) {
            return rowRecord.get('taxRate').toString().replace(/[.,]/, Ext.util.Format.decimalSeparator)+'%';
        }
        return value;
    }

});




