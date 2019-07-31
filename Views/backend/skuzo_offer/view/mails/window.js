/**
 * window for sending mail
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.mails.Window', {
extend:'Enlight.app.Window',
cls: Ext.baseCSSPrefix + 'offer-mail-window',
alias:'widget.offer-mail-window',
width: 600,
height:'90%',
autoScroll: true,
layout: 'fit',
title: '{s name=mail/title}Send an email to the customer{/s}',

    initComponent: function () {
        var me = this;

        me.form = Ext.create('Shopware.apps.sKUZOOffer.view.mails.Form');

        me.items = me.form;

        if (me.mail instanceof Ext.data.Model) {
            me.form.loadRecord(me.mail);
        }

        me.callParent(arguments);
    }

});

