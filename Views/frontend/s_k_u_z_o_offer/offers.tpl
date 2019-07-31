{namespace name="frontend/sKUZOOffer/account/offers"}
{extends file='frontend/account/orders.tpl'}

{* Breadcrumb *}
{block name='frontend_index_start'}
    {assign var='sBreadcrumb' value=[['name'=>"{s name='AccountTitle'}Mein Konto{/s}", 'link' =>{url action='index' controller="account"}]]}
    {$sBreadcrumb[] = ['name'=>"{s name='MyOffersTitle'}MyOffer{/s}", 'link'=>{url}]}
{/block}

{* Main content *}
{block name="frontend_index_content"}
    {if !$theme}
        <style>
            .table .table_row {
                display: table;
            }
        </style>
    {/if}

    <div class="content account--content{if !$theme} account table grid_16{/if}">

        {* Welcome text *}
        {block name="frontend_account_orders_welcome"}
            <div class="account--welcome panel{if !$theme} cat_text{/if}">
                {if !$theme}<div class="inner_container">{/if}
                    {block name="frontend_account_orders_welcome_headline"}
                        <h1 class="panel--title">{s name="OffersHeader"}My Offers{/s}</h1>
                    {/block}

                    {block name="frontend_account_orders_welcome_content"}
                        <div class="panel--body is--wide">
                            <p>{s name="OffersWelcomeText"}Here You can see all offers{/s}</p>
                        </div>
                    {/block}
                {if !$theme}</div>{/if}
            </div>
        {/block}

        {if !$sOpenOrders}
            {block name="frontend_account_orders_info_empty"}
                <div class="account--no-orders-info">
                    {include file="frontend/_includes/messages.tpl" type="warning" content="{s name='OffersInfoEmpty'}There is no offer Available{/s}"}
                </div>
            {/block}
        {elseif $erroCustomProduct}
            {block name="frontend_account_orders_info_empty"}
                <div class="account--no-orders-info">
                    {include file="frontend/_includes/messages.tpl" type="warning" content="{s name='ErrorCustomProduct'}There is no articleID or OfferID passed{/s}"}
                </div>
            {/block}
        {else}
            {* Orders overview *}
            {block name="frontend_account_orders_overview"}
                <div class="account--orders-overview {if !$theme} orderoverview_active{/if} panel is--rounded">

                    {block name="frontend_account_orders_table"}
                    <div class="panel--table{if !$theme} table grid_16{/if}">
                        {block name="frontend_account_orders_table_head"}
                            <div class="orders--table-header panel--tr{if !$theme} table_head{/if}">

                                {block name="frontend_account_orders_table_head_date"}
                                    <div class="panel--th column--date{if !$theme} grid_3{/if}">{s name="OfferColumnDate"}Date{/s}</div>
                                {/block}

                                {block name="frontend_account_orders_table_head_id"}
                                    <div class="panel--th column--id{if !$theme} grid_3{/if}">{s name="OfferColumnId"}OfferNumber{/s}</div>
                                {/block}

                                {block name="frontend_account_orders_table_head_dispatch"}
                                    <div class="panel--th column--dispatch{if !$theme} grid_3{/if}">{s name="OfferColumnDispatch"}Dispatch{/s}</div>
                                {/block}

                                {block name="frontend_account_orders_table_head_status"}
                                    <div class="panel--th column--status{if !$theme} grid_3{/if}">{s name="OfferColumnStatus"}Status{/s}</div>
                                {/block}

                                {block name="frontend_account_orders_table_head_actions"}
                                    <div class="panel--th column--actions is--align-center{if !$theme} grid_3 right{/if}">{s name="OfferColumnActions"}Actions{/s}</div>
                                {/block}
                                </div>
                        {/block}

                        {block name="frontend_account_order_item_overview"}
                            {foreach $sOpenOrders as $offerPosition}
                                {include file="frontend/s_k_u_z_o_offer/offer_item.tpl"}
                            {/foreach}
                        {/block}

                        {/block}

                        {block name="frontend_account_orders_actions_paging"}
                            {if !$theme}
                                <div class="space">&nbsp;</div>
                            {/if}
                            <div class="account--paging panel--paging">
                                {if $sPages.previous}
                                    <a href="{$sPages.previous}" class="btn paging--link paging--prev">
                                        <i class="icon--arrow-left"></i>
                                    </a>
                                {/if}

                                {foreach $sPages.numbers as $page}
                                    {if $page.markup}
                                        <a class="paging--link is--active">{$page.value}</a>
                                    {else}
                                        <a href="{$page.link}" class="paging--link">{$page.value}</a>
                                    {/if}
                                {/foreach}

                                {if $sPages.next}
                                    <a href="{$sPages.next}" class="paging--link paging--next">
                                        <i class="icon--arrow-right"></i>
                                    </a>
                                {/if}

                                {block name='frontend_account_orders_actions_paging_count'}
                                    <div class="paging--display">
                                        {s name="ListingTextSite" namespace="frontend/listing/listing_actions"}{/s}
                                        <span class="is--bold">{if $sPage}{$sPage}{else}1{/if}</span>
                                        {s name="ListingTextFrom" namespace="frontend/listing/listing_actions"}{/s}
                                        <span class="is--bold">{$sNumberPages}</span>
                                    </div>
                                {/block}
                            </div>
                        {/block}
                        <!--- Vor $theme</div> -->
                        {if !$theme}</div>{/if}
                    <!-- Marker Hallo -->
                </div>
            {/block}
        {/if}
    </div>


{/block}