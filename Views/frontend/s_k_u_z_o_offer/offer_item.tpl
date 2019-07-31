{namespace name="frontend/sKUZOOffer/account/offers"}
{block name="frontend_account_order_item_overview_row"}
    <div class="order--item panel--tr{if !$theme} table_row{/if}">
        {* Offer date *}
        {block name="frontend_account_order_item_date"}
            <div class="order--date panel--td column--date{if !$theme} grid_3{/if}">

                {if $theme}
                    {block name="frontend_account_order_item_date_label"}
                        <div class="column--label">
                            {s name="OfferColumnDate" namespace="frontend/sKUZOOffer/account/offers"}Datum{/s}:
                        </div>
                    {/block}
                {/if}

                {block name="frontend_account_order_item_date_value"}
                    <div class="column--value">
                        {$offerPosition.offertime|date}
                    </div>
                {/block}
            </div>
        {/block}

        {* Offer id *}
        {block name="frontend_account_order_item_number"}
            <div class="order--number panel--td column--id is--bold {if !$theme}grid_3 bold{/if}">

                {if $theme}
                    {block name="frontend_account_order_item_number_label"}
                        <div class="column--label">
                            {s name="OfferColumnId" namespace="frontend/sKUZOOffer/account/offers"}Angebot-Nr.{/s}:
                        </div>
                    {/block}
                {/if}

                {block name="frontend_account_order_item_number_value"}
                    <div class="column--value">
                        {$offerPosition.offerNumber}
                    </div>
                {/block}
            </div>
        {/block}

        {* Dispatch type *}
        {block name="frontend_account_order_item_dispatch"}
            <div class="order--dispatch panel--td column--dispatch{if !$theme} grid_3{/if}">
                {if $theme}
                    {block name="frontend_account_order_item_dispatch_label"}
                        <div class="column--label">
                            {s name="OrderColumnDispatch" namespace="frontend/sKUZOOffer/account/offers"}Versandart{/s}:
                        </div>
                    {/block}
                {/if}

                {block name="frontend_account_order_item_dispatch_value"}
                    <div class="column--value">
                        {if $offerPosition.dispatch.name}
                            {$offerPosition.dispatch.name}
                        {else}
                            {s name="OfferInfoNoDispatch"}{/s}
                        {/if}
                    </div>
                {/block}
            </div>
        {/block}

        {* Offer status *}
        {block name="frontend_account_order_item_status"}
            <div class="order--status panel--td column--status{if !$theme} grid_3{/if}">
                {if $theme}
                    {block name="frontend_account_order_item_status_label"}
                        <div class="column--label">
                            {s name="OfferColumnStatus" namespace="frontend/sKUZOOffer/account/offers"}Bestellstatus{/s}:
                        </div>
                    {/block}
                {/if}

                {block name="frontend_account_order_item_status_value"}
                    <div class="column--value">
                        <span class="order--status-icon status--{$offerPosition.status}">&nbsp;</span>
                        {if $offerPosition.status==1}
                            {s name="OfferOpen"}Open{/s}
                        {elseif $offerPosition.status==2}
                            {s name="OfferProcessed"}Processed{/s}
                        {elseif $offerPosition.status==3}
                            {s name="OfferSent"}Sent{/s}
                        {elseif $offerPosition.status==4}
                            {s name="OfferAccepted"}Accepted{/s}
                        {elseif $offerPosition.status==5}
                            {s name="OfferConfirmed"}Confirmed{/s}
                        {/if}
                    </div>
                {/block}
            </div>
        {/block}

        {* Offer actions *}
        {block name="frontend_account_order_item_actions"}
            <div class="order--actions panel--td column--actions{if !$theme} grid_3 right{/if}">
                <a href="#order{$offerPosition.offerNumber}"
                   title="{"{s name="OfferActionSlide"}Details{/s}"|escape} {$offerPosition.offerNumber}"
                   class="btn is--small{if !$theme} button-middle small orderdetails{/if}"
                   data-collapse-panel="true"
                   data-collapseTarget="#order{$offerPosition.offerNumber}"
                   {if !$theme}rel="order{$offerPosition.offerNumber}"{/if}>
                    {s name="OfferActionSlide"}Details{/s}
                </a>
            </div>
        {/block}
    </div>
{/block}

