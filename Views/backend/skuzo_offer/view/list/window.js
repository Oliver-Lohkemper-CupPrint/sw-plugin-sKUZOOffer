
/**
 * Shopware UI - Offer list main window.
 */
//{namespace name=backend/sKUZOOffer/view/offer}
Ext.define('Shopware.apps.sKUZOOffer.view.list.Window', {
    /**
     * Define that the order main window is an extension of the enlight application window
     * @string
     */
    extend:'Enlight.app.Window',
    /**
     * Set base css class prefix and module individual css class for css styling
     * @string
     */
    cls:Ext.baseCSSPrefix + 'offer-detail-window',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.offer-detail-window',
    /**
     * Set no border for the window
     * @boolean
     */
    border:false,
    /**
     * True to automatically show the component upon creation.
     * @boolean
     */
    autoShow:true,
    /**
     * Set fit layout for the window
     * @string
     */
    layout:'fit',
    /**
     * Define window width
     * @integer
     */
    width:1400,
    /**
     * Define window height
     * @integer
     */
    height:'85%',
    /**
     * A flag which causes the object to attempt to restore the state of internal properties from a saved state on startup.
     */
    stateful:true,
    /**
     * The unique id for this object to use for state management purposes.
     */
    stateId:'shopware-offer-detail-window',
    /**
     * Display no footer button for the detail window
     * @boolean
     */
    footerButton:false,
    /**
     * Contains all snippets for this component
     */
    snippets: {
        title: '{s name=tab/title}Offer details:{/s}',
        detail: '{s name=tab/detail}Details{/s}',
        communication: '{s name=tab/communication}Communication{/s}',
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

        //add the order list grid panel and set the store
        me.items = [ me.createTabPanel() ];
        me.title = me.snippets.title + ' ' + me.record.get('number');
        me.callParent(arguments);
    },

    /**
     * Creates the tab panel for the detail page.
     * @return Ext.tab.Panel
     */
    createTabPanel: function() {
        var me = this;

        return Ext.create('Ext.tab.Panel', {
            name: 'main-tab',
            items: [
                Ext.create('Shopware.apps.sKUZOOffer.view.list.CreateOfferWindow', {
                    title: me.snippets.detail,
                    offerId: me.offerId,
                    record: me.record,
                    taxStore: me.taxStore,
                    positionStore: me.positionStore,
                    emailPreview: me.emailPreview,
                    ePost: me.ePost,
                    showPurchasePrice: me.showPurchasePrice
                    {if $swVersion4},variantStore: me.variantStore{/if}
                }), Ext.create('Shopware.apps.sKUZOOffer.view.list.Communication',{
                    title: me.snippets.communication,
                    record: me.record
                })
            ]
        });
    }
});

