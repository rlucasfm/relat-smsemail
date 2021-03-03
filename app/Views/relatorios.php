<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title><?= esc($titulo) ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>    

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
</head>

<body>
    <section class="hero is-small is-link">
        <div class="hero-body">
            <p class="title">Relatório de sms</p>
        </div>
    </section>

    <div class="container is-fluid mt-2">
        <div class="columns">
            <div class="column is-6">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            OVERVIEW MENSAL
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="columns">
                            <div class="column">
                                <div class="has-text-centered">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    Enviados
                                </div>                                
                                <div class="enviados"></div>
                            </div>
                            <div class="column">
                                <div class="has-text-centered">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    Não enviados
                                </div>
                                <div class="nao-avaliados"></div>
                            </div>
                            <div class="column">
                                <div class="has-text-centered">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    Não avaliados
                                </div>
                                <div class="nao-enviados"></div>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-full">
                                <div class="graph1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div> 
    
<!-- BAR CHART -->
<script>
    let optionsBar = {
        chart: {
            type: 'bar',
            animations: {
                speed: 600
            }
        },
        dataLabels: {
            enabled: false
        }, 
        plotOptions: {
            bar: {
                distributed: true
            }
        },
        series: [{
            name: 'SMS BestVoice',
            data: [80,40,120,0]
        }],
        xaxis: {
            categories: ['Enviados', 'Não enviados', 'Não avaliados', 'Erro ao enviar']
        },
        theme: {
            palette: 'palette1'
        }
    }

    let chart = new ApexCharts(document.querySelector(".graph1"), optionsBar);
    chart.render();

</script>

<!-- RADIAL ENVIADOS -->
<script>
var optRad1 = {
          series: [30],
          chart: {
          height: 100,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            dataLabels: {
                value: {
                    fontSize: '0px'
                }
            },      
            hollow: {
                size: '50%',
            }
          },
        },
        labels: ['']
        };

        var rad1 = new ApexCharts(document.querySelector(".enviados"), optRad1);
        rad1.render();
</script>

<!-- RADIAL NÃO ENVIADOS -->
<script>
var optRad1 = {
          series: [30],
          chart: {
          height: 100,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            dataLabels: {
                value: {
                    fontSize: '0px'
                }
            },      
            hollow: {
                size: '50%',
            }
          },
        },
        fill: {
            colors: ['#FEB019']
        },
        labels: ['']
        };

        var rad1 = new ApexCharts(document.querySelector(".nao-enviados"), optRad1);
        rad1.render();
</script>

<!-- RADIAL NÃO AVALIADOS -->
<script>
var optRad1 = {
          series: [30],
          chart: {
          height: 100,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            dataLabels: {
                value: {
                    fontSize: '0px'
                }
            },      
            hollow: {
                size: '50%'                
            }
          },
        },
        fill: {
            colors: ['#00E396']
        },
        labels: ['']
        };

        var rad1 = new ApexCharts(document.querySelector(".nao-avaliados"), optRad1);
        rad1.render();
</script>

</body>
</html>