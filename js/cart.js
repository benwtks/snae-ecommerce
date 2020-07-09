window.onload = function() {
	cartItems = document.querySelector('#snae-items');
	cartTotals = document.querySelector('#cart-totals');

	var cart = JSON.parse(localStorage.getItem(cartKey));
	var previewData = JSON.parse(localStorage.getItem(previewKey));

	var total = 0;

	for (var item in cart) {
		if (cart[item] > 0) {
			total += parseFloat(previewData[item]['price']);
			cartTotals.insertAdjacentHTML('beforeend', getItemTotalHTML(previewData[item]['title'], previewData[item]['price']));

			if (cartItems) {
				// Use insertAdjacentHTML to not affect prior elements
				cartItems.insertAdjacentHTML('beforeend', getItemHTML(previewData[item], item));

				document.querySelector('#' + item).querySelector('.remove-item').addEventListener('click', function() {
					var itemID = event.target.getAttribute('data-workshop');
					var cart = JSON.parse(localStorage.getItem(cartKey)) || {};
					var preview = JSON.parse(localStorage.getItem(previewKey)) || {};

					cart[itemID] = 0;
					preview[itemID] = {};
					
					localStorage.setItem(cartKey, JSON.stringify(cart));
					localStorage.setItem(previewKey, JSON.stringify(preview));

					document.querySelector('#snae-items').removeChild(document.querySelector('#' + itemID));
					setCheckoutItems();
				});
			}
		}
	}

	if(cartItems && cartItems.innerHTML.trim() == "") {
		cartItems.innerHTML = "<p id='empty-message'>Your cart is empty. Time to book some workshops! Add items to your cart and they will appear here ready for checkout.</p>";
	}

	var browse_url = cartItems.getAttribute('data-browse-url');
	cartItems.insertAdjacentHTML('beforeend', "<a id='continue-shopping' href='" + browse_url + "' alt='Continue shopping'>Continue Shopping</a>");

	cartTotals.insertAdjacentHTML('beforeend', getItemTotalHTML("Sub-total", total));

	setCheckoutItems();
}

function setCheckoutItems() {
	var cart = JSON.parse(localStorage.getItem(cartKey));
	var items = '';

	for (var item in JSON.parse(localStorage.getItem(cartKey))) {
		if (cart[item] > 0) {
			items += item + ",";
		}
	}

	document.getElementsByName('cart_items')[0].setAttribute('value', items.slice(0,-1));
}

function getItemTotalHTML(title, amount) {
	var inner = '<span>' + title + '</span><span class="value">£' + parseFloat(amount).toFixed(2) + '</span>';
	return '<div class="item-total">' + inner + '</div>';
}

function getItemHTML(previewData, id) {
	var photo = '<img src="' + previewData['photo'] + '" alt="Item photo">';
	var price = '<span class="item-price"><button data-workshop="' + id + '" class="remove-item"></button>£' + parseFloat(previewData['price']).toFixed(2) + '</span>';
	var title = '<h2 class="item-title"><a href="' + previewData['url'] + '">' + previewData['title'] + '</a></h2>';
	var desc =  '<p>' + previewData['desc'] + '</p>';

	var item_meta = '<div class="item-meta">' + title + desc + '</div>';
	return '<div class="item" id="' + id + '"><div class="item-details">' + photo + item_meta + '</div>' + price + '</div>';
}
