

var successCallback = function() {

	var checkout_form = jQuery( 'form.woocommerce-checkout' );

	// add a token to our hidden input field
	// console.log(data) to find the token
	//checkout_form.find('#misha_token').val(data.token);

  if( 0 ){

	// deactivate the tokenRequest function event
	checkout_form.off( 'checkout_place_order', tokenRequest );

	// submit the form now
	checkout_form.submit();
}
else{
  setInterval(function(){ alert("Hello"); }, 2000);
}

};

var errorCallback = function(data) {
    console.log(data);
};

var paymentRequest = function() {

	var mpesaPhoneNumber = jQuery( '#prizedai-mpesa-number' ).val().trim().split(' ').join('');

	if( isNaN(mpesaPhoneNumber) || !mpesaPhoneNumber.length || mpesaPhoneNumber.length < 10 || mpesaPhoneNumber.length > 13 )
	{
		jQuery( '#prizedai-mpesa-number' ).css('border-bottom-color','#a94442');
		jQuery( '#prizedai-mpesa-number-helper' ).removeClass('hidden');

	}
	else
	{
		jQuery( '#prizedai-mpesa-number-helper' ).addClass('hidden');
		jQuery( '#prizedai-mpesa-number' ).css('border-bottom','0');

	}

	// here will be a payment gateway function that process all the card data from your form,
	// maybe it will need your Publishable API key which is misha_params.publishableKey
	// and fires successCallback() on success and errorCallback on failure
  jQuery.ajax({
        type: "POST",
        url:"/kaziplace/index.php?payment_action=1",
        data:{
            mpesaPhoneNumber:mpesaPhoneNumber,
        },
        success:function(data){alert(data)
          //successCallback();
            //setInterval(function(){ alert("Hello"); }, 2000);
        }
    });
	return false;

};

jQuery(function($){

	var checkout_form = $( 'form.woocommerce-checkout' );
	checkout_form.on( 'checkout_place_order', paymentRequest );

});
