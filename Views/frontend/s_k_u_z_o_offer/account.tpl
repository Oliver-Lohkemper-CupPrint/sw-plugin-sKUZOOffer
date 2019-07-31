{namespace name="frontend/sKUZOOffer/sidebar"}

{block name="frontend_account_content_right_orders" prepend}
    <li>
        <a href="{url controller='sKUZOOffer' action='offers'}">
            {s name="AccountLinkPreviousOffer"}My Offers{/s}
        </a>
    </li>
{/block}

{block name="frontend_account_menu_link_orders" prepend}
    <li class="navigation--entry">
        <a href="{url controller='sKUZOOffer' action='offers'}" title="{s name="AccountLinkPreviousOffer"}My Offers{/s}" class="navigation--link{if $sAction == 'offers'} is--active{/if}">
            {s name="AccountLinkPreviousOffer"}My Offers{/s}
        </a>
    </li>
{/block}

