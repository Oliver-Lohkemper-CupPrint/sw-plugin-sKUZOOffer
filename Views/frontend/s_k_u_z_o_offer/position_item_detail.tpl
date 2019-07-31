{assign var="isCustomDataAvailable" value="0"}

{if $article.customizing}
    {foreach $article.customizing as $customArticle}
        {if $customArticle.quantityID == $var}
            {assign var="isCustomDataAvailable" value="1"}
            {block name="frontend_account_position_item_detail_table_row"}
                <div class="panel--tr">

                    {block name="frontend_account_position_item_info"}
                        <div class="panel--td order--info column--name" style="width: 40%;">

                            {* Name *}
                            {block name="frontend_account_order_item_name"}
                                <p class="order--name is--strong">
                                        {$customArticle.name}
                                </p>
                            {/block}
                        </div>
                    {/block}

                    {* Offer item quantity *}
                    {block name='frontend_account_position_item_quantity'}
                        <div class="panel--td order--quantity column--quantity">

                            {block name='frontend_account_position_item_quantity_value'}
                                <div class="column--value">{$customArticle.quantity} </div>
                            {/block}
                        </div>
                    {/block}


                    {* Offer item tax *}
                    {block name='frontend_account_position_item_tax'}
                        <div class="panel--td order--quantity column--tax"  style="width: 10%;">

                            {block name='frontend_account_order_item_tax_value'}
                                <div class="column--value">
                                    {if $customArticle.tax_rate}
                                        {$customArticle.tax_rate} %
                                    {else}
                                        {s name="OrderItemInfoFree"}{/s}
                                    {/if}
                                </div>
                            {/block}
                        </div>
                    {/block}

                    {* Offer item price *}
                    {block name='frontend_account_position_item_price'}
                        <div class="panel--td order--price column--price">

                            {block name='frontend_account_order_item_price_value'}
                                <div class="column--value">
                                    {if $customArticle.price}
                                        {$customArticle.price|currency} {$offerPosition.currency_html}
                                    {else}
                                        {s name="OrderItemInfoFree"}{/s}
                                    {/if}
                                </div>
                            {/block}
                        </div>
                    {/block}

                    {* Offer item total amount *}
                    {block name='frontend_account_position_item_amount'}
                        <div class="panel--td order--amount column--total">

                            {block name='frontend_account_order_item_amount_value'}
                                <div class="column--value">
                                    {if $customArticle.price && $customArticle.quantity}
                                        {($customArticle.price * $customArticle.quantity)|currency} {$offerPosition.currency_html}
                                    {else}
                                        {s name="OrderItemInfoFree"}{/s}
                                    {/if}
                                </div>
                            {/block}
                        </div>
                    {/block}
                </div>
            {/block}
        {/if}
    {/foreach}
{*{else}
    {assign var="isCustomDataAvailable" value="1"}
    $article.validForAccept = false;

    {block name="frontend_account_order_item_detail_table_row"}
        <div class="panel--tr">

            {block name="frontend_account_order_item_info"}
                <div class="panel--td order--info column--name" style="width: 40%;">

                    *}{* Name *}{*
                    {block name="frontend_account_order_item_name"}

                        {s name="NoCustomInfoMsg"}There are no Custom Info Available{/s}
                        *}{* for custom Product *}{*

                    {/block}
                </div>
            {/block}
        </div>
    {/block}*}
{/if}

{if !$isCustomDataAvailable}
{block name="frontend_account_order_item_detail_table_row"}
    <div class="panel--tr">

        {block name="frontend_account_order_item_info"}
            <div class="panel--td order--info column--name" style="width: 40%;">

                {* Notice *}
                {block name="frontend_account_order_item_name"}
                    {s name="NoCustomInfoMsg"}There are no Custom Info Available {/s}
                {/block}
            </div>
        {/block}
    </div>
{/block}
{/if}