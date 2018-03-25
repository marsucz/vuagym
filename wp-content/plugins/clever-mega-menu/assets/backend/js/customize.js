window.clever_menu_item_title = '';
window.clever_menu_editing = 'customize';
window.vc_current_control = {};
window.vc_current_item_panel = {};

(function(api)
{
    var clever_menu_customize = function($el, $)
    {
        var self = this;

        self.panel = $('#customize-controls');
        self._resize = function()
        {
            var ww   = $(window).width();
            var pw   = self.panel.width();
            var diff = ww - pw;

            if (diff < 850) {
                $('.wp-full-overlay').removeClass('expanded').addClass('collapsed');
                $el.width(ww - 20);
            } else {
                $('.wp-full-overlay').removeClass('collapsed').addClass('expanded');
                $el.width(830);
            }
        };

        self._resize();

        $(window).resize(function()
        {
            self._resize();
        });
    };

    api.controlConstructor['nav_menu'].prototype._setupAddition = function()
    {
        var control = self = this, $ = jQuery;

        params = {
    		'customize-menus-nonce': api.settings.nonce['customize-menus'],
    		'wp_customize': 'on',
            'menu_id': control.params.menu_id
        };

        request = wp.ajax.post('clever_menu_load_items_settings', params);

        request.done(function(res)
        {
            var _s = $(res.menu_settings);

            control.container.append(_s);

            clever_menu_settings(_s);

            clever_setting_fields(_s);

            _s.on('change keyup', 'input, select, textarea', function()
            {
                var _data = $('input, select, textarea', _s).serialize(),
                    settingValue = control.setting();

                settingValue = _.clone(settingValue);
                settingValue['vc_nav_settings'] = _data;
                control.setting.set(settingValue);
                api.previewer.refresh();
            });

            _.each(res.items_setting, function(settings, item_id)
            {
                _control = api.control('nav_menu_item[' + item_id + ']');
                if (settings.options.enable === 1 || settings.options.enable === '1') {
                    _control.container.addClass('clever-mega-menu-item-mega-enabled');
                }
            });
        });

        this.container.find('.add-new-menu-item').on('click', function(event)
        {
            if (self.$sectionContent.hasClass('reordering')) {
                return;
            }

            if (! $('body').hasClass('adding-menu-items')) {
                $(this).attr('aria-expanded', 'true');
                api.Menus.availableMenuItemsPanel.open(self);
            } else {
                $(this).attr('aria-expanded', 'false');
                api.Menus.availableMenuItemsPanel.close();
                event.stopPropagation();
            }
        });
    };

    api.controlConstructor['nav_menu_item'].prototype.ready = function()
    {
        var control = this;

        if ('undefined' === typeof this.params.menu_item_id) {
            throw new Error('params.menu_item_id was not defined');
        }

        this._setupControlToggle();
        this._setupReorderUI();
        this._setupUpdateUI();
        this._setupRemoveUI();
        this._setupLinksUI();
        this._setupTitleUI();

        var btn = jQuery('<span class="clever-mega-menu-item-edit-btn">'+cleverMenuI18n.megaMenu+'</span>');

        control.container.find('.item-title').append(btn);

        btn.on('click', function(e)
        {
            e.preventDefault();

            var settingValue = control.setting();

            window.clever_menu_item_title = settingValue.title;
            window.vc_current_control = control;

            var id = 'clever-mega-menu-frame-'+control.params.menu_item_id;

            if (jQuery('#'+id, jQuery('body')).length > 0){
                jQuery('.clever-mega-menu-customize-vc-iframe-wrapper', jQuery('body')).removeClass('clever-mega-menu-customize-show-vc').addClass('clever-mega-menu-customize-hide-vc');
                jQuery('#'+id, jQuery('body')).removeClass('clever-mega-menu-customize-hide-vc').addClass('clever-mega-menu-customize-show-vc');
            } else {
                var url = cleverMenuConfig.newCleverMenu + '&clever_menu_item_id=' + control.params.menu_item_id;
                var iframe = jQuery('<div id="'+id+'" class="clever-mega-menu-customize-vc-iframe-wrapper"><iframe class="clever-mega-menu-customize-vc-iframe" src="'+url+'"></iframe></div>');
                jQuery( 'body .in-sub-panel').append(iframe);
                new clever_menu_customize(iframe, jQuery);
            }

            window.vc_current_item_panel = jQuery('#'+id, jQuery('body'));

            return false;
        });
    };
})(wp.customize);
