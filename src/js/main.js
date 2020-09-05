jQuery( document ).ready(function() {
		var url = window.location.href;
		var switch_tab = null;

		if( url.includes( 'page=prizedai_mobile_payments_plugin#prizedai-tab-1' ) )
			switchTabOnRefresh( 1 )
		else if( url.includes( 'page=prizedai_mobile_payments_plugin#prizedai-tab-2' ) )
			switchTabOnRefresh( 2 )
    else if( url.includes( 'page=prizedai_mobile_payments_plugin#prizedai-tab-3' ) )
			switchTabOnRefresh( 3 )
});

window.addEventListener( "load", function(){

	//store tabs variables
	var tabs = document.querySelectorAll("ul.prizedai-nav-tabs > li");

	for (var i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener( "click", switchTab );
	}

	function switchTab( event )
	{
		event.preventDefault();
		removeActiveClasses();

		var clickedTab = event.currentTarget;
		var anchor = event.target;
		var activePaneID = anchor.getAttribute("href");


		clickedTab.classList.add("active");
		document.querySelector(activePaneID).classList.add("active");

	}

});

function switchTabOnRefresh( tab )
{
	removeActiveClasses();
	document.getElementById('prizedai-tab-btn-'+tab).classList.add("active");
	document.getElementById('prizedai-tab-'+tab).classList.add("active");
}

function removeActiveClasses()
{
	document.querySelector( "ul.prizedai-nav-tabs li.active" ).classList.remove("active");
	document.querySelector( ".prizedai-tab-pane.active" ).classList.remove("active");
}
