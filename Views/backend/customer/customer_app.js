/**
 *
 */

// {block name="backend/customer/application" append}

// {include file="backend/customer/controller/offer.js"}
// {include file="backend/customer/view/action/offer.js"}
// {include file="backend/customer/model/offer.js"}
// {include file="backend/customer/model/offer_chart.js"}
// {include file="backend/customer/store/offer.js"}
// {include file="backend/customer/store/offer_chart.js"}
// {include file="backend/customer/view/detail/offer.js"}
// {include file="backend/customer/view/offer/chart.js"}
// {include file="backend/customer/view/offer/offer.js"}

// {include file="backend/skuzo_offer/controller/offer.js"}
// {include file="backend/skuzo_offer/model/configuration.js"}
// {include file="backend/skuzo_offer/model/customer.js"}
// {include file="backend/skuzo_offer/model/position.js"}
// {include file="backend/skuzo_offer/model/offer_shipping.js"}
// {include file="backend/skuzo_offer/model/offer_billing.js"}
// {include file="backend/skuzo_offer/model/tax.js"}
// {include file="backend/skuzo_offer/model/states.js"}
// {include file="backend/skuzo_offer/model/receipt.js"}
// {include file="backend/skuzo_offer/model/offer.js"}
{if $swVersion4}
// {include file="backend/skuzo_offer/model/variant.js"}
{/if}

// {include file="backend/skuzo_offer/store/configuration.js"}
// {include file="backend/skuzo_offer/store/customer.js"}
// {include file="backend/skuzo_offer/store/position.js"}
// {include file="backend/skuzo_offer/store/tax.js"}
// {include file="backend/skuzo_offer/store/states.js"}
// {include file="backend/skuzo_offer/store/offer.js"}
{if $swVersion4}
// {include file="backend/skuzo_offer/store/variant.js"}
{/if}
// {include file="backend/skuzo_offer/view/list/create_offer_window.js"}
// {include file="backend/skuzo_offer/view/list/document.js"}
// {include file="backend/skuzo_offer/view/list/position.js"}
// {include file="backend/skuzo_offer/view/list/shipping.js"}
// {include file="backend/skuzo_offer/view/list/billing.js"}
// {include file="backend/skuzo_offer/view/list/sidebar.js"}
// {include file="backend/skuzo_offer/view/list/window.js"}
// {include file="backend/skuzo_offer/view/list/offer.js"}
// {include file="backend/skuzo_offer/view/list/communication.js"}
// {/block}