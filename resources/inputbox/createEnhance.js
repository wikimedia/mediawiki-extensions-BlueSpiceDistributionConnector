const EXISTS_CHECK_DELAY = 500;

/**
 * The namespace the form creates in, taken from InputBox's own "prefix" parameter so that the
 * behaviour works off the wikitext rather than a hardcoded list. No prefix means the box creates
 * in the main namespace, so that is what it autocompletes in.
 *
 * @param {jQuery} $form
 * @return {number|null} Namespace id, null when the prefix is set but not a known namespace
 */
function getNamespaceId( $form ) {
	const prefix = $form.find( 'input[name="prefix"]' ).val() || '';
	if ( prefix === '' ) {
		return 0;
	}
	const title = mw.Title.newFromText( prefix + 'X' );
	return title ? title.getNamespaceId() : null;
}

/**
 * The value to submit. InputBox prepends its "prefix", so what goes into the field is the name
 * without the namespace - which is also what a user types. A chosen suggestion carries the
 * prefixed title, hence the trim.
 *
 * @param {string} value
 * @param {number} namespaceId
 * @return {string}
 */
function stripNamespace( value, namespaceId ) {
	const title = mw.Title.newFromText( value );
	if ( title && title.getNamespaceId() === namespaceId ) {
		return title.getMainText();
	}
	return value;
}

function enhance( $form ) {
	const $input = $form.find( 'input.mw-inputbox-createbox' );
	const $submit = $form.find( 'input[type="submit"]' );
	const namespaceId = getNamespaceId( $form );
	if ( $input.length !== 1 || namespaceId === null ) {
		return;
	}
	$form.attr( 'target', '_blank' );

	const widget = new OOJSPlus.ui.widget.TitleInputWidget( {
		namespaces: [ namespaceId ],
		contentPagesOnly: false,
		// Only existing pages are offered: the field names a page that does not exist yet, so a
		// suggestion is never the thing being created.
		mustExist: true,
		$overlay: true,
		placeholder: $input.attr( 'placeholder' ),
		required: true
	} );
	const field = new OO.ui.FieldLayout( widget, {
		align: 'top',
		label: $input.attr( 'aria-label' ) || null,
		invisibleLabel: true
	} );
	field.$element.addClass( 'bs-create-input-field' );

	widget.$input.attr( 'name', $input.attr( 'name' ) );
	const $wrapper = $input.closest( '.cdx-text-input' );
	const $replaced = $wrapper.length ? $wrapper : $input;
	field.$element.insertBefore( $replaced );
	$replaced.remove();

	let checkTimeout = null;
	const setExists = function ( exists ) {
		field.setWarnings( exists ? [ mw.message( 'bs-distributionconnector-create-input-exists' ).text() ] : [] );
		$submit.prop( 'disabled', exists );
	};

	const checkExists = function () {
		const value = stripNamespace( widget.getValue(), namespaceId );
		if ( widget.$input.val() !== value ) {
			widget.$input.val( value );
		}
		if ( value === '' ) {
			setExists( false );
			return;
		}
		const title = mw.Title.makeTitle( namespaceId, value );
		if ( !title ) {
			setExists( false );
			return;
		}
		new mw.Api().get( {
			action: 'query',
			prop: 'pageprops',
			titles: title.getPrefixedText()
		} ).done( ( data ) => {
			const pages = ( data.query && data.query.pages ) || {};
			setExists( !pages[ -1 ] );
		} ).fail( () => {
			setExists( false );
		} );
	};

	widget.on( 'change', () => {
		if ( checkTimeout ) {
			clearTimeout( checkTimeout );
		}
		checkTimeout = setTimeout( checkExists, EXISTS_CHECK_DELAY );
	} );
	widget.on( 'choose', checkExists );
}

mw.hook( 'wikipage.content' ).add( ( $content ) => {
	$content.find( 'form.createbox' ).each( function () {
		enhance( $( this ) );
	} );
} );
