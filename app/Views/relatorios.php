<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="favicon.ico" />
    <title><?= esc($titulo) ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>      

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>    
    <script src="popover.js"></script>  
    
    <style>
        .s-pre-con{
            display: none;
            z-index: 99999;
            position: fixed;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;      
            background: rgba(220, 230, 232, 0.6);               
        }
        .s-pre-con img{
            height: 50px; 
            width: 50px;
            z-index: 10;
            position: absolute;
            left: 50%;
            top: 35%;
        }
    </style>

</head>
<body>    
    <section class="hero is-small is-link">
        <div class="hero-body">
            <p class="title">Relatório de SMS</p>
        </div>
    </section>    

    <div class="container is-fluid mt-4">
        <div class="columns">
            <div class="column is-8">
                <div class="card">
                    <header class="card-header">
                        <div class="container is-fluid">
                            <div class="columns">
                                <div class="column">
                                    <p class="card-header-title">
                                        OVERVIEW MENSAL                           
                                    </p>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column mb-2">                                    
                                    <div class="columns is-desktop">
                                        <div class="column">
                                            <div class="field">
                                                <label for="datainicio">Data Início</label>
                                                <div class="control">
                                                    <input type="date" class="input" id="datainicio" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="field">
                                                <label for="datafim">Data Fim</label>
                                                <div class="control">
                                                    <input type="date" class="input" id="datafim" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="column">
                                            <div class="field">
                                                <label for="banco">Banco</label>
                                                <div class="control">
                                                    <input type="number" class="input" id="banco">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column" style="display: flex; align-items:center">
                                            <div class="field">                                                
                                                <button type="button" class="button is-primary mt-5" id="btnBuscar">Buscar</button>
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
                                    Confirmados
                                </div>                                
                                <div class="enviados"></div>
                            </div>
                            <div class="column">
                                <div class="has-text-centered">
                                    <i class="bx bx-user mr-25 align-middle"></i>
                                    Não entregues
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
                <div class="columns">
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
                <div class="columns mt-1">
                    <div class="card" style="width: 100%;">
                        <header class="card-header">
                            <h5 class="card-header-title">Relatório de eventos</h5>
                        </header>
                        <div class="card-content">                        
                            <div class="columns">
                                Confira os eventos relacionados ao sms enviados
                            </div>
                            <div class="columns">
                                <div class="column has-text-centered">                            
                                    <button type="button" class="button is-success" onclick="toggleModal('modal-eventos')" id="btnEventos">Abrir</button>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </div>                
            </div>            
        </div>
    </div> 

    <div class="modal" id="modal-eventos">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
            <p class="modal-card-title">Relatório de Eventos</p>
            <button type="button" class="mod-close delete" aria-label="close" data-target="modal-eventos"></button>
            </header>
            <section class="modal-card-body">
                <div class="columns mt-1 ml-2 has-text-centered is-vcentered">
                    <div class="column">
                        <button type="button" class="button is-link" id="btnEventSelect">Selecionar eventos</button>
                    </div>
                    <div class="column">
                        <div class="columns has-text-centered is-vcentered">
                            <div class="column">
                                <div class="field mb-5">
                                    <label for="diasAte">Dias a considerar</label>
                                    <div class="control">
                                        <input type="number" class="input" id="diasate" name="diasate" value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <button class="button is-primary" id="btnBuscarEvent">Buscar</button>
                            </div>
                        </div>                                            
                    </div>                    
                </div>
                <hr>
                <div class="columns mt-1 ml-2">

                    <table class="table is-striped">
                        <thead id="tabHead"></thead>
                        <tbody id="tabBody"></tbody>
                    </table>

                </div>
            </section>
            <footer class="modal-card-foot">            
                <button type="button" class="button is-success" id="btnExportEvent">Exportar XLSX</button>
                <button type="button" class="button mod-close" data-target="modal-eventos">Fechar</button>
            </footer>
        </div>
    </div>

    <div class='s-pre-con'>
        <img src="loader.gif" alt="Loading cool animation">
    </div>            
                       
