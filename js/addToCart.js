const cartKey = "snae_ecommerce_cart";
const previewKey = "snae_ecommerce_preview_data";

window.onload = function() {
	addButton = document.querySelector('#add-to-cart-button');

	if (addButton) {
		addButton.addEventListener('click', function() {
			var workshop = "workshop-" + addButton.getAttribute('data-workshop');

			var cart = JSON.parse(localStorage.getItem(cartKey)) || {};
			var preview = JSON.parse(localStorage.getItem(previewKey)) || {};

			cart[workshop] = 1;
			preview[workshop] = {
				title: addButton.getAttribute('data-preview-title'),
				url: addButton.getAttribute('data-preview-url'),
				photo: addButton.getAttribute('data-preview-photo'),
				desc: addButton.getAttribute('data-preview-desc'),
				price: addButton.getAttribute('data-preview-price'),
			}

			localStorage.setItem(cartKey, JSON.stringify(cart));
			localStorage.setItem(previewKey, JSON.stringify(preview));


			window.location.href = addButton.getAttribute('data-cart');
		});
	}
}


