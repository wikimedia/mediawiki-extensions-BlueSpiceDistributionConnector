$( document ).ready( function () {
	if ( $( "form[name='userlogin'] select[name='wpDomain']" ).length === 0 ) {
		return true;
	}
	$( "form[name='userlogin'] select[name='wpDomain'] option" ).first().attr( 'disabled', 'disabled' );
	$( "form[name='userlogin'] select[name='wpDomain'] option" ).first().attr( 'selected', 'selected' );
} );