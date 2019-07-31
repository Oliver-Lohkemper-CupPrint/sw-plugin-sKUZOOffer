Hallo,

Sie haben eine neue Angebotsanfrage von {$billingaddress.firstname} {$billingaddress.lastname} ({$additional.user.email}) erhalten.

Pos. Art.Nr.              Menge         Preis        Summe
{foreach item=details key=position from=$sOrderDetails}
    {$position+1|fill:4} {$details.ordernumber|fill:20} {$details.quantity|fill:6} {$details.price|padding:8} EUR {$details.amount|padding:8} EUR
    {$details.articlename|wordwrap:49|indent:5}
{/foreach}

{if $sComment}
    Kommentar: {$sComment}
{/if}

Gewählte Zahlungsart: {$additional.payment.description}
Gewählte Versandart: {$sDispatch.name}
{$sDispatch.description}

Rechnungsadresse:
{$billingaddress.company}
{$billingaddress.firstname} {$billingaddress.lastname}
{$billingaddress.street}
{if {config name=showZipBeforeCity}}{$billingaddress.zipcode} {$billingaddress.city}{else}{$billingaddress.city} {$billingaddress.zipcode}{/if}
{$billingaddress.phone}
{$additional.country.countryname}

Lieferadresse:
{$shippingaddress.company}
{$shippingaddress.firstname} {$shippingaddress.lastname}
{$shippingaddress.street}
{if {config name=showZipBeforeCity}}{$shippingaddress.zipcode} {$shippingaddress.city}{else}{$shippingaddress.city} {$shippingaddress.zipcode}{/if}
{$additional.countryShipping.countryname}