window.onload = function() {
	const checkoutBtn = document.querySelector('#checkout-button');
	const publishable = checkoutBtn.dataset.hi;

	var stripe = Stripe(publishable);

	checkoutBtn.addEventListener('click', function() {
	  stripe.redirectToCheckout({
		sessionId: checkoutBtn.dataset.secret
	  }).then(function (result) {
		// If `redirectToCheckout` fails due to a browser or network
		// error, display the localized error message to your customer
		// using `result.error.message`.
	  });
	});
}
