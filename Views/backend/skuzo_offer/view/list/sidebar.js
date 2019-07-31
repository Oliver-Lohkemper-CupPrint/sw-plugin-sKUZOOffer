/**
 *
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Sidebar', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.offer-address-sidebar',
    layout: 'border',
    autoScroll:true,
    width: 330,
    snippets:{
        create:'{s name=filter/create}Create Document{/s}',
        save: '{s name=filter/save}Save{/s}',
        document: {
            title: '{s name=document/title}Documents{/s}',
            date: '{s name=document/date}Date{/s}',
            name:  '{s name=document/name}Name{/s}'
        }
    },

    initComponent: function () {
        var me = this;

        me.items = [
                Ext.create('Ext.panel.Panel', {
                    layout: 'accordion',
                    region: 'center',
                    items:  me.getPanels(),
                    height:650
                }),
                Ext.create('Ext.panel.Panel',{
                    region: 'south',
//                    height:180,
                    items: [me.createButtons(), me.createDocumentsGrid()]
                })
        ]

        me.callParent(arguments);
    },

    /**
     * Returns the three elements of the accordion layout
     */
    getPanels: function () {
        var me = this;

        return [
            Ext.create('Shopware.apps.sKUZOOffer.view.list.Billing', {
                record:         me.record,
		        customerRecord: me.customerRecord,
                offerId:        me.offerId,
                emailPreview:   me.emailPreview
            }),
            Ext.create('Shopware.apps.sKUZOOffer.view.list.Shipping', {
                record:         me.record,
		        customerRecord: me.customerRecord,
                offerId:        me.offerId
            })
         ];
    },

    /**
     * Returns the media selector for the supplier module
     * @return Shopware.MediaManager.MediaSelection
     */
    getDropZone : function() {
        return Ext.create('Shopware.MediaManager.MediaSelection', {
            name: 'media-manager-selection',
            style: 'margin-top: 10px;margin-left:5px;margin-bottom:0;',
            albumId: {config name=offerAlbumId}, // Default supplier albumId
            buttonOnly: true,
            multiSelect: false,
            buttonText: '{s name=button_add_document}Dokumentupload{/s}',
            action: 'main-attachments-add'
        });
    },

    createButtons: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            style: 'margin-top: 10px;',
            items: [
                {
                    xtype: 'button',
                    cls: 'primary',
                    action: 'create-document',
                    text: me.snippets.create,
                    handler: function() {
                        var config = Ext.create('Shopware.apps.sKUZOOffer.model.Configuration');
                        me.fireEvent('createDocument', me.record, config, me);
                    }
                },
                {
                    xtype: 'button',
                    cls: 'primary',
                    action: 'saveOfferBilling',
                    text: me.snippets.save,
                    handler: function() {
                        me.fireEvent('saveOfferBilling',me.customerId);
                    }
                }
                {if {config name=enableOfferUpload}},me.getDropZone(){/if}
            ]
        });
    },

    createDocumentsGrid: function() {
        var me = this;
        var documentStore = me.record['getReceiptStore'];
        if(documentStore == undefined) {
            documentStore = Ext.create('Ext.data.Store', {
                model: 'Shopware.apps.sKUZOOffer.model.Receipt'
            });

        }

        me.documentGrid =  Ext.create('Shopware.apps.sKUZOOffer.view.list.Document', {
            height: 130,
            offerId: me.offerId,
            store: me.record['getReceiptStore'],
            ePost: me.ePost
        });

        return Ext.create('Ext.container.Container', {
            style: 'margin-top: 15px',
            items: [ me.documentGrid ]
        });
    }

});

