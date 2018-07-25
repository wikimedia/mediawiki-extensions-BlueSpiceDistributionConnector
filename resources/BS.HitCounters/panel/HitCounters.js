Ext.define( 'BS.HitCounters.panel.HitCounters', {
	extend: 'Ext.Panel',
	cls: 'bs-hitcounters-flyout-hitcounters',
	text: '',
	counts: 0,
	initComponent: function () {
		if ( this.counts ) {
			var html = '<div class="flyout-hitcounters-hitcounts">';

			html += "<span>" + this.text + ":</span> ";

			html += this.counts;

			html += '</div>';

			this.html = html;
		}

		this.callParent( arguments );
	}
} );
