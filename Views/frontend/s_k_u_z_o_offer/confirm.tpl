{namespace name="frontend/sKUZOOffer/checkout/confirm"}
{extends file="frontend/checkout/confirm.tpl"}

{if $theme}
    {block name="frontend_index_header_css_screen" append}
        {* todo: reactivate/remove if functionality is reestablished *}
        <style>
            {if !$theme}
                .agb_cancelation, .agb_accept {
                    display: none;
                }
{*
                #confirm .personal-information .actions {
                    display:none;
                }
*}
                .ctl_sKUZOOffer .more_info {
                    display:none;
                }

                .ctl_sKUZOOffer .additional-options {
                    display:none;
                }
            {/if}
        </style>
    {/block}
{/if}

{* Step box *}
{block name='frontend_index_navigation_categories_top'}
    {if $theme}
    {include file="frontend/s_k_u_z_o_offer/steps_sw5.tpl" sStepActive="finished"}
    {/if}
{/block}

{block name='frontend_checkout_confirm_tos_panel'}
{/block}



{* delete AGB Checkobx *}
{if $theme}



    {block name="frontend_checkout_confirm_agb_checkbox"}{/block}

    {block name="frontend_index_content_top"}
        <div class="grid_20 first">

            {* Step box *}
            {if !$theme}
                {include file="frontend/s_k_u_z_o_offer/steps.tpl" sStepActive="finished"}
            {/if}

            {* AGB is not accepted by user *}
            {if $sAGBError}
                <div class="error agb_confirm">
                    <div class="center">
                        <strong>
                            {s name='ConfirmErrorAGB'}{/s}
                        </strong>
                    </div>
                </div>
            {/if}
        </div>
    {/block}
{/if}

{if {config name=commentvoucherarticle}||{config name=premiumarticles}||{config name=bonussystem} && {config name=bonus_system_active} && {config name=displaySlider}}
    <div class="additional-options grid_16 first">

        {if {config name=commentvoucherarticle}}
            <h2 class="headingbox">{s name="ConfirmHeadlineAdditionalOptions"}Weitere Optionen{/s}</h2>
        {/if}

        <div class="inner_container">

            {* Voucher and add article *}
            {if {config name=commentvoucherarticle}}
                <div class="voucher-add-article">

                    {block name='frontend_checkout_table_footer_left_add_voucher'}
                        <div class="vouchers">
                            <form method="post" action="{url action='addVoucher' sTargetAction=$sTargetAction}">
                                {block name='frontend_checkout_table_footer_left_add_voucher_agb'}
                                    {if !{config name='IgnoreAGB'}}
                                        <input type="hidden" class="agb-checkbox" name="sAGB"
                                               value="{if $sAGBChecked}1{else}0{/if}"/>
                                    {/if}
                                {/block}
                                <label for="basket_add_voucher">{s name="CheckoutFooterLabelAddVoucher" namespace="frontend/checkout/cart_footer_left"}{/s}</label>
                                <input type="text" class="text" id="basket_add_voucher" name="sVoucher"
                                       onfocus="this.value='';"
                                       value="{s name='CheckoutFooterAddVoucherLabelInline' namespace="frontend/checkout/cart_footer_left"}{/s}"/>
                                <input type="submit"
                                       value="{s name='CheckoutFooterActionAddVoucher' namespace="frontend/checkout/cart_footer_left"}{/s}"
                                       class="box_send"/>
                            </form>
                        </div>
                    {/block}
                    <div class="clear"></div>
                    <div class="space"></div>
                    {block name='frontend_checkout_table_footer_left_add_article'}
                        <div class="add_article">
                            <form method="post" action="{url action='addArticle' sTargetAction=$sTargetAction}">
                                <label for="basket_add_article">{s name='CheckoutFooterLabelAddArticle' namespace="frontend/checkout/cart_footer_left"}{/s}:</label>
                                <input id="basket_add_article" name="sAdd" type="text" value="{s name='CheckoutFooterIdLabelInline' namespace="frontend/checkout/cart_footer_left"}{/s}" onfocus="this.value='';" class="ordernum text" />
                                <input type="submit" class="box_send" value="{s name='CheckoutFooterActionAdd' namespace="frontend/checkout/cart_footer_left"}{/s}" />
                            </form>
                        </div>
                    {/block}
                </div>

                {* Comment functionality *}
                {block name='frontend_checkout_confirm_comment'}
                    <div class="feature--user-comment block">
                        {*<label for="sComment">{s name="ConfirmLabelComment" namespace="frontend/checkout/confirm"}{/s}</label>*}
                        <textarea class="user-comment--field" rows="5" cols="20"  placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                    </div>
                    <div class="clear"></div>
                {/block}
                {* Additional customer comment for the order *}
                {*{block name='frontend_checkout_confirm_comment'}

                    <div class="feature--user-comment block">
                        <textarea class="user-comment--field" rows="5" cols="20" placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                    </div>
                {/block}*}
                <div class="space"></div>
            {/if}

            {* Premiums articles *}
            {block name='frontend_checkout_confirm_premiums'}
                {if $sPremiums}
                    {if {config name=premiumarticles}}
                        <h2 class="headingbox">{s name="sCartPremiumsHeadline" namespace="frontend/checkout/premiums"}{/s}</h2>
                        {include file='frontend/checkout/premiums.tpl'}
                    {/if}
                {/if}
            {/block}
        </div>
    </div>
    <div class="space"></div>
{/if}


