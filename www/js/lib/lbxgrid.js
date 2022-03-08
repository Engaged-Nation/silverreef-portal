/**
 * RampartDV version of lbxgrid
 *
 * @author Matthew Kosolofski <matthew.kosolofski@engagednation.com>
 */

"use strict";

EngagedNation.RequireJS.define(
    ["jquery_1_10", "lbxgridBase"],
    function($, LbxGrid)
    {
        /**
         * Construct.
         *
         * @param object|null config Config overrides.
         */
        var RampartDVLbxGrid = function(config)
        {
            this.config = {
                xcloseBtnClass: 'fa fa-times',
                xcloseOutside: 'off'
            };

            /* LbxGrid will apply the config, no need to do this here */
            LbxGrid.apply(this, arguments);
        };

        /**
         * Add methods
         */
        RampartDVLbxGrid.prototype = $.extend(
            Object.create(LbxGrid.prototype),
            {
                buildGrid: function()
                {
                    this.$gridElement.remove();

                    var button, bg;

                    button = '';
                    bg = '';

                    if (this.config.xclose) {
                        button = '<div class="' + this.config.gridClass + '-close xclose"><span>CLOSE</span></div>';
                    }

                    if (this.config.bg) {
                        bg = 'bg';
                    }

                    if (this.config.html.indexOf('style-title') >= 0) {
                        this.isHeaderSet = true;
                        bg = '';
                    }

                    $('.engagedNation #fzone').append('<div class="' + this.config.gridClass + ' ' + this.config.addClass + '">' +
                        '<div class="' + this.config.gridClass + '-wrapper">' +
                        button +
                        '<div class="' + this.config.gridClass + '-content ' + bg + '">' +
                        '<div class="' + this.config.gridClass + '-box"></div>' +
                        '</div>' +
                        '</div>' +
                        '</div>');

                }
            }
        );

        /**
         * Extend jQuery with with the Silver Reef version of lbxGrid.
         */
        $.fn.lbxgrid = function(config)
        {
            EngagedNation.jQuery.extensions.RampartDVLbxGrid = new RampartDVLbxGrid(config);
        };

        $.lbxgrid = function(config)
        {
            EngagedNation.jQuery.extensions.RampartDVLbxGrid = new RampartDVLbxGrid(config);
        };

        return RampartDVLbxGrid;
    }
);
