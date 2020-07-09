<div id="checkout-container" class="main-section" data-publishable="<?= carbon_get_theme_option('crb_stripe_api_key_publishable') ?>">
	<form id="payment-form">
		<fieldset>
			<div class="row">
				<label>Name</label>
				<input type="text" id="payment-name" required autocomplete="true" autocorrect="off" spellcheck="false" autocomplete="name" name="cc-name">
			</div>
			<div class="row">
				<label>Email</label>
				<input type="email" id="payment-email" required autocomplete="email" autocorrect="off" spellcheck="false" name="email">
			</div>
			<div class="row">
				<label>Phone</label>
				<input type="tel" id="payment-tel" required autocomplete="tel" autocorrect="off" spellcheck="false" name="phone">
			</div>
		</fieldset>
		<fieldset>
			<div class="row">
				<label>Address Line 1</label>
				<input type="text" id="payment-address1" required autocomplete="address-line1" autocorrect="off" spellcheck="false" name="address">
			</div>
			<div class="row">
				<label>Address Line 2</label>
				<input type="text" id="payment-address2" placeholder="Optional" autocomplete="address-line2" autocorrect="off" spellcheck="false" name="address">
			</div>

			<div class="row">
				<label>Town/City</label>
				<input type="text" id="payment-city" required autocomplete="address-level2" autocorrect="off" spellcheck="false" name="city">
			</div>
			<div class="row">
				<label>County</label>
				<input type="text" placeholder="Optional" autocomplete="true" name="county" autocorrect="off" spellcheck="fals">
			</div>
			<div class="row">
				<label>Postcode</label>
				<input type="text" id="payment-postal" required autocomplete="postal-code" name="postcode" autocorrect="off" spellcheck="false" name="city">
			</div>
		</fieldset>
		<fieldset>
			<div class="row" id="dietary">
				<label>Dietary Requirements</label>
				<input id="dietary" type="textarea" required autocomplete="true" spellcheck="true" autocorrect="on" name="dietary">
			</div>
		</fieldset>
		<fieldset id="elements">
			<!-- placeholder for Elements -->
			<div id="card-element"></div>
		</fieldset>
		<div id="card-errors" role="alert"></div>
		<button id="card-button" data-update="<?= admin_url( 'admin-ajax.php' ) ?>" data-secret="<?= $intent->client_secret ?>">Submit Payment<i class="dripicons-lock"></i></button>
	</form>
</div>
