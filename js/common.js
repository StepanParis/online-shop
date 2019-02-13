//Call the function of readiness of page resources
$(document).ready(function() {
	
	//Loading data from the cookie after the page loading
	cart.cart_load();

	/* Bind event click on all elements with class 'product-action' to call the function to
	 change the button and the content of the cart */
	$('.product-action').bind('click', function(event) {
		cart.product_action(event.target);
	});

	// Call the function to fill the modal window when it is called
	$('#cart-modal').on('show.bs.modal', function (e) {
		cart.cart_show();
	});
	
	// Bind event click on the buttons "Checkout" (from the page or the modal window)
	$('#small-checkout-button, #big-checkout-button').bind('click', function(event) {
		cart.checkout(event.target);
	});
	
});

var cart = {
	
	//Cookie lifetime
	COOKIE_EXPIRES:'Fri, 31 Dec 9999 23:59:59 GMT',
	
	//Cookie name
	COOKIE_NAME: 'saved_cart',
	
	//List of products in the user cart
	products_list: {
		product_1: false,
		product_2: false,
		product_3: false
	},
	
	/* CART */
	
	//Loading the cart from cookies
	cart_load:function() {
		var cookie_data = this.cookie_get(this.COOKIE_NAME);
		if(cookie_data != null) {
			var re_save = false;
			var cart_data = JSON.parse(cookie_data);
			for(var product in this.products_list) {
				if(cart_data.hasOwnProperty(product)) {
					if(typeof this.products_list[product] == typeof true){
						this.products_list[product] = cart_data[product];
					} else {
						this.products_list[product] = false;
						re_save = true;
					}
				} else {
					re_save = true;
				}
				var card_obj = $('div[data-productid=\''+ product +'\']');
				var button_obj = $(card_obj).find('button.product-action');
				if(this.products_list[product]) {
					$(button_obj).removeClass('btn-info add-to-cart').addClass('remove-from-cart btn-danger')
                        .text('Remove from cart');
					$(card_obj).data('isadded',true);
				} else {
					$(button_obj).removeClass('btn-danger remove-from-cart').addClass('btn-info add-to-cart')
                        .text('Add to cart');
					$(card_obj).data('isadded',false);
				}
			}
			if(re_save) {
				this.cart_save();
			}
		} else {
			this.cart_save();
		}
		this.cart_count();
	},
	
	//Saving the cart in cookie
	cart_save:function() {
		this.cookie_set(this.COOKIE_NAME,JSON.stringify(this.products_list),{'expires':this.COOKIE_EXPIRES});
	},
	
	//Display cart content
	cart_show:function() {
		var table = $('table#cart-table');
		var added_products_count = Object.values(this.products_list).filter(Boolean).length;
		var content = '';
		var index = 0;
		if(added_products_count > 0) {
			for(var product in this.products_list) {
				if(this.products_list[product]) {
					index++;
					var card = $('div[data-productid='+product+']');
					var title = $(card).find('h4').text();
					var price = $(card).find('div.h5').text();
					content += '<tr data-productid=' + product + '>';
						content += '<td class="align-middle text-left">' + title + '</td>';
						content += '<td class="align-middle text-center">' + price + '</td>';
						content += '<td class="align-middle text-right">';
						content += '<button type="button" class="btn btn-danger delete-product-from-modal ' +
                            ' rounded-pill btn-sm">Remove from cart</button>';
						content += '</td>';
					content += '</tr>';
				}
			}
		} else {
			content += '<tr>';
				content += '<td>Empty</td>';
			content += '</tr>';
		}
		$(table).empty().append(content);
		$('span#cart-modal-products-count').text('(' + added_products_count + ' pcs)');
		$('.delete-product-from-modal').bind('click', function(event) {
			cart.product_remove_from_modal(event.target);
		});
	},
	
	//Count the number of products, show or hide buttons
	cart_count:function() {
		//Count the number of selected products
		var added_products_count = Object.values(this.products_list).filter(Boolean).length;
		$('span#shopping-cart-products-count').text(added_products_count);
		if(added_products_count > 0) {
			//Change the value of the number of products in the cart for the bage of the icon 'shopping-cart'
			$('span#shopping-cart-products-count').text(added_products_count);
			$('span#cart-modal-products-count').text('(' + added_products_count + ' pcs)');
			//Hide the 'Not interested in any product' button and show the 'Checkout' button
			$('button#big-checkout-button').show();
			$('button#small-checkout-button').show();
			$('button#not-interested-button').hide();
		} else {
			//Change the value of the number of products in the cart for the bage of the icon 'shopping-cart'
			$('span#shopping-cart-products-count').text('empty');
			$('span#cart-modal-products-count').text('');
			//Hide the 'Checkout' button and show the 'Not interested in any product' button
			$('button#big-checkout-button').hide();
			$('button#small-checkout-button').hide();
			$('button#not-interested-button').show();
			//If the modal window is open, notify that there are no products in the basket.
			if(($('#cart-modal').data('bs.modal') || {})._isShown) {
				var content = '<tr>';
						content += '<td>Empty</td>';
					content += '</tr>';
				$('table#cart-table').empty().append(content);
			}
		}
	},
	
	/* PRODUCT */
	
	//The called function when you click on the button on the product card to add or delete it
	product_action:function(button_obj) {
		//We take the block in which there is the button to extract product information.
		var card_obj = $(button_obj).parents('div.card');
		var product_id = $(card_obj).data('productid');
		var is_added = $(card_obj).data('isadded');
		//If a product is added, remove it from the cart and vice versa.
		if(is_added) {
			this.product_remove_from_page(product_id,button_obj,card_obj);
		} else {
			this.product_add(product_id,button_obj,card_obj);
		}
	},
	
	//Function to add a product to cart
	product_add:function(product_id,button_obj,card_obj) {
		if(this.products_list.hasOwnProperty(product_id)) {
			//Add a product to cart
			this.products_list[product_id] = true;
			var added_products_count = Object.values(this.products_list).filter(Boolean).length;
			//Set the value "Remove from cart" to button of the product
			$(button_obj).removeClass('btn-info add-to-cart').addClass('remove-from-cart btn-danger')
							.text('Remove from cart');
			//Change the value of the number of products in the cart for the bage of the icon 'shopping-cart'
			$('span#shopping-cart-products-count').text(added_products_count);
			//Change the value of the product card attribute
			$(card_obj).data('isadded',true);
			//Hide the 'Not interested in any product' button and show the 'Checkout' button
			$('button#big-checkout-button').show();
			$('button#small-checkout-button').show();
			$('button#not-interested-button').hide();
			//Save data in cookie
			this.cart_save();
			//Count the number of products, show or hide buttons
			this.cart_count();
			//Call the information window with the message about the operation
			ohSnap('Product successfully added to cart', {'color':'green', 'duration':'3000'});
		} else {
			//Call the information window with the message about the operation
			ohSnap('Undefined product id', {'color':'red', 'duration':'3000'});
		}
	},
	
	//Function to remove product from the cart
	product_remove_from_page:function(product_id,button_obj,card_obj) {
		if(this.products_list.hasOwnProperty(product_id)) {
			//Add item to cart
			this.products_list[product_id] = false;
			//Set the value "Add to cart" to button of the product
			$(button_obj).removeClass('btn-danger remove-from-cart').addClass('btn-info add-to-cart')
							.text('Add to cart');
			//Change the value of the product card attribute
			$(card_obj).data('isadded',false);
			this.cart_save();
			//Count the number of products, show or hide buttons
			this.cart_count();
			//Call the information window with the message about the operation
			ohSnap('Product successfully removed from cart', {'color':'green', 'duration':'3000'});
		} else {
			//Call the information window with the message about the operation
			ohSnap('Undefined product id', {'color':'red', 'duration':'3000'});
		}
	},
	
	//Remove product from the cart/modal window
	product_remove_from_modal:function(button_obj) {
		var tr = $(button_obj).parents('tr');
		var product_id = $(tr).data('productid');
		if(this.products_list.hasOwnProperty(product_id)) {
			//Card product
			var card_obj = $('div[data-productid=' + product_id + ']');
			//Product button
			var page_button_obj = $(card_obj).find('button');
			//Add item to cart
			this.products_list[product_id] = false;
			//Set the value "Add to cart" to button of the product
			$(page_button_obj).removeClass('btn-danger remove-from-cart').addClass('btn-info add-to-cart')
							.text('Add to cart');
			//Change the value of the product card attribute
			$(card_obj).data('isadded',false);
			//Remove from the table the line with the deleted product
			$(tr).remove();
			//Save data in cookie
			this.cart_save();
			//Count the number of products, show or hide buttons
			this.cart_count();
			//Call the information window with the message about the operation
			ohSnap('Product successfully removed from cart', {'color':'green', 'duration':'3000'});
		} else {
			//Call the information window with the message about the operation
			ohSnap('Undefined product id', {'color':'red', 'duration':'3000'});
		}
	},
	
	/* COOKIE */
	
	//Cookie record
	cookie_set:function(name,value,options) {
		options = options || {};
		value = encodeURIComponent(value);
		var updated_cookie = name + '=' + value;
		var expires = options.expires;
		if(typeof expires == 'number' && expires) {
			var d = new Date();
			d.setTime(d.getTime() + expires * 1000);
			expires = options.expires = d;
		}
		if(expires && expires.toUTCString) {
			options.expires = expires.toUTCString();
		}
		for(var prop_name in options) {
			updated_cookie += '; ' + prop_name;
			var prop_value = options[prop_name];
			if (prop_value !== true) {
				updated_cookie += '=' + prop_value;
			}
		}
		document.cookie = updated_cookie;
	},
	
	//Read cookie
	cookie_get:function(name) {
		var matches = document.cookie.match(new RegExp(
			'(?:^|; )' + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'
		));
		return matches ? decodeURIComponent(matches[1]) : null;
	},
	
	//Delete cookie
	cookie_delete:function(name) {
		this.cookie_set(name,'',{expires: -1});
	},
	
	/* CHECKOUT */

	checkout:function() {
		var that = this;
		$.ajax({
			url: 'ajax/checkout.php',
			dataType: 'json',
			data: 'products=' + JSON.stringify(this.products_list) + '&screen_width=' + screen.width + '&screen_height=' + screen.height,
			type: 'post',
			success: function(response){
				if(!response.has_errors) {
					//Clearing the cart
					for(var product in that.products_list) {
						that.products_list[product] = false;
						var card_obj = $('div[data-productid=\''+ product +'\']');
						var button_obj = $(card_obj).find('button.product-action');
						$(button_obj).removeClass('btn-danger remove-from-cart').addClass('btn-info add-to-cart').text('Add to cart');
						$(card_obj).data('isadded',false);
					}
					//Save data in cookie
					that.cart_save();
					//Count the number of products, show or hide buttons
					that.cart_count();
					ohSnap(response.text, {'color':'green', 'duration':'3000'});
				} else {
					ohSnap(response.text, {'color':'red', 'duration':'3000'});
				}
			},
			error: function(response) {
				console.log(response.responseText);
			}
		});
	}
}