{block name='frontend_checkout_confirm_submit'}
    <form id="confirmOffer--form" method="post" action="{url action='finish'}">
        {if $theme}
            <textarea class="is--hidden user-comment--hidden" rows="1" cols="1" name="sComment">{$sComment|escape}</textarea>
                <button type="submit" class="btn is--primary is--large right is--icon-right" form="confirmOffer--form" data-preloader-button="true">
                    {s name='ConfirmActionSubmit'}Confirm Offer{/s}<i class="icon--arrow-right"></i>
                </button>
        {else}
                {* Submit order button *}
                <div class="actions">
                    <input type="hidden" name="confimOffer" value="true" />
                    <input type="submit" class="button-right large" id="confirmOffer" value="{s name='ConfirmActionSubmit'}Confirm Offer{/s}" />
                </div>
        {/if}
    </form>
{/block}

{* Billing address *}
{block name='frontend_checkout_confirm_left_billing_address'}
    {if $theme}
        {$smarty.block.parent}
    {else}
        <div class="invoice-address">
            <h3 class="underline">{s name="ConfirmHeaderBilling" namespace="frontend/checkout/confirm_left"}{/s}</h3>

            {if $sUserData.billingaddress.company}
                <p>
                    {$sUserData.billingaddress.company}{if $sUserData.billingaddress.department}<br/>{$sUserData.billingaddress.department}{/if}
                </p>
            {/if}

            <p>
                {if $sUserData.billingaddress.salutation eq "mr"}
                    {s name="ConfirmSalutationMr" namespace="frontend/checkout/confirm_left"}Herr{/s}
                {else}
                    {s name="ConfirmSalutationMs" namespace="frontend/checkout/confirm_left"}Frau{/s}
                {/if}
                {$sUserData.billingaddress.firstname} {$sUserData.billingaddress.lastname}<br />
                {$sUserData.billingaddress.street} {$sUserData.billingaddress.streetnumber}<br />
                {$sUserData.billingaddress.zipcode} {$sUserData.billingaddress.city}<br />
                {if $sUserData.additional.state.shortcode}{$sUserData.additional.state.shortcode} - {/if}{$sUserData.additional.country.countryname}


            </p>

            {* Action buttons *}
            <div class="actions">
                <a href="{url controller=account action=billing sTarget=sKUZOOffer}" class="button-middle small">
                    {s name="ConfirmLinkChangeBilling" namespace="frontend/checkout/confirm_left"}{/s}
                </a>
                <a href="{url controller=account action=selectBilling sTarget=sKUZOOffer}" class="button-middle small">
                    {s name="ConfirmLinkSelectBilling" namespace="frontend/checkout/confirm_left"}{/s}
                </a>
            </div>
        </div>
    {/if}
{/block}