{* Offer details *}
{block name="frontend_account_order_item_detail"}

    {* Offer details *}
    <div id="order{$offerPosition.offerNumber}" class="{if !$theme}displaynone {/if}order--details panel--table{if !$theme} table grid_16{/if}">

        {block name="frontend_account_order_item_detail_table"}

            {block name="frontend_account_order_item_detail_id"}
                <input type="hidden" name="sAddAccessories" value="{$offerNumber|escape}" />
            {/block}

            {block name="frontend_account_order_item_detail_table_head"}
                <div class="orders--table-header panel--tr is--secondary{if !$theme} table_head{/if}">

                    {block name="frontend_account_order_item_detail_table_head_name"}
                        <div class="panel--th column--name{if !$theme} grid_7{/if}">{s name="OfferItemColumnName"}Article{/s}</div>
                    {/block}

                    {block name="frontend_account_order_item_detail_table_head_quantity"}
                        <div class="panel--th column--quantity is--align-center{if !$theme} grid_2{/if}">{s name="OfferItemColumnQuantity"}Quantity{/s}</div>
                    {/block}

                    {block name="frontend_account_order_item_detail_table_head_tax"}
                        <div class="panel--th column--tax is--align-center{if !$theme} grid_1{/if}">{s name="OfferItemColumnTax"}Tax{/s}</div>
                    {/block}

                    {block name="frontend_account_order_item_detail_table_head_price"}
                        <div class="panel--th column--price is--align-right{if !$theme} grid_2{/if}">{s name="OfferItemColumnPrice"}Price{/s}</div>
                    {/block}

                    {block name="frontend_account_order_item_detail_table_head_total"}
                        <div class="panel--th column--total is--align-right{if !$theme} grid_3 right{/if}">{s name="OfferItemColumnTotal"}Total{/s}</div>
                    {/block}
                </div>
            {/block}

            {block name="frontend_account_order_item_detail_table_rows"}
                {foreach $offerPosition.details as $article}
                    {if $article.modus != 4}

                        {block name="frontend_account_order_item_detail_table_row"}
                            <div class="panel--tr{if !$theme} table_row{/if}">

                                {block name="frontend_account_order_item_info"}
                                    <div class="panel--td order--info column--name{if !$theme} grid_7{/if}">

                                        {* Name *}
                                        {block name="frontend_account_order_item_name"}
                                            <p class="order--name is--strong{if !$theme} bold{/if}">
                                                {* Mode 10 = Bundle Product *}
                                                {if $article.modus == 10}
                                                    {s name="OrderItemInfoBundle"}{/s}
                                                {else}
                                                    {$article.name}
                                                {/if}

                                                {* for custom Product *}
                                                {if $article.customProduct && $article.modus == 0}

                                                    {block name="frontend_account_custom_position_item_actions"}

                                                        <a href="#article{$article.id}"
                                                           title="{"{s name="positionCustomInfoActionSlide"}CustomInfo{/s}"|escape} {$article.id}"
                                                           class="btn is--small"
                                                           data-collapse-panel="true"
                                                           data-collapseTarget="#article{$article.id}">
                                                            {s name="positionCustomInfoActionSlide"}CustomInfo{/s}
                                                        </a>



                                                    {/block}


                                                    {*<form method="post" action="{url controller='sKUZOOffer' action='editCustomProduct'}" id="custom-info-btn">
                                                       <input name="customPosition" type="hidden" value="{$article.customPosition|escape}" />
                                                       <input name="articleId" type="hidden" value="{$article.articleId|escape}" />
                                                       <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                                                       {if $article.customPosition}
                                                       <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkCustomInfoEdit'}edit{/s}" />
                                                       {else}
                                                       <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkCustomInfo'}Add CustomInfo{/s}" />
                                                       {/if}
                                                   </form>*}
                                                {/if}
                                            </p>
                                        {/block}
                                    </div>
                                {/block}

                                {* Offer item quantity *}
                                {block name='frontend_account_order_item_quantity'}
                                    <div class="panel--td order--quantity column--quantity{if !$theme} grid_2{/if}">

                                        {block name='frontend_account_order_item_quantity_label'}
                                            <div class="column--label">
                                                {s name="OfferColumnQuantity" namespace="frontend/s_k_u_z_o_offer/offers"}Anzahl{/s}
                                            </div>
                                        {/block}
                                        {block name='frontend_account_order_item_quantity_value'}
                                            <div class="column--value">{$article.quantity} {$article.packUnit}</div>
                                        {/block}

                                        {block name="frontend_account_order_item_unit"}
                                            <p class="order--name" style="font-size: 10px;width: 100px;line-height: 10px;">
                                                {if $article.unitName}
                                                    ({$article.totalUnit} {$article.unitName})
                                                {/if}
                                            </p>
                                        {/block}
                                        {block name="frontend_account_order_item_base_price"}
                                            <p class="order--name" style="font-size: 10px;width: 100px;line-height: 10px;">
                                                {if $article.pricePerUnit && $article.unitName}
                                                    {$article.pricePerUnit|currency}/{$article.referenceUnit} {$article.unitName}
                                                {/if}
                                            </p>
                                        {/block}


                                    </div>
                                {/block}


                                {* Offer item tax *}
                                {block name='frontend_account_order_item_tax'}
                                    <div class="panel--td order--quantity column--tax{if !$theme} grid_1{/if}">


                                        {block name='frontend_account_order_item_tax_label'}
                                            <div class="column--label">
                                                {s name="OfferColumnTax" namespace="frontend/s_k_u_z_o_offer/offers"}MwSt.{/s}
                                            </div>
                                        {/block}

                                        {block name='frontend_account_order_item_tax_value'}
                                            <div class="column--value">
                                                {if $article.tax_rate}
                                                    {$article.tax_rate} %
                                                {else}
                                                    {s name="OrderItemInfoFree"}{/s}
                                                {/if}
                                            </div>
                                        {/block}
                                    </div>
                                {/block}

                                {* Offer item price *}
                                {block name='frontend_account_order_item_price'}
                                    <div class="panel--td order--price column--price{if !$theme} grid_2{/if}">

                                        {block name='frontend_account_order_item_unit_price_label'}
                                            <div class="column--label">
                                                {s name="OfferColumnUnitPrice" namespace="frontend/s_k_u_z_o_offer/offers"}Stückpreis{/s}
                                            </div>
                                        {/block}

                                        {block name='frontend_account_order_item_price_value'}
                                            <div class="column--value">
                                                {if $article.price}
                                                    {$article.price|currency} {$offerPosition.currency_html}
                                                {else}
                                                    {s name="OrderItemInfoFree"}{/s}
                                                {/if}
                                            </div>
                                        {/block}
                                    </div>
                                {/block}

                                {* Offer item total amount *}
                                {block name='frontend_account_order_item_amount'}
                                    <div class="panel--td order--amount column--total{if !$theme} grid_3 right{/if}">

                                        {block name='frontend_account_order_item_amount_label'}
                                            <div class="column--label">
                                                {s name="OfferColumnAmount" namespace="frontend/s_k_u_z_o_offer/offers"}Summe{/s}
                                            </div>
                                        {/block}
                                        {block name='frontend_account_order_item_amount_value'}
                                            <div class="column--value">
                                                {if $article.price && $article.quantity}
                                                    {($article.price * $article.quantity)|currency} {$offerPosition.currency_html}
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

                    {*custom Package Block*}
                    {block name="frontend_account_position_item"}
                        {if $theme}
                            {* Custom Package details *}
                            <div id="article{$article.id}" class="order--details panel--table">
                                {block name="frontend_account_position_custom_item_table_rows"}
                                    {if $article.quantity > 1}
                                        {* Button for Adding/Editing Default custom Product Detail *}
                                        <div class="order--repeat panel--tr">
                                            <form method="post" action="{url controller='sKUZOOffer' action='editCustomProduct'}" id="custom-info-btn">
                                                <input name="quantityId" type="hidden" value="0" />
                                                <input name="customPosition" type="hidden" value="{$article.customPosition|escape}" />
                                                <input name="articleId" type="hidden" value="{$article.articleId|escape}" />
                                                <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                                                <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkAddDefaultCustomInfo'}Add DefaultCustomInfo{/s}" />

                                            </form>
                                        </div>
                                    {/if}
                                    {for $var=1 to $article.quantity}
                                        <div class="panel--tr is--odd">
                                            {include file="frontend/s_k_u_z_o_offer/position_item.tpl"}
                                        </div>
                                    {/for}
                                {/block}

                            </div>
                        {/if}
                    {/block}
                {/foreach}
            {/block}



            <div class="panel--tr is--odd{if !$theme} table_row{/if}">

                {block name="frontend_account_order_item_detail_info_labels"}
                    <div class="panel--td column--info-labels{if !$theme} grid_3{/if}">
                        {* Offer date label *}
                        {block name="frontend_account_order_item_label_date"}
                            <p class="is--strong">{s name="OfferItemColumnDate"}Date{/s}</p>
                        {/block}

                        {* Offer number label *}
                        {block name="frontend_account_order_item_label_ordernumber"}
                            <p class="is--strong">{s name="OfferItemColumnId"}OfferNumber{/s}</p>
                        {/block}

                        {* Shipping method label  *}
                        {block name="frontend_account_order_item_label_dispatch"}
                            {if $offerPosition.dispatch}
                                <p class="is--strong">{s name="OfferItemColumnDispatch"}Dispatch{/s}</p>
                            {/if}
                        {/block}

                        {* Payment method label  *}
                        {block name="frontend_account_order_item_label_payment"}
                            {if $offerPosition.payment}
                                <p class="is--strong">{s name="OfferItemColumnPayment"}Payment{/s}</p>
                            {/if}
                        {/block}


                    </div>
                {/block}

                {block name="frontend_account_order_item_detail_info_data"}
                    <div class="panel--td column--info-data{if !$theme} grid_3{/if}">
                        {* Offer date *}
                        {block name='frontend_account_order_item_date'}
                            <p>{$offerPosition.offertime|date}</p>
                        {/block}

                        {* Offer number *}
                        {block name='frontend_account_order_item_ordernumber'}
                            <p>{$offerPosition.offerNumber}</p>
                        {/block}

                        {* Shipping method *}
                        {block name='frontend_account_order_item_dispatch'}
                            {if $offerPosition.dispatch}
                                <p>{$offerPosition.dispatch.name}</p>
                            {/if}
                        {/block}

                        {* Payment method *}
                        {block name='frontend_account_order_item_payment'}
                            {if $offerPosition.payment}
                                <p>{$offerPosition.payment.description}</p>
                            {/if}
                        {/block}



                    </div>
                {/block}

                {block name="frontend_account_order_item_detail_summary_labels"}
                    {if $offerPosition.showNet}
                        <div class="panel--td column--summary-labels{if !$theme} grid_3 prefix_3{/if}">

                            {block name="frontend_account_order_item_detail_shipping_costs"}

                                <p class="is--strong">{s name="OfferItemNetTotal"}NetTotal{/s}</p>
                                {* Shipping costs label *}
                                {block name="frontend_account_order_item_detail_shipping_costs_label"}
                                    <p class="is--strong">{s name="OfferItemNetShippingcosts"}Versandkosten Netto{/s}</p>
                                {/block}
                                <p class="is--bold">{s name="OfferItemTotalPriceWithoutTax"}priceWithoutTax{/s}</p>

                                {foreach from=$offerPosition.taxRate item=taxRate key=key}
                                    {if $taxRate}
                                        <p class="is--strong">{s name="DocumentIndexTaxs"}zzgl. {$key} % MwSt:{/s}</p>
                                    {/if}
                                {/foreach}
                                <p class="is--strong">{s name="OfferItemTotal"}Total{/s}</p>
                            {/block}
                        </div>
                    {else}
                        <div class="panel--td column--summary-labels{if !$theme} grid_3 prefix_3{/if}">

                            {block name="frontend_account_order_item_detail_shipping_costs"}

                                <p class="is--strong">{s name="OfferItemBruttoTotal"}Summe{/s}</p>
                                {* Shipping costs label *}
                                {block name="frontend_account_order_item_detail_shipping_costs_label"}
                                    <p class="is--strong">{s name="OfferItemShippingcosts"}Shipping{/s}</p>
                                {/block}
                                <p class="is--bold">{s name="OfferItemTotal"}Total{/s}</p>
                                {foreach from=$offerPosition.taxRate item=taxRate key=key}
                                    {if $taxRate}
                                        <p class="is--strong">{s name="DocumentIndexIncludedTaxs"}inkl. {$key} % MwSt:{/s}</p>
                                    {/if}
                                {/foreach}
                                <p class="is--strong">{s name="OfferItemTotalPriceWithoutTax"}priceWithoutTax{/s}</p>

                            {/block}
                        </div>
                    {/if}
                {/block}

                {block name="frontend_account_order_item_detail_summary_data"}
                    {if $offerPosition.showNet}
                        <div class="panel--td column--summary-data{if !$theme} grid_3 right{/if}">
                            {block name="frontend_acccount_order_item_amount"}
                                <p>{$offerPosition.discount_amount_net|currency} {$offerPosition.currency_html}</p>
                                {* Shopping costs *}
                                {block name="frontend_account_order_item_shippingamount"}
                                    <p>{$offerPosition.invoice_shipping_net|currency} {$offerPosition.currency_html}</p>
                                {/block}
                                <p class="is--bold">{($offerPosition.discount_amount_net+$offerPosition.invoice_shipping_net)|currency} {$offerPosition.currency_html}</p>

                                {foreach from=$offerPosition.taxRate item=taxRate key=key}
                                    {if $taxRate}
                                        <p>{$taxRate|currency}{$offerPosition.currency_html}</p>
                                    {/if}
                                {/foreach}
                                <p>{($offerPosition.invoiceAmountNumeric+$offerPosition.invoiceShippingNumeric)|currency} {$offerPosition.currency_html}</p>
                            {/block}
                        </div>
                    {else}
                        <div class="panel--td column--summary-data{if !$theme} grid_3 right{/if}">
                            {block name="frontend_acccount_order_item_amount"}
                                <p>{$offerPosition.discount_amount|currency} {$offerPosition.currency_html}</p>
                                {* Shopping costs *}
                                {block name="frontend_account_order_item_shippingamount"}
                                    <p>{$offerPosition.invoice_shipping|currency} {$offerPosition.currency_html}</p>
                                {/block}
                                <p class="is--bold">{($offerPosition.discount_amount+$offerPosition.invoice_shipping)|currency} {$offerPosition.currency_html}</p>
                                {foreach from=$offerPosition.taxRate item=taxRate key=key}
                                    {if $taxRate}
                                        <p>{$taxRate|currency}{$offerPosition.currency_html}</p>
                                    {/if}
                                {/foreach}
                                <p>{($offerPosition.discount_amount_net+$offerPosition.invoice_shipping_net)|currency} {$offerPosition.currency_html}</p>
                            {/block}
                        </div>
                    {/if}
                {/block}
            </div>


            {* Repeat order *}
            {block name="frontend_account_order_item_repeat_order"}
                <div class="order--repeat panel--tr{if !$theme} table_row{/if}">
                    {foreach from=$offerPosition.documents item=document key=key}
                    <form method="post" action="{url controller='sKUZOOffer' action='downloadPdf' hash=$document.hash mediaId=$document.mediaID}" id="offer-download-btn"{if !$theme} class="grid_6"{/if}>
                        <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                        <input type="submit" class="btn is--primary is--small{if !$theme} button-middle large{/if}" value="{s name='OfferLinkDownload'}Download Offer{/s}" />
                    </form>
                    {/foreach}

                    <form method="post"{if !$theme} class="grid_9 right"{/if} action="{if $directPayment}{url controller='sKUZOOffer' action='payment'}{else}{url controller='sKUZOOffer' action='acceptOffer'}{/if}" id="offer-accept-btn">
                        <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                        {if $offerPosition.status != 4 && $offerPosition.status != 5 }
                            {if $offerPosition.validForAccept}
                                {block name='frontend_offer_confirm_agb_checkbox'}
                                    {if {config name='offerCheckAGB'}}
                                        <div class="agb-checkbox">
                                            <span class="block column--checkbox">
                                                <input type="checkbox" required="required" aria-required="true" id="sAGB_{$offerPosition.id}" name="sAGB"{if $sAGBChecked} checked="checked"{/if} />
                                            </span>
                                                        <span class="block column--label">
                                                <label for="sAGB_{$offerPosition.id}"{if $sAGBError} class="has--error"{/if}>{s name="ConfirmTerms"}Ich habe die AGB Ihres Shops gelesen und bin mit deren Geltung einverstanden.{/s}</label>
                                            </span>
                                        </div>
                                    {/if}
                                {/block}
                                {block name="frontend_account_order_item_repeat_button"}
                                    {if $directPayment}
                                        <input type="submit" class="btn is--primary is--small{if !$theme} button-right large{/if}" value="{s name='OfferDoPayment'}Accept Offer{/s}" />
                                    {else}
                                        <input type="submit" class="btn is--primary is--small{if !$theme} button-middle large{/if}" value="{s name='OfferLinkRepeat'}Accept Offer{/s}" />
                                    {/if}
                                {/block}
                            {else}
                                <div class="icon--warning"></div>
                                <div class="alert--content is--strong">
                                    <i class="icon--element icon--info"></i>
                                    {s name='OfferNotValidForAccept'}There is not enough Custom Info for this offer to accept{/s}
                                </div>
                            {/if}
                        {/if}
                    </form>

                </div>
            {/block}

        {/block}
    </div>

{/block}
