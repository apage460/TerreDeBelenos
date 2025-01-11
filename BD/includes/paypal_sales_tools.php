<script type="text/javascript">
	function submitRegistration()
	{	
		document.registration.submit();
		const element = document.getElementById('paypal-button-container');
		element.innerHTML = '';
		element.innerHTML = '<h2>Paiement réussi! Merci!</h2>';
	}
	function PaypalOneKid()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '15.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalTwoKids()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '30.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalOneEntry()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '50.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalOneEntryOneKid()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '65.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalOneEntryTwoKids()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '80.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalNewPlayer()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '35.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalNewPlayerOneKid()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '50.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalNewPlayerTwoKids()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '65.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalOlderKidEntry()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Entrée Bélé I 2022', amount: { currency_code:'CAD', value: '30.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
	function PaypalSeasonalPass()
	{	
		paypal.Buttons({
			locale: 'fr-CA',
			style: {size: 'responsive', shape: 'pill', color: 'gold'},
			createOrder: function(data, actions) {
				return actions.order.create({
					      purchase_units: [{ description: 'Passe de saison 2022', amount: { currency_code:'CAD', value: '200.00' } }]
				});
			},
			onApprove: function(data, actions) {
				return actions.order.capture().then(
					function(details) {
						submitRegistration();
					}
				);
			}
		}).render('#paypal-button-container');
	}
</script>