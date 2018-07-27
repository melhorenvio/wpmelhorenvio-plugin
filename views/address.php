
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
                    $add_id->agency =  sanitize_key($_POST[$add_id->id]);
                    $address = wp_filter_nohtml_kses(sanitize_text_field(json_encode($add_id,JSON_UNESCAPED_UNICODE)));
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
            $optionals->CF  = isset($_POST['CF'])? true : false;
            $optionals->AR  = isset($_POST['AR'])? true : false;
            $optionals->MP  = isset($_POST['MP'])? true : false;
            $optionals->VD  = isset($_POST['VD'])? true : false;
            $optionals->MR  = isset($_POST['MR'])? true : false;
            $optionals->AA  = isset($_POST['AA'])? true : false;
            $optionals->SDP = isset($_POST['SDP'])? true : false;
            $optionals->SSL = isset($_POST['SSL'])? true : false;

            $optionals->largura_padrao     = isset($_POST['largura_padrao'])? $_POST['largura_padrao'] : 12;
            $optionals->comprimento_padrao = isset($_POST['comprimento_padrao'])? $_POST['comprimento_padrao'] : 17;
            $optionals->altura_padrao      = isset($_POST['altura_padrao'])? $_POST['altura_padrao'] : 4;
            $optionals->peso_padrao        = isset($_POST['peso_padrao'])? $_POST['peso_padrao'] : 0.5;

            $optionals->DE = isset($_POST['DE'])? (int) wp_filter_nohtml_kses(sanitize_text_field($_POST['DE'])) : "";
            $optionals->PL = isset($_POST['PL'])? (float) wp_filter_nohtml_kses(sanitize_text_field($_POST['PL'])) : "";

            if(wpmelhorenvio_defineConfig($address,json_encode($services),json_encode($optionals))){
                // $url = admin_url('admin.php?page=wpmelhorenvio_melhor-envio-config');
                $url = admin_url('admin.php?page=wc-settings&tab=shipping');
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

<?php
    $addresses = wpmelhorenvio_getApiAddresses();
    $companies = wpmelhorenvio_getApiCompanies();

    $company_addresses = wpmelhorenvio_getApiCompanyAdresses();

    if (!is_array($addresses['data'])) {
        return;
    }

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
?>
<?php if(isset($addresses['data'])): ?>

    <form class="wpme_content" action="<?=$_SERVER['REQUEST_URI']?>" method="post">
        <div class="wpme_config">
            <h2>Escolha o endereço para cálculo de frete</h2>
            <div class="wpme_flex">
                <ul class="wpme_address">
                    <?php foreach ($addresses['data'] as $address): ?>
                        <li>
                            <label for="<?=$address->id?>">
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
                                        ?>
                                        <?php foreach($agencias as $agency): ?>
                                            <option value="<?= $agency->id ?>"
                                                <?php
                                                    if(isset($saved_address->agency)) {
                                                        echo $saved_address->agency == $agency->id ? "selected" :" ";
                                                    }
                                                ?>>
                                                <?= $agency->address->address ?>, <?= $agency->address->number ?>-<?= $agency->address->district ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </label>
                        </li>
                    <?php endforeach; ?>

                    <li>
                        <a href="https://www.melhorenvio.com.br/painel/gerenciar/perfil" class="addenderecos">
                            <div class="wpme_address-top">
                                <h2> <span class="dashicons dashicons-plus"></span> Adicionar Endereços</h2>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="wpme_config">
            <h2>Escolha a empresa para a compra de fretes</h2>
            <div class="wpme_flex">
                <?php foreach ($companies['data'] as $company): ?>
                    <label>
                        <ul class="wpme_address">
                            <li>
                                <div class="wpme_address-top">
                                    <input type="radio" name="company" value='<?php echo json_encode($company) ?>' <?= $saved_company->id == $company->id? "checked":"" ?>>
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
                <?php endforeach; ?>

                <?php if(count($companies['data']) < 1): ?>
                    <p class="txtNoEmployee">Para cadastrar suas lojas no seu <a href='https://www.melhorenvio.com.br/painel/gerenciar/lojas'>painel de controle do Melhor Envio</a>.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="wpme_basepadding">
            <!-- <h2>Métodos de envio</h2> 
            <span>Você precisa definir quais os métodos de envio deseja utilizar, clique no link abaixo para escolher os métodos de envio.</span></br>
            <a target="_blank" class="btn comprar-hard" href="admin.php?page=wc-settings&tab=shipping">Definir metódos de envio</a>
            </br>
            </br> -->

            <div class="wpme_pluginconf">
                <?php
                    $saved_optionals = json_decode(get_option('wpmelhorenvio_pluginconfig'));
                    if($saved_optionals == null){
                        $saved_optionals = new stdClass();
                        $saved_optionals->CF = true;
                        $saved_optionals->AR = false;
                        $saved_optionals->MP = false;
                        $saved_optionals->AA = false;
                        $saved_optionals->VD = true;
                        $saved_optionals->SSL = true;
                        $saved_optionals->SDP = false;
                        $saved_optionals->DE = 0;
                        $saved_optionals->PL = 0;
                        $saved_optionals->largura_padrao = 11;
                        $saved_optionals->comprimento_padrao = 16;
                        $saved_optionals->altura_padrao = 2;
                        $saved_optionals->peso_padrao = 0.5;
                    }

                    wp_nonce_field('wpmelhorenvio_save_address');
                ?>
                </br>
                <hr>

                <h2>Funcionamento do Plugin</h2>
                <div>
                    <input type="checkbox" class="toggle" id="autocomplete_address" name="AA" <?= $saved_optionals->AA ? "checked" : "" ?>>
                    <label title="Usar autocomplete de endereço" for="autocomplete_address">Usar autocomplete de endereço</label>
                </div>
                <hr>

                <h2>Medidas para embalagens padrão</h2>
                <span>Essas medidas são usadas quando o produto não tiver tamanhos cadastrados. As medidas do produto são de extrema importância para a cotaçaõ do valor do frete.</span>
                </br>
               
                <!-- Matheus Thomaz, ajuda para otimizar isso para os padrões de Frontend do projeto -->
                <table>
                    <tr>
                        <td>
                            <label style="text-align:center;" title="Largura padrão" for="largura_padrao">Largura padrão (em cm)</label>
                            <input style="text-align:center; width: 50%;" type="number" class="toggle input_packages" id="largura_padrao" name="largura_padrao" value="<?php echo $saved_optionals->largura_padrao ?>" />
                        </td>
                        <td> 
                            <label style="text-align:center;" title="Comprimento padrão" for="comprimento_padrao">Comprimento padrão (em cm)</label>
                            <input style="text-align:center; width: 50%;" type="number" class="toggle input_packages" id="comprimento_padrao" name="comprimento_padrao" value="<?php echo $saved_optionals->comprimento_padrao ?>" />
                        </td>
                        <td>
                            <label style="text-align:center;" title="Altura padrão" for="altura_padrao">Altura padrão (em cm)</label>
                            <input style="text-align:center; width: 50%;" type="number" class="toggle input_packages" id="altura_padrao" name="altura_padrao" value="<?php echo $saved_optionals->altura_padrao ?>" />
                        </td>
                        <td> 
                            <label style="text-align:center;" title="Peso padrão" for="peso_padrao">Peso padrão  (em g)</label>
                            <input style="text-align:center; width: 50%;" type="number" class="toggle input_packages" id="peso_padrao" name="peso_padrao" value="<?php echo $saved_optionals->peso_padrao ?>" />
                        </td>
                    </tr>
                </table>

            </div>
            <hr>

            <h2>Exibir simulação de cotação da tela de detalhes do produto</h2>
                <div>
                    <input type="checkbox" class="toggle" id="simulate_detail_product" name="SDP" <?= $saved_optionals->SDP ? "checked" : "" ?>>
                    <label title="Usar autocomplete de endereço" for="simulate_detail_product">Exibir</label>
                </div>
            </br>
            
            <div>
                <button class="wpme_button" type="submit" name="submit">Salvar e definir métodos de envio</button>
            </div>
        </div>
    </form>

<?php else: ?>
    <div class="notice notice-error is-dismissible">
        <h2>Não foi possível alterar</h2>
        <p>Tente novamente mais tarde</p>
    </div>
<?php endif; ?>
