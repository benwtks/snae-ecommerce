window.onload = function() {
	var cart = localStorage.getItem(cartKey);
	var previewData = JSON.parse(localStorage.getItem(previewKey));
	var available = document.querySelector('#cart-page').dataset.items;

	for (var item in cart) {
		if (!available.includes(cart[item].substring(9))) {
			alert(previewData[item]['title'] + " is no longer available");
		}
	}

	var stripePublishable = document.querySelector('#checkout-container').dataset.publishable;
	var stripe = Stripe(stripePublishable);

	var elements = stripe.elements({
		fonts: [
			{
				cssSrc: 'https://fonts.googleapis.com/css?family=Open+Sans:400,600&display=swap',
			},
		]
	});

	var style = {
		base: {
			color: "#2b2b2b",
			fontFamily: '"Open Sans", sans-serif',
			fontWeight: 400,
			fontSize: '16px',
		},
		invalid: {
			iconColor: '#ff9791',
			color: '#ff9791',
		},
	};

	var card = elements.create("card", { style: style });
	var cardBtn = document.querySelector('#card-button');
	card.mount("#card-element");

	card.on('change', ({error}) => {
		const displayError = document.getElementById('card-errors');
		if (error) {
			displayError.textContent = error.message;
		} else {
			displayError.textContent = '';
		}
	});

	var form = document.getElementById('payment-form');
	var clientSecret = cardBtn.dataset.secret;

	form.addEventListener('submit', function(ev) {
		ev.preventDefault();
		cardBtn.disabled = true;

		let updateForm = new FormData();
		updateForm.append('action', 'payment');
		updateForm.append('intent', clientSecret);
		updateForm.append('dietary', document.querySelector('#payment-dietary').value);

		let params = new URLSearchParams(updateForm);

		let fetchData = {
			method: 'POST',
			body: params,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'Cache-Control': 'no-cache',
			},
			credentials: 'same-origin',
		};

		fetch(cardBtn.dataset.update, fetchData).then(res => {
			if (res.ok) {
				console.log("Success setting up server for payment");

				confirmPayment(stripe, clientSecret, card,
					cardBtn.getAttribute('data-success'), cardBtn.getAttribute('data-cart'));
			} else {
				if (res.status == 502) {
					alert("Payment unsuccessful - please try again later or get in touch to let us know");
				} else if (res.status == 400) {
					alert("Payment unsuccessful - site misconfigured, please get in touch to let us know");
				} else if (res.status == 406) {
					alert("Payment unsuccessful - one of the selected workshops has become fully booked");
				} else {
					alert("Payment unsuccessful - please try again later or get in touch to let us know");
				}

				window.location.href = cardBtn.getAttribute('data-cart');
			}
		});
	});
}

function confirmPayment(stripe, clientSecret, card, success_url, failure_url) {
	stripe.confirmCardPayment(clientSecret, {
		receipt_email: document.querySelector('#payment-email').value,
		payment_method: {
			card: card,
			billing_details: {
				name: document.querySelector('#payment-name').value,
			}
		},
		shipping: {
			name: document.querySelector('#payment-name').value,
			phone: document.querySelector('#payment-tel').value,
			address: {
				line1: document.querySelector('#payment-address1').value,
				line2: document.querySelector('#payment-address2').value,
				city: document.querySelector('#payment-city').value,
				postal_code: document.querySelector('#payment-postal').value,
			},
		},
	}).then(function(result) {
		if (result.error) {
			// Show error to your customer (e.g., insufficient funds)
			alert(result.error.message);

			window.location.href = failure_url;
		} else {
			// The payment has been processed!
			console.log("Success processing payment");

			window.location.href = success_url;
			localStorage.removeItem(cartKey);
			localStorage.removeItem(previewData);
		}
	});
}
