<style>
    .wpme_flex{
        display: flex;
        float: left;
        width: 100%;
        position: relative;
        flex:1 ;
        order: 3;
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

    .wpme_address-body p{
        padding: 0;
    }
</style>

<div class="wpme_flex">
    <?php
    $addresses = getApiAddresses();

    foreach ($addresses['data'] as $address){

        ?>

        <ul class="wpme_address">
            <li><label for="<?=$address->id?>">
                <div class="wpme_address-top"><input type="radio" name="address"  value="<?=$address->id?>" id="<?=$address->id?>" <?= $address->id == get_option("wpme_address_id") ?"checked" :"" ?> ><h2><?= $address->label ?></h2> </div>
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

