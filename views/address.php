
<?php
if(isset($_POST['submit'])){
    $services = array();
    if(isset($_POST['address'])){
        $address = str_replace("\\",'',$_POST['address']);
    }else{
        $address = '';
    }
    if(isset($_POST['services'])){
        foreach ($_POST['services'] as $service){
            array_push($services,$service);
        }
    }
    //Optionals plugin configuration
    $optionals = new stdClass();
    $optionals->CF = isset($_POST['CF'])? true : false;
    $optionals->AR = isset($_POST['AR'])? true : false;
    $optionals->MP = isset($_POST['MP'])? true : false;
    $optionals->VD = isset($_POST['VD'])? true : false;
    $optionals->MR = isset($_POST['MR'])? true : false;

    $optionals->DE = isset($_POST['DE'])? $_POST['DE'] : "";
    $optionals->PL = isset($_POST['PL'])? $_POST['PL'] : "";

    if(defineConfig($address,json_encode($services),json_encode($optionals))){
        '<div class="notice notice-success is-dismissible\">
                <h2>Configurações alteradas com sucesso</h2>
                <p>Configurações alteradas com sucesso</p>
            </div>';
    }else{
        '<div class="notice notice-error is-dismissible\">
                <h2>Não foi possível alterar</h2>
                <p>Tente novamente mais tarde</p>
            </div>';
    }

}
?>




<style>
    ::-webkit-scrollbar              { background-color: rgba(50,50,50,0.7);width: 8px; height: 8px; border-radius: 5px;}
    ::-webkit-scrollbar-button       { display: none}
    ::-webkit-scrollbar-track        { /* 3 */ }
    ::-webkit-scrollbar-track-piece  { }
    ::-webkit-scrollbar-thumb        {  background-color: rgba(255,255,255,1);
        border-radius: 5px;
    }
    ::-webkit-scrollbar-corner       { display: none;}
    ::-webkit-resizer                { display: none; }

    .wpme_button{
        padding: 8px 30px;
        border-radius: 30px;
        border: solid 1px #e7505a;
        color: #e7505a;
        background-color: rgba(0,0,0,0);
        font-size: 1.300rem;
        display: inline-block;
        transition: 200ms;
        margin: 5px;
    }

    .wpme_button:hover{
        padding: 8px 30px;
        border-radius: 30px;
        border: solid 1px #e7505a;
        color: #FFFFFF;
        transition: 200ms;
        background-color: #e7505a;
        font-size: 1.300rem;
        display: inline-block;
        margin: 5px;
        cursor: pointer !important;
    }

    .wpme_button:active{
        outline: none;
        padding: 8px 30px;
        border-radius: 30px;
        border: solid 1px #e7505a;
        color: #FFFFFF;
        transition: 200ms;
        background-color: #e7505a;
        font-size: 1.300rem;
        display: inline-block;
        margin: 5px;
        cursor: pointer !important;
    }

    .wpme_button:focus{
        outline: none;
    }

    .wpme_config h2{
        text-align: center;
    }

    .wpme_config{
        text-align: center;
    }

    .wpme_options{
        display: inline-block;
        float:left;
        overflow-y:hidden;
        overflow-x: auto;
    }

    .wpme_service{
        display: inline-block;
        float: left;
        width: 200px;
        border-radius: 5px;
        margin: 0 0 15px;
        border:solid 1px #cccccc;
        padding: 20px;
        background-color: #fefeff;
        height: 80px;
    }

    .wpme_service_body{
        text-align: center;
    }

    .wpme_service_body img{
        width: 120px;
        margin: 10px auto;
    }

    .wpme_service_header{
        width: 100%;
        border-bottom: solid 1px #cccccc;
    }

    .wpme_service_header h2{
        display: inline-block;
        padding:0 10px ;
        margin: 0 0 15px;
        text-align: center;
        color: #777789;
        font-weight: 400;
        font-size: 1.010rem;
    }

    .wpme_options{
        list-style-type: none;
        padding: 15px 0;
        text-align: center;
    }

    .wpme_options li{
        list-style: none;
        display: inline-block;
        padding: 0;
        margin: 0 7px;
        color: #777789;
    }


    .wpme_flex{
        display: flex;
        float: left;
        width: 100%;
        position: relative;
        padding: 0;
        flex:1 ;
        order: 3;
        overflow-y: hidden;
        overflow-x: auto;
    }

    .wpme_address{
        text-align: left;
        border-radius: 5px;
        border: solid 1px #cccccc;
        padding: 20px;
        min-width: 300px;
        max-width: 500px;
        margin:15px 7px;
        background: #fefeff;
    }

    .wpme_company{
        text-align: left;
        border-radius: 5px;
        border: solid 1px #cccccc;
        padding: 20px;
        width: 300px;
        min-width: 300px;
        margin:15px 7px;
        background: #fefeff;
    }

    .wpme_address .wpme_address-top{
        width: 100%;
        border-bottom: solid 1px #ddddef;

    }

    .wpme_address .wpme_address-top h2{
        display: inline-block;
        padding:0 20px ;
        margin: 0 0 15px;
        text-align: center;
        color: #777789;
        font-weight: 300;
        font-size: 1.300rem;
    }

    input[type=checkbox].toggle{
        margin:3px;
        -webkit-appearance: none;
        -moz-appearance: none;
        color: #2bf;
        width: 4em;
        height: 1.7em;
        border-radius: 2em;
        background-color: #888;
        transition: background-color 0.3s ease-in-out;
        outline: 0;
        position: relative;
    }

    input[type=checkbox].toggle:checked{
        background-color: #2bf;
        color: #2bf;
        outline: 0;
    }

    input[type=checkbox].toggle:checked::before{
        opacity: 0;        outline: 0;
    }

    input[type=checkbox].toggle::after{
        content:'';
        padding: 0;
        left:0;
        position: absolute;
        background-color: #fff;
        border-radius: 50%;
        width: 1.6em;
        height: 1.6em;
        transform: scale(1.2);
        transition: 0.3s;
        box-shadow:0 0 4px #555;
    }

    input[type=checkbox].toggle:checked::after{
        content:'';
        padding: 0;
        left:2.4em;
        position: absolute;
        background-color: #fff;
        border-radius: 50%;
        width: 1.6em;
        height: 1.6em;
        box-shadow:1px 1px 3px #888;
        transform: scale(1.2);
        transition: 0.3s;
    }


    .wpme_address ul {
        padding: 15px 0;
    }

    .wpme_address ul li{
        padding: 0;
        margin: 0;
        color: #777789;
    }

    .wpme_divtext{
        display: inline-block;
        margin: 30px;
    }

    option,select{
        font-size: .650rem;
    }

    .wpme_divtext div{
        display: inline-block;
        width: 190px;
        padding: 5px;
        border-radius: 5px;
        border: solid 1px #ccc;
        margin: 15px;
        background-color: #f5f5f5;
    }

    .wpme_divtext label{
        color: #888;
        line-height: 40px;
        width: 100%;
        display: inline-block;
    }

    .wpme_pluginconf{
        display: block;
        float: left;
        width: 100%;
        margin: auto;
    }

    .wpme_pluginconf label{
        display: inline-block;
        width: 100%;
        padding: 3px;
    }
    .wpme_pluginconf label{
        display: inline-block;
        width: 100%;
        padding: 3px;
    }


    .wpme_pluginconf div{
        width: 200px;
        padding: 5px;
        text-align: center;
        display: inline-block;
        float: left;
    }

    .wpme_basepadding{
        display: inline-block;
        text-align: center;
    }

    .addenderecos{
        text-decoration: none;
    }

    .addenderecos:hover{

    }
