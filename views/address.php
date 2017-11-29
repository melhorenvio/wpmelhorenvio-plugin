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

    .wpme_address ul {
        padding: 15px 0;
    }

    .wpme_address ul li{
        padding: 0;
        margin: 0;
        color: #777789;
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
                        </ul>
                    </div>
                    </label>
                </li>
            </ul>

            <?php
        }
        ?>
    </div>

    <h2>Escolha seus serviços</h2>
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

    <div class="wpme_pluginconf">
        <label>
            <input>
        </label>
    </div>


    <button class="wpme_button" type="submit">Salvar</button>
    </form>
</div>