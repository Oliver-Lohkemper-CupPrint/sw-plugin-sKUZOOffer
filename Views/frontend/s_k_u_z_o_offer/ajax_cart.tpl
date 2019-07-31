{namespace name="frontend/sKUZOOffer/checkout/ajax_cart"}
{block name='frontend_checkout_ajax_cart_open_basket' prepend}
    {if $sInquiry}
        <a href="{$sInquiryLink}" class="btn button--open-basket is--icon-right" title="{"{s name='AjaxCartLinkBasket'}Ask for Offer{/s}"|escape}">
            <i class="icon--arrow-right"></i>
            {s name='AjaxCartLinkBasket'}Ask for Offer{/s}
        </a>
    {/if}
{/block}
