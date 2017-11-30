<style>

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


    .wpme_config h2{
        text-align: center  ;
    }

    .wpme_options{
        display: inline;
        width: 100%;
        display: flex;
        float:left;
        overflow-y: hidden;
        overflow-x: scroll;

    }

    .wpme_service{
        width: 240px;
        border-radius: 5px;
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
        padding:0 20px ;
        margin: 0 0 15px;
        text-align: center;
        color: #777789;
        font-weight: 300;
        font-size: 1.200rem;
    }

    .wpme_options{
        list-style-type: none;
        padding: 15px 0;
    }

    .wpme_options li{
        list-style: none;
        display: inline-block;
        padding: 0;
        margin: 0 15px;
        color: #777789;
    }


    .wpme_flex{
        display: flex;
        float: left;
        width: 100%;
        position: relative;
        flex:1 ;
        order: 3;
        overflow-y: hidden;
        overflow-x: scroll;
    }

    .wpme_address{
        border-radius: 5px;
        border: solid 1px #cccccc;
        padding: 20px;
        width: 300px;
        margin: 10px;
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
        -moz-appearance: none;
        -webkit-appearance: none;
        appearance: none;
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

    input[type=checkbox].toggle::after{
        content:'';
        padding: 0;
        left:0;
        position: absolute;
        background-color: #fff;
        border-radius: 50%;
        width: 1.6em;
        height: 1.6em;
        transform: scale(1.1);
        transition: 0.3s;
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
        transform: scale(1.1);
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

    .wpme_pluginconf{

    }

    .wpme_pluginconf label{
        display: inline-block;
        width: 100%;
        padding: 3px;
    }

    .wpme_pluginconf div{
        width: 200px;
        text-align: center;
        display: inline-block;
        float: left;
    }




</style>
<div class="wpme_config">
<h2>Escolha o endereço para cálculo de frete</h2>
    <div class="wpme_flex">
        <?php
        $addresses = getApiAddresses();

        foreach ($addresses['data'] as $address){

            ?>

            <ul class="wpme_address">
                <li><label for="<?=$address->id?>">
                    <div class="wpme_address-top"><input type="radio" name="address"  value="<?=$address->id?>" id="<?=$address->id?>" <?= $address->id == get_option("wpme_address_id") ?"checked" :"" ?> ><h2><?= $address->label ?></h2>
                    </div>
                    <div class="wpme_address-body">
                        <ul>
                            <li><?= $address->address?>,<?= $address->number?> - <?= $address->complement?></li>
                            <li><?= $address->district?> - <?= $address->city->city?> / <?= $address->city->state->state_abbr?></li>
                            <li>CEP: <?=$address->postal_code?></li>
                        </ul>
                    </div>
                    </label>
                </li>
            </ul>

            <?php
        }
        ?>
    </div>

    <h2>Selecione seus métodos de envio</h2>
        <ul class="wpme_options">
        <?php
        $services = getApiShippingServices();


        foreach($services as $service){
        ?><li>
                <label>
                <div class="wpme_service">
                    <div class="wpme_service_header">
                            <input type="checkbox">
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
        <h2>Funcionamento do Plugin</h2>
    <div class="wpme_pluginconf">
        <div>
            <label title="Disponibiliza o método de envio para o seu cliente">
                Calculo de fretes
            </label >
            <input type="checkbox" class="toggle">
        </div>
        <div>
            <label title="Adiciona o aviso de recebimento na cotação de frete">
                Aviso de Recebimento
            </label>
                <input type="checkbox" class="toggle">
        </div>
        <div>
        <label title="Adiciona mão própria na cotação de frete">
            Mão Propria</label>
            <input type="checkbox" class="toggle">
        </div>
        <div>
            <label title="Declara o valor dos produtos enviados para a transportadora">
            Valor Declarado
        </label >
            <input type="checkbox" class="toggle">
        </div>
        <div>
        <label title="Adiciona automáticamente a um sistema de rastreio com notificações para os clientes">
            Adicionar ao Melhor Rastreio
        </label>
            <input type="checkbox" class="toggle">
        </div>
        <label>
            Dias extras
            <input type="text">
        </label>
        <label>
            Porcentagem de lucro
            <input type="text">
        </label>

    </div>


    <button class="wpme_button" type="submit">Salvar</button>
    </form>
</div>