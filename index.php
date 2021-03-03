<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

header("Content-Type: text/html; charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8" ?>' . "\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <title>Investment Strategies</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="content-style-type" content="text/css"/>
    <meta http-equiv="content-script-type" content="text/javascript"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="author" content="chrisspaghetti"/>
    <meta name="robots" content="noindex, nofollow"/>
    <link rel="shortcut icon" href="assets/flow-market.ico">
    <link rel="icon" type="image/png" href="assets/flow-market-32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="assets/flow-market-256.png" sizes="96x96"/>
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png"/>

    <!-- VENDOR FILES -->
    <link rel="stylesheet" href="external/jquery-simple-bar-graph/dist/css/jquery.simple-bar-graph.min.css" />
    <link rel="stylesheet" href="external/jquery-ui-1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="external/submit.css" />
    <script src="external/jquery-3.6.0.min.js"></script>
    <script src="external/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="external/jquery-simple-bar-graph/dist/js/jquery.simple-bar-graph.min.js"></script>
    <script src="external/lightweight-charts.js"></script>

    <!-- OWN CSS -->
    <style type="text/css">
        html, body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Verdana, Arial, sans-serif;
            font-size: 1.0em;
            line-height: 110%;
        }

        div#mainBox {
            margin: 10px;
        }

        div.content {
            text-align: left;
            margin: 20px 0 150px 0;
        }

        div#isinChartBox {
            float: right;
            border: 1px solid #C0C0C0;
        }

        div.submitBox {
            text-align: center;
            margin: 30px 0 30px 0;
        }

        div#resultBox {
            padding-top: 30px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        div#history {
            margin: 50px 0 0 0;
            text-align: left;
            line-height: 0.8em;
            font-size: 0.8em;
        }

        a.details {
            padding: 5px;
            font-size: 0.7em;
        }

        div.hidden {
            display: none;
        }

        h1 {
            text-align: center;
            font-size: 150%;
        }

        h2 {
            font-size: 120%;
        }

        form {
            padding: 3px 10px 0 3px;
        }

        fieldset {
            margin: 0 0 20px 0;
        }

        ul.isin {
            list-style: none;
            padding-left: 0;
        }

        input[type=text] {
            padding-left: 2px;
            padding-bottom: 2px;
            font-size: 0.9em;
            width: 80px;
        }

        input.smallnumber {
            width: 50px;
        }

        div.error {
            font-weight: bold;
            color: #444444;
            background-color: #FF1E1E;
            border: 1px dotted #444444;
            padding: 5px;
            margin: 10px;
        }

        div.warning {
            font-weight: bold;
            color: #444444;
            background-color: #FFFFE0;
            border: 1px dotted #444444;
            padding: 5px;
            margin: 10px;
        }

        div.success {
            font-weight: bold;
            color: green;
        }

        .simple-bar-graph__value {
            top: -10px;
            font-size: 0.8em;
            line-height: 1.1em;
        }

        .simple-bar-graph__value::after {
            content: " €"
        }

        .simple-bar-graph__caption {
            bottom: -40px;
            font-size: 0.8em;
            line-height: 1.15em;
        }
    </style>
</head>
<body>

