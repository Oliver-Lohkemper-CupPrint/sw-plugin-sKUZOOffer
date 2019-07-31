/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Document', {

    /**
     * Extend from the standard ExtJS 4
     * @string
     */
    extend:'Ext.grid.Panel',

    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
    */
    alias:'widget.offer-document-list',

    /**
     * Set css class
     * @string
     */
    cls:Ext.baseCSSPrefix + 'document-grid',

    /**
     * The view needs to be scrollable
     * @string
     */
    autoScroll:true,

    /**
     * Contains all snippets for the view component
     * @object
     */
    snippets:{
        columns: {
            name:'{s name=column/name}Name{/s}',
            date:'{s name=column/date}Date{/s}',
            amount:'{s name=column/amount}Amount{/s}',
            downloadDocument: '{s name=column/download}Download{/s}'
        },
        documentName: '{s name=type/document/name}Offer{/s}'
    },

    /**
	 * The initComponent template method is an important initialization step for a Component.
     * It is intended to be implemented by each subclass of Ext.Component to provide any needed constructor logic.
     * The initComponent method of the class being created is called first,
     * with each initComponent method up the hierarchy to Ext.Component being called thereafter.
     * This makes it easy to implement and, if needed, override the constructor logic of the Component at any step in the hierarchy.
     * The initComponent method must contain a call to callParent in order to ensure that the parent class' initComponent method is also called.
	 *
	 * @return void
	 */
    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.pagingbar = me.getPagingBar();
        me.callParent(arguments);
    },


    /**
     * Creates the grid columns.
     */
    getColumns: function() {
        var me = this;

        var cols= [
            {
                header: me.snippets.columns.date,
                dataIndex: 'date',
                flex: 1,
                renderer: me.dateColumn
            }, {
                header: me.snippets.columns.name,
                dataIndex: 'name',
                flex: 2,
                renderer: me.nameColumn
            }, {
                header: "Aktion",
                xtype:'actioncolumn',
                flex: 1,
                items: [{
                    iconCls:'sprite-envelope--arrow',
                    action:'sendDocumentByMail',
                    tooltip: 'Als E-Mail senden',
                    handler:function (view, rowIndex) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);
                        me.fireEvent('sendDocumentByMail', record);
                    }
                }]
            }
        ];

        if(me.ePost){
            cols.push({
                header: "E-POST",
                xtype:'actioncolumn',
                flex: 1.5,
                items: [{
                    iconCls:'sprite-envelope--arrow',
                    action:'sendDocument',
                    tooltip: 'Als E-POSTBRIEF senden',
                    handler:function (view, rowIndex, colIndex, item) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);
                        me.fireEvent('sendDocument', record);
                    }
                },{
                    iconCls:'sprite-document-pdf',
                    action:'previewDocument',
                    tooltip: 'E-POSTBRIEF Vorschau',
                    handler:function (view, rowIndex, colIndex, item) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);
                        me.fireEvent('previewDocument', record);
                    }
                }],
                width:50
            });
        }
        return cols;
    },

    /**
     * Creates the paging bar for the document grid.
     */
    getPagingBar: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Paging', {
            store: me.store,
            dock: 'bottom'
        })
    },

    /**
     * Column renderer function which formats the date column of the document grid.
     * @param value
     */
    dateColumn: function(value ) {
        var me = this;
        if(me.offerId) {
            if (!Ext.isDate(value)) {
                return value;
            }
            return Ext.util.Format.date(value);
        }
    },

    /**
     * Columns renderer for the name column
     * @param value
     */
    nameColumn: function(value, metaData, record, rowIndex, colIndex, store, view) {
        var helper = new Ext.dom.Helper,
            display = '',
            type = '',
            me = this;
        if(me.offerId) {
            if(record.raw.type) {
                type = record.raw.type;
            }
            if (record.get('typeId') === 4) {
                display = type.get('name');
            } else {
                if(type){
                    if(record.get('documentId')==-1) {
                        display = me.snippets.documentName+ ' extern';
                    } else {
                        display = me.snippets.documentName+ ' ' + Ext.String.leftPad(record.get('documentId'), 8, '0');
                    }
                }
            }

            var isMedia = false;
            if(record.get('mediaId') && record.get('mediaId')!=-1) {
                isMedia = true;
            }

            var spec = {
                tag: 'a',
                html: display,
                href: '{url controller="sKUZOOffer" action="openPdf"}?id=' + record.get('hash') + '&isMedia=' + isMedia,
                target: '_blank'
            };

            return helper.markup(spec);
        }

    },

    /**
     * Column renderer function which formats the amount column with the Ext.util.Format.currency() function.
     * @param value
     */
    amountColumn: function(value) {
        if (!Ext.isNumeric(value)) {
            return value;
        }
        return Ext.util.Format.currency(value);
    }

});


