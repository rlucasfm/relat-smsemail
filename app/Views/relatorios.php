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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
</head>

<body>
    <section class="hero is-small is-link">
        <div class="hero-body">
            <p class="title">Relatório de sms</p>
        </div>
    </section>

    <div class="container is-fluid mt-4">
        <div class="columns">
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <div class="container is-fluid">
                            <div class="columns">
                                <div class="column is-two-fifths">
                                    <p class="card-header-title">
                                        OVERVIEW MENSAL                           
                                    </p>
                                </div>
                                <div class="column mb-2">                                    
                                    <div class="columns is-desktop">
                                        <div class="column is-one-third">
                                            <div class="field">
                                                <label for="label">Data Início</label>
                                                <div class="control">
                                                    <input type="date" class="input" id="datainicio" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-one-third">
                                            <div class="field">
                                                <label for="label">Data Fim</label>
                                                <div class="control">
                                                    <input type="date" class="input" id="datafim" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="column is-one-third" style="display: flex; align-items:center">
                                            <div class="field">
                                                <button type="button" class="button is-primary" id="btnBuscar">Buscar</button>
                                            </div>
                                        </div>                                       
                                    </div>                                    
                                </div>
                            </div>                        
                        </div>                        
                    </header>
                    <div class="card-content">
                        <div class="columns is-mobile">
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
                                <div class="envio-incompleto"></div>
                            </div>
                            <div class="column">
                                <div class="has-text-centered">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    Aguardando
                                </div>
                                <div class="nao-avaliados"></div>
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
            <div class="column">
                <div class="card">
                    <div class="card-content">                        
                        <div class="columns">
                            Exporte a tabela com os dados entre as datas escolhidas
                        </div>
                        <div class="columns">
                            <div class="column has-text-centered">                            
                                <button type="button" class="button is-success" id="btnExport">Exportar XLSX</button>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div> 

<script>
    $('#btnExport').on('click', () => {
        let form = $(document.createElement('form'));

        form.attr('action', 'relatorio/downloadSheet');
        form.attr('method', 'POST');
        form.attr('target', '_blank');
        let datainicio = $('<input>').attr('type', 'hidden').attr('name', 'datainicio').val($('#datainicio').val());
        let datafim = $('<input>').attr('type', 'hidden').attr('name', 'datafim').val($('#datafim').val());
        form.append(datainicio);
        form.append(datafim);
        form.appendTo(document.body);
        form.submit();
        form.remove();
    });
</script>

<!-- AJAX BUSCAR -->
<script>    
    let enviados = 0;
    let naoavaliados = 0;
    let naoenviados = 0;    

    $('#btnBuscar').on('click', () => {        

        $.ajax({
            method: "POST",
            url: "relatorio/buscarDatas",
            data: {dataInicio: $('#datainicio').val(), dataFim: $('#datafim').val()},
        }).done((data) => {
            enviados = 0;
            naoavaliados = 0;
            naoenviados = 0; 
            reg_arr = JSON.parse(data);                         

            reg_arr.forEach((obj) => {
                if(obj.statusDesc == 'ENVIADA'){
                    enviados++;
                }

                if(obj.statusConf == 'NAO_ENTREGUE' || obj.statusDesc == 'DESCONHECIDO' || obj.statusDesc == 'REJEITADA'){
                    naoenviados++;
                }

                if(obj.avaliado == '0'){
                    naoavaliados++;
                }
            });

            chart.updateSeries([{
                name: 'SMS BestVoice',
                data: [enviados,naoenviados,naoavaliados]
            }]);

            rad1.updateSeries([100*enviados/(enviados+naoenviados+naoavaliados)]);

            rad2.updateSeries([(100*(naoenviados))/(enviados+naoenviados+naoavaliados)]);

            rad3.updateSeries([100*naoavaliados/(enviados+naoenviados+naoavaliados)]);

        }).fail((jqxhr, textStatus) => {
            console.log(textStatus);
        });
    });    
</script>

<!-- BAR CHART -->
<script>            
    let optionsBar = {
        chart: {
            type: 'bar',
            animations: {
                speed: 600
            },
            height: 300
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
            data: [enviados,naoenviados,naoavaliados]
        }],
        xaxis: {
            categories: ['Enviados', 'Não enviados', 'Aguardando']
        },
        theme: {
            palette: 'palette1'
        }
    }

    let chart = new ApexCharts(document.querySelector(".graph1"), optionsBar);
    chart.render();

    // <!-- RADIAL ENVIADOS -->
    var optRad1 = {
        series: [100*enviados/(enviados+naoenviados+naoavaliados)],
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

    // <!-- RADIAL ENVIO INCOMPLETO -->
    var optRad2 = {
        series: [(100*(naoenviados))/(enviados+naoenviados+naoavaliados)],
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
        colors: ['#00E396']
    },
    labels: ['']
    };

    var rad2 = new ApexCharts(document.querySelector(".envio-incompleto"), optRad2);
    rad2.render();

    // <!-- RADIAL NÃO AVALIADOS -->
    var optRad3 = {
        series: [100*naoavaliados/(enviados+naoenviados+naoavaliados)],
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
        colors: ['#FEB019']
    },
    labels: ['']
    };

    var rad3 = new ApexCharts(document.querySelector(".nao-avaliados"), optRad3);
    rad3.render();

    
</script>

</body>
</html>