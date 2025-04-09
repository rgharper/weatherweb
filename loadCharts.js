function update_charts(from = null, to = null) {
    let params = ""
    if (!from || !to) {
        params = ""
    }
    else {
        params = new URLSearchParams([
            ['from', from],
            ['to', to]
        ])
    }
    fetch("chartData.php?"+params)
        .then((response) => {
            if(!response.ok){
                throw new Error("Something went wrong!");
            }
            return response.json();
        })
        .then((data) => {

            const inside = {
                timestamp: new Array(),
                temperature: new Array(),
                humidity: new Array(),
            }

            for (let frame of data.inside) {
                inside.timestamp.push(frame.timestamp)

                inside.temperature.push(frame.temperature)
                inside.humidity.push(frame.humidity)
            }

            const outside = {
                timestamp: new Array(),
                temperature: new Array(),
                humidity: new Array(),
                wind: {
                    speed: new Array(),
                    gust: new Array(),
                    direction: new Array(),
                },
            }

            for (let frame of data.outside) {
                outside.timestamp.push(frame.timestamp)

                outside.temperature.push(frame.temperature)
                outside.humidity.push(frame.humidity)

                outside.wind.speed.push(frame.windspeed)
                outside.wind.gust.push(frame.windgust)
                outside.wind.direction.push(frame.winddirection)
            }

            console.log(inside)

            Chart.defaults.font.family = "'Consolas', 'Lucida Console', monospace"
            Chart.defaults.font.weight = "Bold"
            th_options = {
                scales: {
                    humidity:{axis: 'y', id: 'humidity', type: 'linear', min:0, max:100, ticks:{min:0, max:100}, title: {display: true, text:"Humidity"}},
                    temp:{axis: 'y', id: 'temp', type: 'linear', title: {display: true, text:"Temperature"}},
                    time:{axis: 'x', id: 'time', type: "time"}
                },
                elements: {
                    line: {
                        tension: 0.2
                    },
                    point: {
                        radius: 0
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        
            var chartdata = {
                labels: inside.timestamp,
                datasets : [
                    {
                        label: 'Temperature (\u{00B0}C)',
                        xAxisID: 'time',
                        yAxisID: 'temp',
                        borderColor: 'rgb(220, 161, 161)',
                        fill: false,
                        data: inside.temperature,
                    },
                    {
                        label: 'Humidity (%RH)',
                        xAxisID: 'time',
                        yAxisID: 'humidity',
                        borderColor: 'rgb(161, 161, 200)',
                        fill: false,
                        data: inside.humidity,
                    }
                ]
            };
            const inside_th_chart = new Chart("inside_th_chart", {
                type: "line",
                data: chartdata,
                options: th_options
            });
        
            var chartdata = {
                labels: outside.timestamp,
                datasets : [
                    {
                        label: 'Temperature (\u{00B0}C)',
                        xAxisID: 'time',
                        yAxisID: 'temp',
                        borderColor: 'rgb(220, 161, 161)',
                        fill: false,
                        data: outside.temperature
                    },
                    {
                        label: 'Humidity (%RH)',
                        xAxisID: 'time',
                        yAxisID: 'humidity',
                        borderColor: 'rgb(161, 161, 200)',
                        fill: false,
                        data: outside.humidity
                    }
                ]
            };
            const outside_th_chart = new Chart("outside_th_chart", {
                type: "line",
                data: chartdata,
                options: th_options
            });
        
            var chartdata = {
                labels: outside.timestamp,
                datasets : [
                    {
                        label: 'Speed (rpm)',
                        xAxisID: 'time',
                        yAxisID: 'speed',
                        borderColor: 'rgb(161, 220, 185)',
                        fill: false,
                        data: outside.wind.speed
                    },
                    {
                        label: 'Direction',
                        xAxisID: 'time',
                        yAxisID: 'dir',
                        borderColor: 'rgb(161, 191, 220)',
                        fill: false,
                        data: outside.wind.direction
                    },
                    {
                        label: 'Gust (rpm)',
                        xAxisID: 'time',
                        yAxisID: 'gust',
                        borderColor: 'rgb(220, 161, 161)',
                        visible: false,
                        fill: false,
                        data: outside.wind.gust
                    }
                ]
            };
            const outside_w_chart = new Chart("outside_w_chart", {
                type: "line",
                data: chartdata,
                options: {
                    scales: {
                        gust:{axis: "y", id: 'gust', type: 'linear', title: {display: true, text:"Gust"}},
                        dir:{axis: "y", id: 'dir', type: 'linear', min:0, max:16, ticks:{min:0, max:16, stepSize:1,
                            callback: function(value, index, ticks) {
                                labels = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
                                return labels[value];
                            }
                        }, title: {display: true, text:"Direction"}},
                        speed:{axis: "y", id: 'speed', type: 'linear', title: {display: true, text:"Speed"}},
                        time:{axis: "x", id: 'time', type: "time"}
                    },
                    elements: {
                        line: {
                            tension: 0.2
                        },
                        point: {
                            radius: 0
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        })
    //     .catch((error) => {
    //         // This is where you handle errors.
    //         console.log(error)
    // });
}