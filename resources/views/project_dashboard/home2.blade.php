<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautiful Bar Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .chart-container {
            width: 15%;
           height: 70%;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 300;
        }
        
        
    </style>
</head>
<body>
    <div class="chart-container">
        
        <canvas id="myChart"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('myChart').getContext('2d');
            
            // Gradient for the bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(58, 123, 213, 0.8)');
            gradient.addColorStop(1, 'rgba(0, 210, 255, 0.6)');
            
            // Gradient for the hover effect
            const hoverGradient = ctx.createLinearGradient(0, 0, 0, 400);
            hoverGradient.addColorStop(0, 'rgba(58, 123, 213, 1)');
            hoverGradient.addColorStop(1, 'rgba(0, 210, 255, 0.8)');
            
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Sales (in thousands)',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        backgroundColor: gradient,
                        hoverBackgroundColor: hoverGradient,
                        borderColor: 'rgba(58, 123, 213, 1)',
                        borderWidth: 0,
                        borderRadius: 10,
                        hoverBorderWidth: 1,
                        hoverBorderColor: 'rgba(255, 255, 255, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    family: 'Arial'
                                },
                                color: '#666',
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                size: 16,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 14
                            },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Sales: $${context.raw}k`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#666',
                                font: {
                                    size: 12
                                },
                                callback: function(value) {
                                    return '$' + value + 'k';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
            
            // Add animation on load
            let delayed;
            chart.options.animation.onComplete = () => {
                delayed = true;
            };
            chart.options.animation.delay = (context) => {
                let delay = 0;
                if (context.type === 'data' && context.mode === 'default' && !delayed) {
                    delay = context.dataIndex * 150 + context.datasetIndex * 100;
                }
                return delay;
            };
            chart.update();
        });
    </script>
</body>
</html>