/**
 * @author Erin Nire <erin@engagednation.com>
 */

"use strict";

EngagedNation.RequireJS.define(
    ["jquery_1_10", "embedInitBase"],
    function($, EmbedInit)
    {
        /**
         * Construct.
         *
         * @param object|null config Config overrides.
         */
        var ClientEmbedInit = function(config)
        {
            EmbedInit.apply(this, arguments);
        };

        /**
         * Override methods
         */
        ClientEmbedInit.prototype = $.extend(
            Object.create(EmbedInit.prototype),
            {
                /**
                 * Renders all embed containers.
                 *
                 */
                renderEmbeds: function(url)
                {
                    var totalEmbeds = this.config.$embed.length;

                    this.config.$embed.each(
                        function() {
                            var hash = "";
                            if (window.location.hash) {
                                hash = "&ltab=" + window.location.hash.replace("#", "");
                            }

                            var target = $(this);

                            target.closest('.main').html(target);

                            var options = target.attr("data-options");
                            if (options == "geturlpath") {
                                options = "&url=" + encodeURI(window.location.pathname);
                            } else if (options) {
                                options = "&options=" + encodeURI(options);
                            } else{
                                options = "";
                            }

                            $.ajax(
                                {
                                    type: "GET",
                                    dataType: "jsonp",
                                    jsonp: "callback",
                                    cache: false,
                                    url: url + "&embed=" + target.attr("data-embed") + options + hash,
                                    success: function(data){
                                        target.html(data.html);

                                        totalEmbeds --;

                                        if (totalEmbeds < 1) {
                                            $(document).trigger(
                                                $.Event(
                                                    "EngagedNation:EmbedInit_EmbedsRendered",
                                                    {}
                                                )
                                            );
                                            
                                            /** Disable forms outside of fzone */
                                            var $fzone = $('#fzone');
                                            if ($fzone.length > 0) {
                                                $fzone.parents('form').on(
                                                    'submit',
                                                    function(event) {
                                                        event.preventDefault();
                                                    }
                                                );
                                            }
                                        }
                                    },
                                    error:function (){
                                        alert("Render error, please close your browser and try again.");
                                    }
                                }
                            );
                        }
                    );
                }
            }
        );
        return ClientEmbedInit;
    }
);
