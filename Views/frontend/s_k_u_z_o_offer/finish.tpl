{namespace name="frontend/sKUZOOffer/checkout/finish"}
{extends file="frontend/checkout/finish.tpl"}

{* Finish teaser message *}
{block name='frontend_checkout_finish_teaser'}
    <div class="finish--teaser{if !$theme} teaser{/if} panel has--border is--rounded">

        {block name='frontend_checkout_finish_teaser_title'}
            <h2 class="panel--title teaser--title is--align-center{if !$theme} center{/if}">{s name="FinishHeaderThankYou"}Thank You for asking an offer at {$sShopname}!{/s} </h2>
        {/block}

        {block name='frontend_checkout_finish_teaser_content'}
            <div class="panel--body is--wide is--align-center{if !$theme} center{/if}">


                <p class="teaser--text">
                    {if !$confirmMailDeliveryFailed}
                        {s name="FinishInfoConfirmation"}We has recived your ask for an offer. Now we would handle your offer and you will become an Offer by mail from us.{/s}
                        <br />
                    {/if}
                </p>

                {block name='frontend_checkout_finish_teaser_actions'}
                    <p class="teaser--actions">
                        {strip}
                            {* Back to the shop button *}
                            <a href="{url controller='index'}" class="btn is--secondary teaser--btn-back is--icon-left{if !$theme} button-right large{/if}" title="{"{s name='FinishButtonBackToShop'}Back to shop{/s}"|escape}">
                                <i class="icon--arrow-left"></i>&nbsp;{"{s name="FinishButtonBackToShop"}{/s}"|replace:' ':'&nbsp;'}
                            </a>
                        {/strip}
                    </p>
                {/block}
            </div>
        {/block}
    </div>
{/block}
