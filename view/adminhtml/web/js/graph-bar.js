define([
    "jquery",
    "chart"
], function (
    $,
    chart
) {
    "use strict";

    $.widget('custom.graph', {

        _create: function () {
            self = this;

            const dataJson = Object.values(this.options.message);

            let data = {
                labels: dataJson[0],
                datasets: []
            };

            dataJson[1].forEach((item, index) => {
                data.datasets.push(item);
            });

            new Chart(this.element, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },

    });

    return $.custom.graph;
});