{namespace name="frontend/sKUZOOffer/checkout/cart"}
{extends file="parent:frontend/checkout/cart.tpl"}
{block name='frontend_checkout_cart_error_messages' append}
    {if $amountError}
        {include file="parent:frontend/_includes/messages.tpl" type="error" content="{s name="AmountErrorMessage"}Um ein Angebot anzufordern ist der Betrag zu gering{/s}"}
    {/if}
{/block}

