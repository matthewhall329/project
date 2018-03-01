/**
 * He is core player of Portfolio Art
 * @package Portfolio Art
 * @author  Wiloke Team
 * @since   1.0
 * @link    http://wiloke.com
 */

;(function ($) {
    "use strict";

    $.fn.WilokeReCalWidth = function() {
        var $self = $(this);
        $self.on('reCalWidth', function() {
            $(this).css('width', '');
            var width = $(this).width();
            $(this).css('width', width + 'px');
        }).trigger('reCalWidth');
        $(window).on('resize', function() {
            $self.trigger('reCalWidth');
        });
    }


    /**
     * Define Wiloke Portfolio Art APP
     * @since 1.0
     */
    var WilokePA = {
            Views       : {},
            Collection  : {},
            Models      : {},
            listOfModels:{}
        },
        listElements = {
            label       : '<% if ( typeof (heading) != "undefined" ) { %><div class="wpb_element_label"><%= heading %></div><% } %>',
            description :  '<% if ( typeof (description) != "undefined" ) { %><span class="vc_description vc_clearfix"><%= description %></span> <% } %>',
            textfield: function () {
                return this.label + '<input type="text" name="<%= param_name %>" class="js_param textfield <%= param_name %>" />' + this.description;
            },
            textarea: function () {
                return this.label + '<textarea name="<%= param_name %>" class="js_param textareafield <%= param_name %>" /></textarea>' + this.description;
            },
            checkbox: function () {
                return '<div class="wo_wpb_toggle">' + this.label + '<label><input type="checkbox" name="<%= param_name %>" class="js_param checkboxfield <%= param_name %>" /><span></span></label></div>' + this.description;
            },
            radio: function () {
                return this.label + '<input type="radio" name="<%= param_name %>" class="js_param radiofield <%= param_name %>" />' + this.description;
            },
            btn_clone: function () {
                return '<button data-target="<%= param_name %>" class="js_param btn_clone button button-primary">'+this.label+'</button>' ;
            },
            select: function (settings) {
                var options = '', i = 1;
                $.each(settings, function (name, value) {
                    var selected = (i==1) ? 'selected' : '';
                    options +=  '<option value="'+value+'" '+ selected +'>'+name+'</option>';
                    i++;
                });

                return this.label + '<select name="<%= param_name %>" class="js_param selectfield <%= param_name %>">'+options+'</select>' + this.description;
            },
            hidden: function () {
                return this.label + '<input type="hidden" name="<%= param_name %>" class="js_param textfield <%= param_name %>" />' + this.description;
            }
        },
        listTemplate = {
            creative: function (layouts) {
                layouts     = layouts ? layouts.split(',') : '';

                var _html   = '<div class="grid-sizer"></div>';
                $.each(layouts, function (i, layout) {
                    _html += '<div class="emulate-item grid-item '+layout+'" data-layout="'+layout+'" data-order="'+i+'"></div>';
                });

                return _html;
            }
        },
        listofLayouts = ['cube', 'wide', 'high', 'large', 'extra-large'],
        previousSettings,
        listOfEvents = {},
        revisionSettings = [],
        WilokePackery = {},
        wilokePortfolioModePortfolioValue = {};

    /**
     * Render Settings of the hand-left of popup
     * @since 1.0
     */
    WilokePA.Views.renderSettings = Backbone.View.extend({
        el: '.vc_ui-panel-window-inner',
        events: {
            'change .wo_portfolio_layout': 'renderSettings',
            'click .js_device_setting': 'renderSettings',
            'click .grid-item': 'resizeLayout',
            'click .vc_ui-button-action.vc_general[data-vc-ui-element="button-save"]': 'saveValue',
            'click .vc_ui-panel-footer-container .vc_ui-button': 'offClick',
            'click .vc_ui-panel-header-controls .vc_ui-close-button': 'offClick',
            'click .wiloke-add-items': 'addItem',
            'click .wiloke-remove-items': 'removeItems',
            'change .wo_number_of_items': 'listenChangeNumberOfPosts',
            'change .items_per_row': 'listenChangeItemsPerRow',
            'click .js_param.btn_clone': 'cloneLatestDeviceSettings',
            'click .wiloke-target': 'cloning',
            'change .wo_portfolio_layout_settings': 'updateSettings',
            'click .wiloke-clone': 'showCloneTarget',
            'click .vc_ui-tabs-line-trigger' : 'backupValue'
        },
        offClick: function () {
            this.undelegateEvents();
        },
        updateSettings: function (event) {
            event.preventDefault();

            var layoutIndex = this.$el.find('.wo_portfolio_layout.active').parent().index(),
                style       = $(event.currentTarget).val(),
                modelInfo = this.collection.at(layoutIndex - 1),
                getBackup = modelInfo.get('value');

            try{
                // Save old value
                if ( ($(document).data('wiloke-portfolio-prev-activated') == 'creative') && (typeof getBackup != 'undefined') && $(document).data('wiloke-portfolio-allow-backup') ){
                    getBackup = typeof getBackup === 'object' ? JSON.stringify(getBackup) : getBackup;
                    $('#wiloke-print-wiloke-design-layout').find('.wo_portfolio_layout_settings.creative').attr('value', base64_encode(getBackup));
                }

                $(event.currentTarget).attr({'data-settings': style});

                modelInfo.set('value', $.parseJSON(style));

                if ( $(document).data('wiloke-portfolio-is-customize') == 'no' ){
                    $(event.currentTarget).closest('.wiloke-portfolio-layout-wrapper').find('.js_wiloke_pa_elumate_zone').addClass('disable');
                }else{
                    $(event.currentTarget).closest('.wiloke-portfolio-layout-wrapper').find('.js_wiloke_pa_elumate_zone').removeClass('disable');
                }

                $(event.currentTarget).prev().trigger('change');
            }catch (err){
                console.log('Line 128 ' + err)
            }
        },
        cloning: function (event) {
            event.preventDefault();
            this.$el.find('.settings-packery-layout').addClass('progressing');
            var _cloneTo = $(event.currentTarget).data('target'),
                layoutIndex   = this.$el.find('.wo_portfolio_layout.active').parent().index(), // detect current position of layout
                currentDevice = this.$el.find('.js_device_setting.active').data('device'),
                modelInfo     = this.collection.at(layoutIndex - 1),
                oValue        = modelInfo.get('value');

            oValue = $.parseJSON(oValue);

            oValue[_cloneTo] = oValue[currentDevice];

            oValue = JSON.stringify(oValue);

            this.$el.find('.wo_portfolio_layout.active').next().attr('value',oValue);
            this.$el.find('.wo_portfolio_layout.active').next().attr('data-settings',oValue);
            modelInfo.set('value', oValue);
            this.$el.find('.settings-packery-layout').removeClass('progressing');
            $(event.currentTarget).css({color: 'green'});
        },
        showCloneTarget: function (event) {
            event.preventDefault();
            $(event.currentTarget).next().toggleClass('hidden');
        },
        cloneLatestDeviceSettings: function () {
            event.preventDefault();
            this.$el.find('.wo_portfolio_layout.active').attr('value', wilokePortfolioModePortfolioValue);
            $('.js_device_setting.active').trigger('click');
        },
        listenChangeItemsPerRow: function (event) {
            var newItemsPerRow = $(event.currentTarget).val(),
                $grid          = this.$el.find('.wil_masonry');
            $grid.attr({'data-col-lg': newItemsPerRow, 'data-col':newItemsPerRow});
            $grid.packery('layout');
        },
        listenChangeNumberOfPosts: function (event) {
            var $grid        = this.$el.find('.wil_masonry'),
                currentItems = $grid.find('.grid-item').length,
                $ctrl        = this.$el.find('.wil_masonry'),
                newItems     = $(event.currentTarget).val();
            var totalClick = currentItems - Number(newItems);
            totalClick = Math.abs(totalClick);

            if ( newItems == 0 || newItems == '' )
            {
                alert('You need at least an item');
            }else{
                if ( currentItems < Number(newItems) )
                {
                    var $items = '';
                    for ( var i = 1; i <= totalClick; i++ )
                    {
                        $items += $('#wilokepa-emulate-addnew').html();
                    }

                    $items = $($items);

                    $ctrl.append($items).packery('appended', $items);
                    $ctrl.find('.draggable').draggable();
                    $ctrl.find('.grid-item').removeClass('draggable');
                    $ctrl.packery('bindUIDraggableEvents', $items);

                }else{
                    for ( (newItems - 1); newItems < currentItems; newItems++ )
                    {
                        $grid.packery('remove', $grid.find('.grid-item').eq(newItems)).packery('shiftLayout');
                    }
                }

                this.resetLayout();
            }
        },
        addItem: function (event) {
            var $this = $(event.currentTarget);
            if ( $this.data('clicked') ) {
                return;
            }

            if ( typeof ( listOfEvents[$(event.currentTarget)] ) === 'undefined' ) {
                listOfEvents[$(event.currentTarget)] = 1;
            }

            $this.data('clicked', true);

            var $numofPosts = $this.prev(),
                $ctrl       = this.$el.find('.wil_masonry'),
                _currentVal = $this.closest('.wo_wpb_setting_layout').find('.grid-item').length,
                $items      = $('#wilokepa-emulate-addnew').html();
            $items      = $($items);

            $ctrl.append($items).packery('appended', $items);
            $ctrl.find('.draggable').draggable();
            $ctrl.find('.grid-item').removeClass('draggable');
            $ctrl.packery('bindUIDraggableEvents', $items);
            $numofPosts.val( Number(_currentVal) + 1 );
            this.resetLayout($this);
        },
        removeItems: function (event) {
            var $this = $(event.currentTarget);

            if ( $this.data('clicked') ) {
                return;
            }

            if ( typeof ( listOfEvents[$(event.currentTarget)] ) === 'undefined' ) {
                listOfEvents[$(event.currentTarget)] = 1;
            }

            var $numofPosts = $this.next(),
                _currentVal = $this.closest('.wo_wpb_setting_layout').find('.grid-item').length,
                $lastItem   = this.$el.find('.grid-item:last');

            $this.data('clicked', true);

            if ( _currentVal > 1 )
            {
                $numofPosts.val( Number(_currentVal) - 1 );
                this.$el.find('.wil_masonry').packery('remove', $lastItem).packery('shiftLayout');
                this.resetLayout($this);
            }else{
                alert('You need at least an item');
            }
        },
        initialize: function () {
            this.renderSettings();
        },
        renderSettings: function (event) {
            this.setActivate(event);
            var layoutIndex   = this.$el.find('.wo_portfolio_layout.active').parent().index(),
                currentDevice = this.$el.find('.js_device_setting.active').data('device'),
                modelInfo     = this.collection.at(layoutIndex - 1),
                oValue        = {},
                oDevicesSettings  = WilokePA.listOfModels['portfolioDeviceSettings'].get('value'),
                oSettings         = modelInfo.get('settings'),
                oLayoutSettings   = $('#wiloke-js-init-wpa input[name="wiloke_portfolio_layout[creative][settings]"]').attr('value');
            oLayoutSettings = $.parseJSON(oLayoutSettings);

            var portfolioGeneralSettings = WilokePA.Models['portfolioGeneral'].get('value');
            this.$el.find('.wo_wpd_settings_zone .wo_wpb_left').html('');

            if ( typeof oLayoutSettings['items_size'] != 'undefined' )
            {
                oLayoutSettings['items_size'] = this.parseSize(oLayoutSettings['items_size']);
            }

            /**
             * Allow clone settings
             * @since 1.0.1
             */
            if ( !$('.wiloke-clone-wrapper').length ) {
                this.$el.find('.wo_wpd_settings_zone .wo_wpb_left').append(this.renderCloneBtn());
                this.$el.find('.wo_wpd_settings_zone .wo_wpb_left').find('[data-target="'+currentDevice+'"]').addClass('current');
            }

            _.each(oSettings, function (setting) {
                setting['value'] = typeof (oDevicesSettings[currentDevice][setting.param_name]) != 'undefined' ? oDevicesSettings[currentDevice][setting.param_name] : '';

                var itemModel    = new WilokePA.Models.portfolioSettings(setting),
                    renderItem   = new WilokePA.Views.renderItemSettings({model: itemModel});

                this.$el.find('.wo_wpd_settings_zone .wo_wpb_left').append(renderItem.render().el);
            },this);

            oValue['layout'] = this.$el.find('.wo_portfolio_layout.active').attr('value');
            oValue['device'] = currentDevice;
            oValue['items_size'] = oLayoutSettings['items_size'];
            oValue['general_settings'] = portfolioGeneralSettings;
            oValue['devices_settings'] = oDevicesSettings;
            oValue['current_col'] = oDevicesSettings[currentDevice]['items_per_row'];

            var emulateModel   = new WilokePA.Models.portfolioEmulate(oValue),
                emulateView    = new WilokePA.Views.renderEmulate({model: emulateModel});
            this.$el.find('.js_wiloke_pa_elumate_zone').html(emulateView.render().el);

            if ( typeof event != 'undefined' )
            {
                $('.wo_wpd_settings_zone .btn_clone').prop('disabled', false);
            }

            this.packery();
        },
        setActivate: function (event) {
            if ( typeof event === 'undefined' ){
                return;
            }
            if ( $(event.currentTarget).hasClass('js_device_setting') ){
                this.saveValue();
                var $parent = $(event.currentTarget).parent();
                $parent.children().removeClass('previous-active');
                $parent.find('.active').addClass('previous-active');
                $parent.children().removeClass('active');
                $(event.currentTarget).addClass('active');
            }
        },
        renderCloneBtn: function () {
            return $("#wilokepa-clonebtn").html();
        },
        resizeLayout: function (event) {
            var $emulateCtrl = this.$el.find('.wil_masonry');

            if ( $emulateCtrl.data('isdragdrop') == 'yes' )
            {
                if ( $(event.currentTarget).hasClass('on-click') )
                {
                    return false;
                }

                var _currentLayout  = $(event.currentTarget).attr('data-size'),
                    _index          = listofLayouts.indexOf(_currentLayout);

                if ( _index == (listofLayouts.length - 1) )
                {
                    _index = 0;
                }else{
                    _index = _index + 1;
                }

                $(event.currentTarget).removeClass(_currentLayout);
                $(event.currentTarget).addClass(listofLayouts[_index]);
                $(event.currentTarget).attr('data-size',listofLayouts[_index]);

                $(event.currentTarget).css({
                    width: '',
                    height: ''
                });

                if ( !$emulateCtrl.data('isotope-initialized') )
                {
                    this.packery();
                }else{
                    $emulateCtrl.packery('layout');
                }

                this.resetLayout();

            }else{
                return false;
            }
        },
        resetLayout: function ($target) {
            var $itemElems = this.$el.find('.wo_wpb_setting_layout .wil_masonry'), _layout = '';

            if ( $itemElems.data('is-working') ) {
                return;
            }

            $itemElems.data('is-working', true);

            if ( !$itemElems.data('isotope-initialized') )
            {
                $itemElems.data('is-working', false);
                return;
            }else{
                var itemElems = $itemElems.packery('getItemElements');
                $itemElems.data('is-working', false);
            }

            $(itemElems).each( function( i, itemElem ) {
                _layout += $(itemElem).attr('data-size') + ',';
            });

            _layout = _layout.replace(/,$/, '');
            this.$el.find('.wo_wpb_left [name="items_size"]').val(_layout);

            if ( typeof $target !== 'undefined' ) {
                $target.data('clicked', false);
            }
        },
        packery: function () {
            var _this = this;
            var $emulateArchor = this.$el.find('.wo_wpb_setting_layout .wil_masonry'),
                currentLayout  = typeof $(document).data('wiloke-portfolio-current-activated') !== 'undefined' ? $(document).data('wiloke-portfolio-current-activated') : $emulateArchor.closest('.wpb_edit_form_elements').find('#wiloke-print-wiloke-design-layout .item.wo_wpb_checked .wo_portfolio_layout_value').val();

            $emulateArchor.attr('data-layout', currentLayout);

            if ( $emulateArchor.length  > 0 )
            {
                setTimeout(function () {
                    if ( $emulateArchor.data('isotope-initialized') )
                    {
                        $emulateArchor.packery('destroy');
                        $emulateArchor.data('isotope-initialized', false);
                    }

                    var $grid = $emulateArchor.packery({
                        itemSelector: '.grid-item',
                        columnWidth: '.grid-sizer',
                        percentPosition: true
                    });

                    //make all items draggable

                    if ( $grid.data('isdragdrop') == 'yes' )
                    {
                        var $items = $grid.find('.grid-item').draggable({
                            // scroll:true,
                            stop: function (event, ui) {
                                $(event.target).removeClass('non-click');
                                var _layouts = _this.resetLayout();
                            },
                            start: function (event, ui) {
                                $(event.target).addClass('non-click');
                            },
                            drag: function(event,ui){

                            }
                        });

                        // bind drag events to Packery
                        $grid.packery( 'bindUIDraggableEvents', $items);
                        $grid.on('layoutComplete', function () {
                            var itemElems = $grid.packery('getItemElements');
                            $grid.addClass('has-order');
                            $(itemElems).each( function( i, itemElem ) {
                                $(itemElem).attr('data-order', i+1);
                            });
                        });

                        $grid.on('dragItemPositioned', function () {
                            var itemElems = $grid.packery('getItemElements');
                            $grid.addClass('has-order');
                            $(itemElems).each( function( i, itemElem ) {
                                $(itemElem).attr('data-order', i+1);
                            });
                        });

                        _this.$draggableControl = $items;
                    }
                    WilokePackery = $grid;

                    $emulateArchor.data('isotope-initialized', true);
                }, 200);
            }
        },
        parseSize: function (sizes) {
            if ( typeof sizes === 'object' ) {
                return sizes;
            }

            var _newValues = [];

            if ( sizes.match(/\*/g) !== null )
            {
                sizes = sizes.split(',');

                for ( var order in sizes )
                {
                    if ( sizes[order].match(/\*/g) !== null )
                    {
                        var parseValue = sizes[order].split('*'),
                            _loop   = Number(parseValue[1]),
                            _layout = parseValue[0];

                        for ( var j = 0; j < _loop; j++ )
                        {
                            _newValues.push(_layout);
                        }
                    }else{
                        _newValues.push(sizes[order]);
                    }
                }

                sizes = _newValues.join(',');
            }

            return sizes.split(',');
        },
        saveValue: function (ignoreLayout, settings) {
            var currentLayout  = this.$el.find('.wo_portfolio_layout.active').attr('value'),
                currentDevice  = this.$el.find('.js_device_setting.active').data('device'),
                oValue         = this.$el.find('#wiloke-js-init-wpa').find('.wo_portfolio_layout_settings').attr('value');

            if (oValue) {
                try{
                    oValue = $.parseJSON(oValue);

                    var $settingZone = this.$el.find('.wo_wpd_settings_zone');
                    $settingZone.wrap('<form></form>');

                    var oNewVal = $settingZone.parent().serializeArray();
                    $settingZone.unwrap('<form></form>');

                    var parseVal = {}, itemsSize = '';

                    _.each(oNewVal, function (value) {
                        // in the case of switch layout, we will ignore item_size field
                        parseVal[value.name] = value.value;
                    });

                    var oDevicesSettings = WilokePA.listOfModels['portfolioDeviceSettings'].get('value');
                    oDevicesSettings[currentDevice] = parseVal;

                    // Get order of items
                    if ( WilokePackery.hasClass('has-order') ){
                        var numberOfItems = WilokePackery.find('.grid-item').length;

                        for ( var order = 1; order <= numberOfItems; order++ ){
                            var currentSize = WilokePackery.find('.grid-item[data-order="'+order+'"]').attr('data-size');
                            if ( typeof currentSize != 'undefined' ) {
                                itemsSize += currentSize + ',';
                            }else{
                                itemsSize += 'cube' + ',';
                            }
                        }
                    }else{
                        WilokePackery.find('.grid-item').each( function() {
                            var currentSize = $(this).attr('data-size');
                            if ( typeof currentSize != 'undefined' ) {
                                itemsSize += currentSize + ',';
                            }else{
                                itemsSize += 'cube' + ',';
                            }
                        });
                    }

                    itemsSize = itemsSize.substring(0, itemsSize.lastIndexOf(','));

                    oValue['items_size'] = itemsSize;
                    oValue['layout'] = currentLayout;

                    this.generateGeneralSettings();

                    $('#wpa_devices_settings').attr('value', JSON.stringify(oDevicesSettings));

                    WilokePA.listOfModels[currentLayout].set('value', oValue);
                    WilokePA.listOfModels['portfolioDeviceSettings'].set('value', oDevicesSettings);

                    oValue = JSON.stringify(oValue);
                    this.$el.find('.wo_portfolio_layout.active').siblings('.wo_portfolio_layout_settings').attr('value', oValue);
                    previousSettings = oValue;
                }catch(err)
                {
                    console.log(err)
                }

                if ( typeof ignoreLayout != 'undefined' ){
                    this.$el.find('.number_of_posts').trigger('change');
                }

                return oValue;
            }

        },
        backupValue: function (event) {
            if ( $(event.currentTarget).text() != 'Design Layout' ) {
                if ( $(document).data('wiloke-portfolio-allow-backup') ) {
                    this.generateGeneralSettings();
                }
            }
        },
        generateGeneralSettings: function () {
            var oGeneralLayoutSettings = {};
            this.$el.find('.wo_general_settings').each(function () {
                var name = $(this).attr('name');
                if ( typeof name != 'undefined' ) {
                    oGeneralLayoutSettings[name] = $(this).attr('value');
                }
            });
            $('#wpa_general_settings').attr('value', JSON.stringify(oGeneralLayoutSettings));
            WilokePA.Models['portfolioGeneral'].set('value', oGeneralLayoutSettings);
            return oGeneralLayoutSettings;
        }
    });

    /**
     * Each layout settings will be put into this model
     * @since 1.0
     */
    WilokePA.Models.portfolioSettings = Backbone.Model.extend({});

    /**
     * Emulate Model
     * @since 1.0
     */
    WilokePA.Models.portfolioEmulate = Backbone.Model.extend({});

    /**
     * Render Item Settings
     * @since 1.0
     */
    WilokePA.Views.renderItemSettings = Backbone.View.extend({
        tagName     : 'div',
        className   : 'form-control',
        render: function () {
            var $view       = $('#wilokepa-'+this.model.get('type')),
                template    = _.template($view.html());

            this.$el.html(template(this.model.toJSON()));
            return this;
        }
    });

    /**
     * Render Emulate Settings
     * @since 1.0
     */
    WilokePA.Views.renderEmulate = Backbone.View.extend({
        tagName     : 'div',
        className   : '',
        render: function () {
            var $view       = $('#wilokepa-emulate-with-packery'),
                template    = _.template('<div class="wo_wpb_setting_layout">' + $view.html() + '</div>');
            this.$el.html(template(this.model.toJSON()));
            return this;
        }
    });

    /**
     * Contains a list of Model Settings
     * @since 1.0
     */
    WilokePA.Collection.portfolioSettings   = Backbone.Collection.extend({
        model: WilokePA.Models.portfolioSettings,
        initialize: function () {
            this.on('change', function (model) {
                console.log('something got changed')
            })
        }
    });

    /**
     * The model contains general settings of all devices
     * @since 1.0
     */
    WilokePA.Models.portfolioGeneralSettings = Backbone.Model.extend({});

    /**
     * The model contains settings and value of each item.
     * @since 1.0
     */
    WilokePA.Models.portfolioItemSettings = Backbone.Model.extend({});


    /**
     * Device Settings
     * @since 1.0
     */
    WilokePA.Models.portfolioDeviceSettings = Backbone.Model.extend({});

    /**
     * WilokePA plugin. Backbone to be initialized here.
     * @since 1.0
     */
    if ( !$().WilokePA )
    {
        $.fn.WilokePA = function () {
            var $self = $(this), oSettings = {}, oSettingsVal = {},
                wilokePASettingsCollection  = new WilokePA.Collection.portfolioSettings();

            WilokePA.Models['portfolioGeneral'] = new WilokePA.Models.portfolioGeneralSettings();
            WilokePA.listOfModels['portfolioDeviceSettings'] =  new WilokePA.Models.portfolioDeviceSettings();


            $self.find('.wo_portfolio_layout').each(function () {
                var $this                = $(this),
                    layout               = $this.val(),
                    // Get fields for render
                    oSettings    = $this.data('settings');
                oSettingsVal = $this.siblings('.wo_portfolio_layout_settings').val();
                oSettingsVal  = $.parseJSON(oSettingsVal);

                var wilokePASettingsModel = new WilokePA.Models.portfolioSettings();

                // Fields of the WPA
                wilokePASettingsModel.set('settings', oSettings);

                // Value of each layout
                wilokePASettingsModel.set('value', oSettingsVal);

                wilokePASettingsCollection.add(wilokePASettingsModel);
                WilokePA.listOfModels[$this.val()] = wilokePASettingsModel; // using this var to update model
            });

            WilokePA.Models['portfolioGeneral'].set('value', $.parseJSON($('#wpa_general_settings').val()));
            WilokePA.listOfModels['portfolioDeviceSettings'].set('value', $.parseJSON($('#wpa_devices_settings').val()));
            new WilokePA.Views.renderSettings({collection: wilokePASettingsCollection});
        }
    }


    $(document).ready(function () {

        if ( typeof vc != 'undefined' )
        {
            /**
             * Override save setting
             * @since 1.0
             */
            vc.atts.wiloke_design_portfolio_layout = {
                parse: function (param) {
                    var $settings = this.$content.find('#wiloke-js-init-wpa'),
                        oValue    = {};

                    oValue['layout'] = $settings.find('.wo_portfolio_layout.active').attr('value');
                    $settings.find('.wo_portfolio_layout').each(function () {
                        oValue['layout'] = $(this).attr('value');
                        oValue[oValue['layout']] = $.parseJSON($(this).siblings('.wo_portfolio_layout_settings').attr('value'));
                    });

                    oValue['devices_settings'] = $.parseJSON($('#wpa_devices_settings').attr('value'));
                    oValue['general_settings'] = $.parseJSON($('#wpa_general_settings').attr('value'));

                    var $currentDemo = this.$content.find('#wiloke-print-wiloke-design-layout .item.wo_wpb_checked .wo_portfolio_layout_value');

                    oValue = JSON.stringify(oValue);
                    oValue = base64_encode(oValue);

                    if ( $currentDemo.length ) {
                        if($currentDemo.val() == 'creative')
                        {
                            $currentDemo.prev().attr('value', oValue);
                        }

                        if ( $currentDemo.closest('.item').data('is-customize') == 'no' ) {
                            this.$content.find('.wiloke-portfolio-layout-emulate').addClass('disable');
                        }
                    }

                    return oValue;
                },
                render: function () {

                }
            };

            vc.atts.wiloke_design_portfolio_choose_layout = {
                parse: function (param) {
                    var oValue     = {},
                        $activated = this.$content.find('#wiloke-print-wiloke-design-layout .item.wo_wpb_checked .wo_portfolio_layout_value'),
                        layout = $activated.val();
                    oValue['layout'] = layout;

                    oValue = JSON.stringify(oValue);
                    oValue = base64_encode(oValue);
                    return oValue;
                },
                render: function () {

                }
            };
        }
    })

})(jQuery);