{namespace name="documents/offer"}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; utf-8">
<meta name="author" content=""/>
<meta name="copyright" content="" />

<title></title>
<style type="text/css">
body {
	{$Containers.Body.style}
}

div#head_logo {
	{$Containers.Logo.style}
}

div#head_sender {
	{$Containers.Header_Recipient.style}
}

div#header {
	{$Containers.Header.style}
}

div#head_left {
	{$Containers.Header_Box_Left.style}
}

div#head_right {
	{$Containers.Header_Box_Right.style}
}

div#head_bottom {
	{$Containers.Header_Box_Bottom.style}
}

div#content {
	{$Containers.Content.style}
}

td {
	{$Containers.Td.style}
}

td.name {
	{$Containers.Td_Name.style}
}

td.units {
    white-space: nowrap;
    word-break: keep-all;
}

td.line {
	{$Containers.Td_Line.style}
}

td.head  {
	{$Containers.Td_Head.style}
}

#footer {
	{$Containers.Footer.style}
}

#amount {
	{$Containers.Content_Amount.style}
}

#sender {
	{$Containers.Header_Sender.style}
}

#info {
	{$Containers.Content_Info.style}
}
</style>

<body>
{foreach from=$Pages item=postions name="pagingLoop" key=page}
	<div id="head_logo">
		{$Containers.Logo.value}
	</div>
	<div id="header">
		<div id="head_left">
		{if $smarty.foreach.pagingLoop.first}
			{block name="document_index_selectAdress"}
				{assign var="address" value="billing"}
			{/block}
			<div id="head_sender">
				<p class="sender">{$Containers.Header_Sender.value}</p>
                {$User.$address.company}<br />
				{$User.$address.firstName} {$User.$address.lastName}<br />
				{$User.$address.street} {$User.$address.streetnumber}<br />
				{$User.$address.zipCode} {$User.$address.city}<br />
				{if $User.$address.state.shortcode}{$User.$address.state.shortcode} - {/if}{$User.$address.country}<br />

			</div>
		{/if}
		</div>
		<div id="head_right">
				<strong>
				{block name="document_index_head_right"}
					{$Containers.Header_Box_Right.value}
					{s name="DocumentIndexCustomerID"}Kunden-Nr.:{/s} {$User.billing.number|string_format:"%06d"}<br />
					{if $User.billing.ustid}
					{s name="DocumentIndexUstID"}USt-IdNr.:{/s} {$User.billing.ustid|replace:" ":""|replace:"-":""}<br />
					{/if}
                  	{s name="DocumentIndexDate"}Datum:{/s} {$Document.date}<br />


				{/block}
				</strong>
		</div>
	</div>
	
	<div id="head_bottom" style="clear:both">
		{block name="document_index_head_bottom"}
			<h1>{s name="DocumentIndexInvoiceNumber"}Offer Nr. {$Document.id}{/s}</h1>
			{s name="DocumentIndexPageCounter"}Seite {$page+1} von {$Pages|@count}{/s}
		 {/block}
	</div>

	<div id="content">

	<table cellpadding="0" cellspacing="0" width="100%">
	<tbody valign="top">
	<tr>
		{block name="document_index_table_head_pos"}
			<td align="left" width="3%" class="head">
				<strong>{s name="DocumentIndHeadPositionIndex"}NO.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_nr"}
			<td align="left" width="14%" class="head">
				<strong>{s name="DocumentIndHeadArticleNumber"}Art-No.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_name"}
			<td align="left" width="48%" class="head">
				<strong>{s name="DocumentIndHeadArticleName"}Article Name{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_quantity"}
			<td align="right" width="7%" class="head">
				<strong>{s name="DocumentIndHeadQnty"}Qnty.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_tax"}
			{if $Document.netto != true}
				<td align="right" width="6%" class="head">
					<strong>{s name="DocumentIndHeadTax"}Tax.{/s}</strong>
				</td>
			{/if}
		{/block}
        {if $User.additional.documentFormat}
            <td align="right" width="10%" class="head">
                <strong>{s name="DocumentIndHeadOriginal"}Original{/s}</strong>
            </td>
            <td align="right" width="12%" class="head">
                <strong>{s name="DocumentIndHeadDiscount"}Discount{/s}</strong>
            </td>

        {/if}
		{block name="document_index_table_head_price"}
		    	 <td align="right" width="10%" class="head">
					<strong>{s name="DocumentIndexHeadOffer"}Price{/s}</strong>
				 </td>
			     <td align="right" width="12%" class="head">
					<strong>{s name="DocumentIndexHeadTotal"}Total{/s}</strong>
				 </td>
		{/block}
	</tr>
	{foreach from=$postions item=position key=number}
	{block name="document_index_table_each"}
	<tr>
		{block name="document_index_table_pos"}
			<td align="left" width="5%" valign="top">
				{$number+1}
			</td>
		{/block}
		{block name="document_index_table_nr"}
			<td align="left" width="10%" valign="top">
                {if $position.customMode == 2}&nbsp;&nbsp;{/if}
                {if $position.customMode == 3}&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-style: italic">{/if}
				{$position.articleNumber|truncate:14:""}
				{if $position.customMode == 3}</span>{/if}
			</td>
		{/block}
		{block name="document_index_table_name"}
			<td align="left" width="43%" valign="top">
			{if $position.name == 'Versandkosten'}
				<strong>{s name="DocumentIndexPositionNameShippingCosts"}{$position.articleName}{/s}</strong>
			{else}
				{assign var=articleName value=$position.articleName|replace:"[totalUnit]":"{$position.totalUnit}"|replace:"[Unit]":"{$position.Unit}"|nl2br}
				{assign var=articleDesc value=$position.attribute.cpQuotation|replace:"[totalUnit]":"{$position.totalUnit}"|replace:"[Unit]":"{$position.Unit}"}
				{if $position.customMode == 2}&nbsp;&nbsp;{assign var=articleName value=$articleName|cat:":"}{/if}
                {if $position.customMode == 3}&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-style: italic">{/if}
				{$articleName}
				{if $position.customMode == 3}</span>{/if}
            {/if}
			</td>
		{/block}
		{block name="document_index_table_quantity"}
			<td align="right" width="5%" valign="top">
				{$position.quantity} {$position.packUnit}
                {if $position.name}
                ({$position.totalUnit} {$position.unitName})
                    {if $position.pricePerUnit && $position.unitName}
                        {$position.pricePerUnit|currency}/{$position.unitName}
                    {/if}
                {/if}

			</td>
		{/block}
		{block name="document_index_table_tax"}
			{if $Document.netto != true}
				<td align="right" width="6%" valign="top">
				{if ($position.price*$position.quantity) == 0 || $position.articleNumber == ''}
					&nbsp;
				{else}
					{$position.taxRate} %
				{/if}
				</td>
			{/if}
		{/block}
        {if $User.additional.documentFormat}
            <td align="right" width="10%" valign="top">
                {$position.originalPrice|currency}
            </td>
            <td align="right" width="14%" valign="top">
                {($position.originalPrice-$position.price)|currency}
            </td>
        {/if}
		{block name="document_index_table_price"}
			{if $Document.netto != true && $Document.nettoPositions != true}
			    <td align="right" width="10%" valign="top">
					{if ( $position.articleNumber == '33333' || ( $position.articleNumber == '999999' || $position.articleNumber == '' ) && $position.price*$position.quantity == 0 ) }
						&nbsp;
					{else}
						{$position.price|currency}
					{/if}
				</td>
			    <td align="right" width="14%" valign="top">
			    	{if ( $position.articleNumber == '999999' || $position.articleNumber == '' ) && $position.price*$position.quantity == 0 }
						&nbsp;
					{else}
						{($position.price*$position.quantity)|currency}
					{/if}
				</td>
			{else}
				<td align="right" width="10%" valign="top">
					{if ( $position.articleNumber == '33333' || ( $position.articleNumber == '999999' || $position.articleNumber == '' ) && $position.price*$position.quantity == 0 ) }
						&nbsp;
					{else}
						{$position.price|currency}
					{/if}
				</td>
			    <td align="right" width="14%" valign="top">
			    	{if ( $position.articleNumber == '999999' || $position.articleNumber == '' ) && $position.price*$position.quantity == 0 }
						&nbsp;
					{else}
						{($position.price*$position.quantity)|currency}
					{/if}
				</td>
			{/if}
		{/block}
	</tr>
	{/block}
    {/foreach}

    //for extra position
    {if $Offer.invoiceShipping || $User.additional.zeroShipping}
		{if $smarty.foreach.pagingLoop.last}
			{block name="document_index_table_each"}
				<tr>
					{block name="document_index_table_pos"}
						<td align="left" width="5%" valign="top">
							{$number+2}
						</td>
					{/block}
					{block name="document_index_table_nr"}
						<td align="left" width="10%" valign="top">

						</td>
					{/block}
					{block name="document_index_table_name"}
						<td align="left" width="43%" valign="top">
							{s name="DocumentIndexShippingTaxes"}Shipping Cost{/s}
						</td>
					{/block}
					{block name="document_index_table_quantity"}
						<td align="right" width="10%" valign="top">
							1
						</td>
					{/block}
					{block name="document_index_table_tax"}
						{if $Document.netto != true}
							<td align="right" width="6%" valign="top">
								{$User.$address.shippingTax} %
							</td>
						{/if}
					{/block}

					{block name="document_index_table_price"}
						{* if not net prices show bruto prices *}
						{assign var="shippingCost" value=$Offer.invoiceShippingNet}
                        {if $User.additional.customerShowTax}
                        	{assign var="shippingCost" value=$Offer.invoiceShipping}
						{/if}

					   <td align="right" width="10%" valign="top">
							{$shippingCost|currency}
						</td>
						{if $User.additional.documentFormat}
							<td align="right" valign="top"></td>
							<td align="right" valign="top">{$shippingCost|currency}</td>
						{/if}
						<td align="right" width="14%" valign="top">
							{$shippingCost|currency}
						</td>

					{/block}
				</tr>
			{/block}
		{/if}
    {/if}
	</tbody>
	</table>
	</div>
	
	{if $smarty.foreach.pagingLoop.last}
		{block name="document_index_amount"}

		 	<div id="amount">
                <table width="300px" cellpadding="0" cellspacing="0">
                    <tbody>
                    {if $User.additional.documentFormat}
                        {* if not net prices show bruto prices *}
                        {assign var="totalOriginalPrice" value=$User.totalOriginalPriceWithoutTax}
                        {assign var="totalPrice" value=$User.totalPriceWithoutTax}
                        {if $User.additional.customerShowTax}
                            {assign var="totalOriginalPrice" value=$User.totalOriginalPriceWithTax}
                            {assign var="totalPrice" value=$User.totalPriceWithTax}
                        {/if}
                        <tr>
                             <td align="right" width="100px">{s name="DocumentIndexTotalOriginal"}Total Original Price:{/s}</td>
                             <td align="right" width="200px">{$totalOriginalPrice|currency}</td>
                        </tr>

                        <tr>
                            <td align="right" width="100px" >{s name="DocumentIndexTotalDiscount"}Total Discount Price:{/s}</td>
                            <td align="right" width="200px" >{($totalOriginalPrice-$totalPrice)|currency}</td>
                        </tr>
                    {/if}

				{if $User.additional.customerShowTax}
                    {if $isTax == 1}
						<tr>
							<td align="right" class="head"><b>{s name="DocumentIndexTotalPrice"}Total Price{/s}</b></td>
							<td align="right" class="head"><b>{($Offer.totalPriceWithTax+$Offer.invoiceShipping)|currency}</b></td>
						</tr>
					{/if}
				{else}
                    {if $isTax == 1}
						<tr>
							<td align="right" width="100px" class="head">{s name="DocumentIndexTotalOffer"}Offer Price:{/s}</td>
							<td align="right" width="200px" class="head">{$User.totalPriceWithoutTax|currency}</td>
						</tr>
					{/if}
				{/if}


			 	{foreach from=$User.taxCost item=taxCost key=key}
				  {if $taxCost}
					  <tr>
                    	{if $User.additional.customerShowTax}
							<td align="right">{s name="DocumentIndexIncludeTaxs"}inkl. {$key} % MwSt:{/s}</td>
						{else}
							<td align="right">{s name="DocumentIndexTaxs"}zzgl. {$key} % MwSt:{/s}</td>
						{/if}
						<td align="right">{$taxCost|currency}</td>
					  </tr>
				  {/if}
				{/foreach}


                {if $User.$address.shippingTax}
                    <tr>
                        <td align="right" class="head">{s name="DocumentIndexShippingTaxes"}Shipping: {$User.$address.shippingTax} % :{/s}</td>
                        <td align="right" class="head">{$Offer.invoiceShipping|currency}</td>
                    </tr>
                {/if}

				{if $User.additional.customerShowTax}
					<tr>
						<td align="right" width="100px">{s name="DocumentIndexTotalOffer"}Offer Price:{/s}</td>
						<td align="right" width="200px">{$User.totalPriceWithoutTax|currency}</td>
					</tr>
				{else}
					<tr>
						<td align="right"><b>{s name="DocumentIndexTotalPrice"}Total Price{/s}</b></td>
						<td align="right"><b>{($Offer.invoiceAmount+$Offer.invoiceShipping)|currency}</b></td>
					</tr>
				{/if}
			  </tbody>
			  </table>
			</div>
		{/block}
		{block name="document_index_info"}
			<div id="info">
			{block name="document_index_info_comment"}
				{if $Document.customerComment}
				<p>
					{s name="DocumentIndexCustomerComment"}Kunden-Kommentar: {/s}&nbsp;{$Document.customerComment|replace:"€":"&euro;"}
				</p>
				{/if}
				{if $Document.comment}
					<p>
						{s name="DocumentIndexComment"}Kommentar: {/s}&nbsp;{$Document.comment|replace:"€":"&euro;"}
					</p>
				{/if}
			{/block}
			{block name="document_index_info_net"}

				<p>{s name="DocumentIndexSelectedPaymentOffer"}Gew&auml;hlte Zahlungsart: {/s} {$User.$address.payment}</p>
			{/block}


			{block name="document_index_info_dispatch"}

					<div style="font-size:11px;color:#333;">
						{s name="DocumentIndexSelectedDispatchOffer"}Gewählte Versandart: {/s}
						{$User.$address.dispatch}
					</div>

			{/block}
			{block name="document_index_info_content"}
				{$Containers.Content_Info.value}
			{/block}
			</div>
		{/block}
	{/if}
	
	<div id="footer">
	{$Containers.Footer.value}
	</div>
	{if !$smarty.foreach.pagingLoop.last}
		<pagebreak />
	{/if}
{/foreach}


</body>
</html>