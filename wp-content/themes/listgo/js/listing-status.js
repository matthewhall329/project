;(function($){
	var aListingIDs  = [];
	var $listingStatus = $('.wiloke-listgo-listing-status-placeholder');
	$(document).ready(function () {
		if ( $listingStatus.length ){
			$listingStatus.each(function () {
				var $this = $(this);
				aListingIDs.push({
					listingid: $this.data('listingid'),
					ignoreclosin: $this.data('ignoreclosin')
				});
			})
		}
	});

	function fetchAjax($this, aData){
		$.ajax({
			type: 'GET',
			data: {
				listingID: aData.listingid,
				ignoreClosein: aData.ignoreclosin,
				action: 'wiloke_listgo_get_listing_status'
			},
			url: WILOKE_GLOBAL.ajaxurl,
			success: function(response){
				$this.html(response.data.msg);
			}
		})
	}

	$(window).on('load', function(){
		if ( aListingIDs.length ){
			var fetchEachTwoSeconds = setInterval(function () {
				if ( aListingIDs.length === 0){
					clearInterval(fetchEachTwoSeconds);
				}
				var count = 0;
				for ( var key in aListingIDs ){
					fetchAjax($('.wiloke-listgo-listing-status-placeholder.listing-id-'+aListingIDs[key]['listingid']), aListingIDs[key]);
					aListingIDs.splice(key, 1);

					if ( count === 5 ){
						break;
					}
					count++;
				}
			}, 2000);
		}
	})
})(jQuery);