<div id="mainBox">
    <div class="content">

        <h1>Time in the market beats timing the market!?</h1>

        <form id="form" action="result.php" method="post">
            <fieldset>
                <legend>Intro</legend>
                <div class="intro">
                    <p>This tool compares different investment strategies.
                        It shows you how effective each strategy is for an ETF like the MSCI World.</p>

                    <h2>Strategies</h2>
                    <ul>
                        <li>Peter Perfect invests at lowest monthly close</li>
                        <li>Ashley Action invests at start of a year</li>
                        <li>Matthew Monthly invests in 12 even chunks at start of each month</li>
                        <li>Rosie Rotten invests at highest monthly close</li>
                        <li>Denise Delay invests on 1st day of every second month</li>
                        <li>Quintus Quantus invests at start of January/April/July/October</li>
                        <li>Whitney Waiting invests up to 3 times at all time lows, but only once per 3 years</li>
                        <li>Larry Linger leaves his money in cash investments</li>
                    </ul>
                </div>
            </fieldset>

            <fieldset>
                <legend>Investment ETF</legend>

                <div id="isinChartBox"></div>

                <ul class="isin">
                    <?php
                    $available_isin = unserialize (AVAILABLE_ISIN);
                    $checked = false;
                    foreach ($available_isin as $isin => $etf_name)
                    {
                        $checked_txt = (!$checked) ? 'checked="checked"' : '';
                        $checked = true;
                        ?>
                        <li><label>
                                <input type="radio" name="isin" value="<?php echo $isin; ?>" <?php echo $checked_txt ?>/>
                                <?php echo $etf_name. ' ('.$isin.')'; ?>
                            </label></li>
                    <?php
                    }
                    ?>
                </ul>
            </fieldset>

            <fieldset>
                <legend>Calculation Options</legend>
                <p>Available amount per year:
                <label>
                    <input type="text" name="amount" value="2400"/> EUR
                </label>
                </p>
                <p>Broker fees for each investment:
                    <label>
                        <input type="text" name="brokerCommissionAnyDayOfMonth" value="5.00" class="smallnumber"> EUR per regular buy and
                    </label>
                    <label>
                        <input type="text" name="brokerCommissionFirstDayOfMonth" value="1.00" class="smallnumber"> EUR per buy on 1st day
                        of month
                    </label>
                </p>
                <p>
                    <label>
                        Investment Time Range
                        <input type="text" name="startDate" value="" class="datepicker" />
                    </label>
                    <label>
                        To: <input type="text" name="endDate" value="" class="datepicker" />
                    </label>
                </p>
            </fieldset>

            <div class="submitBox">
                <a href="#" class="button" onclick="submitForm(); return false;">Submit</a>
            </div>
        </form>

        <div id="resultBox">
            <h1>Calculation Results</h1>
            <div id="graphBox"></div>
            <div id="history"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let myForm = $('#form');
    let isinChartBox = $('#isinChartBox');
    let resultBox = $('#resultBox');
    let graphBox = $('#graphBox');
    let historyBox = $('#history');
    let calculationTimer;
    let datePickerOpts = {
        changeMonth: true,
        changeYear: true,
        minDate: new Date('2010-01-01'), // default, gets overwritten below by ISIN
        maxDate: new Date('2020-12-31'), // default, gets overwritten below by ISIN
        dateFormat: "dd.mm.yy"
    };

    const getCalculationResults = function() {
        $.post( 'result.php',
            myForm.serialize(),
            function (data) {
                let response = jQuery.parseJSON(data);

                historyBox.html('');
                graphBox.html('');

                if (response.error) {
                    resultBox.html(response.error);
                } else {
                    let graphData = [];
                    $.each(response.data, function(index, resultData) {
                        // collect data for chart
                        graphData.push({
                            key: resultData.name,
                            value: resultData.value
                        });

                        // put history data in a DIV which is not visible
                        let html_id = 'history_details_'+index;
                        let historyElement = $("<div/>", {
                            'id': html_id,
                            'title': 'Log of ' + resultData.name,
                            'class': 'hidden'
                        }).appendTo(historyBox);

                        let elem = $('#'+html_id);
                        $.each(resultData.history, function(i, historyData) {
                            $("<p/>").html(historyData.date + ": " + historyData.text).appendTo(elem);
                        });
                    });

                    // show chart
                    graphBox.simpleBarGraph({
                        data: graphData,
                        barsColor: '#2196F3',
                        popups: true,
                        rowCaptionsWidth: '80px',
                        delayAnimation: 15 //ms
                    });

                    // add detail link for each portfolio
                    $.each($('div.simple-bar-graph__caption'), function(i, div) {
                        $( "<br/>").appendTo( div );
                        $( "<a/>", {
                            "class": "details",
                            text: "Details",
                            click: function(e) {
                                e.preventDefault();
                                // modal popup
                                $( "#history_details_"+i ).dialog({
                                    modal: true,
                                    width: 600,
                                    height: 600,
                                    buttons: {
                                        close: function() {
                                            $( this ).dialog( "close" );
                                        }
                                    }
                                });
                            }
                        }).attr('href', '#').appendTo( div );
                    });
                }
            }
        );
    }

    const updateIsinChart = function(triggerCalculation) {
        // plot ISIN with https://www.tradingview.com/HTML5-stock-forex-bitcoin-charting-library/
        let isinValue = $("input[name='isin']:checked").val();

        $.post( 'isin.php',
            'isin='+isinValue,
            function (data) {
                let response = jQuery.parseJSON(data);

                if (response.error) {
                    isinChartBox.html(response.error);
                } else {
                    let isinChartData = [];
                    $.each(response.data, function(index, resultData) {
                        isinChartData.push({
                            time: resultData.date,
                            value: resultData.value
                        });
                    });

                    let chartElement = document.createElement('div');
                    let width = $(window).width() * 0.9;

                    if (width > 1000) {
                        width = 1000;
                    }

                    let chart = LightweightCharts.createChart(chartElement, {
                        width: width,
                        height: 200,
                        grid: {
                            vertLines: {
                                color: 'rgba(70, 130, 180, 0.5)',
                                style: 1,
                                visible: false,
                            },
                            horzLines: {
                                color: 'rgba(70, 130, 180, 0.5)',
                                style: 1,
                                visible: false,
                            },
                        },
                        rightPriceScale: {
                            borderColor: 'rgba(197, 203, 206, 1)',
                        },
                        timeScale: {
                            borderColor: 'rgba(197, 203, 206, 1)',
                        },
                        localization: {
                            priceFormatter: price =>
                                // round to 2decimals and add € symbol
                                // https://stackoverflow.com/questions/11832914/round-to-at-most-2-decimal-places-only-if-necessary
                                ( Math.round((price + Number.EPSILON) * 100) / 100 ) + " €"
                            ,
                        },
                    });

                    isinChartBox.html(chartElement);

                    let areaSeries = chart.addAreaSeries({
                        topColor: 'rgba(33, 150, 243, 0.56)',
                        bottomColor: 'rgba(33, 150, 243, 0.04)',
                        lineColor: 'rgba(33, 150, 243, 1)',
                        lineWidth: 2,
                    });

                    areaSeries.setData(isinChartData);

                    // zoom out
                    chart.timeScale().fitContent();

                    // set start and end date for this ISIN
                    let dateFormatter = new Intl.DateTimeFormat('de-de', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });

                    let startDate = new Date(response.startDate);
                    let endDate = new Date(response.endDate);

                    $('input[name=startDate]').val(dateFormatter.format(startDate));
                    $('input[name=endDate]').val(dateFormatter.format(endDate));

                    $("input.datepicker").datepicker("option", "minDate", startDate);
                    $("input.datepicker").datepicker("option", "maxDate", endDate);
                }

                // send form to get calculation results?
                if (triggerCalculation === true) {
                    myForm.submit();
                }
            }
        );
    }

    const submitForm = function() {
        myForm.submit();

        $('html, body').animate({
            scrollTop: $("#resultBox").offset().top
        }, 2000);
    }

    // send form via AJAX request
    myForm.on('submit', function(e) {
        e.preventDefault();

        // get calculation results
        getCalculationResults();
    });

    $("input[name='isin']").on('change', function() {
      updateIsinChart(false);
    });

    $(document).ready(function() {
        $("input.datepicker").datepicker(datePickerOpts);
        updateIsinChart(true);
    });

</script>

</body>
</html>