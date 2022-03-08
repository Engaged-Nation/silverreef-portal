/**
 * @todo code abstraction with portal core
 */
EngagedNation.RequireJS.define(
    ['jquery_1_10', 'confetti'],
    function($, confetti)
    {
        var canvasId = "wheelgame";
        var theSpeed = 20;
        var pointerAngle = 0;
        var doPrizeDetection = true;
        var surface;
        var wheel;
        var wheelw;
        var wheelh;
        var angle = 0;
        var targetAngle = 0;
        var currentAngle = 0;
        var tmpx = 0;
        var tmpy = 0;
        var power = 1;
        var randomLastThreshold = 150;
        var spinTimer;
        var wheelState = 'reset';
        var isSound = false;
        var beginSpin = false;
        var bonus = 0;
        var landed = 0;
        var points = '';
        var entries = '';
        var voucher = '';
        var alias;

        var clkOne = true;

        var isiPad = navigator.userAgent.match(/iPad/i) != null;

        var wheelImageName = window.wheelImg;
        var prizes = window.wheelPrizes;

        if(window.returnTextOnly) {
            if ($('#confetti').length) {
                confetti.stop();
                $('#confetti').remove();
            }
        }

        /* ie11 fix */
        function msieversion() {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            var rt = '';

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))
                rt = parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
            else
                rt = 'otherbrowser';

            return rt;
        }

        function wheelResult(prize) {
            if (points) {
                /*
                 if (entries) {
                 entries = ' and ' + entries + ' Drawing Entry(s)';
                 }
                 $('#wheelgame-content .-results').html('<span style="color: #d6ff00;">Congratulations! You have earned ' + landed + ' + ' + bonus + ' Login Bonus = ' + points + ' ' + window.wheelAliasPoints + entries + '</span>');*/

                if (entries) {
                    entries = '<div>' + entries +' Drawing Entries Earned</div>';
                }

                if(window.returnTextOnly){
                    $.lbxgrid({
                        html: window.returnTextOnly,
                        xclose: false,
                        xcloseOutside: 'off'
                    });
                }
                else {
                    var p = window.wheelAliasPoints;
                    var html = '<div class="purple box"><div class="box-bg"><div class="wheelgame-box"><h3>' + window.wheelTitleLightbox + '</h3><div style="text-align: center;"><div class="wheelgame-box-small wheelgame-points-landed"><h4>You<br/>Just<br/>Won </h4>' + landed + '<div>' + p + '</div></div><div class="wheelgame-box-small wheelgame-points-bonus"><h4>Daily<br/>Login<br />Bonus</h4>' + bonus + '<div>' + p + '</div></div><div class="wheelgame-box-small wheelgame-points-total"><h4>YOUR <br/>TOTAL <br/>WIN<br/></h4>' + points + '<div>' + p + '</div>' + entries + '</div></div><div class="wheelgame-consecutive">You have <span class="wheelgame-spin-num" style="color: lime;">0</span> spin(s) for today</div></div></div></div>';

                    $('#wheelgame-content .prizes').html(html);
                    $('#wheelgame-content #wheelgame-pointer').css('visibility', 'hidden');
                    
                    $.points({plus: points});

                    setTimeout(function(){
                        $('#lt_welcome').click();
                    }, 3000
                    );

                }
            }
            else {
                $('#wheelgame-content .-results').html('<span style="color: #d6ff00;">Congratulations! You have earned ' + landed + ' + ' + bonus + ' Login Bonus = ' + points + ' ' + window.wheelAliasPoints + '</span>');
            }
            /*
             $.dblng();
             */
        }

        function begin() {
            surface = document.getElementById(canvasId);
            if (surface && surface.getContext) {
                wheel = new Image();
                wheel.onload = initialDraw;
                wheel.src = wheelImageName;

                if(window.wheel === 'false'){
                    return;
                }

                /* ie11 fix */
                var tst = msieversion().toString();
                if (tst.indexOf('NaN') != -1) {
                }
                else {
                    var sound = document.getElementsByTagName('audio')[0];
                    wheel.sound = sound;
                    if (typeof(wheel.sound.load) == 'function') {
                        wheel.sound.load();
                        isSound = true;
                    }
                }

                if (window.wheelOnce) {
                    var beginInterval = null;
                    var timer = 3;

                    if (window.wheel == 'true') {
                        $(document).on('click', '#wheelgame-itself', function () {
                            if (!beginSpin) {
                                spinItWheel();
                                $('#wheelgame-itself .wheelgame-center-img').attr('src', window.wheelImgSpinning);
                                $('#wheelgame-content .wheelgame-spin-num').text('0');
                                window.wheelOnce = false;
                            }
                        });
                    }
                    else {
                        $('#gameover').fadeIn();
                    }
                }
            }
        }

        function spinItWheel() {
            $('.aboveWheelMsg').fadeOut();

            if(window.returnTextOnly) {
            }
            else{
                if ($('#confetti').length) {
                    $('#confetti').fadeOut().promise().done(function () {
                        confetti.stop();
                        $('#confetti').remove();
                    });
                }
            }

            $.ajax({
                dataType: 'jsonp',
                jsonp: 'callback',
                cache: false,
                url: window.wheelUrl,
                success: function (data) {
                    var parts = data.html.split('|');
                    if (parts[1].indexOf("voucher") >= 0) {
                        var tmp = parts[1].split('+');
                        voucher = tmp[0];
                        landed = tmp[1];
                    }
                    else if(window.returnTextOnly){
                        window.returnTextOnly = parts[1];
                        points = 1;
                    }
                    else{
                        if (parts[1]) {
                            var tmp = parts[1].split('+');
                            points = tmp[0];
                            landed = tmp[1];
                            bonus = tmp[2];
                        }
                        if (parts[2]) {
                            entries = parts[2];
                        }
                    }

                    startSpin(parts[0]);
                },
            });
        }

        function initialDraw(e) {
            var w;

            if($('#xlogins-xcal').length > 0){
                w = $('#xlogins-xcal').width() - 70;
            }
            else{
                w = $('#wheelgame-content').width() - 70;
            }


            if (w < 455) {
                w = w - 20;
                $('#' + canvasId).attr({'width': w, 'height': w});
                $('#centerGraphic img').css('width', w);

                var surfaceContext = surface.getContext('2d');
                surfaceContext.drawImage(wheel, 0, 0, w, w);
                wheelw = w;
                wheelh = w;
            }
            else {
                $('#' + canvasId).attr({'width': 400, 'height': 400});
                $('#centerGraphic img').css('width', 400);

                wheelw = 400;
                wheelh = 400;


                var surfaceContext = surface.getContext('2d');
                surfaceContext.drawImage(wheel, 0, 0, wheelw, wheelh);
            }
        }

        function startSpin(determinedValue) {
            if (determinedValue) {
                //determinedValue = 9;
                //console.log('prize : ' + determinedValue + ' sa: ' + prizes[determinedValue]['startAngle'] + ' ea: ' + prizes[determinedValue]['endAngle']);
                //stopAngle = Math.floor(prizes[determinedValue]['startAngle'] + (Math.random() * (prizes[determinedValue]['endAngle'] - prizes[determinedValue]['startAngle'])));
                stopAngle = Math.floor((prizes[determinedValue]['startAngle'] + 1) + (Math.random() * ((prizes[determinedValue]['endAngle'] - 1) - prizes[determinedValue]['startAngle'])));
                if ((typeof(stopAngle) !== 'undefined') && (wheelState == 'reset') && (power)) {
                    stopAngle = (360 + pointerAngle) - stopAngle;
                    //stopAngle = 37;
                    targetAngle = (360 * (power * 6) + stopAngle);
                    //targetAngle = (360 + stopAngle);
                    //randomLastThreshold = Math.floor(90 + (Math.random() * 90));
                    randomLastThreshold = (360 - stopAngle);
                    //console.log("rlt - "+randomLastThreshold);
                    wheelState = 'spinning';
                    beginSpin = true;
                    doSpin();
                }
            }
        }

        function doSpin() {
            var surfaceContext = surface.getContext('2d');

            surfaceContext.save();

            surfaceContext.translate(wheelw * 0.5, wheelh * 0.5);

            surfaceContext.rotate(DegToRad(currentAngle));

            surfaceContext.translate(-wheelw * 0.5, -wheelh * 0.5);

            surfaceContext.drawImage(wheel, 0, 0, wheelw, wheelh);

            surfaceContext.restore();

            currentAngle += angle;
            tmpx = Math.ceil(Math.ceil(currentAngle / 10) / 4.2);

            if (tmpx != tmpy) {
                if (isSound) {
                    /*if(wheel.sound.play()){
                     wheel.sound.pause();
                     }*/
                    // win7 firefox fix error
                    if (wheel.sound.currentTime != 0) {
                        wheel.sound.currentTime = 0;
                    }
                    wheel.sound.play();
                }
                tmpy = tmpx;
            }

            if (currentAngle < targetAngle) {

                var angleRemaining = (targetAngle - currentAngle);
                if (angleRemaining > 6480)
                    angle = 55;
                else if (angleRemaining > 5000)
                    angle = 45;
                else if (angleRemaining > 4000)
                    angle = 30;
                else if (angleRemaining > 2500)
                    angle = 25;
                else if (angleRemaining > 1800)
                    angle = 15;
                else if (angleRemaining > 900)
                    angle = 11.25;
                else if (angleRemaining > 400)
                    angle = 7.5;
                else if (angleRemaining > 220)
                    angle = 3.80;
                else if (angleRemaining > randomLastThreshold)
                    angle = 1.90;
                else
                    angle = 1;
                spinTimer = setTimeout(function () {
                    doSpin();

                }, theSpeed);
            }
            else {
                wheelState = 'stopped';
                if ((doPrizeDetection) && (prizes)) {
                    var times360 = Math.floor(currentAngle / 360);
                    var rawAngle = (currentAngle - (360 * times360));

                    var relativeAngle = Math.floor(pointerAngle - rawAngle);

                    if (relativeAngle < 0) {
                        //relativeAngle = 360 - Math.abs(relativeAngle);
                        relativeAngle = 360 - stopAngle;
                        //console.log(relativeAngle + "-relativeAngle" + " stopAngle-" + stopAngle);
                    }

                    for (x = 0; x < (prizes.length); x++) {
                        if ((relativeAngle >= prizes[x]['startAngle']) && (relativeAngle <= prizes[x]['endAngle'])) {
                            $('#tbl-prize tr').eq(x).addClass('selected-');
                            if (voucher) {
                                var html = '<div class="red box" style="margin-top: 10px; padding: 15px;"><div class="box-bg" style="border: 1px solid #fff; padding: 25px; text-align: center;"><h3 style="color: #fff; text-shadow: none;">Thank you for playing! You were awarded ' + voucher + '</h3><div class="wheelgame-isvoucher" style="margin-top: 10px; text-align: center;"> <input type="button" class="btn btn-box btn-red wheelgame-redeem" value="Redeem My Voucher"/> </div></div></div>';
                                $('#wheelgame-content .prizes').html(html);
                                $.lbxgrid({
                                    html: '<div class="style-title"> <h3>Congratulations!</h3></div><div style="text-align: center;">You have been awarded ' + voucher + '</div><div class="wheelgame-isvoucher" style="margin-top: 10px; text-align: center;"> <input type="button" class="btn btn-box btn-red wheelgame-redeem" value="Redeem My Voucher"/> </div>'
                                });
                            }
                            else{
                                wheelResult(prizes[x]['name']);
                            }
                        }
                    }
                }
            }
        }

        function DegToRad(d) {
            return d * 0.0174532925199432957;
        }

        function isCanvasSupported() {
            var elem = document.createElement('canvas');
            return !!(elem.getContext && elem.getContext('2d'));
        }

        var WheelGame = function () {
            begin();

            if (!isCanvasSupported()) {
                $('#wheelgame-pointer').css('display', 'none');
                $('.spinTheWheel').css('display', 'none');
                /*$('#featured-wheel').css({'margin-left' : '40%','margin-top' : '26px'});*/
                $('#featured-content').css('height', 'auto');
                $('#featured-content .some-text').css('display', 'none');
                $('#dynamic-result').html('<button class="wheelgame-click-ie8 button">Claim Reward!</button>');

                $(document).on('click', '#dynamic-result .wheelgame-click-ie8', function () {
                    spinItWheel();
                });
            }

            window.wheelPrizes = '';

            var onr;
            $(window).resize(function () {
                clearTimeout(onr);
                onr = setTimeout(begin, 500);
            });
        };

        return WheelGame;
    }
);

