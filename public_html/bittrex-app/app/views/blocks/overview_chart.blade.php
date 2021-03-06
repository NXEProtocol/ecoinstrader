<div id="overview">
    <div class="container">     
        <span class="title left"><i class="icon-air"></i> {{trans('frontend_texts.overview')}}</span>       
        <p class="show-all right"><a href="<?=url('/user/profile/orders', $parameters = array(), $secure = null);?>">{{Lang::get('texts.show_orderbook')}}<i class="icon-arrow-right-5"></i></a></p>
        <hr class="clear">
        <!-- <div class="tab-control" data-role="tab-control"> -->
        <div class="chart-data">
            <ul class="pair">
               <!--  <li class="active tabusd"><a href="#_page_1">USD</a></li>
                <li class="tabltc"><a href="#_page_2">LTC</a></li>   -->
                @foreach($btc_markets as $btc_market)            
                <li class="@if($market_id==$btc_market->id) active @endif tab{{$btc_market->id}}"><a href="<?=url('/market/'.$btc_market->id);?>">{{$btc_market->type}}/BTC</a></li>
                @endforeach
                @foreach($ltc_markets as $ltc_market)            
                <li class="@if($market_id==$ltc_market->id) active @endif tab{{$ltc_market->id}}"><a href="<?=url('/market/'.$ltc_market->id);?>">{{$ltc_market->type}}/LTC</a></li>
                @endforeach
            </ul>
            <hr class="clear">
            <!-- <div class="frames">
                <div class="frame" id="_page_1" style="display: block;"> -->
                    <div class="data-tab">
                        <div class="data-tab-left" id="lastprice-{{{Session::get('market_id')}}}">
                            <span><sup class="big-text">{{{$coinsecond}}}</sup><span class="big-text" id="spanLastPrice-{{{Session::get('market_id')}}}">@if(empty($lastest_price)) {{{sprintf('%.8f',0)}}} @else {{sprintf('%.8f',$lastest_price)}} @endif</span></span>
                            <!-- <span class="change"><i class="icon-arrow-up-2"></i><br>0.67%</span> -->
                        </div>
                        <div class="data-tab-right">
                            <table class="table">                               
                                <tbody>
                                    <?php
                                        if(strtoupper($coinsecond) == "LTC"){
                                            $total_coins = (isset($statistic_ltc->total) && !empty($statistic_ltc->total))? sprintf('%.8f',$statistic_ltc->total):0;
                                        }else{
                                            $total_coins = (isset($statistic_btc->total) && !empty($statistic_btc->total))? sprintf('%.8f',$statistic_btc->total):0;
                                        }
                                    ?>
                                    <tr><td class="title-td">{{trans('texts.today_high')}}</td><td class=""><span id="spanHighPrice-{{{Session::get('market_id')}}}">@if(empty($get_prices->max)) {{{sprintf('%.8f',0)}}} @else {{sprintf('%.8f',$get_prices->max)}} @endif</span> {{{$coinsecond}}}</td></tr>

                                    <tr><td class="title-td">{{trans('texts.today_low')}}</td><td class=""><span id="spanLowPrice-{{{Session::get('market_id')}}}">@if(empty($get_prices->min)) {{{sprintf('%.8f',0)}}} @else {{sprintf('%.8f',$get_prices->min)}} @endif</span> {{{$coinsecond}}}</td></tr>
                                    <tr><td class="title-td">{{trans('texts.total')}} {{{$coinsecond}}}</td><td id="spanVolume-{{{Session::get('market_id')}}}">{{$total_coins}}</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="clear select-chart">
                            <button class="button primary" data-chart="chart_price_volume">{{trans('texts.price')}} / {{trans('texts.volume')}}</button>
                            <button class="button" data-chart="order_depth">{{trans('texts.order_depth')}}</button>
                        </div>
                        <div id="orderdepth_chart" class="clear" style="display: none;">                          
                            <div id="chartdiv_orderdepth" style="height:300px;weight:900px"></div>
                        </div>
                        <div id="dashboard_div" class="clear">
                            <input id="lastDay" type="button" value="Last Day" class="chart-filter active"/>
                            <input id="lastWeek" type="button" value="Last Week" class="chart-filter" />
                            <input id="lastMonth" type="button" value="Last Month" class="chart-filter" />
                            <input id="baMonth" type="button" value="3 Months" class="chart-filter" />
                            <input id="sauMonth" type="button" value="6 Months" class="chart-filter" />
                            <input id="mhaiMonth" type="button" value="12 Months" class="chart-filter" />
                            <div id="chart_div" style="height:300px;weight:900px"></div>
                            <div id="control_div"></div>
                        </div>
                    </div>
               <!--  </div>                 
            </div> -->
        </div>
    </div>
