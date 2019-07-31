{namespace name="frontend/sKUZOOffer/register/steps"}
{extends file="parent:frontend/register/steps.tpl"}
{if !$theme}
    {* Step box *}
    <div class="step_box">
        {block name='frontend_register_steps'}
            <ul>
                <li id="first_step" {if $sStepActive=='basket'}class="active"{/if}>
                    {se class="icon" name="CheckoutStepBasketNumber"}1{/se}
                    {se class="text" name="CheckoutStepBasketText"}Ihr Warenkorb{/se}
                </li>
                <li {if $sStepActive=='register'}class="active"{/if}>
                    {se class="icon" name="CheckoutStepRegisterNumber"}2{/se}
                    {se class="text" name="CheckoutStepRegisterText"}Ihre Adresse{/se}
                </li>
                <li id="last_step" {if $sStepActive=='finished'}class="active" {elseif !$sUserLoggedIn}class="grey"{/if}>
                    {se class="icon" name="CheckoutStepConfirmNumber"}3{/se}
                    {se class="text" name="CheckoutStepConfirmText"}Angebot anfordern{/se}
                </li>
            </ul>
        {/block}
    </div>
    <div class="clear">&nbsp;</div>
{/if}