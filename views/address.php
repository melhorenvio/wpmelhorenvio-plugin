
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if(isset($_POST['submit'])){
    if(check_admin_referer('wpmelhorenvio_save_address')){
        $services = array();
        if(isset($_POST['address'])){
            $address = wp_filter_nohtml_kses(sanitize_text_field($_POST['address']));

            $add_id = json_decode(str_replace("\\",'',$address));
            if (isset($_POST[$add_id->id])){
                $id_agency = sanitize_key($_POST[$add_id->id]);
                $add_id->agency = $id_agency;
                $address = wp_filter_nohtml_kses(sanitize_text_field(json_encode($add_id)));
            }
        }else{
            $address = '';
        }
        if(isset($_POST['services'])){
            foreach ($_POST['services'] as $service){
                array_push($services,wp_filter_nohtml_kses(sanitize_key($service)));
            }
        }
        if(isset($_POST['company'])){
            $company = wp_filter_nohtml_kses(sanitize_text_field($_POST['company']));
            update_option('wpmelhorenvio_company',$company);
        }else{
            $company = new stdClass();
            $company->cnpj = '';
            $company->ie = '';
            update_option('wpmelhorenvio_company',json_encode($company));
        }
        //Optionals plugin configuration
        //As it isn't saved nor accessed just verified the existance of the variable I didn't filter.
        $optionals = new stdClass();
        $optionals->CF = isset($_POST['CF'])? true : false;
        $optionals->AR = isset($_POST['AR'])? true : false;
        $optionals->MP = isset($_POST['MP'])? true : false;
        $optionals->VD = isset($_POST['VD'])? true : false;
        $optionals->MR = isset($_POST['MR'])? true : false;

        $optionals->DE = isset($_POST['DE'])? wp_filter_nohtml_kses(sanitize_text_field($_POST['DE'])) : "";
        $optionals->PL = isset($_POST['PL'])? wp_filter_nohtml_kses(sanitize_text_field($_POST['PL'])) : "";

        if(wpmelhorenvio_defineConfig(utf8_encode($address),json_encode($services),json_encode($optionals))){
            $url = admin_url('admin.php?page=wpmelhorenvio_melhor-envio-requests');

            wp_redirect($url);
        }else{
            echo  '<div class="notice notice-error is-dismissible\">
                <h2>Não foi possível alterar</h2>
                <p>Tente novamente mais tarde</p>
            </div>';
        }
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
        display: block;
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
        font-size: 1.150rem;
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
        min-width: 90%;
        max-width: 100%;
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

    .wpme_divtext input{
        max-width: 90%;
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
            $addresses = wpmelhorenvio_getApiAddresses();
            $companies = wpmelhorenvio_getApiCompanies();

            $company_addresses = wpmelhorenvio_getApiCompanyAdresses();
            $addresses['data'] = array_merge($addresses['data'],$company_addresses);

            $saved_address = json_decode(str_replace("\\" ,"", get_option('wpmelhorenvio_address')));

            if($saved_address == null){
                $saved_address = new stdClass();
                $saved_address->id = '';
            }
            $saved_company =  json_decode(str_replace("\\",'',get_option('wpmelhorenvio_company')));
            if($saved_company == null){
                $saved_company = new stdClass();
                $saved_company->id = '';
            }
            wp_nonce_field('wpmelhorenvio_save_address');
            if(isset($addresses['data'])){
            foreach ($addresses['data'] as $address){
                ?>

                <ul class="wpme_address">
                    <li><label for="<?=$address->id?>">
                            <div class="wpme_address-top"><input type="radio" name="address" value='<?php echo json_encode($address,JSON_UNESCAPED_UNICODE) ?>' id="<?=$address->id?>"   <?= $address->id == $saved_address->id ? "checked" : ""?>   required ><h2><?= $address->label ?></h2>
                            </div>
                            <div class="wpme_address-body">
                                <ul>
                                    <li><?= $address->address?>,<?= $address->number?> - <?= $address->complement?></li>
                                    <li><?= $address->district?> - <?= $address->city->city?> / <?= $address->city->state->state_abbr?></li>
                                    <li>CEP: <?=$address->postal_code?></li>
                                </ul>
                                <label>Escolha a Agencia Jadlog</label>
                                <select name="<?php echo $address->id ?>">

                                    <?php
                                    $agencias = wpmelhorenvio_getAgencies('Brazil',$address->city->state->state_abbr,$address->city->city);
                                    if(count($agencias) < 1){
                                        $agencias = wpmelhorenvio_getAgencies('Brazil',$address->city->state->state_abbr);
                                    }
                                    foreach($agencias as $agency){
                                        ?>
                                        <option value="<?=$agency->id?>"
                                            <?php
                                            if(isset($saved_address->agency)){
                                                echo $saved_address->agency == $agency->id ? "selected" :" ";
                                            }
                                            ?> ><?=$agency->address->address?>, <?=$agency->address->number?>-<?=$agency->address->district?> </option>
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
    <div class="wpme_flex">

        <?php foreach ($companies['data'] as $company){?>
            <label>
                <ul class="wpme_address">
                    <li>
                        <div class="wpme_address-top">
                            <input type="radio" name="company" value='<?php echo json_encode($company) ?>' <?php


                            ?>  <?= $saved_company->id == $company->id?"checked":""?>>
                            <h2><?= $company->name?></h2>
                        </div>
                        <div class="wpme_address-body">
                            <ul>
                                <li>CNPJ: <?= $company->document?></li>
                                <li> IE: <?= $company->state_register?></li>
                            </ul>


                        </div>
                    </li>
                </ul>
            </label>
        <?php }if(count($companies['data']) < 1){
            echo "<p style='text-align:center;margin:auto;'> Para cadastrar suas lojas vá em <a href='https://www.melhorenvio.com.br/painel/gerenciar/lojas'>Painel Melhor Envio</a> </p>";
        } ?>

    </div>
    <div class="wpme_basepadding">
        <h2>Selecione seus métodos de envio</h2>
        <ul class="wpme_options">
            <?php
            $services = wpmelhorenvio_getApiShippingServices();
            $active_services = is_array(json_decode(get_option('wpmelhorenvio_services')))? json_decode(get_option('wpmelhorenvio_services')) : array();
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
            $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
            if($saved_optionals == null){
                $saved_optionals = new stdClass();
                $saved_optionals->CF = true;
                $saved_optionals->AR = false;
                $saved_optionals->MP = false;
                $saved_optionals->VD = true;
                $saved_optionals->DE = 0;
                $saved_optionals->PL = 0;

            }

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
        </div>

        <div class="wpme_divtext">
            <div>
                <label title="Dias a mais a serem adicionados na exibição do Prazo de Entrega">
                    Dias extras
                </label>
                <input type="text" name="DE" value="<?= $saved_optionals->DE ?>">
            </div>
            <div>
                <label title="Porcentagem a ser adicionada sobre o valor do frete pra você lojista">
                    Porcentagem de lucro
                </label>
                <input type="text" name="PL" value="<?= $saved_optionals->PL ?>">
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