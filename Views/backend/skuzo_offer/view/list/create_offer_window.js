/**
 *
 */

//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.CreateOfferWindow', {
    extend: 'Ext.container.Container',
    alias:'widget.offer-position-panel',
    cls: Ext.baseCSSPrefix + 'position-panel',
    layout: 'border',
    width : 1100,
    height: 750,
    style: {
        background: '#a4b5c0'
    },

    autoScroll: true,

    snippets:{
        title: '{s name=position/window_title}Positions{/s}',
        add:'{s name=position/button_add}Add{/s}',
        remove:'{s name=position/button_delete}Delete all selected{/s}',
        discount:'{s name=position/toolbar/discount}Discount{/s}',
        addDiscount:'{s name=position/toolbar/add_discount}Add Discount{/s}',
        purchasePrice:'{s name=position/toolbar/purchase_price}Gross Purchase{/s}'
    },

   initComponent:function () {
        var me = this,
            positionGrid="";
        me.offerId = me.offerId;
        if (me.offerId !== 0) {
            me.positionGrid = me.editPositionGrid();
        }
        else
        {
            me.positionGrid = me.createPositionGrid()
            me.positionGrid.setDisabled(true);
        }
        me.items = [
            Ext.create('Shopware.apps.sKUZOOffer.view.list.Sidebar', {
                region: 'west',
                record: me.record,
		customerRecord: me.customerRecord,
                offerId: me.offerId,
                emailPreview: me.emailPreview,
                ePost: me.ePost
            }),
                    me.positionGrid
        ]
        me.title = me.snippets.title;
        me.callParent(arguments);
        me.traceGridEvents();
   },

    traceGridEvents: function() {
        var me = this;

        //register listener on the before edit event to set the article name and number manually into the row editor.
        me.rowEditor.on('beforeedit', function(editor, e) {
            me.fireEvent('beforeEdit', editor, e, me.rowEditor)
        });

        //register listener on the edit event to save the record and convert the price value. Without
        //this listener the insert price "10,55" would be become "1055"
        me.rowEditor.on('edit', function(editor, e) {
            me.articleNumberSearch.getDropDownMenu().hide();
            me.articleNameSearch.getDropDownMenu().hide();

            me.fireEvent('savePosition', editor, e,  me.positionStore, {
                callback: function(offer) {
                    me.fireEvent('updateForms', offer, me.up('window'));
                }
            })
        });

        //register listener on the canceledit event to remove new order positions.
        me.rowEditor.on('canceledit', function(grid, eOpts) {
            me.fireEvent('cancelEdit', grid, eOpts)
        });

        me.articleNumberSearch.on('valueselect', function(field, value, hiddenValue, record) {
            me.fireEvent('articleNumberSelect', me.rowEditor, value, record)
        });
        me.articleNameSearch.on('valueselect', function(field, value, hiddenValue, record) {
            me.fireEvent('articleNameSelect', me.rowEditor, value, record)
        });

        me.on('canceledit', function() {
            me.articleNumberSearch.getDropDownMenu().hide();
            me.articleNameSearch.getDropDownMenu().hide();
        }, me);

        me.rowEditor.on('validateedit', function(editor, e) {
           /* if(Ext.isGecko){
                if(editor.keyNav.map.bindings[0].key == Ext.EventObject.ENTER){
                    e.cancel = true;
                }else{
                    e.cancel = false;
                }
            }*/
            if (Ext.isChrome){
                if(event.keyCode == Ext.EventObject.ENTER){
                    e.cancel = true;
                }else{
                    e.cancel = false;
                }
            }
        });
    },


    editPositionGrid: function() {
        var me = this;

        me.rowEditor = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 2,
            autoCancel: true
        });

        me.offerPositionGrid = Ext.create('Shopware.offer.position.grid', {
            region: 'center',
            store: me.record.getPositions(),
            plugins: [ me.rowEditor ],
            style: {
                borderTop: '1px solid #A4B5C0'
            },
            viewConfig: {
                enableTextSelection: false
            },
            tbar: me.createGridToolbar(),
            selModel: me.getGridSelModel(),
            getColumns: function() {
                return me.getColumns(this);
            }
        });

        return me.offerPositionGrid;
    },

    createPositionGrid: function() {
        var me = this;
        me.rowEditor = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 2,
            autoCancel: true

        });

        me.offerPositionGrid = Ext.create('Shopware.offer.position.grid', {
            region: 'center',
            store: Ext.create('Shopware.apps.sKUZOOffer.store.Position'),
            plugins: [ me.rowEditor ],
            style: {
                borderTop: '1px solid #A4B5C0'
            },
            viewConfig: {
                enableTextSelection: false
            },
            tbar: me.createGridToolbar(),
            selModel: me.getGridSelModel(),
            getColumns: function() {
                return me.getColumns(this);
            }
        });

        return me.offerPositionGrid;
    },

    getColumns:function (grid) {
        var me = this;

        me.articleNumberSearch = me.createArticleSearch('number', 'name', 'articleNumber');
        me.articleNameSearch = me.createArticleSearch('name', 'number', 'articleName');
        grid.taxStore = me.taxStore;
        grid.positionStore = me.positionStore;

        return [
            {
                dataIndex: 'articleDetailsId',
                hidden: true
            },
            {
                header: grid.snippets.articleNumber,
                dataIndex: 'articleNumber',
                flex:2,
                editor: me.articleNumberSearch
            },
            {
                header: grid.snippets.articleName,
                dataIndex: 'articleName',
                flex:2,
                editor: me.articleNameSearch
            },
            {
                header: grid.snippets.quantity,
                dataIndex: 'quantity',
                flex:1,
                editor: {
                    xtype: 'numberfield',
                    allowBlank: false,
                    minValue: 0,
                    listeners: {
                        change: function(field, value) {
                            me.fireEvent('quantityChange', me.rowEditor, value);
                        }
                    }
                }
            },
            {
                header: me.snippets.purchasePrice,
                dataIndex: 'purchasePrice',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00'),
                hidden: !me.showPurchasePrice,
            },
            {
                header: grid.snippets.originalNetPrice,
                dataIndex: 'originalNetPrice',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')

            },
            {
                header: grid.snippets.originalPrice,
                dataIndex: 'originalPrice',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00'),
            },
            {
                header: grid.snippets.price,
                dataIndex: 'price',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00'),
                editor: {
                    xtype: 'numberfield',
                    allowBlank: false,
                    decimalPrecision: 2,
//                    minValue: 0,
                    listeners: {
                        change: function(field, value) {
                            if (MyAppGlobalVars.price != value){
                                var originalPrice = parseFloat(me.positionGrid.columns[7].field.getValue().replace(",","."));
                                var qnt = me.positionGrid.columns[4].field.getValue();
                                var price = me.positionGrid.columns[8].field.getValue();
                                var sum = price * qnt;

                                var new_total = Math.round(sum * 100) / 100
                                me.positionGrid.columns[9].field.setValue(new_total);
                                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[9].innerHTML = new_total;

                                var new_dis = (originalPrice - price) * qnt;
                                new_dis = Math.round(new_dis * 100) / 100;
                                me.positionGrid.columns[10].field.setValue(new_dis);
                                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[10].innerHTML = new_dis;

                                var new_percent = (((price - originalPrice) / originalPrice) * 100) * -1
                                new_percent = Math.round(new_percent * 100) / 100;
                                MyAppGlobalVars.percent = new_percent;
                                me.positionGrid.columns[12].field.setValue(new_percent);
                            }
                        }
                    }
                }
            },
            {
                header: grid.snippets.total,
                dataIndex: 'total',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: grid.snippets.discount,
                dataIndex: 'discount',
                flex:1,
                renderer:  Ext.util.Format.numberRenderer('0.00')
            },
            {
                header: grid.snippets.currency,
                dataIndex: 'currency',
                flex:1,
            },
            {
                header: grid.snippets.discountPercentage,
                dataIndex: 'percentage',
                flex:1,
                decimalPrecision: 2,
                renderer:  function(value, col, record) {
                    if (record.get('originalPrice') == 0) {
                        return Ext.util.Format.number(0,'0.00')+'%';
                    }
                    else{

                        return Ext.util.Format.number(record.get('percentage'),'0.00')+'%';
                    }
                },
                editor: {
                    xtype: 'numberfield',
                    decimalPrecision: 2,
//                    minValue: 0,
                    maxValue:100,
                    listeners: {
                        change: function(field, value) {
                            if (MyAppGlobalVars.percent != value){
                                var originalPrice = parseFloat(me.positionGrid.columns[7].field.getValue().replace(",","."));
                                var qnt = me.positionGrid.columns[4].field.getValue();
                                var sum = originalPrice * qnt;
                                var new_price = (sum - (sum * value) / 100) / qnt;
                                new_price = Math.round(new_price * 100) / 100;
                                MyAppGlobalVars.price = new_price;
                                me.positionGrid.columns[8].field.setValue(new_price);

                                var new_discount = (originalPrice - ((sum - (sum * value) / 100) / qnt)) * qnt;
                                new_discount = Math.round(new_discount * 100) / 100
                                me.positionGrid.columns[10].field.setValue(new_discount);
                                Ext.dom.Query.select('.x-grid-row-selected .x-grid-cell-inner ')[10].innerHTML = new_discount;
                            }
                        }
                    }
                }
            },
            {
                header: grid.snippets.tax,
                dataIndex: 'taxId',
                flex:1,
                renderer: me.taxColumn,
                editor: {
                    xtype: 'combobox',
                    editable: false,
                    queryMode: 'remote',
                    allowBlank: false,
                    store: me.taxStore,
                    displayField: 'name',
                    valueField: 'id'
                }
            },
            {
                header: grid.snippets.inStock,
                dataIndex: 'inStock',
                flex:1,

            },
            {
                dataIndex: 'scalePrice',
                hidden: true,
                value :1
            },
            {
                xtype:'actioncolumn',
                width:90,
                items:[
                    {
                        iconCls:'sprite-minus-circle-frame',
                        action:'deletePosition',
                        tooltip: me.snippets.deletePosition,
                        /**
                         * Add button handler to fire the deleteOrder event which is handled
                         * in the list controller.
                         */
                        handler:function (view, rowIndex, colIndex, item) {
                            var store = view.getStore(),
                                record = store.getAt(rowIndex);

                            me.fireEvent('deletePosition', record, store);
                        }
                    },
                    {
                        iconCls:'sprite-inbox',
                        action:'openArticle',
                        tooltip: grid.snippets.openArticle,
                        handler:function (view, rowIndex) {
                            var store = view.getStore(),
                                record = store.getAt(rowIndex);

                            grid.fireEvent('openArticle', record);
                        },
                        getClass: function(value, metadata, record) {
                            if (!record.get('articleId'))  {
                                return 'x-hidden';
                            }
                        }
                    },
                    {
                        iconCls:'article--properties',
                        tooltip: grid.snippets.priceByQuantity,
                        getClass: function(value, metadata, record) {
                            return 'x-hidden';
                        }
                    }
                ]
            }
        ];

    },

    /**
     *
     * @param returnValue
     * @param hiddenReturnValue
     * @param name
     * @return Shopware.form.field.ArticleSearch
     */
    createArticleSearch: function(returnValue, hiddenReturnValue, name ) {
        var me = this;
        return Ext.create({if $swVersion4}'Shopware.apps.sKUZOOffer.form.field.ArticleSearch'{else}'Shopware.form.field.ArticleSearch'{/if}, {
            name: name,
            returnValue: returnValue,
            hiddenReturnValue: hiddenReturnValue,
            articleStore: {if $swVersion4}me.variantStore,{else}Ext.create('Shopware.apps.Base.store.Variant'),{/if}
            allowBlank: false,
            getValue: function () {
                return this.getSearchField().getValue();
            },
            setValue: function (value) {
                this.getSearchField().setValue(value);
            }
        });
    },

    /**
     * Creates the toolbar for the position grid.
     * @return Ext.toolbar.Toolbar
     */
    createGridToolbar: function() {
        var me = this;

        me.deletePositionsButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-minus-circle-frame',
            text:me.snippets.remove,
            disabled:true,
            action:'deletePosition',
            handler: function() {
                me.fireEvent('deleteMultiplePositions', me.record, me.offerPositionGrid, {
                    callback: function(offer) {
                        me.fireEvent('updateForms', offer, me.up('window'));
                    }
                });
            }
        });

        me.addPositionButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-plus-circle-frame',
            text:me.snippets.add,
            handler: function() {
                me.fireEvent('newPosition', me.record, me.offerPositionGrid, me.rowEditor)
            }
        });

        me.discountField = Ext.create('Ext.form.field.Number', {
            name: "multipleDiscount",
            fieldLabel: me.snippets.discount,
            maxLength:2,
            value:0,
            minValue: 0,
            maxValue:100
        });

        me.addDiscountButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-plus-circle-frame',
            text:me.snippets.addDiscount,
            disabled:true,
            handler: function() {
                me.fireEvent('makeDiscount', me.record, me.offerPositionGrid, me.discountField.getValue())
            }
        });



        return me.gridToolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock:'top',
            ui: 'shopware-ui',
            items:[
                me.addPositionButton,
                me.deletePositionsButton,
                me.discountField,
                me.addDiscountButton
            ]
        });
    },

    getGridSelModel:function () {
        var me = this;

        var selModel = Ext.create('Ext.selection.CheckboxModel', {
            listeners:{
                // Unlocks the save button if the user has checked at least one checkbox
                selectionchange:function (sm, selections) {
                        me.deletePositionsButton.setDisabled(selections.length === 0);
                        me.addDiscountButton.setDisabled(selections.length === 0);
                }
            }
        });
        return selModel;
    },

    taxColumn: function(value, metaData, rowRecord) {
        var me = this;
        if (value === Ext.undefined) {
            return value;
        }
        var record =  me.taxStore.getById(value);
        if (record instanceof Ext.data.Model) {
            return record.get('name');
        } else {
            if(value == 0 || value == null) {
                return rowRecord.get('taxRate')+'%';
            }
            return value;
        }
    }


});

Ext.define('MyAppGlobalVars', {
    singleton: true,
    price: 0,
    percent: 0
});