</div>
@section('script.footer')
@parent
<script type="text/javascript" src="{{ asset('assets/googlechart/jsapi.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/amcharts/amcharts.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/amcharts/serial.js') }}"></script>
<script type="text/javascript">
    function drawVisualization() {        
        var dashboard;
        var chartData=[];
        var timeSpan_ = '1 day';
        var data;
        $.ajax({
            url:"<?php echo action('HomeController@getChart')?>",
            type:'post',
            dataType:'json',
            data: {Ajax:1,timeSpan:timeSpan_,market_id:<?php echo $market_id ?>},
            cache:false,
            async:true,
            success:function(rows){ 
                //console.log('rows: ',rows);          
                $('.loading').hide(); 
                for(var i=0; i<rows.length; i++){                      
                    rows[i][0]=new Date(rows[i][0]);                    
                    //console.log('rows '+i+': ',rows[i][0]);   
                    //data.addRow([rows[i][0], '<div class="highcharts-tooltip" zIndex="8" style="cursor:default;padding:0;white-space:nowrap;" visibility="visible" transform="translate(570,40)" opacity="1">Hellllocccccccc</div>', rows[i][1], rows[i][2], rows[i][3], rows[i][4], rows[i][5]]);             
                }
                //console.log('data: ',data);   
                data = google.visualization.arrayToDataTable(rows, true);
                
                
                var rCandlestickChart  = new google.visualization.ChartWrapper({
                    chartType: 'ComboChart',//'CandlestickChart',
                    containerId: 'chart_div',
                    options: {
                            height: 300, 
                            fontName: '"Open Sans", "Lucida Grande", Verdana, Arial, Helvetica, sans-serif', 
                            backgroundColor: "transparent", 
                            chartArea: {height: "70%", width: "90%"}, 
                            hAxis: {slantedText: !1, minTextSpacing: "40", maxAlternation: 1}, 
                            vAxis: {format: "#,#####0.00000"}, 
                            legend: 'none',//{position: "none"}, 
                            tooltip: {isHtml: true}, 
                            seriesType: "bars",                            
                            series: {
                                0: {
                                    type: "candlesticks",
                                    targetAxisIndex: 0, 
                                    color: "black", 
                                    candlestick: {
                                        fallingColor: {
                                            fill: "#0ab92b",
                                            stroke: "green",
                                            color: "green", 
                                            strokeWidth: 1
                                        }, 
                                        risingColor: {
                                            fill: "#f01717",
                                            stroke: "#d91e1e", 
                                            color: "#d91e1e", 
                                            strokeWidth: 1
                                        }
                                    }   
                                }, 
                                1: {
                                    type: "bars", 
                                    targetAxisIndex:1, 
                                    color:"#4EBDE7"
                                }
                            }                            
                        }
                });
                 
                var control = new google.visualization.ControlWrapper({
                    controlType: 'ChartRangeFilter',
                    containerId: 'control_div',
                    options: {
                        filterColumnIndex: 0, 
                        ui: {
                            chartType: "ComboChart", 
                            backgroundColor: {fill: "transparent"}, 
                            chartOptions: {
                                fontName: '"Open Sans", "Lucida Grande", Verdana, Arial, Helvetica, sans-serif',
                                backgroundColor: {fill: "transparent"}, 
                                height: 70, 
                                chartArea: {width: "90%", backgroundColor: {fill: "transparent"}, height: 50}, 
                                seriesType: "bars", 
                                series: {
                                    0: {
                                        targetAxisIndex: 1, 
                                        type: "bars", 
                                        color: "#4b71a2", 
                                        hAxis: {baselineColor: "none"}
                                    }
                                }, 
                                hAxis: {
                                    baselineColor: "none", 
                                    textPosition: "out", 
                                    textStyle: {color: "#ddd"}, 
                                    format: "yyyy.MM.dd"
                                }
                            },
                            minRangeSize: 216e5
                        }
                    }
                });
                dashboard = new google.visualization.Dashboard(document.querySelector('#dashboard_div'));
                dashboard.bind([control], [rCandlestickChart]);
                dashboard.draw(data); 

                function zoomLastDay () {
                    $('.chart-filter').removeClass('active');
                    $('#lastDay').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastDay range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth(), range.max.getDate() - 1),
                            end: range.max
                        }
                    });                 
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'1 day',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastDay range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1); 
                            control.draw();                           
                        }
                    });        
                    
                }
                function zoomLastWeek () {
                    $('.chart-filter').removeClass('active');
                    $('#lastWeek').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastWeek range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth(), range.max.getDate() - 7),
                            end: range.max
                        }
                    });
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'7 day',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastWeek range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1);
                            control.draw();
                        }
                    });     
                }
                function zoomLastMonth () {
                    // zoom here sets the month back 1, which can have odd effects when the last month has more days than the previous month
                    // eg: if the last day is March 31, then zooming last month will give a range of March 3 - March 31, as this sets the start date to February 31, which doesn't exist
                    // you can tweak this to make it function differently if you want
                    $('.chart-filter').removeClass('active');
                    $('#lastMonth').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastMonth range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth() - 1, range.max.getDate()),
                            end: range.max
                        }
                    });
                    
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'1 month',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastMonth range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1);
                            control.draw();
                        }
                    });   
                }
				function zoombaMonth () {
                    // zoom here sets the month back 1, which can have odd effects when the last month has more days than the previous month
                    // eg: if the last day is March 31, then zooming last month will give a range of March 3 - March 31, as this sets the start date to February 31, which doesn't exist
                    // you can tweak this to make it function differently if you want
                    $('.chart-filter').removeClass('active');
                    $('#baMonth').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastMonth range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth() - 3, range.max.getDate()),
                            end: range.max
                        }
                    });
                    
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'3 month',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastMonth range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1);
                            control.draw();
                        }
                    });   
                }
				function zoomsauMonth () {
                    // zoom here sets the month back 1, which can have odd effects when the last month has more days than the previous month
                    // eg: if the last day is March 31, then zooming last month will give a range of March 3 - March 31, as this sets the start date to February 31, which doesn't exist
                    // you can tweak this to make it function differently if you want
                    $('.chart-filter').removeClass('active');
                    $('#sauMonth').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastMonth range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth() - 6, range.max.getDate()),
                            end: range.max
                        }
                    });
                    
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'6 month',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastMonth range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1);
                            control.draw();
                        }
                    });   
                }
				function zoommhaiMonth () {
                    // zoom here sets the month back 1, which can have odd effects when the last month has more days than the previous month
                    // eg: if the last day is March 31, then zooming last month will give a range of March 3 - March 31, as this sets the start date to February 31, which doesn't exist
                    // you can tweak this to make it function differently if you want
                    $('.chart-filter').removeClass('active');
                    $('#mhaiMonth').addClass('active');
                    var range = data.getColumnRange(0);
                    //console.log('zoomLastMonth range: ',range);
                    control.setState({
                        range: {
                            start: new Date(range.max.getFullYear(), range.max.getMonth() - 12, range.max.getDate()),
                            end: range.max
                        }
                    });
                    
                    $.ajax({
                        url:"<?php echo action('HomeController@getChart')?>",
                        type:'post',
                        dataType:'json',
                        data: {Ajax:1,timeSpan:'12 month',market_id:<?php echo $market_id ?>},
                        cache:false,
                        async:true,
                        success:function(rows){
                            for(var i=0; i<rows.length; i++){
                                rows[i][0]=new Date(rows[i][0]);
                            }
                            //console.log('zoomLastMonth range rows: ',rows);
                            var data1 = google.visualization.arrayToDataTable(rows, true);
                            dashboard.draw(data1);
                            control.draw();
                        }
                    });   
                }
                
                var runOnce = google.visualization.events.addListener(dashboard, 'ready', function () {
                    google.visualization.events.removeListener(runOnce);
                    
                    if (document.addEventListener) {
                        document.querySelector('#lastDay').addEventListener('click', zoomLastDay);
                        document.querySelector('#lastWeek').addEventListener('click', zoomLastWeek);
                        document.querySelector('#lastMonth').addEventListener('click', zoomLastMonth);
                        document.querySelector('#baMonth').addEventListener('click', zoombaMonth);
                        document.querySelector('#sauMonth').addEventListener('click', zoomsauMonth);
                        document.querySelector('#mhaiMonth').addEventListener('click', zoommhaiMonth);
                    }
                    else if (document.attachEvent) {
                        document.querySelector('#lastDay').attachEvent('onclick', zoomLastDay);
                        document.querySelector('#lastWeek').attachEvent('onclick', zoomLastWeek);
                        document.querySelector('#lastMonth').attachEvent('onclick', zoomLastMonth);
						document.querySelector('#baMonth').addEventListener('onclick', zoombaMonth);
                        document.querySelector('#sauMonth').addEventListener('onclick', zoomsauMonth);
                        document.querySelector('#mhaiMonth').addEventListener('onclick', zoommhaiMonth);
                    }
                    else {
                        document.querySelector('#lastDay').onclick = zoomLastDay;
                        document.querySelector('#lastWeek').onclick = zoomLastWeek;
                        document.querySelector('#lastMonth').onclick = zoomLastMonth;
                        document.querySelector('#baMonth').onclick = zoombaMonth;
                        document.querySelector('#sauMonth').onclick = zoomsauMonth;
                        document.querySelector('#mhaiMonth').onclick = zoommhaiMonth;
                    }
                });                         
            }
        });
    }

    //google.setOnLoadCallback(drawVisualization);
    google.load('visualization', '1', {packages:['controls'], callback: drawVisualization});


    /// Order Depth Chart   
    //giai thich depth chart of coin: http://www.snell-pym.org.uk/archives/2013/04/08/understanding-bitcoin-market-depth/   
    function drawOrderDepthChart(){
        $('.loading').show();
        $.ajax({
            url:"<?php echo action('OrderController@getOrderDepthChart')?>",
            type:'post',
            dataType:'json',
            data: {Ajax:1,market_id:<?php echo $market_id ?>},
            cache:false,
            async:true,
            success:function(rows){
                //console.log('response: ',response);
                //var rows = $.parseJSON(response); 
                console.log('Row: ',rows);
                $('.loading').hide();
                var chartData = [];               
               
                for (var j = rows['buy'].length - 1; j >= 0; j--) {
                    chartData.push({
                        price: parseFloat(parseFloat(rows['buy'][j]['price']).toFixed(8)),
                        bid_total: parseFloat(rows['buy'][j]['total'])
                    });
                }  

                for (var i = 0; i < rows['sell'].length; i++) {
                    chartData.push({
                        price: parseFloat(parseFloat(rows['sell'][i]['price']).toFixed(8)),
                        ask_total: parseFloat(rows['sell'][i]['total'])
                    });
                }
                console.log('chartData: ',chartData);
                var chart = AmCharts.makeChart("chartdiv_orderdepth", {
                     "type": "serial",
                     "theme": "light",
                     "usePrefixes": true,
                     /*"pathToImages": "amcharts/images/",*/
                     "dataProvider": chartData,
                     "valueAxes": [{
                         "id": "v1",
                         "axisColor": "#EEE",
                         "axisThickness": 1,
                         "gridAlpha": 0,
                         "axisAlpha": 1,
                         "position": "left",
                         "visible": true,
                         "unit": " {{{$coinsecond}}}",
                         "titleBold": false
                     }],
                     "graphs": [{
                         "id": "g1",
                         "valueAxis": "v1",
                         "lineColor": "#00ff00",
                         "lineThickness": 2,
                         "hideBulletsCount": 30,
                         "valueField": "bid_total",
                         "balloonText": "<b>[[value]]</b> {{{$coinsecond}}} to get to [[price]]",
                         "fillAlphas": 0.4
                     }, {
                         "id": "g2",
                         "valueAxis": "v1",
                         "lineColor": "#ff0000",
                         "lineThickness": 2,
                         "hideBulletsCount": 30,
                         "valueField": "ask_total",
                         "balloonText": "<b>[[value]]</b> {{{$coinsecond}}} to get to [[price]]",
                         "fillAlphas": 0.4
                     }],
                     "chartCursor": {
                         "cursorPosition": "mouse"
                     },
                     "categoryField": "price",
                     "categoryAxis": {
                         "axisColor": "#BBB",
                         "minorGridEnabled": true,
                         "position": "bottom",
                         "labelRotation": 45
                     }
                 });

                setTimeout(function() {
                    drawOrderDepthChart();
                }, 60000);
            }
        });
    }
    
    ///
    $('.select-chart button').click(function(event) {
        if(!$(this).hasClass("primary")) {
            $('.select-chart button').removeClass('primary');
            $(this).addClass('primary');
            console.log("data chart: ",$(this).attr('data-chart'));
            var cur_type_chart = $(this).attr('data-chart');
            if(cur_type_chart=='chart_price_volume'){
                drawVisualization();
                $('#dashboard_div').show();
                $('#orderdepth_chart').hide();
            }else{                
                $('#dashboard_div').hide();
                //$('#chartdiv_orderdepth').html("The Order Depth Chart is Coming soon...")
                $('#orderdepth_chart').show('slow');
                drawOrderDepthChart();
            }
        }
    });
</script>
@stop