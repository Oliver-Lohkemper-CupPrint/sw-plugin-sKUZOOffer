{extends file="parent:frontend/checkout/finish.tpl"}

{block name='frontend_checkout_cart_item_rebate_badge'}
    {if !$sBasketItem.swagCustomProductsMode || $sBasketItem.swagCustomProductsMode==$IS_CUSTOM_PRODUCT_MAIN}
        {$smarty.block.parent}
    {else}
        <div class="panel--td column--image">
            <div class="table--media">
            </div>
        </div>
    {/if}
{/block}