{* Shipping address *}
{block name='frontend_checkout_confirm_left_shipping_address'}
    {if $theme}
        {$smarty.block.parent}
    {else}
        <div class="shipping-address">
            <h3 class="underline">{s name="ConfirmHeaderShipping" namespace="frontend/checkout/confirm_left"}{/s}</h3>
            {if $sUserData.shippingaddress.company}
                <p>
                    {$sUserData.shippingaddress.company}{if $sUserData.shippingaddress.department}<br/>{$sUserData.shippingaddress.department}{/if}
                </p>
            {/if}

            <p>
                {if $sUserData.shippingaddress.salutation eq "mr"}
                    {s name="ConfirmSalutationMr" namespace="frontend/checkout/confirm_left"}Herr{/s}
                {else}
                    {s name="ConfirmSalutationMs" namespace="frontend/checkout/confirm_left"}Frau{/s}
                {/if}
                {$sUserData.shippingaddress.firstname} {$sUserData.shippingaddress.lastname}<br />
                {$sUserData.shippingaddress.street} {$sUserData.shippingaddress.streetnumber}<br />
                {$sUserData.shippingaddress.zipcode} {$sUserData.shippingaddress.city}<br />
                {if $sUserData.additional.stateShipping.shortcode}{$sUserData.additional.stateShipping.shortcode} - {/if}{$sUserData.additional.countryShipping.countryname}
            </p>

            {* Action buttons *}
            <div class="actions">
                <a href="{url controller=account action=shipping sTarget=sKUZOOffer}" class="button-middle small">
                    {s name="ConfirmLinkChangeShipping" namespace="frontend/checkout/confirm_left"}{/s}
                </a>

                <a href="{url controller=account action=selectShipping sTarget=sKUZOOffer}" class="button-middle small">
                    {s name="ConfirmLinkSelectShipping" namespace="frontend/checkout/confirm_left"}{/s}
                </a>
            </div>
        </div>
    {/if}
{/block}


{* Payment method *}
{block name='frontend_checkout_confirm_left_payment_method'}
    {if $theme}
        {$smarty.block.parent}
    {else}
        {if !$sRegisterFinished}
            <div class="payment-display">
                <h3 class="underline">{s name="ConfirmHeaderPayment" namespace="frontend/checkout/confirm_left"}{/s}</h3>
                <p>
                    <strong>{$sUserData.additional.payment.description}</strong><br />

                    {if !$sUserData.additional.payment.esdactive}
                        {s name="ConfirmInfoInstantDownload" namespace="frontend/checkout/confirm_left"}{/s}
                    {/if}
                </p>

                {* Action buttons *}
                <div class="actions">
                    <a href="{url controller=account action=payment sTarget=sKUZOOffer}" class="button-middle small">
                        {s name="ConfirmLinkChangePayment" namespace="frontend/checkout/confirm_left"}{/s}
                    </a>
                </div>
            </div>
        {/if}
    {/if}
{/block}

{block name="frontend_checkout_confirm_left_billing_address_actions"}
        <div class="panel--actions">
            <a href="{url controller=account action=billing sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkChangeBilling" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
            <a href="{url controller=account action=selectBilling sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkSelectBilling" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
{/block}

{block name="frontend_checkout_confirm_left_shipping_address_actions"}
        <div class="panel--actions">
            <a href="{url controller=account action=shipping sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkChangeShipping" namespace="frontend/checkout/confirm_left"}{/s}
            </a>

            <a href="{url controller=account action=selectShipping sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkSelectShipping" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
{/block}

{block name='frontend_checkout_confirm_left_payment_method_actions'}
    {if $minSwVersion52}
        <div class="panel--actions is--wide">
            {* Action buttons *}
            <a href="{url controller=sKUZOOffer action=shippingPayment sTarget=sKUZOOffer}" class="btn is--small btn--change-payment">
                {s name="ConfirmLinkChangePayment" namespace="frontend/checkout/confirm"}{/s}
            </a>
        </div>
    {else}
        <div class="panel--actions payment--actions">
            <a href="{url controller=sKUZOOffer action=shippingPayment sTarget=sKUZOOffer}" class="btn is--small btn--change-payment">
                {s name="ConfirmLinkChangePayment" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
    {/if}
{/block}


