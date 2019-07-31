;(function($) {
    'use strict'

    $.plugin('skuzoOfferSwAddArticle', {

        /** @object Default plugin configuration */
        defaults: {

            /** @string selector for the node on which the plugin is registered */
            swAddArticlePluginObjectSelector: '*[data-add-article="true"]',

            /** @string swAddArticlePluginName */
            swAddArticlePluginName: 'plugin_swAddArticle',

            /** @string swagCustomProductsSwAddArticlePluginName */
            swagCustomProductsSwAddArticlePluginName: 'plugin_swagCustomProductsSwAddArticle',

            /** @string offerButtonSelector */
            offerButtonSelector: '#offerButton',

            /** @string openOfferSelector */
            openOfferSelector: '#sOpenOffer',

            /** @string offerTargetUrlSelector */
            offerTargetUrlSelector: '#offerTargetUrl',

            /** @string ajaxCartSelector */
            ajaxCartSelector: '.container--ajax-cart'
        },

        /**
         * Initializes the plugin, sets up the necessary elements,
         * registers the event listener.
         */
        init: function() {
            var me = this

            me.applyDataAttributes()

            me.offerButton = $(me.opts.offerButtonSelector);

            me.swAddArticlePlugin = $(me.opts.swAddArticlePluginObjectSelector).data(me.opts.swAddArticlePluginName);
            me.swCustomAddArticlePlugin = $(me.opts.swAddArticlePluginObjectSelector).data(me.opts.swagCustomProductsSwAddArticlePluginName);

            me.originalSendFormFunction = $.proxy(me.swAddArticlePlugin.sendSerializedForm, me.swAddArticlePlugin);

            if(typeof me.swCustomAddArticlePlugin === 'object') {
                me.originalSendFormFunction = $.proxy(me.swCustomAddArticlePlugin.sendSerializedForm, me.swCustomAddArticlePlugin);
            }

            $.subscribe('plugin/swAddArticle/onAddArticle', $.proxy(me.onAddArticle, me));

            // register own click event
            me._on(
                me.offerButton,
                'click',
                $.proxy(me.addToOffer, me)
            );
        },

        onAddArticle: function(event, result) {
            var me = this;
            var targetUrl = me.opts.offerTargetUrl;
            if(targetUrl && targetUrl.length>0) {
                $(me.opts.ajaxCartSelector).removeClass('is--open');
                delete me.opts.offerTargetUrl;
                window.location.href=targetUrl;
            }
        },

        addToOffer: function(event) {
            var me = this,
                parentArguments = arguments;

            event.preventDefault()

            me.opts.showModal = false;
            me.opts.offerTargetUrl = $(me.opts.offerTargetUrlSelector).val();
            $(me.opts.openOfferSelector).val(true);
            me.originalSendFormFunction.apply(me, parentArguments)
        },
    })

    // Plugin starter
    $(function() {
        $.subscribe('plugin/swAjaxVariant/onRequestData', function() {
            $('*[data-add-article="true"]').skuzoOfferSwAddArticle()
        })

        $('*[data-add-article="true"]').skuzoOfferSwAddArticle()
    })

})(jQuery)