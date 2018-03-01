;!(function ($) {
	"use strict";

	let rtl = false;

	function mergeObject(obj, src) {
		for (let key in src) {
			if (src.hasOwnProperty(key)) obj[key] = src[key];
		}
		return obj;
	}


	function bs_fix_vc_full_width_row(){
		let $elements = $('[data-vc-full-width="true"]');
		$elements.each(function(index, el) {
			let $el = $(this);
			$el.css('right', $el.css('left')).css('left', '');
		});
	}

	if ( $('html[dir="rtl"]').length ) {
		rtl = true;
		// Fixes rows in RTL
		$(document).on('vc-full-width-row', function () {
			bs_fix_vc_full_width_row();
		});
		bs_fix_vc_full_width_row();
	}

	class WilokeDetectMobile{
		static Android(){
			return navigator.userAgent.match(/Android/i);
		}

		static BlackBerry(){
			return navigator.userAgent.match(/BlackBerry/i);
		}

		static IOS(){
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		}

		static IPHONEIPOD(){
			return navigator.userAgent.match(/iPhone|iPod/i);
		}

		static Opera() {
			return navigator.userAgent.match(/Opera Mini/i);
		}

		static Windows() {
			return navigator.userAgent.match(/IEMobile/i);
		}

		static Any(){
			return (this.Android() || this.BlackBerry() || this.IOS() || this.Opera() || this.Windows());
		}

		static exceptiPad(){
			return (this.Android() || this.BlackBerry() || this.IPHONEIPOD() || this.Opera() || this.Windows());
		}
	}

	class WilokeDetectBrowser{
		static isIE(){
			return ((navigator.userAgent.indexOf('MSIE') !== -1) || (navigator.userAgent.indexOf('Trident') !== -1));
		}
	}

	function decimalAdjust(type, value, exp) {
		// If the exp is undefined or zero...
		if (typeof exp === 'undefined' || +exp === 0) {
			return Math[type](value);
		}
		value = +value;
		exp = +exp;
		// If the value is not a number or the exp is not an integer...
		if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
			return NaN;
		}
		// Shift
		value = value.toString().split('e');
		value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
		// Shift back
		value = value.toString().split('e');
		return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
	}

	if (!Math.round10) {
		Math.round10 = function(value, exp) {
			return decimalAdjust('round', value, exp);
		};
	}

	$.fn.checkRowHandled = function (rmClass, timeout) {
		let $this = $(this);
		let now = new Date();
		$this.data('watchAt', now.getSeconds());

		if ( $this.data('watchAt') ){
			let startedAt = parseInt($this.data('watchAt'));
			if ( now.getSeconds() - startedAt > 4 ){
				clearTimeout($this.data('watchRow'));
				$(window).trigger('resize');
				$(window).trigger('Wiloke.Resizemap');
				return false;
			}
		}

		$this.data('watchRow', setTimeout(function () {
			if ( $this.attr('style') !== '' && typeof $this.attr('style') !== 'undefined' ){
				$this.removeClass(rmClass);
				clearTimeout($this.data('watchRow'));
				$this.removeData('watchRow');
				if ( $this.hasClass('is-last-row') ){
					$(window).trigger('resize');
					$(window).trigger('Wiloke.Resizemap');
				}
			}
		}, timeout));
	};

	function fixStupidVisualComposerCaculation() {
		$('.wiloke-row-handling').each(function () {
			$(this).checkRowHandled('wiloke-row-handling', 2000);
		});
		$('.wiloke-row-handling:last').addClass('is-last-row');

		setTimeout(function () {
			$('body').find('.wiloke-row-handling').removeClass('wiloke-row-handling');
		}, 3000);
	}

	class Helpers{
		constructor(){
			this.blockID = null;
			this.name = null;
			this.prefix = 'wiloke_listgo_';
			this.aPageLoaded = [];
			this.period = 86400;
			// We support 2 modes: localStorage (Period caching) and variable caching (temporary caching)
			this.cachingMode = 'default';
			this.aPageCaching = {};
			this.oText = WILOKE_LISTGO_TRANSLATION;
		}

		notFound(){
			let generateNotFound = _.template('<p><%- text %></p>');
			return generateNotFound({text: this.oText.notfound});
		}

		setPageLoaded(oData){
			this.aPageLoaded.push(oData.fullPath);
		}

		getPageLoaded(oData){
			if ( !this.aPageLoaded.length ){
				return false;
			}

			return ((this.aPageLoaded).indexOf(oData.fullPath) !== -1);
		}

		/**
		 * It may be category or post type such as listing, post, category
		 */
		setCaching(oData, name, blockID){
			this.name = this.prefix + name;
			this.blockID = blockID;

			if( !_.isUndefined(this.blockID) ){
				this.generateBlockIDKey();
			}

			let oDataToJSON = JSON.stringify(oData);
			if ( this.cachingMode === 'default' ){
				this.aPageCaching[this.name] = oDataToJSON;
			}else{
				localStorage.setItem(this.name, oDataToJSON);
				this.saveCreatedAt();
			}
		}

		getCaching(name, blockID, createdAt){
			this.name = this.prefix + name;
			this.blockID = blockID;

			this.generateBlockIDKey();
			let savedAt = this.getCreatedAt();
			createdAt = parseInt(createdAt, 10);
			savedAt = parseInt(savedAt);

			if ( !_.isNaN(createdAt) && !_.isNaN(savedAt) && (createdAt > savedAt) ){
				return false;
			}else{
				if ( this.cachingMode === 'default' ){
					if ( typeof (this.aPageCaching[this.name]) === 'undefined' ){
						return false;
					}

					return JSON.parse(this.aPageCaching[this.name]);
				}else{
					let oData = localStorage.getItem(this.name);
					return JSON.parse(oData);
				}
			}
		}

		static deleteCaching(name){
			localStorage.setItem(name, false);
		}

		parseJSON(data){
			try {
				return JSON.parse(data);
			}catch(error){
				if ( WilokeDevMod ){
					return false;
				}else{
					return false;
				}
			}
		}

		setListings(data){
			this.aPageCaching['all'] = data;
		}

		getListings(){
			let data = this.aPageCaching['all'];
			return _.isUndefined(data) ? false : data;
		}

		generateBlockIDKey(){
			this.name = this.name + '_' + this.blockID;
		}

		saveCreatedAt(){
			let createdAt = new Date().getTime();
			localStorage.setItem(this.name+'_created_at', createdAt);
		}

		getCreatedAt(){
			return localStorage.getItem(this.name+'_created_at');
		}
	}

	const instHelpers = new Helpers();

	class WilokeMap{
		constructor(mapID){
			this.id = mapID;
			this.$container = $('#'+mapID);
			this.xhr = null;
			this.oSearchCaching = [];
			this.reCheck = false;
			this.currentDestination = [];
			this.aLayers = [];
			this.mapInit();
		}

		/**
		 * All templates will be configured here
		 * @since 1.0
		 */
		sidebarListingTpl(oItem){
			let mapItem = _.template(`<li class="listing-item <%- hiddenClass %>" data-placeid="<%- oItem.placeID %>" data-locationid="<%- oItem.first_location_id %>" data-postid="<%- oItem.ID %>" data-latlng="<%- oItem.listing_settings.map.latlong %>" data-catids="<%- oItem.listing_cat_id %>" data-parentlocationid="<%- oItem.parentLocationID %>">
				<% if (!_.isUndefined(oItem.thumbnail) && (oItem.thumbnail !== '') && oItem.thumbnail){ %>
					<div class="listing-item__media">
						<img src="<%- oItem.thumbnail %>" alt="<%- oItem.title %>">
						<% if ( oItem.business_status.status === 'opening'){ %> 
		                    <span class="ongroup">
		                    	<span class="onopen"><%- opennow %></span>
		                    </span>
	                    <% }else if(oItem.business_status.status === 'closed'){ %>
		                    <span class="ongroup">
		                    	<span class="onclose red"><%- closednow %></span>
		                    </span>
	                    <% } %>
					</div>
				<% } %>
                <div class="overflow-hidden">
                    <h4><%- oItem.title %></h4>
                    <div class="listgo__rating">
						<span class="rating__star">
							<% for (var i = 1; i <= 5; i++){ %>
								<% var className = ''; %>
								<% if (oItem.average_rating < i ){ %>
									<% className = i == Math.floor(oItem.average_rating) ? 'fa fa-star-half-o' : 'fa fa-star-o'; %>
								<% }else{ %>
									<% className = 'fa fa-star'; %>
								<% } %>
								<i class="<%- className %>"></i>
							<% } %>
						</span>
						<span class="rating__number"><%- oItem.average_rating %></span>
					</div>
                    <% if (oItem.listing_settings){ %>
                    <p><i class="icon_pin_alt"></i><%- oItem.listing_settings.map.location %></p>
                    <% } %>
                    <span class="actions"><a href="<%- oItem.link %>"><%- readmore %></a> | <a target="_blank" href="//maps.google.com/maps?daddr=<%- oItem.listing_settings.map.latlong %>"><%- getdirections %></a></span>
                </div>
            </li>`);

			let hiddenClass = !this.isInCategory(oItem) || !this.isInLocation(oItem) || !this.isInsideCircle(oItem.listing_settings.map.latlong) || !this.isMatchedTitle(oItem) || !this.isOpenNow(oItem) ? 'hidden' : '';
			return mapItem({oItem: oItem, hiddenClass:hiddenClass, readmore: this.oTranslation.readmore, getdirections: this.oTranslation.getdirections, opennow: this.oTranslation.opennow, closednow: this.oTranslation.closednow});
		}

		generateListingSidebar(){
			if ( !_.isEmpty(this.aListings) ){
				let listings = '';
				let aListings = this.aListings;
				if ( this.s_highest_rated === 1 ){
					aListings = _.sortBy(aListings, function(aListing){
						return aListing.average_rating;
					});
					aListings.reverse();
				}

				_.forEach(aListings, (oItem=>{
					listings += this.sidebarListingTpl(oItem);
				}));

				this.$showListing.html(listings);
			}
		}

		watch(){
			this.$container.on('listing_change', (()=>{
				this.generateListingSidebar();
			}));

			this.$searchForm.on('focus_search', (()=>{
				let s = this.$search.val(), isMatched=false;
				if ( s === '' || _.isUndefined(s) ){
					return false;
				}

				_.forEach(this.aRawListings, (aListing=>{
					s = s.toLowerCase();
					let title = aListing.title.toLowerCase();
					if ( title === s ){
						this.currentDestination = aListing;
						this.panTo(aListing.listing_settings.map.latlong);
					}
				}));
			}));

			this.$showListing.on('click', 'li', (event=>{
				this.panTo($(event.currentTarget).data('latlng'));
				if ( WilokeDetectMobile.Any() ){
					this.$wrap.removeClass('list-map-wrap--setting-active');
				}
			}));
		}

		// Map
		mapIcon(oItem){
			let iconUrl = decodeURIComponent(WILOKE_GLOBAL.homeURI) + 'img/icon-marker.png';

			if( oItem.listing_cat_settings && !_.isUndefined(oItem.listing_cat_settings.map_marker_image) && oItem.listing_cat_settings.map_marker_image !== '' ){
				iconUrl = oItem.listing_cat_settings.map_marker_image;
			}

			return L.icon({
				iconUrl: iconUrl
			});
		}

		/**
		 * Only show service (listing_cat) that has picked up. All services will be shown if listing_cat is null
		 * @since 1.0
		 */
		showService(catID){
			if ( !_.isNaN(catID) && catID !== '' ){
				_.forEach(this.oMapLayers, (oMapLayer=>{
					let check = false;

					if ( check = this.isInLocation(oMapLayer) ){
						check = false;
						_.forEach(oMapLayer.categories, (oTerm=>{
							if ( catID === oTerm.term_id ){
								check = true;
								return false;
							}
						}));
					}
					if ( check ){
						oMapLayer.instLayer.setOpacity(1);
					}else{
						oMapLayer.instLayer.setOpacity(0);
					}
				}));
			}else{
				_.forEach(this.oMapLayers, (oMapLayer=>{
					oMapLayer.instLayer.setOpacity(1);
				}));
			}
		}

		panTo(coordinate, isNotShowPopup){
			let coordinateToStr = null,
				aCoordinate = coordinate;
			if (!_.isArray(coordinate)){
				coordinateToStr = coordinate;
				aCoordinate = coordinate.split(',');
				aCoordinate = _.map(aCoordinate, (value=>{
					return parseFloat(value);
				}));

				this.instMap.panTo(aCoordinate);
			}else{
				coordinateToStr = coordinate.join(',');
				this.instMap.panTo(coordinate);
			}

			if ( !_.isUndefined(this.aLayers[aCoordinate.join('-')]) && typeof isNotShowPopup === 'undefined' ){
				this.aLayers[aCoordinate.join('-')].fire('click');
			}

			this.$showListing.find('li').removeClass('active');
			this.$showListing.find('li[data-latlng="'+coordinateToStr+'"]').addClass('active');
		}

		parseCoordinate(coordirnate){
			if ( coordirnate === '' ){
				return false;
			}
			let newCoodirnate = coordirnate.split(',');
			newCoodirnate = _.map(newCoodirnate, (val=>{
				return parseFloat(val);
			}));

			return newCoodirnate;
		}

		getCenter(){
			for ( let i in this.aListings ){
				return this.aListings[i].listing_settings.map.latlong;
			}
		}

		setPopup(layer, aCoodirnate, oItem){
			this.aLayers[aCoodirnate.join('-')] = layer;
			layer.addEventListener('click', ((event)=>{
				this.createPopup(oItem, aCoodirnate);
			}));
		}

		renderTerms(oTerms){
			let template = '', total = oTerms.length - 1;

			_.forEach(oTerms, ((oTerm, key)=>{
				if ( key === 0 ){
					template += '<a href="'+oTerm.link+'">'+oTerm.name+'</a>';
				}else{
					if ( ( key === 1 ) ) {
						template += '<span class="listing__cat-more">+</span><ul class="listing__cats">';
					}
					template += '<li><a href="'+oTerm.link+'">'+oTerm.name+'</a></li>';

					if ( total === key ){
						template += '</ul>';
					}
				}
			}));

			template = '<div class="listing__cat">' + template + '</div>';

			return template;
		}

		renderContent(oAddress){
			let template = '';
			if ( !_.isUndefined(oAddress) ){
				template += '<div class="listing__content">';
				template += '<div class="address">';
				_.forEach(oAddress, ((info, key)=>{

					if ( (info !== '') && !_.isUndefined(this.oText[key]) && (key !== 'website') && (key !== 'email') ){
						if ( key === 'phone_number' ){
							info = '<a href="tel:'+info+'">'+info+'</a>';
						}
						template += '<span class="address-'+ key +'"><strong>' + this.oText[key] + ':</strong> ' + info + '</span>';
					}

				}));
				template += '</div>';
				template += '</div>';
			}

			return template;
		}

		createPopup(oItem, aCoodirnate){
			let featuredImage = oItem.featured_image ? oItem.featured_image : '',
				address = this.renderContent(oItem.listing_settings);

			let popupTemplate = _.template(`<div class="listing listing--grid">
					<div class="listing__media">
						<a href="<%- oItem.link %>" style='background-image: url(<%= featuredImage %>)'></a>
						<div class="listing__author">
							<a href="<%- oItem.author.link %>">
							<% if ( !_.isUndefined(oItem.author.user_first_character) ){ %>
								<span style="background-color: <%= oItem.author.avatar_color %>" class="widget_author__avatar-placeholder"><%- oItem.author.user_first_character %></span>
							<% }else{ %>
								<img src="<%- oItem.author.avatar %>" alt="<%- oItem.author.nickname %>">
							<% } %>
							</a>
						</div>
					</div>
					<div class="listing__body">
						<h3 class="listing__title"><a href="<%- oItem.link %>"><%- oItem.title %></a></h3>
						<div class="listgo__rating">
							<span class="rating__star">
								<% for (var i = 1; i <= 5; i++){ %>
									<% var className = ''; %>
									<% if (oItem.average_rating < i ){ %>
										<% className = i == Math.floor(oItem.average_rating) ? 'fa fa-star-half-o' : 'fa fa-star-o'; %>
									<% }else{ %>
										<% className = 'fa fa-star'; %>
									<% }  %>
									<i class="<%- className %>"></i>
								<% } %>
							</span>
						</div>
						<%= address %>
						</div>
					</div>`);

			let popup = L.popup({
					maxWidth: 400,
					className: 'wo-map-popup',
					closeButton: false,
					offset: [14, -15]
				})
				.setLatLng(aCoodirnate)
				.setContent(popupTemplate({oItem: oItem, address: address, featuredImage: featuredImage}))
				.openOn(this.instMap);
		}

		/**
		 * When an user click on a map marker. There are two actions will be executed: Listing Popup - some description about
		 * that article, and trigger Map Didrection
		 */
		layerOnClick(oLayer){
			oLayer.addEventListener('click', (()=>{
				this.oDirections.setDestination(oLayer._latlng);
				this.mapDirectionQuery();
			}));
		}

		/**
		 * Add Listings to Map
		 */
		addLayers(oItems){
			_.forEach(oItems, (oItem=>{
				this.aPostIDs.push(oItem.ID);
				if ( oItem.listing_settings && !_.isUndefined(oItem.listing_settings.map) ){
					if ( typeof oItem.listing_settings.map === 'undefined' || oItem.listing_settings.map.latlong === '' ){
						return false;
					}

					let aCoordinate = this.parseCoordinate(oItem.listing_settings.map.latlong);

					// Ensure that we have the both latitude and longitude
					if ( aCoordinate.length === 2 ){
						let layer = L.marker(aCoordinate, {
							icon: this.mapIcon(oItem)
						});

						this.oClusters.addLayer(layer);
						this.instMap.addLayer(this.oClusters);
						this.setPopup(layer, aCoordinate, oItem);

						if ( this.s_listing_cat !== '' ){
							if ( !_.isEmpty(oItem.listing_cat) ){
								let check = false;
								_.forEach(oItem.listing_cat, (oTerm=>{
									if ( this.s_listing_cat === oTerm.term_id ){
										check = true;
										return false;
									}
								}));
								if ( !check ){
									layer.setOpacity(0);
								}
							}else{
								layer.setOpacity(0);
							}
						}

						this.oMapLayers.push({
							instLayer: layer,
							categories: oItem.listing_cat,
							listing_location_id: oItem.listing_location_id,
							listing_cat_id: oItem.listing_cat_id,
							title: oItem.title
						});
					}
				}
			}));

			this.$container.trigger('loadmore_map');
		}

		setUp(){
			this.accessToken = WILOKE_GLOBAL.maptoken;
			this.instHelpers = instHelpers;

			this.aTemporaryCachingResults = [];
			this.cachingMylocation = null;
			this.instMyLocation = null;
			this.radiusInKM = 5;
			this.circle = false;
			this.$wrap = this.$container.closest('.listgo-map-wrap');
			this.$sidebar = this.$wrap.find('.listgo-map__settings');
			this.$toggleSidebar = this.$wrap.find('.searchbox-hamburger');

			this.$mapSidebar = this.$wrap.find('#listgo-map__sidebar');
			this.$showListing = this.$mapSidebar.find('#listgo-map__show-listings');
			this.$searchForm = this.$wrap.find('#listgo-searchform');
			this.$location = this.$wrap.find('#s_listing_location');
			this.$submitSearchKeyWord = this.$wrap.find('#listgo-submit-searchkeyword');
			this.$search = this.$wrap.find('#s_search');
			this.$openNow = this.$wrap.find('#s_opennow');
			this.$highestRated = this.$wrap.find('#s_highestrated');
			this.$category = this.$searchForm.find('#s_listing_cat');
			this.$location = this.$searchForm.find('#s_listing_location');
			this.oData = this.$container.data('configs');
			this.s_open_now = 0;
			this.s_highest_rated = 0;
			this.s_listing_location = this.$location.val();
			this.s_listing_cat = this.$category.val();
			this.s_listing_cat = this.s_listing_cat !== '' ? parseInt(this.s_listing_cat, 10) : '';

			this.aRawListings = this.$container.data('listings');
			this.aListings = this.aRawListings;
			this.oMapLayers = [];
			this.aListingInCats = [];
			this.aListingInLocations = [];
			this.firstItemInCat = null;
			this.oDefault = {
				center: this.getCenter(),
				maxClusterRadius: parseInt(WILOKE_GLOBAL.mapcluster, 10),
				mapTheme: null,
				minZoom: null,
				maxZoom: null,
				centerZoom: null
			};

			this.aPostIDs = [];
			this.oText = WILOKE_LISTGO_TRANSLATION;
			this.oData = $.extend( {}, this.oDefault, this.oData);

			// Search
			this.s = null;
			this.find_listing_location = null;
			this.find_listing_cat = null;
		}

		convertRadiusToMeters() {
			return this.radiusInKM * 1000;
		}

		setCircle() {
			if ( !this.circle ){
				this.circle = L.circle(this.cachingMylocation,{
					radius: 100
				}).addTo(this.instMap);
				this.instMap.fitBounds(this.circle.getBounds());
			}else{
				this.circle.setRadius(100);
			}
			this.filterLocationInsideCircle();
		}

		filterLocationInsideCircle(){
			this.instMyLocation = this.instMyLocation === null ? L.latLng(this.cachingMylocation) : this.instMyLocation;
			_.each(this.aRawListings, (aListing=>{
				if ( this.isInsideCircle(aListing.listing_settings.map.latlong) ){
					this.$showListing.find('[data-latlng="'+aListing.listing_settings.map.latlong+'"]').removeClass('hidden');
					this.aTemporaryCachingResults.push(aListing);
				}else{
					this.$showListing.find('[data-latlng="'+aListing.listing_settings.map.latlong+'"]').addClass('hidden');
				}
			}));
		}

		detectingMyLocation(){
			let instDate = new Date();
			this.$mapSidebar.find('.listgo-map__result').addClass('loading');
			this.getMylocationFromCaching();

			if ( this.cachingMylocation === null ) {
				this.instMap.on('locationfound', (event => {
					this.cachingMylocation = event.latlng;
					localStorage.setItem('listgo_mylocation', JSON.stringify(this.cachingMylocation));
					localStorage.setItem('listgo_mylocation_created_at', instDate.getMinutes());
					this.instMap.panTo(this.cachingMylocation);
					this.setCircle();
				}));

				this.instMap.on('locationerror', (event => {
					alert(event.message);
				}));

				this.instMap.locate({setView: true, maxZoom: this.oData.centerZoom, maximumAge: 30000, enableHighAccuracy: true});
			}else{
				this.instMap.panTo(this.cachingMylocation);
				this.setCircle();
			}
		}

		getMylocationFromCaching(){
			let instDate = new Date();
			if ( this.cachingMylocation === null ){
				let createdAt = localStorage.getItem('listgo_mylocation_created_at');
				if ( (instDate.getMinutes() - createdAt) <= 5 ){
					this.cachingMylocation = $.parseJSON(localStorage.getItem('listgo_mylocation'));
				}
			}
		}

		getMyLocationController() {
			let self = this,
				homeURL = decodeURIComponent(WILOKE_GLOBAL.homeURI),
				iconURL = homeURL + 'img/detect-mylocation.png',
				getMyLocationBtn = L.Control.extend({
					options: {
						position: 'topright'
					},
					onAdd: ((map)=>{
						let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-detect-mylocation');
						container.style.backgroundColor = 'white';
						container.style.backgroundImage = 'url('+iconURL+')';
						container.style.backgroundSize = '30px 30px';
						container.style.width = '30px';
						container.style.height = '30px';

						container.onclick = function(){
							if ( !WilokeDetectMobile.Any() ){
								self.$wrap.addClass('list-map-wrap--setting-active');
								self.$searchForm.find('#s_listing_location').val('mylocation').trigger('change');
								self.s_listing_location = 'mylocation';
								self.s = '';
								self.$showListing.trigger('click');
							}else{
								self.getMylocationFromCaching();
								if ( self.cachingMylocation === null ){
									self.instMap.on('locationfound', (event => {
										self.cachingMylocation = event.latlng;
										self.instMap.panTo(self.cachingMylocation);
									}));
									self.instMap.locate({setView: true, maxZoom: self.oData.centerZoom, maximumAge: 30000, enableHighAccuracy: true});
								}else{
									self.instMap.panTo(self.cachingMylocation);
								}
							}
						};
						return container;
					})
				});
			return new getMyLocationBtn;
		}

		mapInit(){
			if ( !this.$container.length || this.$container.data('initialized') ){
				return false;
			}

			this.ggGeoCode = null;
			this.oTranslation = WILOKE_LISTGO_TRANSLATION;
			this.sByPlaceID = null;
			this.sByLatLng = null;
			this.setUp();
			this.watch();
			this.showResult();

			if ( !_.isEmpty(this.aListings) ){

				L.accessToken = this.accessToken;

				if ( _.isNull(this.oData.mapTheme) || this.oData.mapTheme === '' ){
					this.oData.mapTheme = WILOKE_GLOBAL.maptheme;
				}

				if ( _.isNull(this.oData.maxZoom) || this.oData.maxZoom === '' ){
					this.oData.maxZoom = WILOKE_GLOBAL.mapmaxzoom;
				}

				if ( _.isNull(this.oData.minZoom) || this.oData.minZoom === '' ){
					this.oData.minZoom = WILOKE_GLOBAL.mapminzoom;
				}

				if ( _.isNull(this.oData.centerZoom) || this.oData.centerZoom === '' ){
					this.oData.centerZoom = WILOKE_GLOBAL.centerZoom;
				}

				let center = this.parseCoordinate(this.oData.center);
				let centerZoom = WilokeDetectMobile.Any() ? 2 : this.oData.centerZoom;
				this.$container.data('initialized', true);

				this.instMap = L.mapbox.map(this.id, null, {
					minZoom: this.oData.minZoom,
					maxZoom: this.oData.maxZoom
				}).setZoom(centerZoom).setView(center);
				L.tileLayer(this.oData.mapTheme).addTo(this.instMap);
				this.instMap.addControl(this.toggleDraggableControl());
				this.instMap.addControl(this.getMyLocationController());
				this.oClusters = L.markerClusterGroup({
					animateAddingMarkers: false,
					zoomToBoundsOnClick: true,
					zoomToShowLayer: true,
					showCoverageOnHover: true,
					disableClusteringAtZoom: false,
					spiderfyOnMaxZoom: true,
					chunkedLoading: true,
					maxClusterRadius: this.oData.maxClusterRadius,
					trackResize: true,
					iconCreateFunction: function(cluster) {
						return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>' });
					}
				});

				this.$container.on('loadmore_map', (()=>{
					this.fetching();
				}));

				this.$container.on('reset_listing',(()=>{
					this.aFilterListings();
				}));

				this.$container.trigger('listing_change');

				this.$submitSearchKeyWord.on('click', ((event)=>{
					event.preventDefault();
					this.$searchForm.trigger('focus_search');
				}));

				this.addLayers(this.aListings);
				this.controlPanel();

				if ( this.$search.val() !== '' ){
					this.$searchForm.trigger('focus_search');
					this.$wrap.addClass('list-map-wrap--setting-active');
				}

				let oLocationDefault = {};
				if ( this.s_listing_location !== '' ){
					if ( this.$searchForm.find('#s-location-place-id').val() !== '' ){
						oLocationDefault['placeID'] = this.$searchForm.find('#s-location-place-id').val();
						oLocationDefault['is_suggestion'] = false;
					}else if (this.$searchForm.find('#s-location-term-id').val() !== ''){
						oLocationDefault['term_id'] = this.$searchForm.find('#s-location-term-id').val();
						oLocationDefault['is_suggestion'] = true;
					}

					if ( !_.isEmpty(oLocationDefault) ){
						this.$location.trigger('location_changed', [oLocationDefault]);
					}
				}
			}
		}

		toggleDraggableControl(){
			let $container = this.$container;
			let instMap = this.instMap;
			let homeURL = decodeURIComponent(WILOKE_GLOBAL.homeURI);
			let draggableURL = homeURL + 'img/draggable.png';
			let disableDraggableURL = homeURL + 'img/disabledraggable.png';
			let iconURL = '';
			let toggleDraggable = L.Control.extend({
				options: {
					position: 'topright'
				},
				onAdd: ((map)=>{
					let container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-toggle-draggable');

					if ( $container.data('disabledraggableoninit') === 'enable' ){
						instMap.dragging.disable();
						instMap.touchZoom.disable();
						instMap.zoomControl.disable();
						instMap.scrollWheelZoom.disable();
						instMap.touchZoom.disable();
						instMap.boxZoom.disable();
						if (instMap.tap){
							instMap.tap.disable();
						}
						instMap.doubleClickZoom.disable();
						$container.data('isDisabled', true);
						iconURL = draggableURL;
					}else{
						iconURL = disableDraggableURL;
					}

					container.style.backgroundColor = 'white';
					container.style.backgroundImage = 'url('+iconURL+')';
					container.style.backgroundSize = '30px 30px';
					container.style.width = '30px';
					container.style.height = '30px';

					container.onclick = function(){
						if ( $container.data('isDisabled') ){
							instMap.dragging.enable();
							instMap.touchZoom.enable();
							if (instMap.tap){
								instMap.tap.enable();
							}
							instMap.zoomControl.enable();
							instMap.touchZoom.enable();
							instMap.boxZoom.enable();
							instMap.doubleClickZoom.enable();
							instMap.scrollWheelZoom.enable();
							$container.data('isDisabled', false);
							container.style.backgroundImage = 'url('+disableDraggableURL+')';
						}else{
							instMap.dragging.disable();
							instMap.touchZoom.disable();
							instMap.zoomControl.disable();
							if (instMap.tap){
								instMap.tap.disable();
							}
							instMap.touchZoom.disable();
							instMap.doubleClickZoom.disable();
							instMap.boxZoom.disable();
							instMap.scrollWheelZoom.disable();
							$container.data('isDisabled', true);
							container.style.backgroundImage = 'url('+draggableURL+')';
						}
					};

					return container;
				})
			});

			return new toggleDraggable;
		}

		selected(currentStatus){
			return (this.s_mapdirections === currentStatus);
		}

		isInsideCircle(latlng){
			if ( this.instMyLocation === null || this.s_listing_location !== 'mylocation' ){
				return true;
			}
			return this.instMyLocation.distanceTo(this.parseCoordinate(latlng)) <= this.convertRadiusToMeters();
		}

		hasMatched(aFirst, aSecond){
			if ( !aFirst || !aSecond ){
				return false;
			}

			let status = false;

			_.each(aFirst, (list=>{
				if ( aSecond.indexOf(list) !== -1 ){
					status = true;
					return true;
				}
			}));

			return status;
		}

		detectListingIncatByListinData(aCatOfListing){
			if ( this.s_listing_cat === '' || _.isNaN(this.s_listing_cat) ){
				return true;
			}
			if ( typeof aCatOfListing === 'undefined' ){
				return false;
			}

			if ( _.isNumber(aCatOfListing) ){
				return (this.s_listing_cat === aCatOfListing);
			}

			aCatOfListing = aCatOfListing.split(',');

			return (aCatOfListing.indexOf(this.s_listing_cat) !== -1);
		}

		showUpAllListingInLocation(aParentIDs){
			this.aListingInLocations = this.aListingInLocations.concat(aParentIDs);
			_.map(aParentIDs, ((parentID)=>{
				let oGetParent = this.dominoPopupChildrenLocation(parentID);
				let $item = this.$showListing.find('li.listing-item[data-locationid="'+parentID+'"]');
				if ( this.detectListingIncatByListinData($item.data('catids')) ) {
					$item.removeClass('hidden');
				}else{
					$item.addClass('hidden');
				}
				if ( !_.isEmpty(oGetParent) ){
					this.showUpAllListingInLocation(oGetParent);
				}
			}));
		}

		dominoPopupChildrenLocation(parentID){
			let self = this, oGetParent = [];
			this.$showListing.find('li.listing-item[data-parentlocationid="'+parentID+'"]').each(function(event){
				if ( self.detectListingIncatByListinData($(this).data('catids')) ) {
					$(event.currentTarget).removeClass('hidden');
					oGetParent.push($(this).attr('data-locationid'));
				}
			});

			return oGetParent;
		}

		deg2rad(deg) {
			return deg * (Math.PI/180);
		}

		getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
			let R = 6371, // Radius of the earth in km
				dLat = this.deg2rad(lat2-lat1),  // deg2rad below
				dLon = this.deg2rad(lon2-lon1),
				a =
					Math.sin(dLat/2) * Math.sin(dLat/2) +
					Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
					Math.sin(dLon/2) * Math.sin(dLon/2);
			let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
			let d = R * c; // Distance in km
			return d;
		}

		showUpAllListingInRadius(){
			let aLatLng = null, self = this, distance=0;
			this.$mapSidebar.find('li.listing-item').each(function () {
				aLatLng = $(this).data('latlng');
				if ( typeof aLatLng !== 'undefined' && self.detectListingIncatByListinData($(this).data('catids')) ){
					aLatLng = aLatLng.split(',');
					distance = self.getDistanceFromLatLonInKm(parseFloat(aLatLng[0], 10), parseFloat(aLatLng[1], 10), self.sByLatLng['lat'], self.sByLatLng['lng']);
					if ( distance <= self.radiusInKM ){
						$(this).removeClass('hidden');
						self.aListingInLocations.push($(this).data('locationid'));
					}
				}
			})
		}

		isInLocation(oListing){
			if ( _.isEmpty(this.aListingInLocations) ){
				return true;
			}

			let i = 0, totalLocationIListing = oListing.listing_location_id.length;
			let isInLocation = false;
			for ( i = 0; i < totalLocationIListing; i++ ){
				if ( this.aListingInLocations.indexOf(oListing.listing_location_id[i]) !== -1 ){
					isInLocation = true;
					break;
				}
			}
			return isInLocation;
		}

		isInCategory(aListing){
			if ( _.isNaN(this.s_listing_cat) || this.s_listing_cat === '' ){
				return true;
			}

			if (_.isUndefined(aListing.listing_cat_id) || !aListing.listing_cat_id) {
				return false;
			}

			return (_.indexOf(aListing.listing_cat_id, this.s_listing_cat) !== -1);
		}

		isMatchedTitle(aListing){
			if ( !_.isNaN(this.s_listing_cat) || _.isNull(this.s) || (this.s === '') ){
				return true;
			}
			this.s = this.s.toLowerCase();
			let title = aListing.title.toLowerCase();
			return (title.search(this.s) !== -1);
		}

		isOpenNow(aListing){
			if ( this.s_open_now === 0 ){
				return true;
			}
			return (aListing.business_status.status === 'opening');
		}

		/**
		 * Show the results on the map sidebar
		 * @since 1.0
		 */
		showResult(){
			this.$searchForm.on('show_search_result', (()=>{
				this.$mapSidebar.find('.listgo-map__result').addClass('loading');
				let aListResults = [], aLegalPostIDs = [], aFirstLocation = {};

				if ( this.s_highest_rated === 1 || this.s_highest_rated === -1 ){
					this.$container.trigger('listing_change');
				}

				if (!_.isEmpty(this.aListingInLocations)) {
					aListResults = this.aListingInLocations;
				}else{
					_.forEach(this.aRawListings, (oListing=> {
						if (this.isInLocation(oListing)) {
							aListResults.push(oListing);
						}
					}));
				}

				if ( this.s_listing_cat !== '' && !_.isNaN(this.s_listing_cat) ) {
					aFirstLocation = aListResults[0];
					aListResults = _.filter(aListResults, (aListing => {
						return this.isInCategory(aListing);
					}));
				}

				if ( aListResults.length ){
					let order = 0;
					_.forEach(aListResults, (aResult=>{
						if ( this.s_open_now === 1 ){
							if ( aResult.business_status.status !== 'opening' ){
								return false;
							}
						}

						if ( this.s_highest_rated === 1 ){
							let rated = parseInt(aResult.average_rating, 10);
							if ( rated < 3 ){
								return false;
							}
						}

						if ( this.isMatchedTitle(aResult) && this.isOpenNow(aResult) ){
							aLegalPostIDs.push(aResult.ID);
							if ( order === 0 ){
								aFirstLocation = aResult;
							}
							order++;
						}
					}));

					this.$showListing.find('li').each(function(){
						if ( (aLegalPostIDs.indexOf($(this).data('postid')) === -1) ){
							$(this).addClass('hidden');
						}else{
							$(this).removeClass('hidden');
						}
					});
				}else{
					this.$showListing.find('li').addClass('hidden');
				}

				if ( !_.isEmpty(aFirstLocation) && _.isEmpty(this.aListingInLocations) ){
					this.panTo(aFirstLocation.listing_settings.map.latlong, true);
				}

				this.aTemporaryCachingResults = [];
				this.$mapSidebar.find('.listgo-map__result').removeClass('loading');

				if ( !this.$showListing.find('li:not(.hidden)').length ){
					$('#wiloke-map-no-results').removeClass('hidden');
				}else{
					$('#wiloke-map-no-results').addClass('hidden');
				}
			}));
		}

		aFilterListings(){
			this.aListings = this.aRawListings;

			if ( !_.isNaN(this.s_listing_location) && !_.isUndefined(this.s_listing_location) && (this.s_listing_location !== 'all') && !_.isNull(this.s_listing_location) && (this.s_listing_location !== 'mylocation')  ){
				this.aListings = _.filter(this.aListings, (oListing=>{
					if ( oListing.listing_settings && !_.isUndefined(oListing.listing_settings.map) && !_.isUndefined(oListing.listing_location_id) ){
						this.s_listing_location = parseInt(this.s_listing_location, 10);
						return (_.indexOf(oListing.listing_location_id, this.s_listing_location) !== -1);
					}
				}));
			}

			if ( !_.isNaN(this.s_listing_cat) && this.s_listing_cat !== '' ){
				this.s_listing_cat = parseInt(this.s_listing_cat, 10);
				this.aListings = _.filter(this.aListings, (oListing=>{
					if ( oListing.listing_settings && !_.isUndefined(oListing.listing_settings.map) && !_.isUndefined(oListing.listing_cat_id) ){
						return (_.indexOf(oListing.listing_cat_id, this.s_listing_cat) !== -1);
					}
				}));
			}

			this.$container.trigger('listing_change');
		}
		/**
		 * Search form will be handled here
		 */
		controlPanel(){
			this.$toggleSidebar.on('click', (event=>{
				event.preventDefault();
				this.$wrap.toggleClass('list-map-wrap--setting-active');
			}));

			this.$search.on('focus', (event=>{
				this.$wrap.addClass('list-map-wrap--setting-active');
			}));

			// this.searchSuggestion();
			this.$searchForm.on('change', ((event, oData)=>{
				let $target = $(event.target),
					sTarget = $(event.target).attr('id');

				switch ( sTarget ){
					case 's_listing_cat':
						this.reCheck = true;
						this.s_listing_cat = parseInt($target.val(), 10);
						this.$container.trigger('reset_listing');
						this.showService(this.s_listing_cat);
						this.$searchForm.trigger('show_search_result');
						break;

					case 's_highestrated':
						if ( this.$highestRated.is(':checked') ){
							this.s_highest_rated = 1;
						}else{
							this.s_highest_rated = -1;
						}
						this.$searchForm.trigger('show_search_result');
						break;

					case 's_opennow':
						if ( this.$openNow.is(':checked') ){
							this.s_open_now = 1;
						}else{
							this.s_open_now = 0;
						}
						this.$searchForm.trigger('show_search_result');
						break;

					case 's_listing_location':
						this.aListingInLocations = [];
						this.$searchForm.trigger('show_search_result');
						break;

					case 's_search':
						if ( $target.val() === '' ){
							this.$category.attr('value', '');
							this.$category.trigger('change');
						}
						break;

					case 's_toggle_sidebar':
						this.$sidebar.toggleClass('active');
						break;
				}
			}));

			this.ggGeoCode = this.ggGeoCode === null ? new google.maps.Geocoder : this.ggGeoCode;

			this.$location.on('location_changed', ((event, oData)=>{
				if ( !oData.is_suggestion ){
					this.ggGeoCode.geocode({'placeId': oData.placeID}, ((results, status)=>{
						if (status === 'OK') {
							this.sByLatLng = {
								lat: results[0].geometry.location.lat(),
								lng: results[0].geometry.location.lng()
							}

							this.instMap.panTo({lat:results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()});
							let topLocationID = [];
							this.aListingInLocations = [];
							this.$mapSidebar.find('li.listing-item[data-placeid="'+oData.placeID+'"]').each(function () {
								topLocationID.push($(this).attr('data-locationid'));
							});
							this.$mapSidebar.find('li.listing-item').addClass('hidden');
							if ( !_.isEmpty(topLocationID) ){
								this.showUpAllListingInLocation(topLocationID);
							}else{
								this.showUpAllListingInRadius();
							}

						}
					}));
				}else{
					this.reCheck = true;
					this.aListingInLocations = [];
					this.aListingInLocations.push(oData.term_id);
					let $firstListing = this.$mapSidebar.find('li.listing-item[data-locationid="'+oData.term_id+'"]:first');
					if ( $firstListing.length ){
						let aGeoCode = $firstListing.data('latlng').split(',');
						this.instMap.panTo({
							lat: aGeoCode[0],
							lng: aGeoCode[1]
						});
					}

					this.showUpAllListingInLocation(this.aListingInLocations);
				}
			}));

			let filterByKeyWords = null;
			let autoCompleteOff = false;
			this.$search.on('keyup', (event=>{
				if ( filterByKeyWords !== null && filterByKeyWords ){
					clearTimeout(filterByKeyWords);
				}

				this.s = this.$search.attr('value');
				if (this.s !== '') {
					if (!autoCompleteOff) {
						$(".ui-menu-item").hide();
						autoCompleteOff = true;
					}
				} else {
					if (autoCompleteOff) {
						$(".ui-menu-item").show();
						autoCompleteOff = false;
					}
				}

				filterByKeyWords = setTimeout(()=>{
					this.$searchForm.trigger('show_search_result');
					clearTimeout(filterByKeyWords);
				}, 500);
			}));
		}

		/**
		 * Smart loading map. Firstly, We will show 10 listings, then loading the rest of listing via ajax
		 */
		fetching(){
			if ( this.isGetAll ){
				return false;
			}
			let self = this;
	
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_loadmore_map', security: WILOKE_GLOBAL.wiloke_nonce, post__not_in: this.aPostIDs, atts: this.oData, s: this.s, s_current_cat: this.find_listing_cat, s_current_location: this.find_listing_location},
				success: (response=>{
					this.s = null;
					this.find_listing_cat = null;
					this.find_listing_location =  null;
					if ( !response.success ){
						this.isGetAll = true;
					}else{
						let aNewListing = response.data;
						if ( !_.isEmpty(aNewListing) ){
							// this.aRawListings = typeof this.aRawListings === 'object' ? Object.assign(this.aRawListings, aNewListing) : this.aRawListings.concat(aNewListing);
							this.aRawListings = typeof this.aRawListings === 'object' ? mergeObject(this.aRawListings, aNewListing) : this.aRawListings.concat(aNewListing);
							this.$container.trigger('reset_listing');
							this.addLayers(aNewListing);
							this.$container.trigger('has_new_listing');
						}else{
							this.isGetAll = true;
						}
					}
				})
			});
		}
	}

	class WilokeSingleMap{
		constructor($target){
			this.accessToken = WILOKE_GLOBAL.maptoken;
			this.$controller = $target;
			this.coordinate = null;
			this.init();
		}

		parseCoordinate(coordirnate){
			let newCoodirnate = coordirnate.split(',');
			newCoodirnate = _.map(newCoodirnate, (val=>{
				return parseFloat(val);
			}));
			return newCoodirnate;
		}

		mapPopup(){
			if ( !_.isUndefined(this.$controller.data('info')) && !_.isEmpty(this.$controller.data('info')) ){
				let popup = L.popup({
					maxWidth: 400,
					className: 'wo-map-popup',
					closeOnClick: false,
					closeButton: false
				}).setLatLng(this.center).setContent(this.$controller.data('info')).openOn(this.instMap);
			}
		}

		init(){
			if ( this.$controller.length ){
				this.coordinate = this.$controller.data('map');
				if ( !_.isUndefined(this.coordinate) ){
					// Let's build map
					if ( !this.$controller.data('initialized') ){
						L.mapbox.accessToken = this.accessToken;
						let center = this.parseCoordinate(this.coordinate);
						this.center = center;
						this.instMap = L.mapbox.map(this.$controller.attr('id'), null, {
							minZoom: WILOKE_GLOBAL.mapSingleMinZoom,
							maxZoom: WILOKE_GLOBAL.mapSingleCenterZoom
						}).setZoom(WILOKE_GLOBAL.mapSingleMaxZoom).setView(center);
						L.tileLayer(WILOKE_GLOBAL.maptheme).addTo(this.instMap);

						if ( !_.isUndefined(this.$controller.data('marker')) && !_.isEmpty(this.$controller.data('marker')) ){
							let markerIcon = L.icon({
								iconUrl: this.$controller.data('marker')
							});
							L.marker(center, {
								icon: markerIcon
							}).addTo(this.instMap);
						}

						this.instMap.addEventListener('click', (()=>{
							this.mapPopup();
						}));
						this.$controller.trigger('click');

						if ( WilokeDetectMobile.Any() ){
							this.instMap.dragging.disable();
							if (this.instMap.tap){
								this.instMap.tap.disable();
							}
							this.instMap.touchZoom.disable();
							this.instMap.scrollWheelZoom.disable();
						}
					}
				}
			}
		}
	}

	class WilokeGridTemplate{
		static calculateStars(score, compareWith){
			let starClass = '';
			score = parseFloat(score);
			compareWith = parseFloat(compareWith);
			if ( compareWith < score ){
				starClass = Math.floor(score) === compareWith ? 'fa fa-star-half-o' : 'fa fa-star';
			}else if(compareWith === score){
				starClass = 'fa fa-star';
			}else{
				starClass = 'fa fa-star-o';
			}
			return starClass;
		}
		static generateTemplate(oData, oSettings){
			let oText = WILOKE_LISTGO_TRANSLATION;

			if ( _.isEmpty(oData) ){
				let notFoundTemplate = _.template('<p><%- notfound %></p>');
				return notFoundTemplate({notfound: oText.notfound});
			}else{
				let prefixLayout = 'landmark',
					titleClass   = 'listing__title',
					mediaClass   = 'listing__media',
					contentClass = 'listing__content',
					bodyClass = 'listing__body';

				let aListings = _.sortBy(oData, function(aListing){
					return aListing[oSettings.order_by];
				});

				aListings.reverse();
				
				let compiled = _.template(`<% _.forEach(oListings, (aListing=>{ %><div class="<%- oSettings.before_item_class %>">
							<div class="<%- aListing.className %> <% if (_.isEmpty(aListing.terms_id)) { %> <%- aListing.terms_id.join(' ') %><% } %>" data-postid="<%- aListing.ID %>"><div class="<%- oSettings.item_class %>">
							<div class="<%- mediaClass %>">
								<a href="<%- aListing.link %>" style="background-image: url(<%- aListing.featured_image.main.src %>)"><img src="<%- aListing.featured_image.main.src %>" alt="<%- aListing.title %>"></a>
									<% var aTerms = []; %> 
									<% if ( layout!=="listing--list1" ){ %> 
										<% if ( oSettings.show_terms === "both" ){ %>
											<% if ( aListing.listing_location ){ %>
												<% aTerms = aTerms.concat(aListing.listing_location); %>
											<% } %>
											<% if (aListing.listing_cat){ %>
												<% aTerms = aTerms.concat(aListing.listing_cat); %>
											<% } %>
										<% }else if(oSettings.show_terms==="listing_location"){ %>
											<% aTerms = aTerms.concat(aListing.listing_location); %>
										<% }else{ %>
											<% aTerms = aTerms.concat(aListing.listing_cat); %>
										<% } %>
									<% } %>
									<% if (!_.isEmpty(aTerms)) {  %>
										<% var total = aTerms.length;  %>
										<% if(total!==0) { %>
										<div class="listing__cat">
											<% var i = 1; _.forEach(aTerms, (oTerm=>{ %>
												<% if (i === 1) { %>
												<ul class="listing__cats">
												<% } %>
													<% if (i === 1 ) { %>
														<a href="<%- oTerm.link %>"><%- oTerm.name %></a>
														<% if(total > 1) { %>
															<span class="listing__cat-more">+</span>
														<% } %>
													<% }else{ %>
														<li><a href="<%- oTerm.link %>"><%- oTerm.name %></a></li>
													<% } %>
												<% if (i === total) { %>
												</ul>
												<% } %>
											<% i++; })); %>
										</div>
										<% } %>
									<% } %>
									<% if (oSettings.toggle_render_author === "enable"){ %>
										<div class="listing__author">
											<a href="<%- aListing.author.link %>">
												<img src="<%- aListing.author.avatar %>" alt="<%- aListing.author.nickname %>">
												<h6><%- aListing.author.nickname %></h6>
											</a>
										</div>
									<% } %>
									<% if (aListing.is_featured_post && aListing.is_featured_post != 0){ %>
										<span class="onfeatued"><i class="fa fa-star-o"></i></span>
									<% } %>
									<% if (aListing.business_status){ %>
										<span class="ongroup">
											<% if ( aListing.business_status["closesininfo"] !== "") { %>
												<span class="closesin orange">
													<%- aListing.business_status.closesininfo %></span><%
											} else if(aListing.business_status["status"] === "opening"){ %>
												<span class="onopen green"><%- oTranslation.opennow %></span><%} else if(aListing.business_status["status"] === "closed"){ %><span class="onclose red"><%- oTranslation.closednow %></span><span class="onopensin yellow"><%- aListing.business_status.nextdayinfo %></span><% } %></span><% } %>
								</div>
								<div class="<%- bodyClass %>">
								<h3 class="<%- titleClass %>"><a href="<%- aListing.link %>"><%- aListing.title %></a></h3>
								<div class="listgo__rating">
								<span class="rating__star">
								<% for ( var i = 1; i <= 5; i++){ var className = ""; %>
								<% if (aListing.average_rating < i ){ %>
								<% className = i == Math.floor(aListing.average_rating) ? "fa fa-star-half-o" : "fa fa-star-o"; %>
								<% }else{ %>
								<% className = "fa fa-star"; %>
								<% } %>
								<i class="<%- className %>"></i>
								<% } %>
								</span>
								<span class="rating__number"><%- aListing.average_rating %></span>
								</div>
								<div class="<%- contentClass %>">
								<p><%- aListing.post_except %></p>
								<% if (oSettings.toggle_render_address === "enable" && aListing.listing_settings){ %>
									<div class="address">
										<% if ( aListing.listing_settings.map && aListing.listing_settings.map.location !== "" ){ %>
											<span><strong><%- oTranslation.location %></strong>: <%- aListing.listing_settings.map.location %></span>
										<% } %>
										<% if ( aListing.listing_settings.website !== "" ){ %>
											<span><strong><%- oTranslation.address %>:</strong> <a target="_blank" href="<%- aListing.listing_settings.website %>"><%- aListing.listing_settings.website %></a></span>
										<% } %>
										<% if ( aListing.listing_settings.phone_number !== "" ){ %>
											<span><strong><%- oTranslation.phone_number %>:</strong> <a href="tel:<%- aListing.listing_settings.phone_number %>"><%- aListing.listing_settings.phone_number %></a></span>
										<% } %>
									</div>
								<% } %>
								</div>
								<div class="item__actions">
									<div class="tb">
										<% if (layout === "listing--list" || layout === "listing--grid") { %>
											<div class="tb__cell cell-large">
											<a href="<%- aListing.link %>"><%- oTranslation.viewdetail %></a>
											</div>
											<div class="tb__cell">
												<a href="<%- mapPageUrl %>?s_search=<%- aListing.title %>" data-tooltip="<%- oTranslation.gotomap %>">
													<i class="icon_pin_alt"></i>
												</a>
											</div>
											<div class="tb__cell">
												<a href="https://www.google.com/maps/place/<% if (aListing.listing_settings) { %><%- aListing.listing_settings.map.location %>/<%- aListing.listing_settings.map.latlong %><% }else{ %><%- aListing.title %><% } %>" data-tooltip="<%- oTranslation.finddirections %>"><i class="arrow_left-right_alt"></i>
												</a>
											</div>
											<div class="tb__cell">
												<a href="#" class="js_favorite <%- aListing.favorite_class %>" data-postid="<%- aListing.ID %>" data-tooltip="<%- oTranslation.save %>"><i class="icon_heart_alt"></i></a>
											</div>
										<% }else if(layout==="listing--list1"){ %>
											<div class="tb__cell cell-large">
												<a href="<%- aListing.link %>"><%- oTranslation.viewdetail %></a>
											</div>
											<div class="tb__cell">
												<a href="#" class="js_favorite <%- aListing.favorite_class %>" data-postid="<%- aListing.ID %>" data-tooltip="<%- oTranslation.save %>"><i class="icon_heart_alt"></i></a>
											</div>
										<% }else{ %>
											<div class="tb__cell cell-large">
												<a href="<%- mapPageUrl %>?s_search=<%- aListing.title %>" data-tooltip="<%- oTranslation.gotomap %>"><i class="icon_pin_alt"></i></a>
											</div>
											<div class="tb__cell cell-large">
												<a href="<%- aListing.link %>"><%- oTranslation.viewdetail %></a>
											</div>
											<div class="tb__cell">
												<a href="#" class="js_favorite <%- aListing.favorite_class %>" data-postid="<%- aListing.ID %>" data-tooltip="<%- oTranslation.save %>"><i class="icon_heart_alt"></i></a>
											</div>
										<% } %>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><% })); %>`);

				return compiled({'oListings': aListings, oSettings: oSettings, 'prefixLayout': prefixLayout, layout: oSettings.layout, 'mediaClass': mediaClass, 'bodyClass': bodyClass, 'contentClass': contentClass, 'titleClass': titleClass, oTranslation: oText, mapPageUrl: WILOKE_GLOBAL.mappage});
			}
		}
	}

	class WilokeMasonry{
		constructor(){
			this.$masonryCaching = null;
			this.$masonry = $('.wil_masonry');
			this.init();
		}

		init(){
			if( this.$masonry.length ) {
				this.$masonryWrapper = this.$masonry.parent();
				this._oOwnData = this.$masonryWrapper.data();
				this.$items = this.$masonry.find('.grid-item');
				this.listenResize();
				this.getCurrentWindowWidth();
				this.setColMdForGridItems();
				this.gridItemCalculation();

				this.$masonryCaching = this.$masonry.isotope({
					itemSelector: '.grid-item',
					masonry: {
						columnWidth: '.grid-sizer'
					}
				});
			}
		}

		listenResize(){
			let handler = null;
			$(window).on('resize', (()=>{
				if ( this.$masonryCaching === null ){
					return false;
				}

				if ( handler !== null ){
					clearTimeout(handler);
				}

				handler = setTimeout((()=>{
					this.getCurrentWindowWidth();
					this.gridItemCalculation();
					this.setColMdForGridItems();
					this.$masonryCaching.isotope('layout');
					clearTimeout(handler);
				}), 400);
			}));
		}

		getCurrentWindowWidth() {
			this._windowInnerWidth = $(window).innerWidth();
		}

		gridItemCalculation(){
			let calculated = false;
			this.$items.each(function () {
				$(this).css('width', '');
				let width = Math.floor($(this).outerWidth());

				$(this).css('width', width + 'px');

				if ( !calculated ){
					let $wideItem = $(this).parent().children('.wide'),
						height = $wideItem.outerWidth()/2;
					$wideItem.css('height', Math.floor(height) + 'px');
					calculated = true;
				}
			});
		}

		setColMdForGridItems(){
			let eh, ev;
			if (this._windowInnerWidth >= 768 && this._windowInnerWidth < 992) {
				eh = this._oOwnData['smHorizontal'];
				ev = this._oOwnData['smVertical'];
			} else if (this._windowInnerWidth >= 992 && this._windowInnerWidth < 1200) {
				eh = this._oOwnData['mdHorizontal'];
				ev = this._oOwnData['mdVertical'];
			} else if (this._windowInnerWidth >= 1200) {
				eh = this._oOwnData['lgHorizontal'];
				ev = this._oOwnData['lgVertical'];
			} else {
				eh = this._oOwnData['xsHorizontal'];
				ev = this._oOwnData['xsVertical'];
			}

			this.$masonryWrapper.css({
				'margin-top': -ev/2 + 'px',
				'margin-bottom': -ev/2 + 'px',
				'margin-left': -eh/2 + 'px',
				'margin-right': -eh/2 + 'px'
			});

			this.$items.find('.grid-item__content-wrapper').each(function(){
				$(this).css({
					'margin': ev/2 + 'px ' + eh/2 + 'px',
					'top': ev/2 + 'px',
					'bottom': ev/2 + 'px',
					'left': eh/2 + 'px',
					'right': eh/2 + 'px'
				});
			})
		}
	}

	class WilokeListLayout{
		constructor(){
			this.$main          = $('#main');
			this.$component     = this.$main.find('.wiloke-listing-layout');
			this.init();
		}

		init(){
			if ( this.$component.length ){
				this.isFirstSearch  = true;
				this.oText          = WILOKE_LISTGO_TRANSLATION;
				this.$top           = this.$main.find('.listgo-listlayout-on-page-template');
				this.isNewLoad      = false;
				this.$navFilter     = this.$component.find('.nav-filter, .listgo-dropdown-filter');
				this.$navLink       = this.$component.find('.nav-links');
				this.$loadmoreBtn   = this.$component.find('.listgo-loadmore');
				this.$gridWrapper   = this.$component.find('.listgo-wrapper-grid-items');
				this.currentPosts   = this.$gridWrapper.children().length;
				this.currentFilter  = null;
				this.totalPosts     = this.$navLink.data('total');
				this.filterBy       = this.$navFilter.data('filterby');
				this.atts           = this.$component.data('atts');
				this.postsPerPage   = null;
				this.orderBy        = null;
				this.displayStyle   = null;
				this.aPostIDs       = [];
				this.paged          = 1;
				this.isFocusQuery   = false;
				this.aListOfLoaded  = [];
				this.blockID        = this.$component.attr('id');
				this.blockCreatedAt = this.$component.attr('createdat');
				this.cachingKey     = null;
				this.aListingLocations = [];
				this.locationIDs = null;
				this.aListingTags   = [];

				this.searchWithin   = 5;
				this.searchUnit     = 'KM';
				this.s = '';
				this.method = '';
				this.xhr            = null;

				this.$complexFormSearch = this.$top.find('#listgo-searchform');

				this.orderBy = this.atts.order_by;
				this.displayStyle = this.atts.display_style;
				this.postsPerPage = this.atts.posts_per_page;
				this.$sOpenNow    = this.$complexFormSearch.find('#s_opennow');
				this.$sUnit    = this.$complexFormSearch.find('#s_unit');
				this.$sRadius    = this.$complexFormSearch.find('#s_radius');
				this.$sLocation   = this.$complexFormSearch.find('#s_listing_location');
				this.$sCat   = this.$complexFormSearch.find('#s_listing_cat');
				this.priceSegment = this.$complexFormSearch.find('#s_price_segment').val();
				this.isOpenNow    = this.$sOpenNow.is(':checked');
				this.isHigestRated  = this.$complexFormSearch.find('#s_highestrated').is(':checked');
				this.placeID        = this.$complexFormSearch.find('#s-location-place-id').attr('value');
				this.latLng        = this.$complexFormSearch.find('#s-location-latitude-longitude-id').attr('value');

				this.aListingCats   = this.$sCat.val();
				this.aListingLocations = this.$complexFormSearch.find('#s-location-term-id').val();

				this.complexSearch();
				this.navFilter();
				this.pagination();
				this.loadmore();
				this.fetchPostIDs();
				this.sOpenNowConditional();
				this.searchOnMobileOnly();
				this.currentFilter = this.$navFilter.length > 0 ? this.$navFilter.find('.active').data('filter') : 'all';
				this.addFirstCaching();
				this.$navLink.on('reset_pagination', (()=>{
					this.generatePagination();
				})).trigger('reset_pagination');
			}
		}

		searchOnMobileOnly(){
			$('#listgo-mobile-search-only').on('click', (event=>{
				event.preventDefault();
				this.ajaxLoading('html');
			}))
		}

		complexSearch(){
			this.filterBy  = '';

			let ajaxProcessing = null;

			this.$complexFormSearch.on('ajax_loading', ((event, oData)=>{
				if ( !_.isNull(ajaxProcessing) ){
					clearTimeout(ajaxProcessing);
				}

				ajaxProcessing = setTimeout(()=>{
					this.isNewLoad = true;
					if ( this.isFirstSearch ){
						this.isFirstSearch = false;
						this.aListingLocations = this.locationIDs !== null ? this.locationIDs : this.$complexFormSearch.find('#s-location-term-id').val();
						this.aListingCats = this.$complexFormSearch.find('[name="s_listing_cat[]"]').val();
						this.$complexFormSearch.find('.listgo-filter-by-tag:checked').each((index, element)=>{
							this.aListingTags.push($(element).val());
						});
						this.searchUnit = this.$sUnit.val();
						this.searchWithin = this.$sRadius.val();
					}else{
						if ( typeof oData.changed !== 'undefined' ){
							switch (oData.changed){
								case 'location':
									this.aListingLocations = this.locationIDs !== null ? this.locationIDs : this.$complexFormSearch.find('#s_listing_location').val();
									break;
								case 'cat':
									this.aListingCats = this.$complexFormSearch.find('[name="s_listing_cat[]"]').val();
									break;
								case 'tag':
									this.aListingTags = [];
									this.$complexFormSearch.find('.listgo-filter-by-tag:checked').each((index, element)=>{
										this.aListingTags.push($(element).val());
									});
									break;
								case 's_unit':
									this.searchUnit = this.$sUnit.val();
									break;
								case 's_radius':
									this.searchWithin = this.$sRadius.val();
									break;
							}
						}
					}

					this.isFocusQuery = true;

					if ( this.paged === 1 ){
						this.aPostIDs = [];
					}

					if ( !WilokeDetectMobile.exceptiPad() ){
						this.ajaxLoading('html');
					}
				}, 100);
			}));

			this.$complexFormSearch.change(()=>{
				this.aPostIDs = [];
				this.paged = 1;
			});

			this.$complexFormSearch.find('.listgo-filter-by-tag').on('change', (()=>{
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 'tag'}]);
			}));

			this.$complexFormSearch.find('#s_listing_cat').on('change', (()=>{
				this.s = '';
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 'cat'}]);
			}));

			this.$complexFormSearch.find('#s_search').on('keywordchanged search_changed', ((event)=>{
				this.s = $(event.currentTarget).attr('value');
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 'search'}]);
			}));

			this.$sOpenNow.on('change', ((event)=>{
				this.isOpenNow = this.$sOpenNow.is(':checked');
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 'time'}]);
			}));

			this.$complexFormSearch.find('#s_highestrated').on('change', ((event)=>{
				this.isHigestRated = $(event.currentTarget).is(':checked');
				this.$complexFormSearch.trigger('ajax_loading',[{changed: 'rate'}]);
			}));

			this.$complexFormSearch.find('#s_price_segment').on('change', ((event)=>{
				this.priceSegment = $(event.currentTarget).val();
				this.$complexFormSearch.trigger('ajax_loading',[{changed: 'price'}]);
			}));

			this.$sLocation.on('change', ((event)=>{
				this.sOpenNowConditional();
				this.locationIDs = null;
				$('#s_listing_location').data('previous-location', '');
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 'location'}]);
			}));

			this.$sLocation.on('location_changed', ((event, oData)=>{
				this.paged = 1;
				this.sOpenNowConditional(oData);
				if ( oData.is_suggestion ){
					this.locationIDs = oData.term_id;
					this.$complexFormSearch.trigger('ajax_loading', [{changed: 'location'}]);
					this.placeID = null;
					this.latLng = null;
				}else{
					this.placeID = oData.placeID;
					this.latLng = oData.latLng;
					this.locationIDs = oData.value;
					this.$complexFormSearch.trigger('ajax_loading', [{changed: 'location'}]);
				}
			}));

			this.$sRadius.on('change', (event=>{
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 's_radius'}]);
			}));

			this.$sUnit.on('change', (event=>{
				this.$complexFormSearch.trigger('ajax_loading', [{changed: 's_unit'}]);
			}))
		}

		sOpenNowConditional(oData){
			if ( (typeof oData === 'undefined' && this.$sLocation.val() === '') || (typeof oData !== 'undefined' && oData.value === '')){
				this.$sOpenNow.prop('disabled', true);
				this.$sOpenNow.prop('checked', false);
				this.$sOpenNow.closest('label').attr('data-tooltip', this.oText.requirelocation);
			}else{
				this.$sOpenNow.prop('disabled', false);
				this.$sOpenNow.closest('label').removeAttr('data-tooltip');
			}
		}

		addFirstCaching(){
			
			if ( WilokeDetectBrowser.isIE() ){
				return false;
			}

			let oInfo = {}, oListingCaching = instHelpers.getCaching('listing_html', this.blockID, this.blockCreatedAt), oNewListing = {};
			this.$gridWrapper.find('.wiloke-listgo-listing-item').each((index, element)=>{
				let oListingInfo = $(element).data('info');
				let className = $(element).attr('class');
				oInfo[oListingInfo.ID] = oListingInfo;
				oInfo[oListingInfo.ID]['className'] = className;
				this.aPostIDs.push($(element).data('postid'));
			});
			let keyCaching = this.currentFilter + '_' + this.paged;

			oNewListing[keyCaching] = WilokeGridTemplate.generateTemplate(oInfo, this.atts);
			let oCurrentCaching = instHelpers.getCaching('listing_info', this.blockID, this.blockCreatedAt);

			if ( oCurrentCaching ){
				let aCurrentCachingKeys = _.keys(oCurrentCaching);
				let aCurrentKeys = _.keys(aInfo);
				let aKeys = _.differenceBy(aCurrentKeys, aCurrentCachingKeys);
				if ( !_.isEmpty(aKeys) ){
					let aNeedToCaching = _.pick(oInfo, aKeys);
					oCurrentCaching = _.merge(oCurrentCaching, aNeedToCaching);
				}
			}else{
				oCurrentCaching = oInfo;
			}

			// oListingCaching = oListingCaching ? Object.assign(oListingCaching, oNewListing) : oNewListing;
			oListingCaching = oListingCaching ? mergeObject(oListingCaching, oNewListing) : oNewListing;
			instHelpers.setCaching(oListingCaching, 'listing_html', this.blockID);
			instHelpers.setCaching(oCurrentCaching, 'listing_info', this.blockID);
		}

		/**
		 * method is a special case ;), of course. When user click on Nav Filter and the number of posts
		 * of the term currently is smaller than the both the total posts of this term and the posts per page
		 * We will load (PostsPerPage - CurrentListings), and We need to cache response plus currently listings
		 */
		ajaxLoading(method, numberOfPosts){
			// let getCaching = instHelpers.getCaching(this.generateCachingKey());
			// Degree more than one request with the same action
			// let CancelToken = axios.CancelToken;
			// let source = CancelToken.source();
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}
			this.preloader();

			numberOfPosts = _.isUndefined(numberOfPosts) || _.isNaN(numberOfPosts) ? this.postsPerPage : numberOfPosts;

			let oArgs = {
				term: this.currentFilter,
				post__not_in: this.aPostIDs,
				action: 'wiloke_loadmore_listing_layout',
				security: WILOKE_GLOBAL.wiloke_nonce,
				posts_per_page: numberOfPosts,
				filter_by: this.filterBy,
				listing_locations: this.aListingLocations,
				location_place_id: this.placeID,
				latLng: this.latLng,
				listing_tags: this.aListingTags,
				listing_cats: this.aListingCats,
				get_posts_from: null,
				is_focus_query: this.isFocusQuery,
				is_open_now: this.isOpenNow,
				is_highest_rated: this.isHigestRated,
				price_segment: this.priceSegment,
				paged: this.paged,
				customerUTCTimezone: this.getVisitorTimezone(),
				s: this.s,
				sUnit: this.searchUnit,
				sWithin: this.searchWithin,
				target: null,
				atts: this.atts
			};

			let pageCaching         = this.currentFilter + '_' + this.paged,
				oListingCaching     = instHelpers.getCaching('listing_html', this.blockID, this.blockCreatedAt);
			this.$gridWrapper.find('.notfound').remove();
			
			// temporary disable caching the two new layouts
			if ( this.atts.layout !== 'circle-thumbnail' && this.atts.layout !== 'creative-rectangle' && !WilokeDetectBrowser.isIE() && this.atts.layout !== 'listing-grid3' && this.atts.layout !== 'listing-grid4' ){
				if( !this.isFocusQuery && !_.isUndefined(oListingCaching[pageCaching]) ){
					this.addNewPost(oListingCaching[pageCaching], method);
					this.preloader('loaded');
					return false;
				}
			}

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: oArgs,
				success: (response=>{
					if ( response.success ){
						let oNewListingCaching = {};
						oNewListingCaching[pageCaching] = response.data.content;
						if ( this.isFocusQuery ){
							this.totalPosts = response.data.total;
						}
						this.addNewPost(response.data.content, method);
						this.fetchPostIDs();

						if ( !WilokeDetectBrowser.isIE() ){
							// oListingCaching = oListingCaching ? Object.assign(oListingCaching, oNewListingCaching) : oNewListingCaching;
							 oListingCaching = oListingCaching ? mergeObject(oListingCaching, oNewListingCaching) : oNewListingCaching;
							instHelpers.setCaching(oListingCaching, 'listing_html', this.blockID);
						}
						
					}else{
						this.totalPosts = 0;
						if ( this.method !== 'loadmore' ){
							this.addNewPost('<div class="col-xs-12"><div class="wil-alert wil-alert-has-icon alert-danger"><span class="wil-alert-icon"><i class="icon_box-checked"></i></span><p class="wil-alert-message">'+[instHelpers.notFound()]+'</p></div></div>');
						}
						this.$loadmoreBtn.addClass('hidden');
					}

					if ( (this.$loadmoreBtn.hasClass('listgo-loadmore') && (this.$loadmoreBtn.data('total') <= this.$gridWrapper.children().length)) || this.s !== '' ){
						this.$loadmoreBtn.remove();
					}

					this.preloader('loaded');
					this.isNewLoad = false;
					this.method = '';
				})
			})
		}

		getVisitorTimezone(){
			if ( this.visitorUTCTimezone ){
				return this.visitorUTCTimezone;
			}
			let userTimezone = -(new Date().getTimezoneOffset() / 60),
				defineUTC = '';
			if ( userTimezone > 0 ){
				defineUTC = 'UTC+';
			}else{
				defineUTC = 'UTC-';
			}
			this.visitorUTCTimezone = defineUTC + userTimezone;
			return this.visitorUTCTimezone;
		}

		preloader(status){
			let $ctr = this.$gridWrapper;
			if (this.displayStyle === 'loadmore' && this.s === '' && this.isNewLoad === false){
				$ctr = this.$loadmoreBtn;
			}

			if ( status === 'loaded' ){
				$ctr.removeClass('loading');
			}else{
				$ctr.addClass('loading');
			}
		}

		addNewPost(newListings, specifyMethod){
			let createHTML = '', method='';
			if (_.isArray(newListings)){
				createHTML = newListings.join('');
			}else{
				createHTML = _.map(newListings, (val=>{
					return val;
				})).join('');
			}

			if ( !_.isUndefined(specifyMethod) ){
				method = specifyMethod;
			}else{
				if ( this.method === 'loadmore'  ){
					method = 'append';
				}else{
					method = 'html';
				}
			}

			let $createHTML = $(createHTML);
			if ( method !== 'append' ){
				this.$gridWrapper.html(createHTML);
			}else{
				this.$gridWrapper.append($createHTML).isotope('appended', $createHTML);
			}

			this.$gridWrapper.find('.lazy').Lazy();

			this.$navLink.trigger('reset_pagination');
			this.$component.trigger('recheck_loadmore');

			
			this.$component.trigger('ajax_completed');

			this.isFocusQuery = false;

			this.$gridWrapper.find('.listing[class*="listing--grid"]').WilokeNiceGridTitle();
		}

		fetchPostIDs(){
			this.$gridWrapper.find('.wiloke-listgo-listing-item').each(((index, element)=>{
				let postID = $(element).data('postid');
				if ( _.isEmpty(this.aPostIDs) || ( _.indexOf(this.aPostIDs, postID) === -1 ) ){
					this.aPostIDs.push(postID.toString());
				}
			}));
		}

		generatePagination(){
			let s = this.$complexFormSearch.find('#s_search').val();
			if ( !_.isUndefined(s) && s !== '' && $('#s_listing_cat').val() === '' ){
				this.$navLink.html('');
			}else{
				let instPagination = new WilokePagination(this.totalPosts, this.postsPerPage, this.paged);
			
				this.$navLink.html(instPagination.createPagination());
			}
		}

		navFilter(){
			if ( this.$navFilter.hasClass('listgo-dropdown-filter') ){
				this.$navFilter.on('change', ((event)=>{
					this.handleFilter($(event.target).find('option:selected'));
				}));
				this.showResultOnDropdownFilter();
			}else{
				this.$navFilter.find('a').on('click', ((event)=>{
					event.preventDefault();
					this.handleFilter($(event.target));
				}));
			}
		}

		showResultOnDropdownFilter(){
			let resultText = '',
				$result         = this.$component.find('.listing__result-right'),
				resultStructure = $result.data('result'),
				singularRes     = $result.data('singularres'),
				pluralRes       = $result.data('pluralres');
			resultStructure = resultStructure.replace('*open_result*', '<span><ins>');
			resultStructure = resultStructure.replace('*end_result*', '</ins></span>');
			resultStructure = resultStructure.replace('%total_listing%', this.$gridWrapper.data('total'));

			this.$component.on('ajax_completed', (()=>{
				let target = '.wiloke-listgo-listing-item',
					generateHTMl = resultStructure;
				if ( this.currentFilter !== 'all' ){
					target += '.'+this.currentFilter;
				}

				let foundListings = this.$gridWrapper.find(target).length;

				if ( generateHTMl > 1 ){
					resultText = singularRes;
				}else{
					resultText = pluralRes;
				}

				generateHTMl = generateHTMl.replace('RESULT_TEXT_HERE', resultText);
				generateHTMl = generateHTMl.replace('%found_listing%', foundListings);

				$result.html(generateHTMl);
				
				// this.$component.find('.listgo-wrapper-grid-items').isotope('reloadItems');
			}));
		}

		handleFilter($target){
			$target.siblings('.active').removeClass('active');
			$target.addClass('active');
			this.paged = 1;
			this.currentFilter = $target.data('filter');

			let hasAjax = false;

			if ( this.currentFilter !== 'all' ){
				this.filterBy = this.$navFilter.data('filterby');
				// this.$gridWrapper.find('.wiloke-listgo-listing-item:not(.'+this.currentFilter+')').parent().fadeOut().removeClass('active');
				// this.$gridWrapper.find('.wiloke-listgo-listing-item.'+this.currentFilter).parent().fadeIn('slow').addClass('active');
				this.$gridWrapper.isotope({filter: '.'+this.currentFilter});

				let currentListingsInTerm = this.$gridWrapper.find('.wiloke-listgo-listing-item.'+this.currentFilter).length;

				if ( (currentListingsInTerm < $target.data('total')) && (currentListingsInTerm < this.postsPerPage) ){
					// this is special case
					hasAjax = true;
					this.ajaxLoading('append', this.postsPerPage - currentListingsInTerm);
				}
			}else{
				this.$gridWrapper.find('.wiloke-listgo-listing-item').parent().fadeIn('slow');
			}

			if ( !hasAjax ){
				this.$component.trigger('ajax_completed');
			}

			this.getCurrentTermInfo();
			this.$component.trigger('recheck_loadmore');
		}

		pagination(){
			this.$component.on('click', 'a.page-numbers', ((event)=>{
				event.preventDefault();

				let $target = $(event.currentTarget);
				if ( $target.hasClass('current') ){
					return false;
				}

				$('html, body').animate({
					scrollTop: this.$component.offset().top + 100
				}, 600);

				if ( $target.hasClass('next') ){
					$target = this.$navLink.find('.page-numbers.current').next();
				}else if ( $target.hasClass('prev') ){
					$target = this.$navLink.find('.page-numbers.current').prev();
				}

				this.paged = $target.data('page');

				if ( this.paged > $target.siblings('.page-numbers.current').data('page') ){
					this.fetchPostIDs();
				}else{
					this.aPostIDs = this.aPostIDs.slice(0, (this.paged*this.postsPerPage)-1);
				}

				$target.siblings('.page-numbers').removeClass('current');
				$target.addClass('current');

				this.$component.trigger('search_handle');
				this.ajaxLoading();
			}));
		}

		loadmore(){
			// Firstly, We need to set a status for the load more button
			this.$component.on('recheck_loadmore', (()=>{
				let totalPosts = 0, current = '.grid-item';

				if ( this.currentFilter === 'all' ) {
					totalPosts = this.$gridWrapper.data('total');
				}else{
					totalPosts = this.$navFilter.find('.active').data('total');
					current += '.' + this.currentFilter;
				}

				if ( totalPosts === this.$gridWrapper.find(current).length ){
					if ( this.currentFilter !== 'all' ){
						this.$loadmoreBtn.addClass('hidden');
					}else{
						this.$loadmoreBtn.remove();
					}
				}else{
					this.$loadmoreBtn.removeClass('hidden');
				}
			}));

			this.$loadmoreBtn.on('click', (event=>{
				event.preventDefault();
				this.method = 'loadmore';
				this.paged++;
				this.ajaxLoading();
			}));
		}

		getCurrentTermInfo(){
			this.totalPosts = this.$navFilter.find('.active').data('total');

			if ( this.currentFilter !== 'all' ){
				this.currentPosts = this.$component.find('.grid-item.'+this.currentFilter).length;
			}else{
				this.currentPosts = this.$component.find('.grid-item').length;
			}

			this.$navLink.trigger('reset_pagination');
		}
	}

	class WilokeEvent{
		constructor(){
			this.$app = $('.wiloke-listgo-event');
			this.init();
		}

		init(){
			if ( !this.$app.length ){
				return false;
			}

			this.xhr = null;
			this.searchByTerm = false;
			this.$paginationWrapper =  this.$app.find('#wiloke-event-pagination');
			this.$eventsWrapper = this.$app.find('.listgo-event-items:first');
			this.paged = 1;
			this.prevLatLng = null;
			this.prevLocationID = null;
			this.prevCatID = '';
			this.locationID = '';
			this.catID = '';
			this.s = null;
			this.lngLng = null;

			this.oConfiguration = this.$app.data('configuration');
			this.totalPosts = this.$paginationWrapper.data('maxposts');
			this.postsPerPage = this.oConfiguration.events_per_page;

			/* Functions Below */
			this.generalPagination();
			this.formChanged();
			this.getPage();
		}

		getPage(){
			this.$paginationWrapper.on('click', 'a', (event=>{
				event.preventDefault();
				this.paged = $(event.currentTarget).data('page');
				this.fetchEvents();
			}));
		}

		formChanged(){
			$('#listgo-event-searchfrom').on('change', (event=>{
				if ( ($(event.target).attr('id') === 's_listing_cat') && $(event.target).val() === this.prevCatID ){
					return false;
				}

				this.catID = this.$app.find('#s_listing_cat').val();

				if ( this.$app.find('#s_listing_location').val() === '' ){
					this.locationID = '';
					this.lngLng = '';
				}else{
					this.lngLng = $('#s-location-latitude-longitude-id').val();
					this.locationID = $('#s-location-term-id').val();
				}

				if ( ($(event.currentTarget).attr('id') === 's_search') && (this.catID === null) ){
					return false;
				}

				this.paged = 1;
				this.fetchEvents();
			}));

			let sKeyWordHandle = null;
			this.$app.find('#s_search').on('keyup', (event=>{
				this.catID = null;

				if ( sKeyWordHandle !== null ){
					clearTimeout(sKeyWordHandle);
				}

				sKeyWordHandle = setTimeout((()=>{
					this.s = $(event.currentTarget).val();
					this.fetchEvents();
					clearTimeout(sKeyWordHandle);
				}), 400);
			}));
		}

		eventsNotIn(){
			let aEventIDs = [];
			this.$eventsWrapper.find('.listing-event').each(function(){
				return aEventIDs.push($(this).data('id'));
			});

			return aEventIDs;
		}

		fetchEvents(){
			this.$app.addClass('loading');
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			this.xhr = $.ajax({
				type: 'GET',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {
					action: 'wiloke_fetch_events',
					paged: this.paged,
					configuration: JSON.stringify(this.oConfiguration),
					eventsNotIn: this.eventsNotIn(),
					latLng: this.lngLng,
					prevLatLng: this.prevLatLng,
					locationID: this.locationID,
					prevLocationID: this.prevLocationID,
					catID: this.catID,
					s:this.s,
					prevCatID: this.prevCatID,
				},
				success: (response=>{
					if ( response.success ){
						this.$eventsWrapper.html(response.data.msg);
						this.$eventsWrapper.find('.lazy').Lazy();
						this.prevLatLng = this.lngLng;
						this.prevLocationID = this.locationID;
						this.prevCatID 	= this.catID;

						if ( !_.isUndefined(response.data.totalposts) && response.data.totalposts !== null ){
							this.totalPosts = response.data.totalposts;
						}
					}else{
						this.totalPosts = 0;
						this.$eventsWrapper.html(response.data.msg);
					}

					this.generalPagination();
					this.$app.removeClass('loading');
					this.searchByTerm = false;
				})
			})
		}

		generalPagination(){
			let instPagination = new WilokePagination(this.totalPosts, this.postsPerPage, this.paged);
			this.$paginationWrapper.html(instPagination.createPagination());
		}
	}

	class WilokeGoogleAutocomplete{
		constructor(){
			this.$app = $('#s_listing_location');
			this.init();
		}

		parseSuggestion(){
			let oSuggestion = this.$suggestLocation.val();
			if ( oSuggestion !== '' && typeof oSuggestion !== 'undefined' ){
				oSuggestion = $.parseJSON(oSuggestion);

				_.each(oSuggestion, (oVal=>{
					this.aSuggestion.push(
						{
							value: oVal.name,
							label: oVal.name,
							term_id: oVal.term_id
						}
					);
					this.aListOfLocationName.push(oVal.name);
				}));
			}
		}

		init(){
			if ( !this.$app.length || !this.$app.hasClass('auto-location-by-google') ){
				return false;
			}
			this.$suggestLocation = $('#s-listing-location-suggestion');
			this.$placeID = $('#s-location-place-id');
			this.$latLng = $('#s-location-latitude-longitude-id');
			this.$termID = $('#s-location-term-id');
			this.ggAutoCompleteService = new google.maps.places.AutocompleteService();
			
			this.aSuggestion = [];
			this.aListOfLocationName = [];
			this.aLocations = [];
			this.delay = false;
			this.parseSuggestion();
			this.autoComplete();
			this.ggGeoCode = new google.maps.Geocoder;
		}

		inLocationSuggestion(term){
			return (this.aListOfLocationName.indexOf(term) !== -1);
		}

		autoComplete(){
			this.$app.autocomplete({
				source: ((request, response)=> {
					let term = request.term;
					if (term.length === 0 || this.inLocationSuggestion(term) ) {
						if (_.isEmpty(this.aSuggestion)) {
							return false;
						}
						response(this.aSuggestion);
						return false;
					}else{
						let self = this;
						if  ( term.length < 2 ){
							return false;
						}
						this.ggAutoCompleteService.getPlacePredictions({ 
							input: request.term,
							componentRestrictions: {
								country: WILOKE_GLOBAL.countryRestriction
							}
						}, function(predictions, status){
							if (status !== google.maps.places.PlacesServiceStatus.OK) {
								return;
							}
							self.aLocations = _.map(predictions, (oData=>{
								return {
									label: oData.description,
									value: oData.description,
									real_value: oData.structured_formatting.main_text,
									details: oData,
									placeID: oData.place_id
								};
							}));
							response(self.aLocations);
						});
					}
				}),
				minLength: 0,
				select: ((event, ui)=>{
					if ( ui.item.value === this.$app.data('previous-location') ){
						return false;
					}

					if ( _.isUndefined(ui.item.placeID) ){
						this.$app.trigger('location_changed', [{is_suggestion:true, value:ui.item.value, term_id: ui.item.term_id, latLng: ui.item.latLng}]);
						this.$placeID.attr('value', '');
						this.$latLng.attr('value', '');
						this.$termID.attr('value', ui.item.term_id);
						this.delay = false;
					}else{
						this.delay = true;
						let latLng = null;

						this.$termID.attr('value', '');
						this.$placeID.attr('value', ui.item.placeID);

						this.ggGeoCode.geocode({'placeId': ui.item.placeID}, ((results, status)=>{
							if ( status === 'OK' ){
								latLng = Math.round10(results[0].geometry.location.lat(), -4) + ',' + Math.round10(results[0].geometry.location.lng(), -4);
								this.$latLng.attr('value', latLng);
								this.$app.trigger('location_changed', [{is_suggestion:false, value:ui.item.real_value, placeID: ui.item.placeID, details: ui.item.details, latLng: latLng}]);
							}else{
								this.$app.trigger('location_changed', [{is_suggestion:false, value:ui.item.real_value, placeID: ui.item.placeID, details: ui.item.details, latLng: latLng}]);
							}
						}));
					}

					this.$app.data('previous-location', ui.item.value);
					let reTriggerBlur = setTimeout(()=>{
						clearTimeout(reTriggerBlur);
						this.$app.trigger('blur');
					}, 400);
				}),
				focus: ((event, ui)=>{
					if ( WilokeDetectMobile.Any() ){
						this.$app.attr('value', ui.item.value);

						if ( _.isUndefined(ui.item.placeID) ){
							this.$app.trigger('location_changed', [{is_suggestion:true, value:ui.item.value, term_id: ui.item.term_id, latLng: ''}]);
							this.$placeID.attr('value', '');
							this.$termID.attr('value', ui.item.term_id);
							this.$latLng.attr('value', '');
							this.delay = false;
						}else{
							this.delay = true;
							let latLng = null;
							this.$termID.attr('value', '');
							this.$placeID.attr('value', ui.item.placeID);

							this.ggGeoCode.geocode({'placeId': ui.item.placeID}, ((results, status)=>{
								if ( status === 'OK' ){
									latLng = Math.round10(results[0].geometry.location.lat()) + ',' + Math.round10(results[0].geometry.location.lng(), -4);
									this.$latLng.attr('value', latLng);
									this.$app.trigger('location_changed', [{is_suggestion:false, value:ui.item.real_value, placeID: ui.item.placeID, details: ui.item.details, latLng: latLng}]);
								}else{
									this.$app.trigger('location_changed', [{is_suggestion:false, value:ui.item.real_value, placeID: ui.item.placeID, details: ui.item.details, latLng: latLng}]);
								}
							}));
						}
						this.$app.data('previous-location', ui.item.value);

						this.$app.autocomplete('close');
						if ( !this.delay ){
							this.$app.trigger('blur');
						}else{
							let reTriggerBlur = setTimeout(()=>{
								clearTimeout(reTriggerBlur);
								this.$app.trigger('blur');
							}, 400);
						}
					}
				})
			}).bind('focus', function(){
				$(this).autocomplete('search');
			}).bind('blur', ((event)=>{
				let that = this;
				
				if ( $(event.currentTarget).val() == '' ){
					that.$latLng.attr('value', '');
					that.$placeID.attr('value', '');
					that.$termID.attr('value', '');
					that.$app.trigger('location_changed', [{
						placeID: '',
						latLng: ''
					}]);
				}
			}));
		}
	}

	class WilokeAskForCurrentPosition {
		constructor(isFocusSetLocationField){
			this.oResults = {};
			this.oLatLng = {};
			this.address = '';
			this.isFocusSetLocationField = isFocusSetLocationField;
			this.askForPosition();
		}

		askForPosition(){
			if ( !$('#s_listing_location').length && !$('#listing-single_listing_near_by').length ){
				return false;
			}

			let oLatLng 	= localStorage.getItem('listgo_mylocation'),
				address 	= localStorage.getItem('listgo_address'),
				createdAt 	= localStorage.getItem('listgo_mylocation_created_at');
				
			if ( address ){
				oLatLng = $.parseJSON(oLatLng);
				let instDate = new Date();

				if ( (instDate.getMinutes() - parseInt(createdAt, 10)) <= 5 ){
					if ( !_.isEmpty(oLatLng) ){
						this.address = address;
						this.oLatLng = oLatLng;
						this.findYourLocationInSearchForm();
						$('body').trigger('wiloke_listgo_user_latlng', [this.oLatLng]);
					}
					return false;
				}
			}

			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition((position)=>{
					this.detectingPosition(position);
					$('body').trigger('wiloke_listgo_got_geocode', [position]);
				});
			}
		}

		cachingPosition(){
			let instDate = new Date();
			localStorage.setItem('listgo_adress', this.address);
			localStorage.setItem('listgo_mygeocode', JSON.stringify(this.oResults));
			localStorage.setItem('listgo_mylocation_created_at', instDate.getMinutes());
		}

		findYourLocationInSearchForm(){
			let $searchLocation = $('#s_listing_location');
			if ( $searchLocation.length ){
				if ( this.isFocusSetLocationField || ( $searchLocation.val() === '' && $('.is-saprated-searchform').length ) ){
					$searchLocation.attr('value', this.address);
					let $sLatLng = $('#s-location-latitude-longitude-id'), $sTermID = $('#s-location-term-id');
					$sLatLng.attr('value', this.oLatLng.lat+','+this.oLatLng.lng);
					$sTermID.attr('value', '');
					$searchLocation.trigger('location_changed', [{
						latLng: this.oLatLng.lat+','+this.oLatLng.lng,
						is_suggestion: false,
						term_id: ''
					}]);
					$('#listgo-event-searchfrom').trigger('change');
				}
			}
		}

		detectingPosition(position){
			this.oLatLng = {
				lat: parseFloat(position.coords.latitude),
				lng: parseFloat(position.coords.longitude)
			};
			$('body').trigger('wiloke_listgo_user_latlng', [this.oLatLng]);

			let oGeocoder = new google.maps.Geocoder;
	        oGeocoder.geocode({'location': this.oLatLng}, ((results, status)=>{
	          if (status === 'OK') {
	            if (results[0]) {
					$('body').trigger('wiloke_listgo_got_location', [results]);
					this.address = results[0].formatted_address;
					this.findYourLocationInSearchForm();
					this.oResults = results;
					this.cachingPosition();
            	}
	          }
	        }));
		}
	}

	class WilokeSearchSuggestion{
		constructor(instMap){
			this.$searchForm    = $('#listgo-searchform, #listgo-event-searchfrom');
			this.$search        = this.$searchForm.find('#s_search');
			this.$hiddenOriginalSuggestion = this.$searchForm.find('#wiloke-original-search-suggestion');
			this.$location      = this.$searchForm.find('#s_listing_location');
			this.$category      = this.$searchForm.find('#s_listing_cat');
			this.$tag           = this.$searchForm.find('[name="s_listing_tag[]"]');
			/* ListGo 1.0.7 */
			this.$sTermID       = this.$searchForm.find('#s-location-term-id');
			this.$sPlaceID      = this.$searchForm.find('#s-location-place-id');
			this.$sLatLng      = this.$searchForm.find('#s-location-latitude-longitude-id');

			this.oCache         = {};
			this.aSuspects      = {};
			this.aListings      = {};
			this.oOriginalSuggestion = {};
			this.aAvailableTags = [];
			this.latestSelected = null;
			this.reCheck        = false;
			this.isShowingCats  = false;
			this.oListingsInfo   = {};
			this.xhr = null;
			this.instMap        = instMap;
			this.init();
		}

		subscribeTaxonomy(){
			this.$location.on('change', (()=>{
				this.reCheck = true;
			}));

			this.$category.on('change', (()=>{
				this.reCheck = true;
			}));

			this.$tag.on('change', (()=>{
				this.reCheck = true;
			}));
		}

		renderNestedCategories(aCategory, level){
			let menuItem = '', mapIcon = '';
			level = typeof level === 'undefined' ? 0 : level;

			mapIcon = aCategory.icon !== '' ? '<img src="'+aCategory.icon+'">' : '';
			menuItem += '<li class="ui-menu-item wil-search-suggestion-item wiloke-cat-level-'+level+'" data-value="'+aCategory.name+'"><div class="ui-menu-item-wrapper">'+mapIcon+'<div class="label-and-info">' + aCategory.name + '</div></div></li>';

			if ( !_.isEmpty(aCategory.children) ){
				for ( let i in aCategory.children ){
					let item = aCategory.children[i];

					if ( _.isEmpty(item.children) ){
						mapIcon = item.icon !== '' ? '<img src="'+item.icon+'">' : '';
						menuItem += '<li class="ui-menu-item wil-search-suggestion-item wiloke-cat-level-'+level+'" data-value="'+item.name+'"><div class="ui-menu-item-wrapper">'+mapIcon+'<div class="label-and-info">' + item.name + '</div></div></li>';
					}else{
						menuItem += this.renderNestedCategories(item, level);
					}
					level++;
				}
			}

			return menuItem;
		}

		blackenSearchFieldWhenFocus(){
			this.$search.on('focus', function(){
				$(this).select();
				this.setSelectionRange(0,9999);
			});
			this.$location.on('focus', function(){
				$(this).select();
				this.setSelectionRange(0,9999);
			});
		}

		init(){
			if ( !this.$search.length ){
				return false;
			}
			this.blackenSearchFieldWhenFocus();

			if ( this.instMap ){
				this.instMap.$container.on('reset_listing', (()=>{
					if ( !_.isEmpty(this.instMap.aListings) ){
						this.reCheck = true;
						this.aListings = _.map(this.instMap.aListings, (aListing=>{
							return aListing.title;
						}));
					}
				}));
			}

			if ( this.$hiddenOriginalSuggestion.length && this.$hiddenOriginalSuggestion.val() !== '' ){
				this.oOriginalSuggestion = $.parseJSON(this.$hiddenOriginalSuggestion.val());
			}
			this.subscribeTaxonomy();
			this.$search.autocomplete({
				at: 'left bottom',
				minLength: 0,
				create: (event=>{
					$(event.target).data('ui-autocomplete')._renderItem = ((ul, item)=>{
						let moreinfo = '';
						if ( this.isShowingCats ){
							let mapIcon = item.icon !== '' ? '<img src="'+item.icon+'">' : '';
							let menuItem = '<li class="ui-menu-item wil-search-suggestion-item" data-value="'+item.name+'"><div class="ui-menu-item-wrapper">'+mapIcon+'<div class="label-and-info">' + item.name + '</div></div></li>';
							return $(menuItem).appendTo(ul);
						}else{
							if ( item.label.search('Nothing found') !== -1 ){
								return $('<li class="ui-menu-item wil-search-suggestion-item">').append('<div class="ui-menu-item-wrapper"><div class="label-and-info">' + item.label + '</div></div>').appendTo(ul);
							}else{
								if ( item.full.listing_settings ){
									moreinfo = '<span class="more-info">' + item.full.listing_settings.map.location + '</span>';
								}
								if ( _.isUndefined(item.full.first_cat_info) || !item.full.first_cat_info ||  item.full.first_cat_info.map_marker_image === '' ){
									return $('<li class="ui-menu-item wil-search-suggestion-item">').append('<div class="ui-menu-item-wrapper"><div class="label-and-info">' + item.label + moreinfo + '</div></div>').appendTo(ul);
								}else{
									return $('<li class="ui-menu-item wil-search-suggestion-item">').append('<div class="ui-menu-item-wrapper"><img src="'+item.full.first_cat_info.map_marker_image+'"><div class="label-and-info">' + item.label + moreinfo + '</div></div>').appendTo(ul);
								}
							}
						}
					});
				}),
				open: ((event, ui) => {
					let widget = this.$search.data('ui-autocomplete'),
						ul = widget.menu.element,
						width = this.$search.innerWidth();
					ul.width(width);
				}),
				source: ((request, response)=>{
					let categories  = this.$category.val(),
						tags        = this.$tag.val(),
						locations   = this.$location.val(),
						term = request.term.toLowerCase(),
						$wrap = this.$search.closest('.input-text');

					if ( term.length === 0 || ( ( (typeof this.$search.data('is-disable-suggestion') === 'undefined') || (this.$search.data('is-disable-suggestion') === false) ) && (_.isUndefined(this.oCache[term]) || _.isEmpty(this.oCache[term])) ) ){
						if ( _.isEmpty(this.oOriginalSuggestion) ){
							return false;
						}
						$wrap.removeClass('loading');
						this.isShowingCats = true;
						response(this.oOriginalSuggestion);
						return false;
					}

					if ( this.$search.closest('form').hasClass('listing-template') ){
						this.$search.autocomplete('close');
						if ( this.latestSelected !== term ){
							this.$search.trigger('search_changed');
						}
						return false;
					}

					if ( term === this.$search.data('latest-search') || this.$searchForm.hasClass('listgo-search-on-map') ){
						return false;
					}

					this.isShowingCats = false;
					if ( !_.isEmpty(this.oCache) && !this.reCheck && !_.isUndefined(this.oCache[term]) ){
						response(this.oCache[term]);
						return false;
					}

					if ( this.xhr  !== null && this.xhr.status !== 200 ){
						this.xhr.abort();
					}

					if ( this.$search.hasClass('disabled-autocomplete-ajax') ){
						return false;
					}

					this.xhr = $.ajax({
						type: 'GET',
						url: WILOKE_GLOBAL.ajaxurl,
						beforeSend: ((xhr)=>{
							$wrap.addClass('loading');
						}),
						data: {action: 'wiloke_search_suggestion', s: term, listing_locations: locations, listing_tags: tags, listing_cats: categories, security: WILOKE_GLOBAL.wiloke_nonce, location_place_id: this.$sPlaceID.attr('value'), location_term_id: this.$sTermID.val(), latLng: this.$sLatLng.val()},
						success: (data=>{
							if ( data.success ){
								this.oListingsInfo = data.data;
								let aListings = _.map(data.data, (oData=>{
									return {
										label: oData.title,
										value: oData.title,
										full: oData
									};
								}));

								this.oCache[term] = aListings;
								response(aListings);
								this.reCheck = false;
							}else{
								this.oListingsInfo = data.data;
								let aListings = [{
									label: data.data.message,
									value: ''
								}];
								response(aListings);
								this.reCheck = false;
							}

							$wrap.removeClass('loading');
						})
					});
				}),
				select: ((event, ui)=>{
					this.$search.data('is-disable-suggestion', false);
					this.$search.data('latest-search', ui.item.value);
					this.$search.attr('value', ui.item.label);
					this.latestSelected = ui.item.value;
					if ( typeof ui.item.taxonomy !== 'undefined' && ui.item.taxonomy === 'listing_cat' ){
						this.$category.attr('value', ui.item.term_id);
						$('#cache_previous_search').attr('value', ui.item.name);
						this.$category.trigger('change');
						this.$search.trigger('blur');
					}else{
						this.$category.val('');
						this.$search.trigger('change', ui.item.label);
						if ( $(event.target).closest('form').parent().hasClass('is-saprated-searchform') ){
							$(event.target).val(ui.item.label);
							window.location.href = ui.item.full.link;
						}
						this.$search.val(ui.item.label);
						this.$search.trigger('keywordchanged');
					}
				}),
				focus: ((event, ui)=>{
					if ( WilokeDetectMobile.Any() ){
						this.$search.attr('value', ui.item.label);
						this.$search.data('latest-search', ui.item.value);

						if ( typeof ui.item.taxonomy !== 'undefined' && ui.item.taxonomy === 'listing_cat' ){
							$('#cache_previous_search').attr('value', ui.item.name);
							this.$category.attr('value', ui.item.term_id);
							this.$category.trigger('change');
							this.$search.trigger('blur');
						}else{
							this.$category.val('');
							$('#cache_previous_search').attr('value', '');
							if ( $(event.target).closest('form').parent().hasClass('is-saprated-searchform') ){
								$(event.target).val(ui.item.label);
								window.location.href = ui.item.full.link;
							}
							this.$search.val(ui.item.label);
							this.$search.trigger('keywordchanged');
						}
						this.$search.autocomplete('close');
					}
				})
			}).bind('focus', function(){
				$(this).autocomplete('search');
				$(this).data('is-disable-suggestion', true);
				return this;
			}).on('blur', function(event){
				let val = $(this).attr('value');
				if ( val === ''){
					let $cats = $('#s_listing_cat');
					$('#cache_previous_search').attr('value', '');
					$cats.attr('value', '');
					$cats.trigger('change');
					if ( $(this).data('triggerAllowable')  ){
						$(this).data('triggerAllowable', false);
						$(this).on('keypress', function(){
							$(this).data('triggerAllowable', true);
							$(this).off('keypress');
						});
					}
				}
				$(this).data('is-disable-suggestion', false);
			}).one('keypress', function(event){
				$(this).data('triggerAllowable', true);

				if ( $(this).hasClass('disabled-autocomplete-ajax') ){
					$('.ui-menu-item').hide();
				}
			});
		}
	}

	class WilokeFavorite{
		constructor(){
			this.$main = $('#main');
			this.$favorite = this.$main.find('.js_favorite');
			this.handle();
			this.removeFavorite();
		}

		handle(){
			this.$main.on('click', '.js_favorite', ((event)=>{
				event.preventDefault();

				if (WILOKE_GLOBAL.isLoggedIn === 'no'){
					$('.header__user').find('.user__icon').trigger('click');
					return false;
				}

				let $target = $(event.currentTarget), xhr = null;

				$target.toggleClass('active');
				if ( !_.isNull(xhr) && xhr.status !== 4 ){
					xhr.abort();
				}

				$.ajax({
					url: WILOKE_GLOBAL.ajaxurl,
					type: 'POST',
					data: {action: 'wiloke_toggle_favorite_list', ID: $target.data('postid'), security: WILOKE_GLOBAL.wiloke_nonce},
					success: ((response)=>{

					})
				});
			}));
		}

		removeFavorite(){
			$(document).on('click', '.js-remove-favorite', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				$target.closest('.f-listings-item').remove();
				$.ajax({
					url: WILOKE_GLOBAL.ajaxurl,
					type: 'POST',
					data: {action: 'wiloke_remove_favorite', ID: $target.data('postid'), security: WILOKE_GLOBAL.wiloke_nonce}
				});
			}))
		}
	}

	class WilokePagination{
		constructor(totalPosts, postsPerPage, currentPage){
			this.currentPage        = currentPage;
			this.totalPosts         = totalPosts;
			this.postsPerPage       = postsPerPage;
			this.maxPages           = 0;
			this.oText              = WILOKE_LISTGO_TRANSLATION;
		}

		createPagination(){
			let aPages = [], pagination='';

			if ( this.totalPosts === 0 ){
				return '';
			}

			this.maxPages = Math.ceil(this.totalPosts/this.postsPerPage);

			// If, in case, We have only one page, simply print nothing
			if ( this.maxPages <= 1 ){
				return false;
			}

			this.currentPage = !_.isUndefined(this.currentPage) ? this.currentPage : 1;

			// If the total page is smaller than 8 or equal to 8, We print all
			if ( this.maxPages <= 8 ){
				for ( let i = 1; i <= this.maxPages; i++ ){
					aPages.push(i);
				}
			}else{
				if ( this.currentPage <= 3 ){
					// If the current page is smaller than 4, We print the first three pages and the last page
					aPages = [1, 2, 3, 4, 'x', this.maxPages];
				}else if(this.currentPage < 7){
					// if the current page is smaller than 7, We print the first seven pages and the last page
					aPages = [1, 2, 3, 4, 5, 6, 7, 'x', this.maxPages];
				}else{
					// And, in the last casfe, We print the first three pages and the pages range of [currentPage-3, currentPage]
					aPages = [1, 'x'];

					for ( let i = 2;  i >= 0; i--  ){
						aPages.push(this.currentPage-i);
					}

					let currentToLast = this.maxPages - this.currentPage;
					if ( currentToLast <= 8 ){
						for ( let j = this.currentPage+1; j <= this.maxPages ; j++ ){
							aPages.push(j);
						}
					}else{
						for ( let j = 0; j <= 2 ; j++ ){
							aPages.push(this.currentPage+1+j);
						}

						aPages.push('x');
						aPages.push(this.maxPages);
					}
				}
			}

			for ( let i = 0, maximum = aPages.length; i < maximum; i++ ){
				if ( this.currentPage === aPages[i] ){
					pagination += '<a class="page-numbers current" data-page="'+aPages[i]+'">'+aPages[i]+'</a>';
				}else if(aPages[i] === 'x'){
					pagination += '<a href="#" class="page-numbers dots">...</a>';
				}else{
					pagination += '<a href="#" class="page-numbers"  data-page="'+aPages[i]+'">'+aPages[i]+'</a>';
				}
			}

			if ( pagination !== '' ){

				if ( this.currentPage !== 1 ){
					pagination += '<a href="#" class="prev page-numbers">'+this.oText.prev+'</a>';
				}

				if ( this.currentPage !== this.maxPages ){
					pagination += '<a href="#" class="next page-numbers">'+this.oText.next+'</a>';
				}

				// pagination = '<div class="nav-links text-center">' + pagination + '</div>';
			}

			return pagination;
		}

	}

	class wilokeMediaUpload{
		constructor($target){
			this.$trigger = $target;
			this.init();
		}

		template(url, id){
			let item = _.template("<li class='gallery-item bg-scroll' data-id='<%- id %>' style='background-image: url(<%- backgroundUrl %>)'><span class='wil-addlisting-gallery__list-remove'>Remove</span></li>");
			return item({
				backgroundUrl: url,
				id: id
			});
		}

		init(){
			if ( this.$trigger.length ){
				// ADD IMAGE LINK
				this.$trigger.on( 'click', (event=>{
					let $target = $(event.currentTarget);
					event.preventDefault();

					let isMultiple = $target.data('multiple');
					// If the media frame already exists, reopen it.
					if ( $target.data('frame') ) {
						$target.data('frame').open();
						return false;
					}
					let imgSize = typeof $target.data('imgsize') !== 'undefined' ? $target.data('imgsize') : 'thumbnail';
					// Create a new media frame
					let frame = wp.media({
						title: '',
						button: {
							text: 'Select'
						},
						multiple: isMultiple  // Set to true to allow multiple files to be selected
					});
					$target.data('frame', frame);

					// When an image is selected in the media frame...
					$target.data('frame').on( 'select', (()=>{
						// Get media attachment details from the frame state
						if ( isMultiple ){
							let aAttachemnts = $target.data('frame').state().get('selection').toJSON();
							let imgs = '';
							_.forEach(aAttachemnts, (oAttachment=>{
								let thumbnailUrl = !_.isUndefined(oAttachment.sizes[imgSize]) && oAttachment.sizes[imgSize] ? oAttachment.sizes[imgSize].url : oAttachment.url;
								imgs += this.template(thumbnailUrl, oAttachment.id);
							}));
							$target.parent().before(imgs);
						}else{
							let attachment = $target.data('frame').state().get('selection').first().toJSON();
							// Send the attachment URL to our custom image input field.
							let thumbnailUrl = !_.isUndefined(attachment.sizes[imgSize]) && attachment.sizes[imgSize] ? attachment.sizes[imgSize].url : attachment.url;
							$target.find('.wiloke-preview').attr( 'src', thumbnailUrl ).removeClass('hidden');
							// Send the attachment id to our hidden input
							$target.find('.wiloke-insert-id').val( attachment.id );
							$target.find('#wiloke-avatar-by-text').addClass('hidden');
							if ( $target.hasClass('profile-background') ){
								$('.header-page').css('background-image', 'url('+attachment.url+')');
							}
						}

					}));

					// Finally, open the modal on click
					$target.data('frame').open();
				}));
			}
		}
	}

	class WilokeReview{
		constructor(){
			this.$btnReview = $('#submit-review');
			this.$previewGallery = null;
			this.init();
		}

		init(){
			if ( this.$btnReview.length ){
				this.$formRating = $('#form-rating');
				this.xhr = null;
				this.xhrThanksReviewing = null;
				this.paged = 1;
				this.orderBy = 'newest_first';
				this.xhrFetchComments = null;
				this.currentTabKey = 'wiloke_listgo_current_tab';
				this.currentReviewIDKey = 'wiloke_listgo_current_reviewID';
				this.aListFiles = [];
				this.aListingGalleryIDs = [];
				this.formData = null;
				this.isProcessData = false;
				this.contentType = false;

				let self = this, $uploadFotos = $('#upload_photos');
				this.$comments   = $('#comments');
				this.$showReview = this.$comments.find('.commentlist');
				this.$paginationPlaceholder = $('#pagination-placeholder');
				this.$orderBy = $('#comments_orderby');
				this.$rating = this.$formRating.find('#rating');

				this.autoSwitchToTab();
				this.switchOrderBy();
				this.switchPage();
				this.scrollToReviewForm();
				this.pagination();
				this.selectRating();
				this.thanksReviewing();

				if ( !$uploadFotos.hasClass('is-using-media') ){
					$uploadFotos.on('change', function(){
						self.aListFiles = this.files;
						self.showImageReview();
					});
				}

				if ( this.xhr !== null && this.xhr.status !== 200 ){
					this.xhr.abort();
				}

				this.$formRating.on('submit', (function(event){
					event.preventDefault();
					self.fetchImgGallery();
					if ( !$uploadFotos.hasClass('is-using-media') ){
						self.formData = new FormData($(this)[0]);
					}else{
						self.formData = self.$formRating.serialize();
						self.isProcessData = true;
						self.contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
					}

					let $this   = $(this),
						$title  = $this.find('#title'),
						$review = $this.find('#review'),
						$username = $this.find('#username'),
						$photos = $this.find('#upload_photos'),
						$previewGallery = $this.find('#preview-gallery'),
						$email  = $this.find('#email'),
						$password  = $this.find('#password');

					$this.addClass('loading');
					self.$btnReview.addClass('loading');

					if ( $title.val() === '' ){
						$title.parent().addClass('validate-required');
						return false;
					}

					if ( $review.val() === '' ){
						$review.parent().addClass('validate-required');
						return false;
					}

					if ( $email.length && $email.val() === '' ){
						$email.parent().addClass('validate-required');
						return false;
					}

					if ( $password.length && $password.val() === '' ){
						$password.parent().addClass('validate-required');
						return false;
					}

					self.xhr = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl+'?action=wiloke_listgo_submit_review',
						async: true,
						cache: self.isProcessData,
						contentType: self.contentType,
						processData: self.isProcessData,
						data: self.formData,
						success: (response=>{
							if ( response.success ){
								if ( response.data.refresh === 'yes' ){
									localStorage.setItem(self.currentTabKey, '#tab-review');
									localStorage.setItem(self.currentReviewIDKey, response.data.review_ID);
									location.reload();
								}else{
									self.$btnReview.siblings('.review_status').html(response.data.message);
									self.$showReview.prepend(response.data.review);
									new WilokeGalleryPopup(self.$showReview.find('.popup-gallery'));

									setTimeout(function () {
										$('body, html').animate({
											scrollTop: self.$comments.find('.comment[data-reviewid="'+response.data.review_ID+'"]').offset().top
										}, 600);
									}, 1000);

									self.$showReview.find('.lazy').Lazy();
								}

								$title.val('');
								$review.val('');
								$username.val('');
								$photos.val('');
								$previewGallery.html('');
								self.$rating.val(5);
								self.$formRating.find('.comment__rate a').removeClass('active');
								self.$formRating.find('.comment__rate a:last').addClass('active');
								self.$btnReview.siblings('.review_status').html('');
								self.$formRating.find('#wiloke-preview-gallery .bg-scroll').remove();
								self.$formRating.find('#upload_photos').val('');
								self.aListingGalleryIDs = [];
								if ( $email ){
									$email.val('');
								}
							}else{
								if ( !_.isUndefined(response.data) ){
									if ( typeof response.data.alert !== 'undefined' ){
										alert(response.data.alert);
									}else{
										_.forEach(response.data, ((value, key)=>{
											$this.find('#'+key).parent().addClass('validate-required');
										}));
									}
								}
							}
							$this.removeClass('loading');
							self.$btnReview.removeClass('loading');
						})
					})
				}));
			}
		}

		fetchImgGallery(){
			if( this.$formRating.find('#wiloke-preview-gallery').length ){
				let self = this;
				this.$formRating.find('#wiloke-preview-gallery .gallery-item.bg-scroll').each(function(){
					let galleryID = $(this).data('id');
					if ( typeof galleryID !== 'undefined' ){
						self.aListingGalleryIDs.push(galleryID);
					}
				});
				this.$formRating.find('#upload_photos').val(this.aListingGalleryIDs.join(','));
			}
		}

		showImageReview(){
			if ( this.aListFiles.length ){
				let $previewGallery = this.$formRating.find('#preview-gallery');
				$previewGallery.html('');
				_.each(this.aListFiles, (file=>{
					if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
						let reader = new FileReader(), allow = $previewGallery.data('allow');

						reader.addEventListener('load', function () {
							if ( file.size <= allow*1000000 ){
								$previewGallery.append('<a href="#" class="bg-scroll" style="background-image: url('+this.result+')"><img src="'+this.result+'"></a>');
							}
						}, false);

						reader.readAsDataURL(file);
					}
				}));
			}
		}

		validated(){
			this.$formRating.on('keydown', '#email, #title, #review', (event=>{
				let $target = $(event.currentTarget);
				let handleFunc = null;

				if ( handleFunc !== null ){
					clearTimeout(handleFunc);
				}

				handleFunc = setTimeout((()=>{
					if ( $target.val() !== '' ){
						$target.parent().addClass('validate-required');
						clearTimeout(handleFunc);
					}else{
						$target.parent().removeClass('validate-required');
						clearTimeout(handleFunc);
					}
				}), 300);

			}));
		}

		scrollToReviewForm(){
			$('a[href="#comment-respond"]').on('click', (event=>{
				event.preventDefault();
				$('body, html').animate({
					scrollTop: $('#comment-respond').offset().top
				}, 600);
			}));
		}

		autoSwitchToTab(){
			let currentTab = localStorage.getItem(this.currentTabKey);
			if ( currentTab ){
				$('a[href="'+currentTab+'"]').trigger('click');
				let currentReviewID = localStorage.getItem(this.currentReviewIDKey),
					$currentPos = $('[data-reviewid="'+currentReviewID+'"]');
				if ( $currentPos.length ){
					$('html, body').animate({
						scrollTop: $currentPos.offset().top
					}, 600);
				}

				localStorage.removeItem(this.currentReviewIDKey);
				localStorage.removeItem(this.currentTabKey);
			}else{
				let currentURL = window.location.href;
				if ( currentURL.search('#tab-review') !== -1 ){
					$('a[href="#tab-review"]').trigger('click');
					let aCurrentReview = currentURL.split('&current-review=');
					if ( _.isUndefined(aCurrentReview[1]) ){
						let $currentPos = $('[data-reviewid="'+aCurrentReview[1]+'"]');
						if ( $currentPos.length ){
							$('html, body').animate({
								scrollTop: $currentPos.offset().top
							}, 600);
						}
					}
				}
			}
		}

		thanksReviewing(){
			this.$comments.on('click', '.wiloke-listgo-thanks-for-reviewing', ((event)=> {
				event.preventDefault();
				let $target = $(event.currentTarget);

				if ( this.xhrThanksReviewing !== null && this.xhrThanksReviewing.status !== 200 ){
					this.xhrThanksReviewing.abort();
				}

				let current = $target.find('.comment-like__count').text();
				current = parseInt(current, 10);
				$target.addClass('active disabled');
				$target.find('.comment-like__count').html(current+1);

				this.xhrThanksReviewing = $.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: { action: 'wiloke_listgo_thanks_reviewing', post_ID: $target.data('id'), security: WILOKE_GLOBAL.wiloke_nonce },
					success: (response=>{

					})
				})
			}));
		}

		selectRating(){
			this.$formRating.on('click', '.comment__rate a', (event=>{
				event.preventDefault();
				let self = $(event.currentTarget),
					$parent = self.parent();

				$parent.addClass('selected');
				$parent.find('.active').removeClass('active');
				self.addClass('active');
				this.$rating.val(self.data('score'));
			}));
		}

		switchOrderBy(){
			this.$orderBy.on('change', (event)=>{
				this.orderBy = $(event.currentTarget).find('option:selected').attr('value');
				this.paged = 1;
				this.$comments.addClass('loading');
				this.fetchReviews();
			});
		}

		pagination(){
			let commentsPerPage = this.$showReview.data('commentsperpage'),
				totalComments = this.$showReview.data('totalcomments'),
				instPagination = new WilokePagination(totalComments, commentsPerPage, this.paged);
			this.$paginationPlaceholder.html(instPagination.createPagination());
		}

		switchPage(){
			this.$paginationPlaceholder.on('click', '.page-numbers', ((event)=>{
				event.preventDefault();
				let $target = $(event.currentTarget);

				if ( $target.hasClass('prev') ){
					this.paged = this.paged - 1;
				}else if ( $target.hasClass('next') ){
					this.paged = this.paged + 1;
				}else{
					this.paged = $target.data('page');
				}

				$('html, body').animate({
					scrollTop: this.$comments.offset().top
				}, 600);

				this.$comments.addClass('loading');
				this.fetchReviews();
			}));
		}

		fetchReviews(){
			if ( this.xhrFetchComments !== null && this.xhrFetchComments.status !== 200 ){
				this.xhrFetchComments.abort();
			}

			this.xhrFetchComments = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_fetch_new_reviews', paged: this.paged, security: WILOKE_GLOBAL.wiloke_nonce, orderBy: this.orderBy, post_ID: WILOKE_GLOBAL.postID},
				beforeSend: (response=>{
					this.pagination();
				}),
				success: (response=>{
					if ( response.success ){
						this.$showReview.html(response.data.review);
						this.$showReview.find('.lazy').Lazy();
						new WilokeGalleryPopup(this.$showReview.find('.popup-gallery'));
					}
					$('#comments').removeClass('loading');
				})
			})
		}
	}

	class WilokeGalleryPopup{
		constructor($gallery){
			this.$gallery = $gallery;
			this.init();
		}

		init(){
			if ( this.$gallery.length ){
				this.$gallery.magnificPopup({
					delegate: 'a',
					type: 'image',
					tLoading: 'Loading image #%curr%...',
					mainClass: 'mfp-with-zoom mfp-img-mobile',
					closeBtnInside: false,
					closeMarkup: '<div class="popup-gallery__close pe-7s-close"></div>',
					gallery: {
						enabled: true,
						navigateByImgClick: true,
						preload: [0,1],
						tCounter: '%curr%/%total%'
					},
					image: {
						verticalFit: true,
						tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
						titleSrc: function(item) {
							if(typeof item.el.attr('data-title') !== 'undefined'){
								let title = item.el.attr('data-title');

								if ( !_.isUndefined(item.el.data('linkto')) ){
									let target = !_.isUndefined(item.el.attr('target')) ? item.el.attr('target') : '_self';
									title = '<a target="'+target+'" href="'+item.el.data('linkto')+'">'+title+'</a>';
								}

								return title;
							}
							return '';
						}
					}
				});
			}
		}
	}

	class WilokeListingManagement{
		constructor(){
			this.$app = $('#wiloke-listgo-listing-management');
			this.init();
			this.xhr = null;
		}

		init(){
			if ( this.$app.length ){
				this.$navFilter = this.$app.find('#nav-filters');
				this.$pagination = this.$app.find('#wiloke-listgo-pagination');
				this.$showListings = this.$app.find('#wiloke-listgo-show-listings');
				this.totalPosts = this.$navFilter.find('.active').data('total');
				this.postsPerPage = this.$app.data('postsperpage');
				this.paged = 1;
				this.status = 'all';
				this.pagination();
				this.switchPostStatus();
				this.switchPage();
				this.temporaryClosed();
				this.removeListing();
			}
		}

		temporaryClosed(){
			let xhr = null;
			this.$showListings.on('click', '.wiloke-listgo-temporary-closed', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);

				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}

				$target.addClass('loading');

				xhr = $.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'wiloke_listgo_temporary_closed_listing', post_ID: $target.data('postid'), security: WILOKE_GLOBAL.wiloke_nonce},
					success: (response=>{
						if ( response.success ){
							if ( response.data === 'publish' ){
								$target.removeClass('active');
							}else{
								$target.addClass('active');
							}
						}else{
							alert(response.data);
							$target.removeClass('active');
						}
						$target.removeClass('loading');
					})
				});

			}));
		}

		removeListing(){
			let xhr = null;
			this.$showListings.on('click', '.wiloke-listgo-remove-listing', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);

				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}

				let needToConfirm = prompt('Please enter "delete" in the field below to remove this listing');

				if ( needToConfirm === 'delete' ){
					$target.addClass('loading');

					xhr = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl,
						data: {action: 'wiloke_listgo_remove_listing', post_ID: $target.data('postid'), security: WILOKE_GLOBAL.wiloke_nonce},
						success: (response=>{
							if ( response.success ){
								$target.closest('.f-listings-item').fadeOut();
							}else{
								alert(response.data);
							}
							$target.removeClass('loading');
						})
					});
				}


			}));
		}

		switchPostStatus(){
			this.$navFilter.on('click', 'a', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				if ( $target.data('status') === this.status ){
					return false;
				}

				this.paged = 1;
				this.status = $target.data('status');
				this.$navFilter.find('a').removeClass('active');
				$target.addClass('active');
				this.fetchNewListings();

				this.$app.on('ajax_loaded', (event=>{
					this.pagination();
				}));
			}));
		}

		switchPage(){
			this.$pagination.on('click', '.page-numbers', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				if ( this.paged === $target.data('page') ){
					return false;
				}

				if ( $target.hasClass('next') ){
					this.paged = this.paged + 1;
				}else if ( $target.hasClass('prev') ){
					this.paged = this.paged - 1;
				}else{
					this.paged = $target.data('page');
				}
				this.pagination();
				this.fetchNewListings();
			}));
		}

		fetchNewListings(){
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			$('body, html').animate({
				scrollTop: this.$app.offset().top
			}, 400);

			this.$app.addClass('loading');

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_new_listing_management', post_status: this.status, paged: this.paged, security: WILOKE_GLOBAL.wiloke_nonce, postsperpage: this.postsPerPage},
				success: (response=>{
					if ( response.success ){
						this.$showListings.html(response.data.content);
						this.$navFilter.find('.active').data('total', response.data.total);
						this.totalPosts = response.data.total;
						this.$showListings.find('.lazy').Lazy();
					}else{
						alert('Something went wrong');
					}
					this.$app.removeClass('loading');
					this.$app.trigger('ajax_loaded');
				})
			})
		}

		pagination(){
			let instPagination = new WilokePagination(this.totalPosts, this.postsPerPage, this.paged);
			this.$pagination.html(instPagination.createPagination());
		}
	}

	class WilokeMyFavorite{
		constructor(){
			this.$app = $('#wiloke-listgo-my-favorites');
			this.init();
			this.xhr = null;
		}

		init(){
			if ( this.$app.length ){
				this.$pagination = this.$app.find('#wiloke-listgo-pagination');
				this.$showListings = this.$app.find('#wiloke-listgo-show-listings');
				this.totalPosts = this.$app.data('total');
				this.postsPerPage = this.$app.data('postsperpage');
				this.paged = 1;
				this.pagination();
				this.switchPage();
			}
		}

		switchPage(){
			this.$pagination.on('click', '.page-numbers', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				if ( this.paged === $target.data('page') ){
					return false;
				}

				if ( $target.hasClass('next') ){
					this.paged = this.paged + 1;
				}else if ( $target.hasClass('prev') ){
					this.paged = this.paged - 1;
				}else{
					this.paged = $target.data('page');
				}
				this.pagination();
				this.fetchNewListings();
			}));
		}

		fetchNewListings(){
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			$('body, html').animate({
				scrollTop: this.$app.offset().top
			}, 400);

			this.$app.addClass('loading');

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_fetch_favorites', paged: this.paged, security: WILOKE_GLOBAL.wiloke_nonce, postsperpage: this.postsPerPage},
				success: (response=>{
					if ( response.success ){
						this.$showListings.html(response.data.content);
						this.$showListings.find('.lazy').Lazy();
					}else{
						alert('Something went wrong');
					}
					this.$app.removeClass('loading');
					this.$app.trigger('ajax_loaded');
				})
			})
		}

		pagination(){
			let instPagination = new WilokePagination(this.totalPosts, this.postsPerPage, this.paged);
			this.$pagination.html(instPagination.createPagination());
		}
	}

	class WilokeBillingHistory{
		constructor(){
			this.$app = $('#wiloke-listgo-my-billing');
			this.init();
			this.xhr = null;
		}

		init(){
			if ( this.$app.length ){
				this.$pagination = this.$app.find('#wiloke-listgo-pagination');
				this.$showListings = this.$app.find('#wiloke-listgo-show-listings');
				this.totalPosts = this.$app.data('total');
				this.postsPerPage = this.$app.data('postsperpage');
				this.paged = 1;
				this.pagination();
				this.switchPage();
			}
		}

		switchPage(){
			this.$pagination.on('click', '.page-numbers', (event=>{
				event.preventDefault();
				let $target = $(event.currentTarget);
				if ( this.paged === $target.data('page') ){
					return false;
				}

				if ( $target.hasClass('next') ){
					this.paged = this.paged + 1;
				}else if ( $target.hasClass('prev') ){
					this.paged = this.paged - 1;
				}else{
					this.paged = $target.data('page');
				}
				this.pagination();
				this.fetchNewListings();
			}));
		}

		fetchNewListings(){
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				this.xhr.abort();
			}

			$('body, html').animate({
				scrollTop: this.$app.offset().top
			}, 400);

			this.$app.addClass('loading');

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_fetch_my_billings', paged: this.paged, security: WILOKE_GLOBAL.wiloke_nonce, postsperpage: this.postsPerPage},
				success: (response=>{
					if ( response.success ){
						this.$showListings.html(response.data.content);
						this.$showListings.find('.lazy').Lazy();
					}else{
						alert('Something went wrong');
					}
					this.$app.removeClass('loading');
					this.$app.trigger('ajax_loaded');
				})
			})
		}

		pagination(){
			let instPagination = new WilokePagination(this.totalPosts, this.postsPerPage, this.paged);
			this.$pagination.html(instPagination.createPagination());
		}
	}

	class wilokeNotification{
		constructor(){
			this.$notifications = $('#wiloke-notifications');
			this.filterBy = 'all';
			this.init();
		}

		init(){
			$(window).load((()=>{
				setTimeout((()=>{
					if ( this.$notifications.length ){
						this.totalNewFeed = 0;
						this.$countNewFeeds = this.$notifications.find('.count');
						this.$showLists = this.$notifications.find('.notifications__list');
						this.xhrLastCheck = null;
						this.xhrFetchNotification = null;
						this.updateLastCheck();
						this.fetchNotification();
					}
				}), 3000);
			}));
			this.infiniteLoadMoreNotification();
			this.filterReview();
			this.removeNotification();
			this.dismissNotification();
		}

		updateLastCheck(){
			this.$notifications.on('click', '.notifications__icon', (event=>{
				if (this.totalNewFeed !== 0 && this.totalNewFeed !== '' && !$(event.currentTarget).data('clicked')) {
					if (this.xhrLastCheck !== null && this.xhrLastCheck.status !== 200) {
						this.xhrLastCheck.abort();
					}
					this.xhrLastCheck = $.ajax({
						type: 'POST',
						url: WILOKE_GLOBAL.ajaxurl,
						data: {
							action: 'wiloke_listgo_update_lastcheck_notification',
							security: WILOKE_GLOBAL.wiloke_nonce
						},
						success: (response => {
							this.$countNewFeeds.html('');
							this.totalNewFeed = 0;
							$(event.currentTarget).data('clicked', true);
						})
					});
				}
			}));
		}

		fetchNotification(){
			if ( typeof WILOKE_GLOBAL.isDisableFetchNotification !== 'undefined' && WILOKE_GLOBAL.isDisableFetchNotification == 'yes' ){
				return '';
			}

				if ( this.xhrFetchNotification !== null && this.xhrFetchNotification.status !== 200 ){
				this.xhrFetchNotification.abort();
			}

			this.$showLists.addClass('loading');
			this.xhrFetchNotification = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_listgo_update_fetch_notifications', security: WILOKE_GLOBAL.wiloke_nonce, user_id: this.$notifications.data('userid')},
				success: (response=>{
					if ( response.success ){
						if ( response.data.countnew === 0 ){
							response.data.countnew = '';
						}
						this.totalNewFeed = response.data.countnew;
						this.$countNewFeeds.html(response.data.countnew);
						this.$showLists.html(response.data.notifications);
						if ( !response.data.is_empty ){
							this.$showLists.next().removeClass('hidden');
						}
					}
					this.$showLists.removeClass('loading');
				})
			});
		}

		infiniteLoadMoreNotification(){
			let isAjaxProcessing = false,
				aReviewIDs = [],
				$showNotifications = $('#wiloke-show-notifications'),
				cursor = $showNotifications.data('cursor'),
				$loadmoreBtn = $('#wiloke-loadmore-notifications'),
				btnText = $loadmoreBtn.html();

			if ( $loadmoreBtn.data('loadall') || isAjaxProcessing ){
				return false;
			}

			$loadmoreBtn.on('click', function (event) {
				event.preventDefault();

				if ( this.filterBy === 'hide' ){
					return false;
				}

				let self = this, $reviewIDs = $showNotifications.find('li.notification-item');
				$reviewIDs.each(function () {
					aReviewIDs.push($(this).data('objectid'));
				});

				$loadmoreBtn.html('Loading...');

				$.ajax({
					type: 'GET',
					url: WILOKE_GLOBAL.ajaxurl + '?action=fetch_more_notifications&posts__not_in='+aReviewIDs+'&cursor='+cursor+'&security='+WILOKE_GLOBAL.wiloke_nonce+'&filter_by='+this.filterBy,
					success: function (response) {
						$loadmoreBtn.html(btnText);
						if ( response.success ){
							cursor = response.data.cursor;
							$showNotifications.append(response.data.notifications);
							self.showFilterResult();
						}else{
							$loadmoreBtn.data('loadall', true);
							$loadmoreBtn.remove();
						}
						$showNotifications.removeClass('loading');
					}
				});
			});

			if ( $showNotifications.length && $showNotifications.find('li.notification-item').length !== $('.wiloke-notifications-wrapper').data('total') ){
				$loadmoreBtn.removeClass('hidden');
			}
		}

		filterReview(){
			let self = this,
				$filterByReview = $('#filter_by_review'),
				$filterByListing = $('#filter_by_listing');

			$('#wiloke-filter-notifications').on('change', function () {

				let filterByReview = $filterByReview.is(':checked'),
					filterByListing = $filterByListing.is(':checked');

				if ( !filterByReview && !filterByListing  ){
					self.filterBy = 'hide';
				}else if ( !filterByListing && filterByReview ){
					self.filterBy = 'review';
				}else if ( filterByListing && !filterByReview ){
					self.filterBy = 'listing';
				}else{
					self.filterBy = 'all';
				}

				self.showFilterResult();
			})
		}

		showFilterResult(){
			let $showNotifications = $('#wiloke-show-notifications');
			if ( this.filterBy === 'review' ){
				$showNotifications.find('li.notification-item[data-type="review"]').show();
				$showNotifications.find('li.notification-item[data-type!="review"]').hide();
			}else if ( self.filterBy === 'listing' ){
				$showNotifications.find('li.notification-item[data-type!="review"]').show();
				$showNotifications.find('li.notification-item[data-type!="review"]').show();
			}else{
				$showNotifications.find('li.notification-item').show();
			}
		}

		removeNotification(){
			let $notificationWrapper = $('.wiloke-notifications-wrapper'),
				$showNotifications = $('#wiloke-show-notifications');

			$showNotifications.on('click', '.notifications__remove', function(){
				let $this = $(this),
					$parent = $this.closest('li.notification-item'),
					total = parseInt($notificationWrapper.data('total'), 10) - 1,
					objectID = $parent.data('objectid');
				$.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'remove_more_notifications', object_ID: objectID, security: WILOKE_GLOBAL.wiloke_nonce},
					success: function (response) {
						if ( response.success ){
							$parent.remove();
							$('.wiloke-notifications-count').html('(' +total+ ')');
						}else{
							alert(response.data);
						}
					}
				});
			});
		}

		dismissNotification(){
			let $app = $('.wil-alert-remove');
			let xhr = null;
			$app.on('click', (event=>{
				event.preventDefault();
				if ( xhr !== null && xhr.status !== 200 ){
					xhr.abort();
				}
				let $event = $(event.currentTarget);
				let oUserInfo = $.parseJSON(WILOKE_GLOBAL.userInfo);
				let currentUserID = typeof oUserInfo.user_id !== 'undefined' ? oUserInfo.user_id : '';

				$event.closest('.wil-alert').fadeOut();
				xhr = $.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'wiloke_dismiss_notification', objectID: $event.data('id'), currentUserID: currentUserID, security: WILOKE_GLOBAL.wiloke_nonce},
					success: (response=>{})
				})
			}))
		}
	}

	class Tabs{
		constructor(){
			this.popupSignUpSignIn();
			this.sidebar();
		}

		popupSignUpSignIn(){
			let $navTab     = $('#signup-signin-navtab'),
				$tabWrapper = $('#signup-signin-wrapper'),
				$usingSocial= $tabWrapper.find('.signup-signin-with-social'),
				$tabPanel   = $tabWrapper.find('.tab__panel');


			$('.tab .tab__nav ').on('click', 'a', (event=>{
				event.preventDefault();

				let $this   = $(event.currentTarget),
					$tabs   = $this.closest('.tab'),
					href 	= $this.attr('href'),
					$panel  = null,
					$li     = $this.closest('li');

				if ( href.search('http') !== -1 ){
					href = href.split('#');
					href = '#'+href[1];
				}
				$panel = $(href);
				if( !$li.hasClass('active') ) {
					$('.tab__panel.active, .tab__nav .active', $tabs).removeClass('active');
					$li.addClass('active');
					$panel.addClass('active');
				}
			}));

			$('.tab--form').on('click', '.switch-tab', (event=>{
				event.preventDefault();
				let $this = $(event.currentTarget),
					currentTarget = $this.attr('href');

				switch (currentTarget){
					case '#lostpassword':
						$navTab.addClass('hidden');
						$tabPanel.removeClass('active');
						$usingSocial.addClass('hidden');
						$(currentTarget).addClass('active');
						break;
					default:
						$navTab.removeClass('hidden');
						$tabPanel.removeClass('active');
						$usingSocial.removeClass('hidden');
						$navTab.find('li').removeClass('active');
						$navTab.find('a[href="'+currentTarget+'"]').parent().addClass('active');
						$(currentTarget).addClass('active');
						break;
				}
			}))
		}

		sidebar(){
			if( $('.listing-single-bar').length ) {

				$('.listing-single-bar .tab__nav').on('click', 'a', function(event) {
					event.preventDefault();

					let $this = $(this),
						offsetTop = $('.listing-single__tab').offset().top,
						$wrap = $this.closest('.tab__nav'),
						index = $this.parent('li').index();

					$('body').stop().animate({scrollTop: offsetTop - 30}, 300);

					if( $this.parent('li').hasClass('active') ) {
						return false;
					} else {
						$wrap.find('.active').removeClass('active');
						$this.parent().addClass('active');
						$('.listing-single .tab .tab__nav li').eq(index).find('a').trigger('click');
					}
				});

				$('.listing-single__tab .tab__nav').on('click', 'a', function(event) {
					event.preventDefault();
					let $this = $(this),
						index = $this.parent('li').index();

					$('.listing-single-bar .tab__nav .active').removeClass('active');
					$('.listing-single-bar .tab__nav li').eq(index).addClass('active');
				});
			}
		}
	}

	class PopupSignUpSignIn{
		constructor(){
			this.$app = $('#signup-signin-wrapper, #wiloke-sc-signup-signin-wrapper, #wiloke-widget-signup-signin-wrapper');
			this.controller();
		}

		controller(){
			if ( this.$app.length ){
				this.xhr    = null;
				this.action = null;
				this.$this  = null;
				this.$btn   = null;
				this.oData  = {};
				this.clearErrorIndication();
				this.signIn();
				this.signUp();
				this.resetPassword();
				this.socialConnection();
			}
		}

		clearErrorIndication(){
			this.$app.find('input').on('keydown', (event=>{
				let $target = $(event.currentTarget),
					currentTimeout = null;
				if ( currentTimeout !== null ){
					clearTimeout(currentTimeout);
				}

				currentTimeout = setTimeout((event=>{
					$target.closest('.form-item').removeClass('validate-required');
					clearTimeout(currentTimeout);
				}), 1000);
			}))
		}

		socialConnection(){
			let $body = $('body');
			$body.on('wiloke_login_with_social/connecting', ((event, $target)=>{
				$target.addClass('loading');
				$target.closest('.signup-signin-with-social').find('.print-msg-here').html('');
			}));

			$body.on('wiloke_login_with_social/ajax_completed', ((event, $target, response)=>{
				$target.removeClass('loading');
				if ( !_.isUndefined(response) && !response.success ){
					$target.closest('.signup-signin-with-social').find('.print-msg-here').html(response.data.message);
				}
			}));
		}

		signIn(){
			$('#wiloke-popup-signin-form').on('submit', (event=>{
				let $form = $(event.target),
					$btn  = $form.find('.signin-btn');

				event.preventDefault();
				this.action = 'wiloke_signin';
				this.$this  = $form;
				this.$btn   = $btn;

				delete this.oData;
				this.oData = {
					userlogin: $form.find('[name="userlogin"]').val(),
					password: $form.find('[name="password"]').val(),
					remember: $form.find('[name="remember"]').is(':checked') ? 'yes' : ''
				};

				this.timeout = 1000;
				this.ajaxProcessing();
			}));
		}

		signUp(){
			$('#wiloke-widget-signup-form, #wiloke-shortcode-signup-form, #wiloke-popup-signup-form').on('submit', (event=>{
				event.preventDefault();
				let $form = $(event.target),
					$btn  = $form.find('.signup-btn');

				this.action = 'wiloke_signup';
				this.$this  = $form;
				this.$btn   = $btn;
				this.oData = {
					username: $form.find('[name="username"]').val(),
					email: $form.find('[name="email"]').val(),
					password: $form.find('[name="password"]').val(),
					ggrecaptcha: typeof grecaptcha !== 'undefined' ? $form.find('[name="g-recaptcha-response"]').val() : ''
				};

				this.timeout = 3000;
				this.ajaxProcessing();
			}));
		}

		resetPassword(){
			let $btn  = this.$app.find('#recoverpassword'),
				$form = this.$app.find('#recoverpassword-form');

			$form.on('submit', (event=>{
				event.preventDefault();
				this.action = 'wiloke_resetpassword';
				this.$this  = $form;
				this.$btn   = $btn;

				delete this.oData;
				this.oData = {
					user_login: $form.find('[name="user_login"]').val()
				};

				this.timeout = 1000;
				this.ajaxProcessing();
			}));
		}

		ajaxProcessing(){
			if ( this.xhr !== null && this.xhr.status !== 200 ){
				return false;
			}

			this.$btn.addClass('loading');
			let oData = {
				action: this.action,
				security: WILOKE_GLOBAL.wiloke_nonce
			};

			oData = _.extend(oData, this.oData);

			this.$this.find('.print-msg-here').html('');
			
			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				// dataType: "json", // and this
			 	// contentType: "application/json; charset=utf-8", // this
				data: oData,
				success: (response=>{
					if ( response.success ){
						this.$this.find('.print-msg-here').removeClass('error-msg').addClass('success-msg').html(response.data.message);
						if ( this.action === 'wiloke_signin' || this.action === 'wiloke_signup' ){
							setTimeout((()=>{
								location.reload();
							}), this.timeout);
						}else if ( this.action === 'wiloke_resetpassword' ){
							this.$this.find('.form-item').remove();
						}
					}else{
						if ( !_.isUndefined(response.data) && !_.isUndefined(response.data.target) && this.$this.find('#'+response.data.target).length ){
							this.$this.find('#'+response.data.target).closest('.form-item').addClass('validate-required');
						}else if( !_.isUndefined(response.data) && !_.isUndefined(response.data.message)) {
							this.$this.find('.print-msg-here').html(response.data.message);
						}
					}
					this.$btn.removeClass('loading');
				})
			})
		}
	}

	class WilokeGridRotator{
		constructor($app){
			this.$app = $app;
			this.init();
		}

		init(){
			if ( !this.$app.find('.wil-gridratio__list').children().length ){
				return false;
			}

			this.oConfigs = {
				preventClick: false,
				resize: (()=>{

					this.$app.find('li').hoverdir({
						speed: 250,
						hoverElem: '.wil-gridratio__caption'
					});
				}),
				replaceItem: ((i)=>{
					$('.wil-gridratio__caption',i).css('left', '-100%');
					$(i).hoverdir({
						speed: 250,
						hoverElem: '.wil-gridratio__caption'
					});
				})
			};

			this.oConfigs = $.extend({}, this.oConfigs, this.$app.data('configuration'));

			this.letStart();
		}

		letStart(){
			this.$app.gridrotator(this.oConfigs);
		}
	}

	class updateProfile{
		constructor(){
			this.$formUpdate = $('#wiloke-listgo-update-profile');
			this.passedValidation = true;
			this.xhr = null;
			this.init();
		}

		scrollTop($target){
			$('body, html').animate({
				scrollTop:$target.offset().top - 100
			}, 600);
		}

		validate(){
			let aRequired = ['user_email', 'display_name', 'nickname'];

			_.forEach(aRequired, (val=>{
				if ( this.$formUpdate.find('#'+val).val() === '' ){
					this.passedValidation = false;
					this.$formUpdate.find('#'+val).closest('.form-item').addClass('validate-required');
					this.scrollTop($('#'+val));
					return false;
				}

				let currentSession = null;
				this.$formUpdate.find('#'+val).on('keydown', (event=>{
					let $target = $(event.currentTarget);
					if ( currentSession !== null ){
						clearTimeout(currentSession);
					}
					currentSession = setTimeout(()=>{
						$target.closest('.form-item').removeClass('validate-required');
						$target.closest('.form-item').find('.validate-message').remove();
						clearTimeout(currentSession);
					}, 1000);
				}))
			}));

			let $currentPassword = this.$formUpdate.find('#current_password'),
				$newPassword = this.$formUpdate.find('#new_password'),
				$confirmPassword = this.$formUpdate.find('#confirm_new_password');

			$newPassword.on('keydown', function () {
				$newPassword.closest('.form-item').removeClass('validate-required');
				$newPassword.find('.validate-message').remove();
			});

			$currentPassword.on('keydown', function () {
				$currentPassword.closest('.form-item').removeClass('validate-required');
				$currentPassword.find('.validate-message').remove();
			});

			$confirmPassword.on('keydown', function () {
				$confirmPassword.closest('.form-item').removeClass('validate-required');
				$confirmPassword.find('.validate-message').remove();
			});

			if ( $newPassword.val() !== '' ){
				if ( $currentPassword.val() === '' ){
					this.passedValidation = false;
					$currentPassword.closest('.form-item').addClass('validate-required');
					this.scrollTop($currentPassword);
					return false;
				}else{
					if ( $newPassword.val() !== $confirmPassword.val() ){
						this.passedValidation = false;
						$newPassword.closest('.form-item').addClass('validate-required');
						$confirmPassword.closest('.form-item').addClass('validate-required');
						this.scrollTop($newPassword);
						return false;
					}
				}
			}
		}

		init(){
			this.$btnUpdate = this.$formUpdate.find('#wiloke-listgo-submit-update-profile');
			if ( this.$formUpdate.length > 0 ){

				this.$formUpdate.on('submit', (event=>{
					event.preventDefault();

					this.passedValidation = true;
					this.validate();

					if ( !this.passedValidation ){
						return false;
					}

					if ( this.xhr !== null && this.xhr.status !== 200 ){
						this.xhr.abort();
					}

					if ( this.$formUpdate.find('#current_password').val() !== '' ){
						if ( this.$formUpdate.find('#new_password').val() === '' ){
							this.$formUpdate.find('#new_password').closet('.form-item').addClass('validate-required');
						}
					}

					this.$formUpdate.addClass('loading');
					this.$btnUpdate.addClass('loading');

					this.xhr = $.ajax({
						type: 'POST',
						data: {action: 'wiloke_listgo_update_profile', security: WILOKE_GLOBAL.wiloke_nonce, data: this.$formUpdate.serialize()},
						url: WILOKE_GLOBAL.ajaxurl,
						success: (response=>{
							if ( response.success ){
								this.$formUpdate.find('.update-status').html('<i class="fa fa-check-circle"></i> '+response.data.message);
							}else{
								if ( response.data !== '' ){
									_.forEach(response.data, (val, key)=>{
										let $errorPlace = $('#'+key);
										if ( $errorPlace.length ){
											$errorPlace.closest('.form-item').addClass('validate-required').append('<span class="validate-message">'+val+'</span>');
											this.scrollTop($errorPlace);
											return false;
										}
									})
								}
							}

							this.$formUpdate.removeClass('loading');
							this.$btnUpdate.removeClass('loading');
						})
					})
				}))

			}
		}
	}

	class WilokeSubscribe{
		constructor(){
			this.init();
		}

		init(){
			this.xhr = false;
			this.authorID = null;
			this.status = 'follow';
			this.oTranslate = WILOKE_LISTGO_TRANSLATION;

			let $jsSubscribe = $('.js_subscribe');
			$jsSubscribe.on('click', ((event)=>{
				event.preventDefault();
				if ( WILOKE_GLOBAL.isLoggedIn  === 'no'){
					$('.header__user').find('.user__icon').trigger('click');
					return false;
				}

				let $target = $(event.currentTarget);
				this.$app = $target;
				this.authorID = $target.data('authorid');

				$target.parent().toggleClass('active');

				if (_.isUndefined(this.authorID)) {
					return false;
				}

				this.status = $target.data('status') === 'follow' || typeof $target.data('status') === 'undefined' ? 'unfollow' : 'follow';
				$target.data('status', this.status);
				this.ajax();
			}));

			$jsSubscribe.on('completed', ((event, response)=>{
				if ( response.data.status === 'following' ){
					$jsSubscribe.html(response.data.text+' <i class="fa fa-rss"></i>');
				}else{
					$jsSubscribe.html(response.data.text+' <i class="fa fa-rss"></i>');
				}
			}));

			$('.js_subscribe_on_profile').on('click', ((event)=>{
				event.preventDefault();
				if ( WILOKE_GLOBAL.isLoggedIn  === 'no'){
					alert(this.oTranslate.needsingup);
					return false;
				}
				let $target = $(event.currentTarget);
				this.$app = $target;
				this.authorID = $target.data('authorid');
				this.status = $target.data('status') === 'follow' || typeof $target.data('status') === 'undefined' ? 'unfollow' : 'follow';

				let $followers = $('.account-subscribe').find('.followers .count'),
					followers = $followers.html();
				followers = parseInt(followers, 10);

				this.$app.on('completed', ((event, response)=>{
					if ( response.data.status === 'following' ){
						this.$app.html(this.oTranslate.followingtext+' <i class="fa fa-rss"></i>');
						$followers.html(followers+1);
					}else{
						this.$app.html(this.oTranslate.unfollowingtext+' <i class="fa fa-rss"></i>');
						$followers.html(followers-1);
					}
				}));

				this.ajax();
			}));
		}

		ajax(){
			if (this.xhr && this.xhr.status !== 200) {
				this.xhr.abort();
			}

			this.xhr = $.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: {action: 'wiloke_follow', security: WILOKE_GLOBAL.wiloke_nonce, author_id: this.authorID, status: this.status},
				success: (response=>{
					if ( WILOKE_GLOBAL.is_debug ){
						console.log(response);
					}

					if ( !response.success ){
						alert(response.data);
					}else{
						this.status = response.data.status;
						this.$app.trigger('completed', response);
					}
				})
			})
		}
	}

	class WilokeReport{
		constructor(){
			this.xhr = null;
			this.init();
		}

		init(){
			this.$report = $('.js_report');
			this.oTranslation = WILOKE_LISTGO_TRANSLATION;
			this.$report.on('click', (event=>{
				event.preventDefault();
				let $this = $(event.currentTarget);
				if ( $this.data('didit') ){
					return false;
				}
				let reason = prompt(this.oTranslation.report, '');
				if ( reason === null || reason === '' ){
					return false;
				}else{
					if ( this.xhr !== null && this.xhr.status !== 200 ){
						this.xhr.abort();
					}

					this.xhr = $.ajax({
						type: 'POST',
						data: {action: 'add_report', ID: WILOKE_GLOBAL.postID, reason: reason, security: WILOKE_GLOBAL.wiloke_nonce},
						url: WILOKE_GLOBAL.ajaxurl,
						success: (response=>{
							if ( response.success ){
								$this.data('didit', true);
								alert(response.data.msg);
							}else{
								alert(response.data.msg);
							}
						})
					})
				}
			}))
		}
	}
	
	class WilokeStatistics{
		constructor(){
			this.totalView();
		}
		totalView(){
			if ( WILOKE_GLOBAL.post_type !== 'listing' ){
				return false;
			}

			let oCurrentUserInfo = JSON.parse(WILOKE_GLOBAL.userInfo);
			let currentUserID = typeof oCurrentUserInfo.user_id !== 'undefined' ? oCurrentUserInfo.user_id : -1;

			setTimeout(function () {
				$.ajax({
					type: 'POST',
					url: WILOKE_GLOBAL.ajaxurl,
					data: {action: 'wiloke_submission_statistic_view', authorID: WILOKE_GLOBAL.authorID, listingID: WILOKE_GLOBAL.postID, security: WILOKE_GLOBAL.wiloke_nonce, currentUserID: currentUserID},
					success: (response=>{

					})
				})
			}, 400);
		}
	}

	function general() {
		let $select2 = $('.js_select2');

		if ( $select2.length && !WilokeDetectMobile.Any() ) {
			let oTranslation = WILOKE_LISTGO_TRANSLATION;
			$select2.each(function(){

				let isTags = (typeof $(this).data('tags') !== 'undefined'),
					placeholder = typeof $(this).data('placeholder') !== 'undefined' ? $(this).data('placeholder') : oTranslation.selectoption;

				$(this).select2({
					tags: isTags,
					placeholder: placeholder,
					templateResult: function (state) {
						let imgUrl = $(state.element).data('img');
						if ( imgUrl === '' || _.isUndefined(imgUrl) ){
							return state.text;
						}
						return $(
							'<span><img src="' + imgUrl + '" class="img-flag"> ' + state.text + '</span>'
						);
					}
				}).addClass('created');
			});
		}

		// Magnific Popup
		new WilokeGalleryPopup($('.popup-gallery'));

		$('#commentform').wrap('<div class="row"></div>');

		// Overlay color for row
		$('.vc_row').each(function () {
			let overlayColor = $(this).data('overlaycolor');
			$(this).css({'background-color': overlayColor});
		});

		let $simplePostsSlider = $('.wiloke-simple-posts-slider');
		if ( $simplePostsSlider.children().length ){
			$simplePostsSlider.owlCarousel({
				items: 1,
				nav: true,
				loop: true,
				rtl: rtl,
				navText: ['<i class="arrow_carrot-left"></i>', '<i class="arrow_carrot-right"></i>']
			});
		}

		let $twitterSlider = $('.twitter-slider');
		if ( $twitterSlider.children().length ){
			$twitterSlider.owlCarousel({
				items: 1,
				nav: true,
				loop: true,
				rtl: rtl,
				autoHeight: true,
				navText: ['<i class="arrow_carrot-left"></i>', '<i class="arrow_carrot-right"></i>']
			});
		}

		let $searchForm = $('#listgo-searchform');
		$searchForm.on('click', '.icon_search', function () {
			$searchForm.trigger('submit');
		});

		if ( !$('.copyright').children().length && !$('.social_footer').children().length && !$('.footer__widget').length ){
			$('#footer').css({'background-color': '#fff'});
		}

		let $widgetAuthor = $('.widget_author__content');
		if ( !$widgetAuthor.find('.widget_author__address').children().length && !$widgetAuthor.find('.widget_author__social').children().length ){
			$widgetAuthor.remove();
		}
	}

	function wooMiniCart(){
		let $miniCart = $('#cart-mini-content');
		$(document).on('click', '.add_to_cart_button', function(){
			let currentCart = parseInt($miniCart.data('total'), 10);
			if ( !_.isNaN(currentCart) ){
				currentCart = currentCart + 1;
			}else{
				currentCart = 1;
			}
			$miniCart.data('total', currentCart);
			if ( currentCart > 1 ){
				currentCart = '(' + currentCart + ' items)';
			}else{
				currentCart = '(' + currentCart + ' item)';
			}
			$miniCart.find('span').html(currentCart);
		});
	}

	function teamPreviewSticky() {
		let $carousel = $('.wil-team__carousel'),
			$list = $('.wil-team-list');

		if( $carousel.length ) {
			$carousel.owlCarousel({
				items:1,
				singleItem: true,
				loop:false,
				nav: false,
				dots: false,
				rtl: rtl,
				lazyLoad: true,
				URLhashListener:true,
				autoplayHoverPause:true,
				startPosition: 'URLHash'
			});
		}

		if( $list.length ) {
			$list.perfectScrollbar({
				wheelPropagation: true
			});
		}
	}

	function runLazyLoad() {
		$('.lazy:not(.wiloke-grid-rotator-item)').Lazy({
			placeholder:'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==',
		});

		let $gridRotator = $('.wil-gridratio');
		$('.lazy.wiloke-grid-rotator-item').Lazy({
			placeholder:'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==',
			beforeLoad: function () {
				$gridRotator.closest('.vc_row-has-fill').addClass('wiloke-row-handling');
			},
			onFinishedAll: function () {
				new WilokeGridRotator($gridRotator);
				$gridRotator.closest('.vc_row-has-fill').removeClass('wiloke-row-handling');
			}
		});
	}

	function Accordion() {
		$('.wil_accordion').on('click', '.wil_accordion__header a', function(event) {
			event.preventDefault();
			let $this = $(this),
				$accordion = $this.closest('.wil_accordion'),
				$header = $this.closest('.wil_accordion__header'),
				href = $this.attr('href'),
				$panel = null,
				$headerActive = $('.wil_accordion__header.active', $accordion),
				$panelActive = $('.wil_accordion__content.active', $accordion);
			$panel = $('#'+href.split('#')[1]);
			if( $header.hasClass('active') ) {
				$panel.slideToggle(300).toggleClass('active');
				$header.toggleClass('active');
			} else {
				$panelActive.slideUp(300).removeClass('active');
				$headerActive.removeClass('active');
				$panel.slideDown(300).addClass('active');
				$header.addClass('active');
			}
		});
	}

	function owlCarousel() {
		let $events = $('.events-carousel');
		if( $events.length ) {
			$events.each(function () {
				if ( $(this).find('.event-item').length ){
					$(this).owlCarousel({
						items: 1,
						lazyLoad: true,
						nav: true,
						autoHeight: true,
						loop: true,
						rtl: rtl,
						navText: ['<i class="arrow_left"></i>', '<i class="arrow_right"></i>']
					})
				}else{
					$(this).closest('.listgo-event-container').remove();
				}
			});
		}

		let $blogCarousel = $('.blog-carousel');

		if( $blogCarousel.length ) {

			$blogCarousel.each(function () {
				let self = $(this);
				self.owlCarousel({
					nav: true,
					lazyLoad: true,
					margin: 30,
					loop: true,
					rtl: rtl,
					navText: ['<i class="arrow_carrot-left"></i>', '<i class="arrow_carrot-right"></i>'],
					responsive: {
						1200 : {
							items: parseInt(self.data('showposts'), 10)
						},
						768 : {
							items: 2
						},
						0 : {
							items: 1
						}
					}
				})
			})
		}

		$('.testimonials').each(function(index, el) {
			let self = $(this),
				$owl1 = self.find('.testimonial__avatars'),
				$owl2 = self.find('.testimonials-carousel');

			if( $owl1.length ) {
				$owl1.owlCarousel({
					startPosition: 0,
					center: true,
					items: 3,
					loop: true,
					nav: false,
					autoplay: true,
					margin: 10,
					mouseDrag: false,
					touchDrag: false,
					pullDrag: false,
					freeDrag: false,
					rtl: rtl,
					smartSpeed: 1000,
				});
			}

			if( $owl2.length ) {
				$owl2.owlCarousel({
					startPosition: 0,
					items: 1,
					nav: true,
					loop: true,
					autoplay: true,
					rtl: rtl,
					smartSpeed: 1000,
					animateIn: 'fadeIn',
					animateOut: 'fadeOut',
					navText: ['<i class="arrow_carrot-left"></i>', '<i class="arrow_carrot-right"></i>']
				});
			}

			$owl2.on('changed.owl.carousel', function(event) {
				$owl1.trigger('to.owl.carousel', [event.item.index, 300]);
			});

		});
	}

	function toggleDropDown() {
		$('.header__user').on('click', '.user__avatar', function(event) {
			event.preventDefault();
			$(this).closest('.header__user').toggleClass('active');
		});

		$('.header__notifications').on('click', '.notifications__icon', function(event) {
			$(this).closest('.header__notifications').toggleClass('active');
		});

		$('.account-nav__toggle').on('click', function(event) {
			event.preventDefault();

			$('.account-nav').toggleClass('active');
		});

		$('.listing-filter__button').on('click', function(event) {
			event.preventDefault();

			$('.from-wide-listing').slideToggle();
		});

		$('.label--dropdown').on('click', function(event) {
			event.preventDefault();
			$(this).toggleClass('active');
		});

	}

	function formEvent() {
		$('.input-slider').each(function() {
			let self = $(this);

			self.slider({
				value: self.data('currentRadius'),
				min: self.data('minRadius'),
				max: self.data('maxRadius'),
				slide: function( event, ui ) {
					self.find('input').val(ui.value);
					self.find('input').trigger('change');
					self.attr('data-current-radius', ui.value);
					$(event.target).find('span.ui-slider-handle').attr('data-value', ui.value);
				},
				create: function( event, ui ) {
					$(event.target).find('span.ui-slider-handle').attr('data-value', self.data('currentRadius'));
				}
			});
		});

		$('.input-datepicker').each(function(index, el) {
			let self = $(this);

			self.datepicker({
				beforeShow: function(input, inst) {
					$('#ui-datepicker-div').addClass('wo_datepicker');
				}
			});
		});

		$('.icon-search-map, .mapsearch__close').on('click', function(event) {
			event.preventDefault();
			$('.listgo-mapsearch, .icon-search-map').toggleClass('active');
		});

		$('.dropdown').on('click', 'span' , function(event) {
			event.preventDefault();
			let $this = $(this),
				target = $this.data('tagert');

			if($this.hasClass('active')){
				return false;
			}

			$this.parent().find('.active').removeClass('active');
			$this.addClass('active');
			$('#leave-now, #depart-at').removeClass('active');
			$(target).addClass('active');
			$this.closest('.label--dropdown').find('.label-dropdown--text').text($this.text());
		});

	}

	// Modal Popup
	function ModaPopup() {
		$('[data-modal]').on('click', function(event) {
			let $this   = $(this),
				id      = $this.data('modal');

			if( typeof id !== 'undefined' && $(id).length ) {
				$(id).addClass('wil-modal--open');
			}
		});
		$('.wil-modal__close').on('click', function(event) {
			$(this).closest('.wil-modal').removeClass('wil-modal--open').trigger('closed');
		});

		$('.wil-modal').on('click', function(event) {
			let self = $(event.target);
			if( !self.closest('.wil-modal__content').length ) {
				self.closest('.wil-modal').removeClass('wil-modal--open');
				self.trigger('closed');
			}
		});
	}

	// Scroll Bar
	function scrollBar() {

		if(!WilokeDetectMobile.Any()) {
			let $modernForm = $('.mapsearch__form');
			if ($modernForm.length) {
				$modernForm.perfectScrollbar();
			}
		}
	}

	// Menu
	function menuResponsive() {
		let $header = $('#header');
		if( $header.length ) {
			let dataBreak = $header.data('breakMobile');
			if( typeof dataBreak === 'undefined') {
				dataBreak = 991;
			}

			$(window).on('resize', function(event) {

				if( $(window).innerWidth() <= dataBreak ) {
					$header.addClass('header-responsive').removeClass('header-desktop');
				} else {
					$header.removeClass('header-responsive').addClass('header-desktop');
					$('body').removeClass('menu-mobile__open');
				}

			}).trigger('resize');
		}

		$('.header__toggle').on('click', function(event) {
			event.preventDefault();
			$('body').toggleClass('menu-mobile__open');
		});

	}

	// Background Video Youtube
	function bgVideoYoutube() {
		let $bgVideo = $('.bg_video');
		if( $bgVideo.length ) {
			$bgVideo.each(function() {
				let container = self.closest('.bg-video');
				$(this).mb_YTPlayer({
					containment: container
				});
			});
		}
	}

	// Single Listing Bar
	function listingBar() {
		let $listingSingleSidebar = $('.listing-single-bar');
		if( $listingSingleSidebar.length ) {
			$listingSingleSidebar.on('ListingBarScroll', function(event) {
				let self = $(this),
					$singleTab = $('.listing-single__tab'),
					offsetFooter = $('#footer').offset().top,
					offset = $singleTab.offset().top,
					h = $singleTab.height(),
					wh = $(window).height(),
					ws = $(window).scrollTop(),
					active = false;

				if (ws > offset && ws <= offsetFooter - wh - 90) {
					active = true;
				} else {
					active = false;
				}

				if(active) {
					self.addClass('active')
				} else {
					self.removeClass('active');
				}

			}).trigger('ListingBarScroll');
		}
	}

	// Document onClick
	function onclickDocument() {
		$(document).on('click', function(event) {
			let self = $(event.target);
			if( !self.closest('.header__notifications').length ) {
				$('.header__notifications').removeClass('active');
			}
			if( !self.closest('.header__user').length ) {
				$('.header__user').removeClass('active');
			}
			if( !self.closest('.header-mobile').length ) {

				if(!self.closest('.header__toggle').length) {
					$('body').removeClass('menu-mobile__open');
				}
			}
		});
	}

	// Scroll Map
	function scrollResultMap() {

		let $listgoMapResult = $('.listgo-map__result');

		if( $listgoMapResult.length) {

			$listgoMapResult.perfectScrollbar();

			$listgoMapResult.on('scrollResultMap', function(event) {

				let windowWidth = $(window).innerWidth();

				if ( windowWidth >= 567 ) {

					let self = $(this),
						$wrap = self.closest('.listgo-map-wrap'),
						$settings = $wrap.find('.listgo-map__settings'),
						$field = $wrap.find('.listgo-map__field'),
						height = $wrap.innerHeight();

					if($settings.length) {
						height = height - parseInt($settings.css('padding-top'));
					}

					if($field.length && $field.css('display') !== 'none') {
						height = height - $field.outerHeight(true);
					}

					self.height(height);

					self.perfectScrollbar('update');
				}

			}).trigger('scrollResultMap');

		}
	}

	function scrollTopJs() {
		let $wilScrollTop = $('.wil-scroll-top');
		$wilScrollTop.on('click', function(event) {
			event.preventDefault();
			jQuery('html, body').stop().animate({
				scrollTop: 0
			}, 300);
		});

		let isStopScroll = false;

		$wilScrollTop.on('scrollTop', function() {
			if ( isStopScroll !== false ){
				clearTimeout(isStopScroll);
			}

			isStopScroll = setTimeout(function(){
				let win_top = $(window).scrollTop(),
					screenHeight = $(window).height();

				if(screenHeight < win_top) {
					$(this).addClass('active');
				} else {
					$(this).removeClass('active');
				}
			}, 2000);
			
		});
	}

	function mapExpand() {
		let $body = $('body');
		$('.listgo-map-wrap-expand').on('click', function(event) {
			let self = $(this),
				status = self.data('status'),
				$wrap = self.closest('.listgo-map-wrap'),
				id = $wrap.data('id'),
				$container = self.closest('.listgo-map-container'),
				$popup = $('<div class="wil-popup-map"></div>');

			if(status) {
				self.data('status', false);
				$('#' + id).css('height', '').html($wrap);
				$('body').find('.wil-popup-map').remove();
			} else {
				self.data('status', true);

				$container.height($wrap.innerHeight());

				$body.find('.wil-popup-map').remove();

				$popup.css({
					'position': 'fixed',
					'width': '100%',
					'height': '100%',
					'z-index': 99999,
					'top': 0,
					'left': 0,
					'background-color': '#fff'
				});

				$popup.html($wrap);

				$body.append($popup);
			}
		});
	}

	function ToggleTags() {
		$('.item--tags').on('click', '.label', function(event) {
			event.preventDefault();
			let self = $(this),
				parent = self.parent('.item--tags');

			parent.find('.item--tags-toggle').slideToggle();
		});

		if ( WilokeDetectMobile.Any() ){
			$('.item--tags').trigger('click');
		}
	}

	function ToggleFilterLising() {
		let $body = $('body'),
			$pageWrapper = $('#wrap-page');
		$('.header-page__breadcrumb-filter, .from-wide-listing__header-close, .from-wide-listing__footer').on('click', function(event) {
			$pageWrapper.toggleClass('form-search-active');
			if ( WilokeDetectMobile.IOS() ){
				if ( $pageWrapper.hasClass('form-search-active') ){
					$body.addClass('body--disable-scrolling');
				}else{
					$body.removeClass('body--disable-scrolling');
				}
			}
		});

		$(document).on('click', function(event) {
			let self = $(event.target);

			if( !self.closest('.from-wide-listing').length && !self.closest('.header-page__breadcrumb-filter').length ) {
				$pageWrapper.removeClass('form-search-active');
			}
		});

		let filter = $('.header-page__breadcrumb-filter');

		if( filter.length && ( $body.hasClass('page-template-listing') || $body.hasClass('page-template-half-map') ) ) {

			let top = 15,
				offsetTop = 0;

			filter.on('filterScoll', function(event) {
				let _self = $(this),
					_top = top,
					_offsetTop = 0,
					scrollTop = $(window).scrollTop(),
					winWidth = $(window).innerWidth();

				if ( !_self.hasClass('activeScroll') ) {
					offsetTop = _self.offset().top;
				}

				let $adminBar = $('#wpadminbar');
				if ( $adminBar.length ) {
					_offsetTop = offsetTop - _top - $adminBar.height();
					_top += $adminBar.height();
				} else {
					_offsetTop = offsetTop - _top;
				}

				if( _offsetTop < scrollTop && winWidth < 768 ) {
					$('.header-page').css('z-index', 100);
					_self.addClass('activeScroll').css('top', _top);
				} else {
					$('.header-page').css('z-index', '');
					_self.removeClass('activeScroll').css('top', '');
				}
			}).trigger('filterScoll');

			$(window).on('scroll', function(event) {
				filter.trigger('filterScoll');
			});
		}
	}

	function pinListingToTopAuthorPage() {
		let $app = $('.wiloke-pin-to-top');
		let xhr = null;
		let oTranslation = {};

		$app.on('click', (event=>{
			event.preventDefault();
			oTranslation = !_.isEmpty(oTranslation) ? oTranslation : WILOKE_LISTGO_TRANSLATION;

			let $target = $(event.currentTarget);

			if ( xhr !== null && xhr.status !== 200 ){
				xhr.abort();
			}
			$target.append('<span class="second-loading"></span>');

			$.ajax({
				type: 'POST',
				url: WILOKE_GLOBAL.ajaxurl,
				data: { action: 'wiloke_listgo_pin_to_top', listingID: $target.data('postid'), status: $target.attr('data-status'), security: WILOKE_GLOBAL.wiloke_nonce },
				success: (response=>{
					if ( response.success ){
						$target.attr('data-status', response.data.new_status);
						if ( response.data.new_status !== 'pinned' ){
							$('.wiloke-listgo-pinned-'+$target.data('postid')).remove();
						}
						$target.html('<i class="fa fa-thumb-tack"></i> ' + oTranslation[response.data.new_status]);
						$target.attr('data-tooltip', response.data.title);
					}else{
						alert(response.data.msg);
					}
					$target.find('.second-loading').remove();
				})
			})
		}))
	}

	function onHoverMapAddListing() {
		// Hover Map
		$('.wiloke-latlongwrapper #wiloke-map').hover(function() {
			var self = $(this),
				$wrap = self.closest('.wiloke-latlongwrapper'),
				$location = $wrap.find('#wiloke-location'),
				$latlong = $wrap.find('#wiloke-latlong');

			$location.css({
				'box-shadow': '0 0 5px rgba(73,198,48, 0.9)',
				'border-color': 'rgba(73,198,48, 1)',
				'transition': 'all 0.3s ease'
			});

			$latlong.css({
				'box-shadow': '0 0 5px rgba(73,198,48, 0.9)',
				'border-color': 'rgba(73,198,48, 1)',
				'transition': 'all 0.3s ease'
			});

		}, function() {
			var self = $(this),
				$wrap = self.closest('.wiloke-latlongwrapper'),
				$location = $wrap.find('#wiloke-location'),
				$latlong = $wrap.find('#wiloke-latlong');

			$location.css({
				'box-shadow': '',
				'border-color': '',
				'transition': ''
			});

			$latlong.css({
				'box-shadow': '',
				'border-color': '',
				'transition': ''
			});
		});

		// Focus Input
		$('.wiloke-latlongwrapper #wiloke-location, .wiloke-latlongwrapper #wiloke-latlong').on('focus', function(event) {
			var self = $(this),
				$wrap = self.closest('.wiloke-latlongwrapper'),
				$map = $wrap.find('#wiloke-map');

			$map.css({
				'box-shadow': '0 0 10px rgba(0,0,0, 0.4)',
				'transition': 'all 0.3s ease'
			});
		});

		// Focus Input
		$('.wiloke-latlongwrapper #wiloke-location, .wiloke-latlongwrapper #wiloke-latlong').on('blur', function(event) {
			var self = $(this),
				$wrap = self.closest('.wiloke-latlongwrapper'),
				$map = $wrap.find('#wiloke-map');

			$map.css({
				'box-shadow': '',
				'transition': ''
			});
		});
	}

	addlistingEvents();
	function addlistingEvents() {

		let style = $('.add-listing__style');
		let $preview = $('.add-listing-group-preview'),
			$previewMap = $('.add-listing-group-preview-map'),
			$selected = style.find('.add-listing__style-selected'),
			previewTitle = $selected.data('preview-title'),
			previewCategory = $selected.data('preview-category'),
			inputStyle = $('#listing_style');

		$preview.find('img').attr('src', previewTitle);
		
		if (style.length) {
			style.owlCarousel({
				items: 3,
				margin: 20,
				nav: true,
				rtl: rtl,
				lazyLoad: true,
				mouseDrag: false,
				navText: ['<i class="arrow_carrot-left"></i>', '<i class="arrow_carrot-right"></i>'],
				responsive: {
					992: {
						items: 3
					},
					0: {
						items: 2
					}
				}
			});

			$('.add-listing__style-item').on('click', function(event) {

				let self = $(this),
					template = self.data('template');

				if (self.hasClass('add-listing__style-selected') || self.hasClass('add-listing__style-disable')) {
					return;
				}
				inputStyle.val(template);
				style.find('.add-listing__style-selected').removeClass('add-listing__style-selected');
				self.addClass('add-listing__style-selected');
				$selected = self;
				previewTitle = self.data('preview-title'),
					previewCategory = self.data('preview-category');

				if ( $('.add-listing-input-title').find('.input-text').hasClass('active') ) {
					$preview.find('img').attr('src', previewTitle);
				}

				if ( $('.add-listing-input-categories').find('.input-select2').hasClass('active') ) {
					$preview.find('img').attr('src', previewCategory);
				}

			});
		}

		// Focus Title
		$('.add-listing-input-title').on('focus', 'input', function(event) {
			$(this).closest('.input-text').addClass('active');
			$previewMap.css({
				'opacity': 0,
				'visibility': 'hidden'
			});

			$preview.css({
				'opacity': 1,
				'visibility': 'visible',
			}).find('img').attr('src', previewTitle);

			$('.add-listing-input-location').find('.input-text').removeClass('active');
			$('.add-listing-input-categories').find('.input-select2').removeClass('active');

		});

		// Focus Category
		$('.add-listing-input-categories').on('focus','select, input', function(event) {

			var self = $(this);
			self.closest('.input-select2').addClass('active');

			$previewMap.css({
				'opacity': 0,
				'visibility': 'hidden'
			});

			$preview.css({
				'opacity': 1,
				'visibility': 'visible'
			}).find('img').attr('src', previewCategory);;

			$('.add-listing-input-title, .add-listing-input-location').find('.input-text').removeClass('active');

		});


		$('.add-listing-input-location').on('focus', 'input', function(event) {
			var self = $(this);

			self.closest('.input-text').addClass('active');

			$('.add-listing-input-categories').find('select').parent().removeClass('active');
			$('.add-listing-input-title').find('input').parent().removeClass('active');

			$preview.css({
				'opacity': 0,
				'visibility': 'hidden'
			});

			$previewMap.css({
				'opacity': 1,
				'visibility': 'visible'
			});

			$('.add-listing-input-categories, .add-listing-input-title').find('.input-text, .input-select2').removeClass('active');
		});

		
		// Businees Hour
		$('#toggle-business-hours').on('change', function(event) {
			let self = $(this),
				$table = $('#table-businees-hour');
			if ( self.is(":checked") ) {
				$table.addClass('active');
			} else {
				$table.removeClass('active');
			}
		}).trigger('change');

		// Account
		$('#createaccount').on('change', function(event) {
			let self = $(this),
				$account = $('#wiloke-signup-signin-wrapper');

			if ( self.is(":checked") ) {
				$account.addClass('active');
			} else {
				$account.removeClass('active');
			}
		})
	}

	// Hover Rating
	hoverRating();
	function hoverRating() {
		$('.comment__rate a').mouseenter(function() {
			var self = $(this),
				index = self.index(),
				text = self.data('title'),
				parent = self.closest('.comment__rate'),
				placeholder = parent.find('.comment__rate-placeholder');
			placeholder.text(text);
			self.addClass('hover-active');
			$('.comment__rate a:lt('+index+')').addClass('hover-active');
		});


		$('.comment__rate').mouseout(function(event) {
			var self = $(this),
				text = self.data('title'),
				placeholder = self.find('.comment__rate-placeholder'),
				text = placeholder.attr('data-placeholder');

			placeholder.text(text);

			self.find('a').removeClass('hover-active');
		});

		$('.comment__rate a').on('click', function(event) {
			var self = $(this),
				text = self.data('title'),
				parent = self.closest('.comment__rate'),
				placeholder = parent.find('.comment__rate-placeholder');
			placeholder.attr('data-placeholder', text);
		});
	}


	$(window).on('scroll', function(event) {
		let $listgoSingleBar = $('.listing-single-bar');
		if( $listgoSingleBar.length ) {
			$listgoSingleBar.trigger('ListingBarScroll');
		}

		let $listgoMapResult = $('.listgo-map__result');
		if( $listgoMapResult.length ) {
			$listgoMapResult.trigger('scrollResultMap');
		}

		let $willScrollTop = $('.wil-scroll-top');
		if( $willScrollTop.length ) {
			$willScrollTop.trigger('scrollTop');
		}

		let filter = $('.header-page__breadcrumb-filter');
		if( filter.length ) {
			filter.trigger('filterScoll');
		}

	});

	function listingNearBy(latLng){
		let $appendTo = $('#listing-single_listing_near_by');
		if ( !$appendTo.length ){
			return false;
		}
		
		jQuery.ajax({
			type: 'POST',
			data: {
				action: 'wiloke_get_listing_near_by_widget',
				latLng: latLng,
				postID: WILOKE_GLOBAL.postID
			},
			url: WILOKE_GLOBAL.ajaxurl,
			success: function(response){
				if ( response.success ){
					$appendTo.html(response.data);
				}else{
					$appendTo.closest('.listing-single__related').remove();
				}
			}
		})
	}

	function triggerEventTab(){
		let currentHref = window.location.href;

		if ( currentHref.search('#tab-event') !== -1 ){
			let $goTo = '',
				$eventTab = $('a[href="#tab-event"]:first');
			if ( currentHref.search('--goto-') !== -1 ){
				let parseGoto = currentHref.split('--goto-');
				$goTo = $('#'+ parseGoto[1]);
			}

			$eventTab.trigger('click');
			if ( $goTo !== '' && $goTo.length ){
				setTimeout(function(){
					$('body, html').animate({
						scrollTop: $goTo.offset().top - 100
					}, 400);
				}, 500);
			}
		}
	}

	$(window).on('resize', function() {
		let widow_w = $(window).innerWidth(),
			$mapResult = $('.listgo-map__result'),
			$fromListing = $('.from-wide-listing');

		if(widow_w > 480) {
			if( $fromListing ) {
				$fromListing.css('display', '');
			}
		}

		if( $mapResult.length ) {
			$mapResult.trigger('scrollResultMap');
		}

		let $listgoRegistrationForm = $('.listgo-register');
		if ( $listgoRegistrationForm.length ) {
			$listgoRegistrationForm.trigger('onRegister');
		}

	});

	$(window).on('load', function() {
		teamPreviewSticky();
		scrollTopJs();
	});

	new WilokeReport();
	runLazyLoad();
	
	Accordion();
	owlCarousel();
	toggleDropDown();
	formEvent();
	ModaPopup();
	scrollBar();
	menuResponsive();
	bgVideoYoutube();
	listingBar();
	onclickDocument();
	scrollResultMap();
	mapExpand();
	ToggleTags();
	ToggleFilterLising();
	// onHoverMapAddListing();

	if ( $('#listgo-map').length ){
		$(window).on('Wiloke.Resizemap', (()=>{
			if ( $('#listgo-map').data('initialized') ){
				return false;
			}
			new WilokeMap('listgo-map');
		}));

		let reCheckMap = setTimeout(function(){
			if ( $('#listgo-map').data('initialized') ){
				clearTimeout(reCheckMap);
				return false;
			}
			new WilokeMap('listgo-map');
			clearTimeout(reCheckMap);
		}, 4000);
	}

	new WilokeMasonry();
	new WilokeFavorite();
	new WilokeListLayout();
	new WilokeSearchSuggestion();
	new updateProfile();
	new Tabs();
	new WilokeListingManagement();
	new WilokeMyFavorite();
	new WilokeBillingHistory();
	new PopupSignUpSignIn();
	general();
	wooMiniCart();
	$('.tab__nav .has-single-map').one('click', (()=> {
		setTimeout(function () {
			new WilokeSingleMap($('.listing-single__map'));
		}, 300);
	}));

	let $widgetMap = $('.widget-map');
	if ( $widgetMap.length ){
		new WilokeSingleMap($widgetMap);
	}

	let $mapShortcode = $('.wiloke-listgo-map-shortcode');
	if ( $mapShortcode.length ){
		new WilokeSingleMap($mapShortcode);
	}

	new WilokeReview();
	new WilokeGoogleAutocomplete();
	new WilokeSubscribe();
	new wilokeNotification();
	pinListingToTopAuthorPage();

	$('.wiloke-js-upload').each(function(){
		new wilokeMediaUpload($(this));
	});

	new WilokeEvent();
	
	let instAskForCurrentPosition = null;
	if ( WILOKE_GLOBAL.toggleAskForPosition !== 'disable' ){
		instAskForCurrentPosition = new WilokeAskForCurrentPosition();
	}
	$('#listgo-current-location').on('click', function(){
		if ( instAskForCurrentPosition === null ){
			instAskForCurrentPosition = new WilokeAskForCurrentPosition(true);
			instAskForCurrentPosition.isFocusSetLocationField = false;
		}else{
			instAskForCurrentPosition.isFocusSetLocationField = true;
			instAskForCurrentPosition.findYourLocationInSearchForm();
			instAskForCurrentPosition.isFocusSetLocationField = false;
		}
	});

	$('body').on('wiloke_listgo_user_latlng', function(event, oData){
		listingNearBy(oData);
	})

	new WilokeStatistics();
	

	// Form Register
	let $registrationForm = $('.listgo-register');
	if ( $registrationForm.length ) {
		$registrationForm.on('onRegister', function(event) {
			let self = $(this),
				parent = self.closest('.vc_row');
			if ( parent.length ){
				let offsetThis = self.offset().top,
					offsetParent = parent.offset().top;
				if(offsetThis > offsetParent) {
					self.addClass('listgo-register--remove-line');
				} else {
					self.removeClass('listgo-register--remove-line');
				}
			}
		}).trigger('onRegister');
	}

	listingv7();
	function listingv7() {
		let $listingV7 = $('.listing-single__wrap-header7 .listing-single__title');
		if ($listingV7.length) {
			$listingV7.textfill({
				maxFontPixels: 30,
				innerTag: 'h1',
				explicitHeight: 60
			});
		}

		let singlelisting7 = $('.listing-single-wrap7 .listing-single');

		if (singlelisting7.length) {

			singlelisting7.on('onSinglelisting7', function() {
				let self = $(this),
					winWidth = $(window).innerWidth(),
					singleHeader = $('.listing-single__wrap-header7'),
					height = singleHeader.innerHeight(),
					top = singleHeader.css('margin-top'),
					bottom = singleHeader.css('margin-bottom');

				let _top = (height + parseInt(top));

				if (winWidth >=992) {
					self.css('margin-top', -_top);
				} else {
					self.css('margin-top', '');
				}

			}).trigger('onSinglelisting7');

			$(window).on('resize', function(event) {
				singlelisting7.trigger('onSinglelisting7');
			});
		}

	}

	scrollDown();
	function scrollDown() {
		let $headerPage = $('.header-page__scrolldown');
		if ($headerPage.length > 0) {

			$(window).on('scroll', function() {

				let st = $(window).scrollTop(),
					wh = $(window).outerHeight(),
					o = $headerPage.offset(),
					h = $headerPage.outerHeight(true),
					ratio = 0.5,
					opacity = (o.top*ratio-st)/(o.top*ratio+h);

				if (st > o.top*ratio) {
					opacity = 0;
				}

				if (st < 20) {
					opacity = 1;
				}

				$headerPage.css('opacity', opacity);

			});
		}

		$headerPage.on('click',  function(event) {
			let self = $(this),
				offset = self.offset().top + self.height();

			$('html, body').stop().animate({
				scrollTop: offset
			}, 300);
		});
	}

	$('.listgo-map-wrap .listgo-map__apply').on('click', function(event) {
		$(this).closest('.listgo-map-wrap').removeClass('list-map-wrap--setting-active');
	});

	$('.listing--grid3, .listing--grid4').closest('.page-template-listing').css('background-color', '#f6f7f8');

	// New Dashbroad
	$('.wil-dashbroad__bar-menu-toggle').on('click', function(event) {
		event.preventDefault();
		$(this).next('ul').toggleClass('active');
	});

	$('.wil-dashbroad__bar-menu').on('click', '.has-children > a', function(event) {
		event.preventDefault();
		$(this).parent('li').toggleClass('active');
	});

	function listGoFlexibleSingleListingSidebar(){
		let isiPad = navigator.userAgent.match(/iPad/i) !== null;

		if ( isiPad ){
			let $smartDeviceSidebar = $('.listing-single__sidebar'),
				$sidebarPlaceholder = $('#listgo-sidebar-placeholder'),
				$listingSidebar = $('.listgo-single-listing-sidebar-wrapper');

			if ( $(window).width() > 768 ){
				if (  $smartDeviceSidebar.children().length ){
					$listingSidebar.appendTo($sidebarPlaceholder);
				}
			}else{
				if (  !$smartDeviceSidebar.children().length ){
					$listingSidebar.appendTo($smartDeviceSidebar);
				}
			}
		}
	}

	// Editor
	$(window).on('load',  function(event) {
		let $listingContentIf = $('.page-template-addlisting #listing_content_ifr');
		if ($listingContentIf.length) {

			$listingContentIf.contents().find('body').addClass('wil-mce-editor');

			$listingContentIf.contents().on('mouseenter', '[data-wpview-type="gallery"]', function(event) {

				$(this).addClass('addlisting-placeholder');

				$('.page-template-addlisting .ui-helper-hidden-accessible').next('.mce-inline-toolbar-grp').addClass('wil-hidden');

				if ( !$(this).find('.addlisting-placeholder__actions').length ) {
					$(this).append('<div class="addlisting-placeholder__actions"><span class="addlisting-placeholder__action-edit">Edit</span><span class="addlisting-placeholder__action-remove">Remove</span></div>');
				}
			});

			$('#listgo-googlefont-css').clone().appendTo($listingContentIf.contents().find('head'));

			$listingContentIf.contents().find('head').append('\
	            <style type="text/css">\
		         	body, .mce-content-body p, .mce-content-body div:not(.addlisting-placeholder__title) {\
						font: ' + $("body").css("font") + ';\
						line-height: ' + $("body").css("line-height") + ';\
						color: ' + $("body").css("color") + ';\
					}\
					.mce-content-body h1,.mce-content-body h2,.mce-content-body h3,.mce-content-body h4,.mce-content-body h5,.mce-content-body h6 {\
						font: ' + $(".page-template-addlisting .header-page__title").css("font") + ';\
						font-weight: 600;\
						color: #212122;\
					}\
					.mce-content-body h1 {\
						font-size: 40px;\
					}\
					.mce-content-body h2 {\
						font-size: 34px;\
					}\
					.mce-content-body h3 {\
						font-size: 28px;\
					}\
					.mce-content-body h4 {\
						font-size: 22px;\
					}\
					.mce-content-body h5 {\
						font-size: 18px;\
					}\
					.mce-content-body h6 {\
						font-size: 14px;\
					}\
					.mce-content-body h1, .mce-content-body h2, .mce-content-body h3 {\
						margin-top: 20px;\
						margin-bottom: 10px;\
					}\
					.mce-content-body h4, .mce-content-body h5, .mce-content-body h6 {\
						margin-top: 10px;\
						margin-bottom: 10px;\
					}\
					.mce-content-body > p {\
						margin-top: 0;\
						margin-bottom: 10px\
					}\
				</style>\
	        ');
		}

		$('.wiloke-listing-layout').each(function() {
			$(this).addClass('loaded');
		})
	});

	$.fn.WilokeNiceGridTitle = function(){
		$(this).each(function() {
			let listing = $(this),
				titleW = null,
				title = $('.listing__title', listing);
			titleW = listing.hasClass('listing--grid3') || listing.hasClass('listing--grid4') ? $('a', title).outerWidth(true)+100 : $('a', title).outerWidth(true)+30;
			if (titleW >= listing.width()) {
				let listingClassFix = null;
				if (title.children().length === 1) {
					listingClassFix = 'listing-fix-title'
				} else if (title.children().length === 2) {
					listingClassFix = 'listing-fix-title2'
				} else if (title.children().length === 3) {
					listingClassFix = 'listing-fix-title3'
				}
				listing.addClass(listingClassFix);
			}
		});
	}

	// Responsive embed
	responsiveEmbed();
	function responsiveEmbed() {
		let selectors = [
			'iframe[src*="player.vimeo.com"]',
			'iframe[src*="youtube.com"]',
			'iframe[src*="youtube-nocookie.com"]',
			'iframe[src*="kickstarter.com"][src*="video.html"]',
			'object',
			'embed'
		];
		let $allVideos = $('body').find(selectors.join(','));
		$allVideos.each(function() {
			let vid = $(this),
				vidWidth = vid.outerWidth(),
				vidHeight = vid.outerHeight(),
				ratio = (vidHeight / vidWidth) * 100;
			$allVideos
				.addClass('embed-responsive-item');
			if (ratio === 75) {
				$allVideos
					.css('display', 'block')
					.wrap('<div class="embed-responsive embed-responsive-4by3"></div>');
			} else {
				$allVideos
					.css('display', 'block')
					.wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
			}
		});
	}
	triggerEventTab();
	$('.listing--grid3, .listing--grid4').closest('.page-template-listing').css('background-color', '#f4f4f4');

	if ( $('.listing[class*="listing--grid"]').length ){
		$('.listing[class*="listing--grid"]').WilokeNiceGridTitle();
	}

	listGoFlexibleSingleListingSidebar();

	let beforeWidth = 0;
	$(window).on('resize', function(){
		if ( beforeWidth !== $(window).width() ){
			listGoFlexibleSingleListingSidebar();
		}
	});

	function customNavColor(){
		let navColor = $('#header').data('navcolor');
		if ( typeof navColor !== 'undefined' ){
			$('.header__content .wiloke-menu:not(.wiloke-menu-responsive) .wiloke-menu-list > .wiloke-menu-item > a,.header__content .wiloke-menu:not(.wiloke-menu-responsive) .wiloke-menu-list .wiloke-menu-sub > .wiloke-menu-item > a,.header__add-listing a, .notifications__icon').css({
				color:navColor
			});
			$('.user__icon g').css({
				fill:navColor
			});
		}
	}

	customNavColor();
	fixStupidVisualComposerCaculation();

	$('.header-page-form').closest('.vc_row').css({
		'padding-top': 0,
		'padding-bottom': 0
	});

	$(window).on('load', function() {
		let $listingLayout = $('.nav-filter').next('.wiloke-listgo-listlayout').children('.listgo-wrapper-grid-items');
		if ($listingLayout.length) {
			$listingLayout.imagesLoaded(function() {
				$listingLayout.isotope({
			        layoutMode: 'fitRows',
			        itemSelector: '[class^="col-"]',
			        masonry: {
			            columnWidth: '[class^="col-"]'
			        }
			    });
		    });
	    }

	    // Remove .wiloke-menu-sub-mega if emptied
		var isSubMega = $('.wiloke-menu-sub.wiloke-menu-sub-mega');
		isSubMega.each(function() {
			if( !$.trim( $(this).html() ).length ) {
				$(this).remove();
			}
		});
    });
})(jQuery);
