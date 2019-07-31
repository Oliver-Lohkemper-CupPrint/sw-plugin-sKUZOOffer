{namespace name="frontend/sKUZOOffer/register/steps"}
{extends file="parent:frontend/register/steps.tpl"}

{* Third Step - Confirmation *}
{block name='frontend_register_steps_confirm'}
    <li class="steps--entry step--confirm{if $sStepActive=='finished'} is--active{/if}">
        <span class="icon">{s namespace="frontend/register/steps" name="CheckoutStepConfirmNumber"}3{/s}</span>
        <span class="text"><span class="text--inner">{s name="CheckoutStepOfferConfirmText"}Prüfen und Anfragen{/s}</span></span>
    </li>
{/block}