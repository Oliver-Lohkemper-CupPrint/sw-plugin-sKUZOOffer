{block name="frontend_account_order_item_detail_table_row"}
    <div class="panel--tr" {if !$theme}table_row{/if}>

        {block name="frontend_account_order_item_info"}
            {block name="frontend_account_order_item_name"}
                {* Name for custom Product Detail *}
                <div class="panel--td order--info column--name" style="width: 40%;">
                    {s name="customArticleGroupName"}customProcuct {$var} {/s}
                </div>

                {* Button for custom Product Detail *}
               {block name="frontend_account_custom_position_item_actions"}
                    <div class="panel--td order--quantity column--quantity">
                        <div class="column--value">
                        <a href="#articleDetail{$article.id}{$var}"
                           title="{"{s name="positionDetailActionSlide"}Details{/s}"|escape} {$article.id}"
                           class="btn is--small"
                           data-collapse-panel="true"
                           data-collapseTarget="#articleDetail{$article.id}{$var}">
                            {s name="positionDetailActionSlide"} Details {/s}
                        </a>
                        </div>
                    </div>

                   {* Button for Adding/Editing custom Product Detail *}
                    <div class="panel--td order--amount column--total">
                        <div class="column--value">
                        <form method="post" action="{url controller='sKUZOOffer' action='editCustomProduct'}" id="custom-info-btn">
                            <input name="quantityId" type="hidden" value="{$var|escape}" />
                            <input name="customPosition" type="hidden" value="{$article.customPosition|escape}" />
                            <input name="articleId" type="hidden" value="{$article.articleId|escape}" />
                            <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                            {foreach $article.customizing as $customArticle}
                                {if $customArticle.quantityID == $var}
                                    {assign var="isCustomInfoAvailable" value="1"}
                                    {break}
                                {/if}
                            {/foreach}
                            {if $isCustomInfoAvailable == "1"}
                                <input type="submit" class="btn" value="{s name='OfferLinkEditCustomInfo'}Edit CustomInfo{/s}" />
                            {else}
                                {assign var='validForAccept' value='0'}
                                <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkAddCustomInfo'}Add CustomInfo{/s}" />
                            {/if}
                        </form>
                        </div>
                    </div>
                {/block}
            {/block}
        {/block}
    </div>
{/block}

{*custom Package Block*}
{block name="frontend_account_position_item_detail"}

    {* Custom Package details *}
    <div id="articleDetail{$article.id}{$var}" class="order--details panel--table">
        {block name="frontend_account_position_custom_item_detail_table_rows"}
            {include file="frontend/s_k_u_z_o_offer/position_item_detail.tpl"}
        {/block}
    </div>
{/block}

