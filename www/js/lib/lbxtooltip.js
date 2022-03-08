/**
 * RampartDV version of lbxtooltip
 *
 * @author Erin Nire <erin@engagednation.com>
 */

"use strict";

EngagedNation.RequireJS.define(
    ["jquery_1_10", "lbxtooltipBase"],
    function($, LbxToolTip)
    {
        /**
         * Construct.
         *
         * @param object|null config Config overrides.
         */
        var RampartDVLbxToolTip = function(config)
        {
            LbxToolTip.apply(this, arguments);
        };

        /**
         * Add methods
         */
        RampartDVLbxToolTip.prototype = $.extend(
            Object.create(LbxToolTip.prototype),
            {
                buildHtml: function($me){
                    $me.prepend('<div class="' + this.config.htmlClass + '" style="position: relative;"><div class="' + this.config.htmlClass + '-body" style="display: none; position: absolute; z-index: 1000; margin-top: -50px; top: 0; pointer-events: none;"><div class="' + this.config.htmlClass + '-wrapit" style="position: relative; background: #b9202f; padding: 9px 17px; color: #fff; font-size: 12px; text-align: center; border: 1px solid #ff0d25; box-shadow: 0 2px 10px #333;"><div style="white-space: nowrap; text-shadow: none;">' + $me.attr('data-title') + '</div></div></div></div>');
                },
            }
        );

        $.lbxtooltip = function(config)
        {
            EngagedNation.jQuery.extensions.RampartDVLbxToolTip = new RampartDVLbxToolTip(config);
        };

        return RampartDVLbxToolTip;
    }
);
