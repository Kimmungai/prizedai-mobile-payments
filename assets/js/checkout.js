

var successCallback = function() {

	var checkout_form = jQuery( 'form.woocommerce-checkout' );

	// add a token to our hidden input field
	// console.log(data) to find the token
	//checkout_form.find('#misha_token').val(data.token);

  if( 1 ){

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

var tokenRequest = function() {

	// here will be a payment gateway function that process all the card data from your form,
	// maybe it will need your Publishable API key which is misha_params.publishableKey
	// and fires successCallback() on success and errorCallback on failure
  jQuery.ajax({
        type: "POST",
        url:"/kaziplace/index.php?payment_action=1",
        data:{
            'ref_id': '' ,
            '_token' : ''
        },
        success:function(data){
          successCallback();
            //setInterval(function(){ alert("Hello"); }, 2000);
        }
    });
	return false;

};

jQuery(function($){

	var checkout_form = $( 'form.woocommerce-checkout' );
	checkout_form.on( 'checkout_place_order', tokenRequest );

});