</style>
<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
    <div class="wpme_config">
        <h2>Escolha o endereço para cálculo de frete</h2>
        <div class="wpme_flex">
            <?php
            $addresses = getApiAddresses();
            $companies = getApiCompanies();
            $saved_address = json_decode(get_option('wpme_address'));
            if(isset($addresses['data'])){
            foreach ($addresses['data'] as $address){

                ?>

                <ul class="wpme_address">
                    <li><label for="<?=$address->id?>">
                            <div class="wpme_address-top"><input type="radio" name="address" value='<?php echo json_encode($address) ?>' id="<?=$address->id?>" <?= $address->id == $saved_address->id? "checked" :""?>     required ><h2><?= $address->label ?></h2>
                            </div>
                            <div class="wpme_address-body">
                                <ul>
                                    <li><?= $address->address?>,<?= $address->number?> - <?= $address->complement?></li>
                                    <li><?= $address->district?> - <?= $address->city->city?> / <?= $address->city->state->state_abbr?></li>
                                    <li>CEP: <?=$address->postal_code?></li>
                                </ul>
                                <label>Escolha a Agencia Jadlog</label>
                                <select>

                                <?php
                                $agencias = getAgencies('Brazil',$address->city->state->state_abbr,$address->city->city);
                                if(count($agencias) < 1){
                                    $agencias = getAgencies('Brazil',$address->city->state->state_abbr);
                                }
                                var_dump($agencias);
                                foreach($agencias as $agency){
                                    var_dump($agency);
                                    ?>

<!--                                    --><?php //var_dump($agency)?>
                                    <option value="<?=$agency->id?>"><?=$agency->address->address?>, <?=$agency->address->number?>-<?=$agency->address->district?> </option>
                                    <?php
                                } ?>

                                </select>
                            </div>
                        </label>
                    </li>
                </ul>

                <?php
            }
            ?>
            <div class="wpme_address">
                <a href="https://www.melhorenvio.com.br/painel/gerenciar/perfil" class="addenderecos">
                    <div class="wpme_address-top">
                        <h2> <span class="dashicons dashicons-plus"></span> Adicionar Endereços</h2>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <h2 style="text-align: center; margin: 15px;">Escolha a empresa para a compra de fretes</h2>
    <label>
        <div class="wpme_flex">
            <ul class="wpme_address">
                <?php foreach ($companies['data'] as $company){?>
            <li>
                <div class="wpme_address-top">
                    <input type="radio">
                    <h2><?= $company->name?></h2>
                </div>
                <div class="wpme_address-body">
                    <ul>
                        <li>CNPJ: <?= $company->document?></li>
                        <li> ID: <?= $company->state_register?></li>
                    </ul>


                </div>
            </li>
                <?php } ?>
            </ul>
        </div>
    </label>
    <div class="wpme_basepadding">
        <h2>Selecione seus métodos de envio</h2>
        <ul class="wpme_options">
            <?php
            $services = getApiShippingServices();
            $active_services = is_array(json_decode(get_option('wpme_services')))? json_decode(get_option('wpme_services')) : array();
            foreach($services as $i => $service){
                ?><li>
                <label>
                    <div class="wpme_service">
                        <div class="wpme_service_header">
                            <input type="checkbox" name="services[<?= $i ?>]" value="<?= $service->id ?>" <?= in_array($service->id,$active_services)? "checked" : ""?> >
                            <h2><?= $service->company->name?>  <?= $service->name?></h2>
                        </div>
                        <div class="wpme_service_body">
                            <img src="<?=$service->company->picture?>">
                        </div>
                    </div>
                </label>
                </li>
                <?php
            }
            ?>
        </ul>

        <div class="wpme_pluginconf">
            <?php

            $saved_optionals = json_decode(get_option('wpme_pluginconfig'));


            ?>
            <h2>Funcionamento do Plugin</h2>

            <div>
                <label title="Disponibiliza o método de envio para o seu cliente" for="calculo_fretes">
                    Calculo de fretes
                </label >
                <input type="checkbox" class="toggle" id="calculo_fretes" name="CF" <?= $saved_optionals->CF ? "checked" : "" ?>>
            </div>
            <div>
                <label title="Adiciona o aviso de recebimento na cotação de frete" for="aviso_recebimento">
                    Aviso de Recebimento
                </label>
                <input type="checkbox" class="toggle" id="aviso_recebimento" name="AR" <?= $saved_optionals->AR ? "checked" : "" ?>>
            </div>
            <div>
                <label title="Adiciona mão própria na cotação de frete" for="mao_propria">
                    Mão Propria</label>
                <input type="checkbox" class="toggle" id="mao_propria" name="MP" <?= $saved_optionals->MP ? "checked" : "" ?>>
            </div>
            <div>
                <label title="Declara o valor dos produtos enviados para a transportadora" for="valor_declarado">
                    Valor Declarado
                </label >
                <input type="checkbox" class="toggle" id="valor_declarado" name="VD" <?= $saved_optionals->VD ? "checked" : "" ?>>
            </div>
            <div>
                <label title="Adiciona automáticamente a um sistema de rastreio com notificações para os clientes" for="melhor_rastreio">
                    Adicionar ao Melhor Rastreio
                </label>
                <input type="checkbox" class="toggle" id="melhor_rastreio" name="MR" <?= $saved_optionals->MR ? "checked" : "" ?>>
            </div>

        </div>

        <div class="wpme_divtext">
            <div>
                <label title="Dias a mais a serem adicionados na exibição do Prazo de Entrega">
                    Dias extras
                </label>
                <input type="text" name="DE" value="<?= $saved_optionals->DE?>">
            </div>
            <div>
                <label title="Porcentagem a ser adicionada sobre o valor do frete pra você lojista">
                    Porcentagem de lucro
                </label>
                <input type="text" name="PL" value="<?= $saved_optionals->PL?>">
            </div>
        </div>
        <div>
            <button class="wpme_button" type="submit" name="submit">Salvar</button>
        </div>
    </div>
</form>

<?php
}else{
    echo '<div class="notice notice-error is-dismissible\">
                        <h2>Não foi possível alterar</h2>
                        <p>Tente novamente mais tarde</p>
                    </div>';
}


?>