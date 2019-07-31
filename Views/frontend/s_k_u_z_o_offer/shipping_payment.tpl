{namespace name="frontend/sKUZOOffer/checkout/shipping_payment"}
{extends file="frontend/checkout/shipping_payment.tpl"}

{block name='frontend_register_steps'}
{/block}

{* Main content *}
{block name="frontend_index_content"}
    <div class="content content--confirm product--table" data-ajax-shipping-payment="true">
        {include file="frontend/s_k_u_z_o_offer/shipping_payment_core.tpl"}
    </div>
{/block}