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

    <!-- FAVICON -->
    <link rel="shortcut icon" href="assets/flow-market.ico">
    <link rel="icon" type="image/png" href="assets/flow-market-32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="assets/flow-market-256.png" sizes="96x96"/>
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png"/>

    <!-- VENDOR FILES -->
    <link rel="stylesheet" href="external/jquery-simple-bar-graph/dist/css/jquery.simple-bar-graph.min.css" />
    <link rel="stylesheet" href="external/jquery-ui-1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" href="external/submit.css" />
    <script src="external/jquery-3.6.0.min.js" type="application/javascript"></script>
    <script src="external/jquery-ui-1.12.1/jquery-ui.min.js" type="application/javascript"></script>
    <script src="external/jquery-simple-bar-graph/dist/js/jquery.simple-bar-graph.min.js" type="application/javascript"></script>
    <script src="external/lightweight-charts.js" type="application/javascript"></script>

    <!-- OWN CSS -->
    <link rel="stylesheet" href="style.css" />
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

                    <p>Participants:</p>
                    <ul>
                    <?php
                    $participants = unserialize(PARTICIPANTS);
                    foreach ($participants as $participant) { ?>
                        <li><?php echo $participant[0]; ?></li>
                    <?php } ?>
                    </ul>
                </div>
            </fieldset>

            <fieldset>
                <legend>Investment</legend>

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
                <p>Available amount per month:
                <label>
                    <input type="text" name="amount" value="200"/> EUR
                </label>
                </p>
                <p>Broker fees for each investment:
                    <label>
                        <input type="text" name="brokerCommissionAnyDayOfMonth" value="1.00" class="smallnumber"> EUR per regular buy and
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

        <div id="loadingBox" style="display:none">
            <div class="spinnerBox">
                <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                </svg>
            </div>
        </div>

        <div id="resultBox" style="display:none;">
            <h1>Calculation Results</h1>
            <p>On <span id="end_date"></span> each person has a total value (cash + share value) of:</p>
            <div id="graphBox"></div>
            <div id="history"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let myForm = $('#form');
    let isinChartBox = $('#isinChartBox');
    let loadingBox = $('#loadingBox');
    let resultBox = $('#resultBox');
    let graphBox = $('#graphBox');
    let historyBox = $('#history');
    let calculationTimer;
    let datePickerOpts = {
        changeMonth: true,
        changeYear: true,
        //minDate: new Date('2010-01-01'),
        //maxDate: new Date('2020-12-31'),
        yearRange: '1990:2021',
        dateFormat: "dd.mm.yy"
    };
    let dateFormatter = new Intl.DateTimeFormat('de-de', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });

    const getCalculationResults = function() {
        resultBox.css({ display: 'none' });
        loadingBox.css({ display: '' });

        // AJAX request
        $.post( 'result.php',
            myForm.serialize(),
            function (data) {
                // clear any existing result data
                historyBox.html('');
                graphBox.html('');

                let response = jQuery.parseJSON(data);

                if (response.error) {
                    resultBox.html(response.error);
                } else {
                    $('#end_date').html(dateFormatter.format(new Date(response.endDate)));

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
                            'title': resultData.name + ' Transactions',
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

                // make results visible
                loadingBox.css({ display: 'none' });
                resultBox.css({ display: '' });

                $('html, body').animate({
                    scrollTop: $("#resultBox").offset().top
                }, 2000);
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
    }

    // send form via AJAX request
    myForm.on('submit', function(e) {
        e.preventDefault();
        getCalculationResults();
    });

    // allow submitting form via ENTER
    $('input').keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            myForm.submit();
        }
    });

    // dynamically update ISIN chart
    $("input[name='isin']").on('change', function() {
      updateIsinChart(false);
    });

    // enable datepicker and load first results
    $(document).ready(function() {
        $("input.datepicker").datepicker(datePickerOpts);
        updateIsinChart(false); // dont submit already
    });

</script>

</body>
</html>