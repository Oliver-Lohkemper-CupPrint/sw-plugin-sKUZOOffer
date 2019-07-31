{namespace name="frontend/sKUZOOffer/buy/index"}
{extends file="frontend/plugins/SwagCustomizing/detail.tpl"}

{block name="frontend_detail_data_block_price_include"}
{/block}


{* Product price info *}

{block name='frontend_detail_data_price_info'}
{/block}


{* Article price *}

{block name='frontend_detail_data_price'}
{/block}

{* Default price *}

{block name='frontend_detail_data_price_configurator'}
{/block}


{* Discount price *}

{block name='frontend_detail_data_pseudo_price'}
{/block}


{* Unit price *}

{block name='frontend_detail_data_price'}
{/block}


{* Tax information *}

{block name='frontend_detail_data_tax'}
{/block}

{block name="frontend_detail_data_delivery"}
{/block}

{block name='frontend_index_content' append}
    {if $requiredError}
    <div class="detail-error content listing--content">
        {assign var="sErrorMessages" value=array("please select *required Field")}
        {include file="frontend/_includes/messages.tpl" type="error" content=$sErrorMessages.0}
    </div>
    {/if}
{/block}


{block name="frontend_detail_buy" append}
    <form name="sAddToOffer" method="post" action="{url controller=sKUZOOffer action=saveCustomDetail}" >
        <input type="hidden" name="quantityId" id="quantityId" value="{$quantityId}"/>
        <input type="hidden" name="offerId" id="offerId" value="{$offerId}"/>
        <button type="submit" class="btn is--primary is--icon-right is--large right main--actions">{s namespace='frontend/sKUZOOffer/buy/index' name='save_custom_data'}Save{/s}<i class="icon--arrow-right"></i></button>
    </form>
{/block}




{block name="frontend_detail_buy_button"}

{/block}