<script>

    $('#btnEventSelect').fu_popover({
        title: 'Eventos',        
        placement: 'right',
        dismissable: true,
        width: '240px',
        content: `
        <form id="eventosRelat">
            <div class="columns">
                <div class="column">
                    <label class="checkbox"><input type="checkbox" name="5" value="true" id="check5" checked> 5</label>                
                </div>
                <div class="column">
                    <label class="checkbox"><input type="checkbox" name="31" value="true" id="check31"> 31</label>                
                </div>
                <div class="column">
                    <label class="checkbox"><input type="checkbox" name="9023" value="true" id="check9023"> 9023</label>               
                </div>
            </div> 
            <div class="columns">
                <div class="column">
                    <label class="checkbox"><input type="checkbox" name="9005" value="true" id="check9005"> 9005</label>
                </div>
                <div class="column">
                    <label class="checkbox"><input type="checkbox" name="9034" value="true" id="check9034"> 9034</label>
                </div>
            </div>
        </form>
        `
    });   
    
    $('#btnBuscarEvent').on('click', function(){
        let dataForm = $('#eventosRelat').serializeArray();
        $('.s-pre-con').show();
        reqEventos(dataForm, 'relatorio/buscarEventos', function(counts){            
            let htmlHead = `<tr><th><abbr title="Código">Cod.</abbr></th>`;
            let htmlBody = `<tr><th><abbr title="Quantidade">Qnt.</abbr></th>`;
            Object.keys(counts).forEach((element, index) => {
                htmlHead += `<th>${element}</th>`;
                htmlBody += `<td>${counts[element]}</td>`;
            });
            htmlHead += "</tr>";
            htmlBody += "</tr>";
            $('#tabHead').html(htmlHead);
            $('#tabBody').html(htmlBody);
        });               
    });

    $('#btnExportEvent').on('click', function(){
        let dataForm = $('#eventosRelat').serializeArray();
        dataForm = JSON.stringify(dataForm);

        let form = $(document.createElement('form'));

        form.attr('action', 'relatorio/exportEventos');
        form.attr('method', 'POST');
        form.attr('target', '_blank');
        let datainicio = $('<input>').attr('type', 'hidden').attr('name', 'datainicio').val($('#datainicio').val());
        let datafim = $('<input>').attr('type', 'hidden').attr('name', 'datafim').val($('#datafim').val());
        let banco = $('<input>').attr('type', 'hidden').attr('name', 'banco').val($('#banco').val());
        let eventos = $('<input>').attr('type', 'hidden').attr('name', 'eventos').val(dataForm);
        let diasate = $('<input>').attr('type', 'hidden').attr('name', 'diasate').val($('#diasate').val());
        form.append(datainicio);
        form.append(datafim);
        form.append(banco);
        form.append(eventos);
        form.append(diasate);
        form.appendTo(document.body);
        form.submit();
        form.remove();
    });
</script>

<script>
    function reqEventos(dataF, endpoint,callback){    
       $.ajax({
           method: 'POST',
           url: endpoint,
           data: {codes: dataF, dataInicio: $('#datainicio').val(), dataFim: $('#datafim').val(), diasate: $('#diasate').val(), banco: $('#banco').val()}
       }).done((dataRes) => {
            $('.s-pre-con').hide();
           //console.log(dataRes);
           counts = JSON.parse(dataRes);           
           callback(counts, dataRes);
       }).fail(function(jqXHR, textStatus, errorThrown) {
            $('.s-pre-con').hide();
            console.log("Error: "+textStatus);
            callback({});
       });
   }
</script>

<script>    
    function toggleModal(id){
        let target = $('#'+id);
        if(target.hasClass("is-active")){
            target.removeClass("is-active");
        } else {
            target.addClass("is-active");
        }
    }

    $('.mod-close').on('click', function() {
        toggleModal($(this).attr('data-target'));
    });
</script>

<script>
    $('#btnExport').on('click', () => {
        let form = $(document.createElement('form'));

        form.attr('action', 'relatorio/downloadSheet');
        form.attr('method', 'POST');
        form.attr('target', '_blank');
        let datainicio = $('<input>').attr('type', 'hidden').attr('name', 'datainicio').val($('#datainicio').val());
        let datafim = $('<input>').attr('type', 'hidden').attr('name', 'datafim').val($('#datafim').val());
        let banco = $('<input>').attr('type', 'hidden').attr('name', 'id_banco').val($('#banco').val());
        form.append(datainicio);
        form.append(datafim);
        form.append(banco);
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
        updateCharts();
    });  
        
    setInterval(function() {
        updateCharts();        
    }, 10 * 1000)

    function updateCharts()
    {
        $.ajax({
            method: "POST",
            url: "relatorio/buscarDatas",
            data: {dataInicio: $('#datainicio').val(), dataFim: $('#datafim').val(), id_banco: $('#banco').val()},
        }).done((data) => {
            enviados = 0;
            naoavaliados = 0;
            naoenviados = 0; 
            reg_arr = JSON.parse(data);                         

            reg_arr.forEach((obj) => {
                if(obj.statusConf == 'CONFIRMADO'){
                    enviados++;
                }

                if(obj.statusConf == 'NAO_ENTREGUES' || obj.statusConf == 'DESCONHECIDO' || obj.statusConf == 'REJEITADA'){
                    naoenviados++;
                }

                if(obj.avaliado == '0'){
                    naoavaliados++;
                }
            });

            chart.updateSeries([{
                name: 'SMS',
                data: [enviados,naoenviados,naoavaliados]
            }]);

            rad1.updateSeries([100*enviados/(enviados+naoenviados+naoavaliados)]);

            rad2.updateSeries([(100*(naoenviados))/(enviados+naoenviados+naoavaliados)]);

            rad3.updateSeries([100*naoavaliados/(enviados+naoenviados+naoavaliados)]);

        }).fail((jqxhr, textStatus) => {
            console.log(textStatus);
        });
    }
</script>

<!-- BAR CHART -->
<script>            
    let optionsBar = {
        chart: {
            type: 'bar',
            animations: {
                speed: 600
            },
            height: 300,
            // Capturar cliques nas barras
            events: {
                dataPointSelection: function(event, chartContext, config){
                    console.log('Click: '+config.dataPointIndex);
                }
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
            name: 'SMS',
            data: [enviados,naoenviados,naoavaliados]
        }],
        xaxis: {
            categories: ['Confirmados', 'Não entregues', 'Aguardando']
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