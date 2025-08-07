<!DOCTYPE html>
<html>

<head>
    <title>Gauge Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        .chart-container {
            width: 100px;
            margin: auto;
            text-align: center;
            border: 2px solid #0b3a4b;
            border-radius: 8px;
            padding: 10px;
        }

        h3 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #0b3a4b;
        }
    </style>
</head>

<body>

    <div class="chart-container">
        <h3>Analysis % Complete of the Open Claim Files</h3>
        <canvas id="gaugeChart" ></canvas>
    </div>

    <script>
        const ctx = document.getElementById('gaugeChart').getContext('2d');

        const percentage = 43; // Change this value (0–100)
        const segmentSizes = [10, 15, 20, 25, 30]; // must sum to 100
        const segmentColors = ['#39ab19', '#9cd323', '#fbc02d', '#ff8000', '#d32f2f'];
        let startThickness = [];
        let endThickness = [];
        startThickness[0] = 1; // px
        endThickness[0] = 2; // px
        startThickness[1] = 2; // px
        endThickness[1] = 4; // px
        startThickness[2] = 4; // px
        endThickness[2] = 7; // px
        startThickness[3] = 7; // px
        endThickness[3] = 11; // px
        startThickness[4] = 11; // px
        endThickness[4] = 12.5; // px

        const taperedArcPlugin = {
            id: 'taperedArc',
            beforeDatasetsDraw(chart) {
                const {
                    ctx,
                    chartArea,
                    chartArea: {
                        top,
                        bottom,
                        left,
                        right
                    }
                } = chart;
                const cx = left + (right - left) / 2;
                const cy = bottom;
                const radius = Math.min(right - left, bottom - top) / 2;

                const total = segmentSizes.reduce((a, b) => a + b, 0);
                let startAngle = Math.PI;

                for (let i = 0; i < segmentSizes.length; i++) {
                    const segAngle = (segmentSizes[i] / total) * Math.PI;
                    const endAngle = startAngle + segAngle;

                    // Draw with taper from startThickness → endThickness within the segment
                    ctx.beginPath();
                    ctx.strokeStyle = segmentColors[i];
                    ctx.lineCap = 'butt';

                    // We draw tiny arcs from start to end, increasing thickness gradually
                    const steps = 50;
                    for (let s = 0; s < steps; s++) {
                        const t1 = s / steps;
                        const t2 = (s + 1) / steps;
                        const angle1 = startAngle + (segAngle * t1);
                        const angle2 = startAngle + (segAngle * t2);

                        const thickness1 = startThickness[i] + (endThickness[i] - startThickness[i]) * t1;
                        const thickness2 = startThickness[i] + (endThickness[i] - startThickness[i]) * t2;

                        // Draw arc segment with average thickness
                        const avgThickness = (thickness1 + thickness2) / 2;
                        ctx.lineWidth = avgThickness;
                        ctx.beginPath();
                        ctx.arc(cx, cy, radius - avgThickness / 2, angle1, angle2);
                        ctx.stroke();
                    }

                    startAngle = endAngle;
                }
            }
        };

        const needlePlugin = {
            id: 'needle',
            afterDatasetsDraw(chart) {
                const {
                    ctx,
                    chartArea: {
                        width,
                        height,
                        top
                    }
                } = chart;
                const total = segmentSizes.reduce((a, b) => a + b, 0);
                const angle = Math.PI + (percentage / total) * Math.PI;

                const cx = width / 2;
                const cy = chart._metasets[0].data[0].y;

                ctx.save();
                ctx.translate(cx, cy);
                ctx.rotate(angle);
                ctx.beginPath();
                ctx.moveTo(0, -2);
                ctx.lineTo(height - top - 10, 0);
                ctx.lineTo(0, 2);
                ctx.fillStyle = '#0b3a4b';
                ctx.fill();

                ctx.beginPath();
                ctx.arc(0, 0, 6, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();

                ctx.font = 'bold 16px Arial';
                ctx.fillStyle = '#0b3a4b';
                ctx.textAlign = 'center';
                ctx.fillText(percentage + '%', cx, cy + 30);
            }
        };

        const gaugeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Very Low', 'Low', 'Medium', 'High', 'Critical'],
                datasets: [{
                    data: segmentSizes,
                    backgroundColor: segmentColors,
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                   
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 2,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [taperedArcPlugin, needlePlugin]
        });
    </script>




</body>

</html>
