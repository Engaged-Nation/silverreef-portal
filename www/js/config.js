"use strict";

var EngagedNation = EngagedNation || {};

if (!EngagedNation.hasOwnProperty("Config")) {
    EngagedNation.Config = {};
}

if (!EngagedNation.Config.hasOwnProperty("requireJS")) {
    EngagedNation.Config.requireJS = {};
}

EngagedNation.Config.requireJS = {
    "paths" : {
        "embedInit": "js/embed/init",
        "lbxgrid": "js/lib/lbxgrid",
        "wheelGameScript": "js/lib/wheelgame",
        "lbxtooltip": "js/lib/lbxtooltip"
    }
};
