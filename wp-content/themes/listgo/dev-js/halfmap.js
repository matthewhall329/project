;(function ($) {
	'use strict';
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

	class WilokeHalfMap{
		constructor(app){
			this.$app = $('#'+app);
			this.mapInit();
		}

		mapIcon(oItem){
			let iconUrl = decodeURIComponent(WILOKE_GLOBAL.homeURI) + 'img/icon-marker.png';
			if( oItem.listing_cat_settings && !_.isUndefined(oItem.listing_cat_settings.map_marker_image) && oItem.listing_cat_settings.map_marker_image !== '' ){
				iconUrl = oItem.listing_cat_settings.map_marker_image;
			}
			return L.icon({
				iconUrl: iconUrl
			});
		}

		panTo(coordinate, isNotShowPopup){
			let coordinateToStr = null,
				aCoordinate = coordinate;
			if (!_.isArray(coordinate)){
				aCoordinate = coordinate.split(',');
				aCoordinate = _.map(aCoordinate, (value=>{
					return parseFloat(value);
				}));

				this.instMap.panTo(aCoordinate);
			}else{
				this.instMap.panTo(coordinate);
			}

			if ( !_.isUndefined(this.aLayers[aCoordinate.join('-')]) && typeof isNotShowPopup === 'undefined' ){
				this.aLayers[aCoordinate.join('-')].fire('click');
			}
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

		setPopup(layer, aCoodirnate, oItem){
			this.aLayers[aCoodirnate.join('-')] = layer;
			layer.addEventListener('click', ((event)=>{
				this.createPopup(oItem, aCoodirnate);
			}));
		}

		renderPopupContent(oAddress){
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

		getCenter(){
			let aFirst = _.first(this.aListings);
			return aFirst.listing_settings.map.latlong;
		}

		setPopup(layer, aCoodirnate, oItem){
			this.aLayers[aCoodirnate.join('-')] = layer;
			layer.addEventListener('click', ((event)=>{
				this.createPopup(oItem, aCoodirnate);
			}));
		}

		createPopup(oItem, aCoodirnate){
			let featuredImage = oItem.featured_image ? oItem.featured_image.main.src : '',
				address = this.renderPopupContent(oItem.listing_settings);

			let popupTemplate = _.template(`<div class="listing listing--grid">
					<div class="listing__media">
						<a href="<%- oItem.link %>" style="background-image: url(<%= featuredImage %>)"></a>
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
								<% for (var i = 1; i <= 5; i++){ 
									var className = '';
									if (oItem.average_rating < i ){
										className = i == Math.floor(oItem.average_rating) ? 'fa fa-star-half-o' : 'fa fa-star-o';
									}else{
										className = 'fa fa-star';
									}  %>
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
		 * Add Listings to Map
		 */
		addLayers(oItems){

			_.forEach(oItems, (oItem=>{
				this.aPostIDs.push(oItem.ID);
				if ( oItem.listing_settings && !_.isUndefined(oItem.listing_settings.map) ){
					if ( typeof oItem.listing_settings.map === 'undefined' || oItem.listing_settings.map.latlong === ',' || _.isNaN(oItem.listing_settings.map.latlong) ){
						return false;
					}

					let aCoordinate = this.parseCoordinate(oItem.listing_settings.map.latlong);

					// Ensure that we have the both latitude and longitude
					if ( aCoordinate.length === 2 ){
						let layer = L.marker(aCoordinate, {
							icon: this.mapIcon(oItem)
						});

						this.aIDAndLatLng[oItem.ID] = aCoordinate;
						this.oClusters.addLayer(layer);
						this.instMap.addLayer(this.oClusters);
						this.setPopup(layer, aCoordinate, oItem);

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

			this.$app.trigger('loadmore_map');
		}

		getListOfListingAtTheFirstTime(){
			let self = this;
			this.$app.find('.wiloke-listgo-listing-item').each(function () {
				self.aListings.push($(this).data('info'));
			});
			this.$app.trigger('initMap');
		}

		setUp(){
			this.accessToken = WILOKE_GLOBAL.maptoken;
			this.instHelpers = instHelpers;

			this.aTemporaryCachingResults = [];
			this.cachingMylocation = null;
			this.instMyLocation = null;
			this.radiusInKM = 5;
			this.circle = false;
			this.$toggleSidebar = this.$app.find('.searchbox-hamburger');

			this.$wilokeListingLayout = this.$app.find('.wiloke-listing-layout');
			this.$searchForm = this.$app.find('#listgo-searchform');
			this.$location = this.$app.find('#s_listing_location');
			this.$submitSearchKeyWord = this.$app.find('#listgo-submit-searchkeyword');
			this.$search = this.$app.find('#s_search');
			this.$openNow = this.$app.find('#s_opennow');
			this.$highestRated = this.$app.find('#s_highestrated');
			this.$category = this.$searchForm.find('#s_listing_cat');
			this.$location = this.$searchForm.find('#s_listing_location');
			this.oData = this.$app.data('configs');
			this.s_open_now = 0;
			this.s_highest_rated = 0;
			this.s_listing_location = this.$location.val();
			this.s_listing_cat = this.$category.val();
			this.s_listing_cat = this.s_listing_cat !== '' ? parseInt(this.s_listing_cat, 10) : '';
			this.aRawListings = [];
			this.aListings = [];
			this.oMapLayers = [];

			this.$app.on('initMap', (()=>{
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
					this.$app.data('initialized', true);

					this.instMap = L.mapbox.map(this.mapID, null, {
						minZoom: this.oData.minZoom,
						maxZoom: this.oData.maxZoom
					}).setZoom(centerZoom).setView(center);

					L.tileLayer(this.oData.mapTheme).addTo(this.instMap);
					// console.log(this.oData.maxClusterRadius);
					this.oClusters = L.markerClusterGroup({
						animateAddingMarkers: false,
						zoomToBoundsOnClick: false,
						zoomToShowLayer: true,
						showCoverageOnHover: false,
						disableClusteringAtZoom: false,
						spiderfyOnMaxZoom: true,
						chunkedLoading: true,
						maxClusterRadius: this.oData.maxClusterRadius,
						trackResize: true,
						iconCreateFunction: function(cluster) {
							return L.divIcon({ html: '<b>' + cluster.getChildCount() + '</b>' });
						}
					});

					this.addLayers(this.aListings);
				}
			}));

			// Get Listing at the first time
			this.getListOfListingAtTheFirstTime();
			this.updateMap();
			this.panToMarker();
			this.closeMap();
		}

		panToMarker(){
			this.$wilokeListingLayout.on('click', '.listgo_panto_marker', (event=>{
				event.preventDefault();
				this.$wilokeListingLayout.find('.listgo_panto_marker').addClass('active');
				this.$map.addClass('active');
				let listingID = $(event.target).closest('.wiloke-listgo-listing-item').data('postid');
				if ( !_.isUndefined(this.aIDAndLatLng[listingID]) ){
					this.panTo(this.aIDAndLatLng[listingID]);
				}
			}))
		}

		closeMap(){
			this.$map.on('click', '.listgo-half-map__close', (event=>{
				this.$map.removeClass('active');
			}))
		}

		updateMap(){
			this.$wilokeListingLayout.on('ajax_completed', (()=>{
				if ( !this.$wilokeListingLayout.find('.wil-alert').length ){
					let self = this;
					this.$app.on('updateMap', (event=>{
						this.oClusters.clearLayers();
						this.instMap.closePopup();
						this.addLayers(this.aListings);
						let center = this.parseCoordinate(this.getCenter());
						this.panTo(center, true);
					}));
					this.aListings = [];
					this.$wilokeListingLayout.find('.wiloke-listgo-listing-item').each(function () {
						self.aListings.push($(this).data('info'));
					});
					this.$app.trigger('updateMap');
				}
			}));
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
								self.$app.addClass('list-map-wrap--setting-active');
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
			if ( !this.$app.length || this.$app.data('initialized') ){
				return false;
			}
			this.mapID = 'listgo-half-map';
			this.$map = $('#'+this.mapID);
			this.xhr = null;
			this.oSearchCaching = [];
			this.reCheck = false;
			this.currentDestination = [];
			this.aLayers = [];
			this.aIDAndLatLng = [];
			this.ggGeoCode = null;
			this.oTranslation = WILOKE_LISTGO_TRANSLATION;
			this.setUp();
		}
		selected(currentStatus){
			return (this.s_mapdirections === currentStatus);
		}

	}

	$(document).ready(function () {
		new WilokeHalfMap('listgo-half-map-wrap');

		$('.listgo-half-map-wrap').each(function() {
			let halfMap = $(this),
				halfMapOffset = halfMap.offset(),
				halfMapHeight = halfMap.outerHeight(),
				wh = $(window).outerHeight();
			$(window).on('scroll', function() {
				let st = $(window).scrollTop();
				if ((st >= halfMapOffset.top) && (st + wh < halfMapOffset.top + halfMapHeight)) {
					$('.listgo-half-map', halfMap).addClass('fixed');
				} else {
					$('.listgo-half-map', halfMap).removeClass('fixed');
				}

				if (st + wh >= halfMapOffset.top + halfMapHeight) {
					$('.listgo-half-map', halfMap).addClass('abs');
				} else {
					$('.listgo-half-map', halfMap).removeClass('abs');
				}
			});
		});
	})

})(jQuery);