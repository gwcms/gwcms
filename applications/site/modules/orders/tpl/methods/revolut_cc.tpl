

{GW::ln('/m/CREDIT_CARD_PAY_INITIALISED')}


{if $revolog->test}
<a href="https://developer.revolut.com/docs/accept-payments/tutorials/test-in-the-sandbox-environment/test-cards/">About test payments</a>
<li>4929420573595709 VISA
<li>4929420573595709 MASTERCARD
{/if}

{literal}
	<script>!function(e,o,t){e[t]=function(n,r){var c={sandbox:"https://sandbox-merchant.revolut.com/embed.js",prod:"https://merchant.revolut.com/embed.js",dev:"https://merchant.revolut.codes/embed.js"},d=o.createElement("script");d.id="revolut-checkout",d.src=c[r]||c.prod,d.async=!0,o.head.appendChild(d);var s={then:function(r,c){d.onload=function(){r(e[t](n))},d.onerror=function(){o.head.removeChild(d),c&&c(new Error(t+" is failed to load"))}}};return"function"==typeof Promise?Promise.resolve(s):s}}(window,document,"RevolutCheckout");</script></head>
{/literal}

<div id='revolut-pay'></div>





<script>
RevolutCheckout("{$revolog->public_id}", 'sandbox').then(function(instance) {
  instance.payWithPopup({
    target: document.getElementById('revolut-pay'),
	{if $revolog->phone}phone: '{$revolog->phone}',{/if} // recommended
	{if $revolog->email}email: "{$revolog->email}",   {/if}
    onSuccess() {
	    location.href="/{$ln}/direct/orders/orders?id={$revolog->id}&act=doRevolutAccept"
    },
    onError(error) {
      console.error('Payment failed: ' + error.message)
    }
  })
})

</script>