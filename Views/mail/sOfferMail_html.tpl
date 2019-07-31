<p>
Hallo,
<br><br>
Sie haben eine neue Angebotsanfrage von {$billingaddress.firstname} {$billingaddress.lastname} ({$additional.user.email}) erhalten.
<br/><br/>
</p>
<table width="80%" border="0" style="font-family:Arial, Helvetica, sans-serif; font-size:10px;">
    <tr>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Artikel</strong></td>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Pos.</strong></td>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Art-Nr.</strong></td>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Menge</strong></td>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Preis</strong></td>
        <td bgcolor="#F7F7F2" style="border-bottom:1px solid #cccccc;"><strong>Summe</strong></td>
    </tr>

    {foreach item=details key=position from=$sOrderDetails}
        <tr>
            <td rowspan="2" style="border-bottom:1px solid #cccccc;">{if $details.image.src.0}<img style="height: 57px;" height="57" src="{$details.image.src.0}" alt="{$details.articlename}" />{else} {/if}</td>
            <td>{$position+1|fill:4} </td>
            <td>{$details.ordernumber|fill:20}</td>
            <td>{$details.quantity|fill:6}</td>
            <td>{$details.price|padding:8}{$sCurrency}</td>
            <td>{$details.amount|padding:8} {$sCurrency}</td>
        </tr>
        <tr>
            <td colspan="5" style="border-bottom:1px solid #cccccc;">{$details.articlename|wordwrap:80|indent:4}</td>
        </tr>
    {/foreach}
</table>

<p>
    {if $sComment}
        <br/>
        <strong>Kommentar:</strong> {$sComment}
    {/if}
    <br/><br/>
    <strong>Gewählte Zahlungsart:</strong> {$additional.payment.description}<br>
    <strong>Gewählte Versandart:</strong> {$sDispatch.name}
</p>
<p>
    <strong>Rechnungsadresse:</strong><br/>
    {$billingaddress.company}<br/>
    {$billingaddress.firstname} {$billingaddress.lastname}<br/>
    {$billingaddress.street}<br/>
    {$billingaddress.zipcode} {$billingaddress.city}<br/>
    {$billingaddress.phone}<br/>
    {$additional.country.countryname}<br/>
    <br/>
    <br/>
    <strong>Lieferadresse:</strong><br/>
    {$shippingaddress.company}<br/>
    {$shippingaddress.firstname} {$shippingaddress.lastname}<br/>
    {$shippingaddress.street}<br/>
    {$shippingaddress.zipcode} {$shippingaddress.city}<br/>
    {$additional.countryShipping.countryname}<br/>
</p>