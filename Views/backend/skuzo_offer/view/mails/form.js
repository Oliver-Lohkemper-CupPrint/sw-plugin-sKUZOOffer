/**
 * Form for mail preview
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.mails.Form', {
extend:'Ext.form.Panel',
alias:'widget.offer-mail-form',
cls: Ext.baseCSSPrefix + 'batch-mail-panel',
layout: {
        type: 'vbox',
        align: 'stretch'
    },
bodyPadding: 10,
border: false,

    snippets: {
        subject: '{s name=mail/subject}Subject{/s}',
        to: '{s name=mail/to}To{/s}',
        button: '{s name=mail/button}Send mail{/s}'
    },

   initComponent:function () {
        var me = this;

        me.items = me.getFormItems();

        me.dockedItems = me.getToolbar();

        me.callParent(arguments);
    },

    getToolbar: function () {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            items: [
                '->',
                {
                    xtype: 'button',
                    cls: 'primary',
                    text: me.snippets.button,
                    handler: function () {
                        me.fireEvent('sendMail', me);
                    }
                }
            ]
        });
    },

    getFormItems: function() {
        var me = this;

        return [
            {
                xtype: 'textfield',
                name: 'to',
                fieldLabel: me.snippets.to
            },
            {
                xtype: 'textfield',
                name: 'subject',
                fieldLabel: me.snippets.subject
            },
            {
                xtype: 'textarea',
                name: 'content',
                minHeight: 90,
                flex: 1
            }
        ];
    }

});
