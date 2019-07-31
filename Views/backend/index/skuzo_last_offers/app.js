/**
 *
 */

//{block name="backend/index/application" append}

//{include file="backend/skuzo_offer/model/offer.js"}
//{include file="backend/skuzo_offer/store/offer.js"}
//{include file="backend/index/skuzo_last_offers/model/offers.js"}
//{include file="backend/index/skuzo_last_offers/store/offers.js"}
//{include file="backend/index/skuzo_last_offers/view/main.js"}
//{include file="backend/skuzo_offer/model/tax.js"}
//{include file="backend/skuzo_offer/model/position.js"}
//{include file="backend/skuzo_offer/model/configuration.js"}
//{include file="backend/skuzo_offer/model/customer.js"}
//{include file="backend/skuzo_offer/model/offer_billing.js"}
//{include file="backend/skuzo_offer/model/offer_shipping.js"}
//{include file="backend/skuzo_offer/model/receipt.js"}
//{include file="backend/skuzo_offer/model/states.js"}
//{include file="backend/skuzo_offer/model/mail.js"}
//{include file="backend/skuzo_offer/model/shop.js"}
{if $swVersion4}
//{include file="backend/skuzo_offer/model/variant.js"}
{/if}
//{include file="backend/skuzo_offer/store/tax.js"}
//{include file="backend/skuzo_offer/store/position.js"}
//{include file="backend/skuzo_offer/store/configuration.js"}
//{include file="backend/skuzo_offer/store/customer.js"}
//{include file="backend/skuzo_offer/store/states.js"}
//{include file="backend/skuzo_offer/store/shop.js"}
{if $swVersion4}
//{include file="backend/skuzo_offer/store/variant.js"}
{/if}
//{include file="backend/skuzo_offer/view/list/window.js"}
//{include file="backend/skuzo_offer/view/list/communication.js"}
//{include file="backend/skuzo_offer/view/list/create_offer_window.js"}
//{include file="backend/skuzo_offer/view/list/position.js"}
//{include file="backend/skuzo_offer/view/list/sidebar.js"}
//{include file="backend/skuzo_offer/view/list/billing.js"}
//{include file="backend/skuzo_offer/view/list/shipping.js"}
//{include file="backend/skuzo_offer/view/list/offer.js"}
//{include file="backend/skuzo_offer/view/list/document.js"}
//{include file="backend/skuzo_offer/view/main/window.js"}
//{include file="backend/skuzo_offer/view/mails/form.js"}
//{include file="backend/skuzo_offer/view/mails/window.js"}
//{include file="backend/skuzo_offer/controller/offer.js"}
//{include file="backend/skuzo_offer/controller/main.js"}
//{include file="backend/skuzo_offer/app.js"}

//{/block}