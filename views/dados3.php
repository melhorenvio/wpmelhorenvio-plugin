<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<script>
    jQuery(document).ready(function($) {
        // site preloader -- also uncomment the div in the header and the css style for #preloader
        $(window).load(function(){
            $('.loader').fadeOut('slow',function(){$(this).remove();})
            $('.content').show();
        });
        
        $(document).on('change', '.filter-status', function(){
            var status = $(this).val();
            createUrlRedirect(status, 'status');
        });
        
        $(document).on('change', '.filter-time', function(){
            var time = $(this).val();
            createUrlRedirect(time, 'time');

        });

        function createUrlRedirect(param, type) {

            var url  = window.location.href + '';
            var urlSplited = url.split('?page=wpmelhorenvio_melhor-envio-dados&');

            if (urlSplited.length == 1) {
                window.location = urlSplited[0] + '&' + type + '=' + param;
            }

            var params = urlSplited[1].split('&');
            var numberParams = params.length;
            var findParam = null;
            var extractValue = null;

            if (type == 'time')   { findParam = 'status'; }
            if (type == 'status') {  findParam = 'time';  }

            for (var i=0; i<params.length; i++) {   
                if (params[i].indexOf(findParam + '=') > -1) {
                    var val = params[i].split(findParam + '=');
                    extractValue = val[1];
                }
            }
            window.location = urlSplited[0] + '?page=wpmelhorenvio_melhor-envio-dados&' + findParam + '=' +extractValue + '&' + type + '=' + param;
        }

    });
</script>

<div id="app">
    <div class="loader">
    </div>
    <div class="content">

        <div v-if="pedidos.length < 1" class="wpme_nothing">
            <template v-if="!finished">
                <h3>
                    <svg class="ico" width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" stroke="#3598dc ">
                        <g fill="none" fill-rule="evenodd" stroke-width="2">
                            <circle cx="55" cy="55" r="1">
                                <animate attributeName="r"
                                            begin="0s" dur="1.8s"
                                            values="1; 50"
                                            calcMode="spline"
                                            keyTimes="0; 1"
                                            keySplines="0.165, 0.84, 0.44, 1"
                                            repeatCount="indefinite" />
                                <animate attributeName="stroke-opacity"
                                            begin="0s" dur="1.8s"
                                            values="1; 0"
                                            calcMode="spline"
                                            keyTimes="0; 1"
                                            keySplines="0.3, 0.61, 0.355, 1"
                                            repeatCount="indefinite" />
                            </circle>
                            <circle cx="55" cy="55" r="1">
                                <animate attributeName="r"
                                            begin="-0.9s" dur="1.8s"
                                            values="1; 20"
                                            calcMode="spline"
                                            keyTimes="0; 1"
                                            keySplines="0.165, 0.84, 0.44, 1"
                                            repeatCount="indefinite" />
                                <animate attributeName="stroke-opacity"
                                            begin="-0.9s" dur="1.8s"
                                            values="1; 0"
                                            calcMode="spline"
                                            keyTimes="0; 1"
                                            keySplines="0.3, 0.61, 0.355, 1"
                                            repeatCount="indefinite" />
                            </circle>
                        </g>
                    </svg>
                </h3>
            </template>
            <template v-if="finished">
                <table>
                    <thead>
                        <tr class="action-line">
                            <td colspan="6">
                                <span>SELECIONADOS:</span>
                                <a href="javascript;" class="btn comprar-hard" @click.prevent="addManyToCart()"> Adicionar </a>
                                <a href="javascript;" class="btn melhorenvio" @click.prevent="openMultiplePaymentSelector()"> Pagar </a>
                                <a href="javascript;" class="btn imprimir" @click.prevent="PrintMultiple()"> Imprimir </a>
                            </td>
                            <td>
                                Data
                                <select class="filter-time">
                                    <option>Selecione uma opção</option>
                                    <option <?php if ($_GET['time'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                    <option <?php if ($_GET['time'] == 'day') { echo 'selected'; } ?> value="day">Hoje</option>
                                    <option <?php if ($_GET['time'] == 'week' || !isset($_GET['time'])) { echo 'selected'; } ?> value="week">Última semana</option>
                                    <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
                                    <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
                                </select>
                            </td>
                            <td>
                                Status
                                <select class="filter-status">
                                    <option>Selecione um status</option>
                                    <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                    <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                        <option 
                                            <?php 
                                                if ($_GET['status'] == $status) { echo 'selected'; } 
                                                if (!isset($_GET['status']) && $status == 'processing') { echo 'selected';  }
                                            ?>  
                                                value="<?php echo $status ?>"><?php echo $name ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </thead>
                </table>
                <h3> Ainda não há nenhum pedido por aqui...</h3>
            </template>
        </div>

        <div v-else class="table-pedidos">
            <table>
                <thead>
                    <tr class="action-line">
                        <td colspan="5">
                            <span>SELECIONADOS:</span>
                            <a href="javascript;" class="btn comprar-hard" @click.prevent="addManyToCart()"> Adicionar</a>
                            <a href="javascript;" class="btn melhorenvio" @click.prevent="openMultiplePaymentSelector()"> Pagar </a>
                            <a href="javascript;" class="btn imprimir" @click.prevent="PrintMultiple()"> Imprimir </a>
                        </td>
                        <td>
                            Data </br>
                            <select class="filter-time">
                                <option>Selecione uma opção</option>
                                <option <?php if ($_GET['time'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <option <?php if ($_GET['time'] == 'day') { echo 'selected'; } ?> value="day">Hoje</option>
                                <option <?php if ($_GET['time'] == 'week' || !isset($_GET['time'])) { echo 'selected'; } ?> value="week">Última semana</option>
                                <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
                                <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
                            </select>
                        </td>
                        <td>
                            Status
                            <select class="filter-status">
                                <option>Selecione um status</option>
                                <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                    <option 
                                        <?php 
                                            if ($_GET['status'] == $status) { echo 'selected'; } 
                                            if (!isset($_GET['status']) && $status == 'processing') { echo 'selected';  }
                                        ?>  
                                            value="<?php echo $status ?>"><?php echo $name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr class="header-line">
                        <th width="10px"><input type="checkbox" @click="selectall" v-model="selectallatt"></th>
                        <th width="50px"><span>Pedido</span></th>
                        <th width="50px"><span>Data</span></th>
                        <th width="150px"><span>Destinatário</span></th>
                        <th width="75px"><span>Transportadora</span></th>
                        <th width="75px"><span>Dados adicionais</span></th>
                        <th width="250px"><span>Opções</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(pedido, i) in pedidos_page">
                        <td>
                            <input type="checkbox" v-model="pedidos_checked[i]" :value="pedido">
                        </td>
                        <td>
                            <a :href='pedido.link_edit' target="_blank">{{pedido.number}}</a>
                        </td>
                        <td>
                            {{pedido.date_paid}}
                        </td>
                        <td>
                            <ul>
                                <li><strong>{{pedido.shipping.first_name}} {{pedido.shipping.last_name}} </strong></li>
                                <li>{{pedido.shipping.address_1}} {{pedido.shipping.address_2}} - {{pedido.shipping.postcode}}</li>
                                <li>{{pedido.shipping.neighborhood}} - {{pedido.shipping.city}}/{{pedido.shipping.state}}</li>
                            </ul>
                        </td>
                        <td>
                            <span
                                v-if="pedido.status != 'cart' && pedido.status != 'paid' && selected_shipment[i] > 2"
                            >
                                Para comprar essa etiqueta é necessário completar as informações de NF
                            </span>

                            <span v-if="pedido.bought_tracking" v-for="cotacao in pedido.cotacoes">
                                <template v-if="(cotacao.id == pedido.bought_tracking)">
                                    {{cotacao.company.name}} {{cotacao.name}} |  {{cotacao.delivery_time}}  dia
                                    <template v-if="cotacao.delivery_time > 1">s</template> | 
                                    {{cotacao.currency}} {{cotacao.price}}
                                </template>
                            </span> 
                    
                            <select class="select" v-model="selected_shipment[i]" v-if="!pedido.bought_tracking">
                                <option v-for="cot in pedido.cotacoes"
                                        v-if="!cot.error" 
                                        :value="cot.id"
                                >
                                    {{cot.name}} | {{cot.delivery_time}}  dia<template v-if="cot.delivery_time > 1">s</template> | {{cot.currency}} {{cot.price}}
                                </option>
                            </select>

                        </td>
                        <td width="75px">
                            <!-- v-if="!pedido.bought_tracking" -->
                            <template v-if="pedido">
                                <label>
                                    <strong>Chave-NF:</strong>
                                    {{pedido.docs.key_nf}}
                                    <input type="hidden"  v-model="pedido.docs.key_nf">
                                </label> </br>
                                <label>
                                    <strong>NF:</strong>
                                    {{pedido.docs.nf}}
                                    <input  type="hidden" v-model="pedido.docs.nf">
                                </label>
                                <label v-if="company.document == '' || company.document == null">
                                    <strong>CNPJ:</strong>
                                    {{pedido.docs.cnpj}}
                                    <input  type="hidden" v-model="pedido.docs.cnpj">
                                </label>
                                <label v-if="company.state_register == '' || company.state_register == null">
                                    <strong>IE:</strong>
                                    {{pedido.docs.ie}}
                                    <input  type="hidden" v-model="pedido.customer_state_register">
                                </label>
                            </template>
                            <template v-else><p>--</p></template>
                        </td>
                        <td>
                            <template v-if="pedido.status != 'cart' && pedido.status != 'paid'">
                                <a href="javascript;" class="btnTable comprar" @click.prevent="toogleFormModal(i)" >
                                    <img alt="Editar informações" title="Editar informações" src="<?=plugins_url("assets/img/editar.svg",__DIR__ )?>" />
                                </a>
                            </template>
                            


                            <template v-if="
                                pedido.status != 'cart' &&
                                pedido.status != 'paid' && 
                                pedido.status != 'printed' && 
                                pedido.status != 'waiting' && ( pedido.docs.nf != '' || selected_shipment[i] <= 2 ) 
                                ">
                                <a href="javascript;" class="btnTable comprar" @click.prevent="addToCart(i)" >
                                    <img alt="Adicionar ao carrinho" title="Adicionar ao carrinho" src="<?=plugins_url("assets/img/cart-add.svg",__DIR__ )?>" /> 
                                </a>
                            </template>
                                    
                        
                            <template v-if="pedido.status == 'cart'">
                                <a href="javascript;" class="btnTable melhorenvio" @click.prevent="openSinglePaymentSelector(pedido.tracking_code)">
                                    <img alt="Pagar" title="Pagar" src="<?=plugins_url("assets/img/pagar.svg",__DIR__ )?>" />
                                </a>
                                <a href="javascript;" class="btnTable cancelar" @click.prevent="removeFromCart(i,pedido.tracking_code)" >
                                    <img alt="Excluir" title="Excluir" src="<?=plugins_url("assets/img/excluir.svg",__DIR__ )?>" />
                                </a>
                            </template>
                            <template v-if="pedido.status == 'paid'">
                                <a href="javascript;" class="btnTable imprimir" @click.prevent="printTicket(pedido.tracking_code)">
                                    <img alt="Imprimir etiqueta" title="Imprimir etiqueta" src="<?=plugins_url("assets/img/imprimir.svg",__DIR__ )?>" /> 
                                </a>
                                <a href="javascript;" class="btnTable cancelar" @click.prevent="openCancelTicketConfirmer(pedido.tracking_code)" >
                                    <img alt="Cancelar" title="Cancelar" src="<?=plugins_url("assets/img/excluir.svg",__DIR__ )?>" />
                                </a>
                            </template>
                            <template v-if="pedido.status == 'waiting'">
                                <a href="javascript;" class="btnTable cancelar" @click.prevent="deleteTracking([pedido.tracking_code])" >
                                    <img alt="Cancelar pagamento" title="Cancelar pagamento" src="<?=plugins_url("assets/img/excluir.svg",__DIR__ )?>" />
                                </a>
                            </template>

                            <template v-if="
                                pedido.status != 'cart' && 
                                pedido.status != 'paid' && 
                                pedido.status != 'printed'
                                ">
                                <a href="javascript;" class="btnTable" @click.prevent="updateQuotation(pedido.id)" >
                                    <img alt="Atualizar cotação" title="Atualizar cotação" src="<?=plugins_url("assets/img/ico_refresh.png",__DIR__ )?>" /> 
                                </a>
                            </template>

                            <template>
                                    <a href="javascript;" class="btnTable" @click.prevent="getTrackingMR(pedido)">
                                        <img alt="Ver rastreio" title="Ver rastreio" src="<?=plugins_url("assets/img/map2.png",__DIR__ )?>" /> 
                                    </a>
                            </template>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="action-line">
                        <td colspan="5">
                            <span>SELECIONADOS: </span>
                            <a href="javascript;" class="btn comprar-hard" @click.prevent="addManyToCart()"> Adicionar</a>
                            <a href="javascript;" class="btn melhorenvio"  @click.prevent="openMultiplePaymentSelector()"> Pagar </a>
                            <a href="javascript;" class="btn imprimir" @click.prevent="PrintMultiple()"> Imprimir </a>
                        </td>
                        <td>
                            Data </br>  
                            <select class="filter-time">
                                <option>Selecione uma opção</option>
                                <option <?php if ($_GET['time'] == 'all' ) { echo 'selected'; } ?> value="all">Todos</option>
                                <option <?php if ($_GET['time'] == 'day') { echo 'selected'; } ?> value="day">Hoje</option>
                                <option <?php if ($_GET['time'] == 'week' || !isset($_GET['time'])) { echo 'selected'; } ?> value="week">Última semana</option>
                                <option <?php if ($_GET['time'] == 'month') { echo 'selected'; } ?> value="month">Último mês</option>
                                <option <?php if ($_GET['time'] == 'year') { echo 'selected'; } ?> value="year">último ano</option>
                            </select>
                        </td>
                        <td>
                            Status
                            <select class="filter-status">
                                <option>Selecione um status</option>
                                <option <?php if ($_GET['status'] == 'all') { echo 'selected'; } ?> value="all">Todos</option>
                                <?php foreach (wc_get_order_statuses() as $status => $name ) { $status = str_replace('wc-', '', $status);  ?>
                                    <option 
                                        <?php 
                                            if ($_GET['status'] == $status) { echo 'selected'; } 
                                            if (!isset($_GET['status']) && $status == 'processing') { echo 'selected';  }
                                        ?>  
                                            value="<?php echo $status ?>"><?php echo $name ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="wpme_pagination_wrapper">
            <ul class="wpme_pagination" >
                <li v-for="i in Math.ceil(total/perpage)" v-show="total > perpage" :class="{'active': i == page}">
                    <a href="javascript;"  @click.prevent="pagego(i)"  v-if="i < 2 && i != page || i < page+2 && i > page || i > page-2 && i < page  ||  i > (total / perpage)-1 && i != page || i == page" >{{i}}</a>
                    <span class="ret"  v-show="(i == page-2 | i == page+1) && (Math.ceil(total / perpage) > 4) && Math.ceil(total /perpage) > i+1" > ...  </span>
                </li>
            </ul>
        </div>
        <div class="mask" v-show="show_mask" @click.prevent="toogleModal">
        </div>
        <div class="modal" v-show="show_modal">
            <a href="javascript;" @click.prevent="toogleModal()" class="close-modal"> &times </a>
            <h1 >Escolha seu método de pagamento</h1>
            <div class="select">
                <label>
                    <input type="radio" v-model="selected_payment_method" value="1">
                    <img src="<?=plugins_url("assets/img/moip.png",__DIR__ )?>">

                </label>
                <label>
                    <input type="radio"  v-model="selected_payment_method" value="2">
                    <img src="<?=plugins_url("assets/img/mpago.png",__DIR__ )?>">
                </label>

                <label>
                    <input type="radio"  v-model="selected_payment_method" value="99">
                    <div class="pgsaldo">
                        <h4>Pagar com Saldo</h4>
                        <p>Saldo <strong>{{user_info.balance}}</strong></p>
                    </div>
                </label>
            </div>
            <a href="javascript;" class="btn pagar" @click.prevent="payTicket(selected_payment_method)"> Pagar </a>
        </div>
        <div class="mask" v-show="show_confirm_mask" @click.prevent="toogleConfirmer">
        </div>
        <div class="modal" v-show="show_confirm_modal">
            <a href="javascript;" @click.prevent="toogleConfirmer" class="close-modal"> &times </a>
            <h1 class="wpme_error">Você tem certeza que deseja cancelar?</h1>
            <p>Ao clicar em "Quero Cancelar" a etiqueta se torna inutilizavel.</p>
            <a href="javascript;" class="btn cancelar" @click.prevent="cancelTicket()">Quero cancelar</a>  <a href="javascript;" @click.prevent="toogleConfirmer" class="btn fechar">Fechar</a>
        </div>

        <div class="mask" v-show="show_form_modal_mask" @click.prevent="toogleFormModal">
        </div>
        <div class="modal" style="height:auto;" v-show="show_form_modal">
            <h1>Inserir os dados</h1>

            <div class="wpme_wrapper_center">
                <form  class="update-order">
                    <input type="hidden" name="id" :value="modal_info_selected.id" ref="order_selected_id" />

                    <div class="form">
                        <fieldset>
                            <label>Chave da nota fiscal</label>
                            <input type="text" name="key-nf" ref="order_selected_key_nf" :value="this.order_key_nf_selected"  />
                        </fieldset>

                        <fieldset>
                            <label>Número da nota fiscal</label></br>
                            <input type="text" name="nf" ref="order_selected_nf" :value="this.order_nf_selected" />
                        </fieldset>

                        <fieldset> 
                            <label>CNPJ</label></br>
                            <input type="text" name="cnpj" ref="order_selected_cnpj" :value="this.order_cnpj_selected" />
                        </fieldset>

                        <fieldset> 
                            <label>Inscrição estadual</label></br>
                            <input type="text" name="ie" ref="order_selected_ie" :value="this.order_ie_selected" />
                        </fieldset>
                    </div>
                    <div class="buttons">
                        <a class="close-form-docs" href="javascript;" @click.prevent="toogleFormModalClose">Fechar</a>
                        <button type="submit" @click.prevent="getFormValues()" class="btn" >Atualizar</button>
                    </div>

                    <input type="hidden" name="ind_selected" ref="ind_selected" :value="this.ind_selected"  />
                </form>                
            </div>
        </div>


        <div class="mask" v-if="message.show_message" @click.prevent="toogleMessage"></div>
        <div class="wpme_message" v-if="message.show_message" >
            <div class="wpme_message_header" :class="{'wpme_success': message.type == 'success', 'wpme_error': message.type == 'error'}">{{message.title}}</div>
            <div class="wpme_message_body" v-html="message.message"   ></div>
            <div class="wpme_wrapper_center">
                <template v-if="payment_tracking_codes.length > 0 ">
                    <div class="wpme_message_comprar"><a href="javascript;" @click.prevent="goDirectPay">Pagar</a></div>
                </template>
                <div class="wpme_message_action"><a href="javascript;" @click.prevent="toogleMessage">Fechar</a></div>
            </div>
        </div>

        <div class="wpme_loader" v-show="loader">
            <svg class="ico" width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" stroke="#3598dc ">
                <g fill="none" fill-rule="evenodd" stroke-width="2">
                    <circle cx="55" cy="55" r="1">
                        <animate attributeName="r"
                                 begin="0s" dur="0.8s"
                                 values="1; 50"
                                 calcMode="spline"
                                 keyTimes="0; 1"
                                 keySplines="0.165, 0.84, 0.44, 1"
                                 repeatCount="indefinite" />
                        <animate attributeName="stroke-opacity"
                                 begin="0s" dur="1.8s"
                                 values="1; 0"
                                 calcMode="spline"
                                 keyTimes="0; 1"
                                 keySplines="0.3, 0.61, 0.355, 1"
                                 repeatCount="indefinite" />
                    </circle>
                    <circle cx="55" cy="55" r="1">
                        <animate attributeName="r"
                                 begin="-0.9s" dur="0.8s"
                                 values="1; 20"
                                 calcMode="spline"
                                 keyTimes="0; 1"
                                 keySplines="0.165, 0.84, 0.44, 1"
                                 repeatCount="indefinite" />
                        <animate attributeName="stroke-opacity"
                                 begin="-0.9s" dur="1.8s"
                                 values="1; 0"
                                 calcMode="spline"
                                 keyTimes="0; 1"
                                 keySplines="0.3, 0.61, 0.355, 1"
                                 repeatCount="indefinite" />
                    </circle>
                </g>
            </svg>
        </div>
    </div>
</div>
<script src="https://unpkg.com/vue@2.5.13/dist/vue.js"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            payment_tracking_codes:[],
            finished:false,
            selected_payment_method: 99,
            updated:true,
            security_read: '<?php echo wp_create_nonce( "wpmelhorenvio_read" ); ?>',
            security_action: '<?php echo wp_create_nonce( "wpmelhorenvio_action" ); ?>',
            pedidos: [],
            loader:false,
            succes_desc: [],
            error_desc: [],
            total:0,

            company:{
                document:'',
                state_register:''
            },
            show_mask:false,
            show_modal:false,
            message:{
                type:'',
                show_message:false,
                title:'',
                message:''
            },
            opcionais:{},
            show_button:[],
            endereco:{
                city:{
                    city:"",
                    state:""
                }
            },
            pedidos_checked:[],
            pedidos_chave_nf:[],
            order_key_nf_selected: null,
            order_nf_selected: null,
            order_cnpj_selected: null,
            order_ie_selected: null,
            ind_selected: null,
            pedidos_nf: [],
            pedidos_cnpj: [],
            pedidos_ie: [],
            selected_shipment: [],
            tracking_codes:[],
            page:1,
            selectallatt:false,
            perpage:10,
            user_info: {
                firstname:'',
                lastname:'',
                thumbnail:'',
                balance:''
            },
            cancel_tracking_codes: [],
            show_confirm_modal:false,
            show_form_modal:false,
            show_form_modal_mask:false,
            show_confirm_mask:false,
            show_form_mask:false,
            modal_info_selected: [],
            pedidos_page: []
        },

        created: function(){
            this.getOrders();
            this.verifyTracking();
            this.pedidos_page = this.perPage();
            this.getBalance();
        },

        computed:{
        },

        methods: {
            perPage() {
                this.total = this.pedidos.length;

                let shipments = this.pedidos.slice(((this.page -1) * this.perpage), this.page*this.perpage);

                return shipments;
            },
            loadShipments() {
                this.total = this.pedidos.length;

                let shipments = this.pedidos.slice(((this.page -1) * this.perpage), this.page*this.perpage);

                shipments.forEach((shipment, key) => {
                    shipment.cotacoes.forEach((cotacao) => {
                        if(cotacao.selected == true) {
                            this.selected_shipment[key] = cotacao.id;
                        }
                    })
                });

                this.pedidos_page = this.perPage();
            },

            change_updated: function () {
                setTimeout(() => {
                    this.updated = !this.updated;
                }, 100);
            },
            
            getFormValues (submitEvent) {

                this.order_key_nf_selected = this.$refs.order_selected_key_nf.value;
                this.order_nf_selected = this.$refs.order_selected_nf.value;
                this.order_cnpj_selected = this.$refs.order_selected_cnpj.value;
                this.order_ie_selected = this.$refs.order_selected_ie.value;
                this.ind_selected = this.$refs.ind_selected.value;

                data = {
                    action:'wpmelhorenvio_ajax_update_info_order',
                    security: this.security_action,
                    id: this.$refs.order_selected_id.value,
                    key_nf: this.$refs.order_selected_key_nf.value,
                    nf: this.$refs.order_selected_nf.value,
                    cnpj: this.$refs.order_selected_cnpj.value,
                    ie: this.$refs.order_selected_ie.value
                };

                var indexSelected = this.ind_selected;
                jQuery.post(ajaxurl,data, function (response) {

                    app.pedidos[indexSelected].docs.ie = response.ie;
                    app.pedidos[indexSelected].docs.cnpj = response.cnpj;
                    app.pedidos[indexSelected].docs.key_nf = response.key_nf;
                    app.pedidos[indexSelected].docs.nf = response.nf;

                    app.show_form_modal_mask = false;
                    app.show_form_modal = false;
                });

            },

            toogleLoader: function(){
                this.loader = !this.loader;
            },

            stripcode: function(string) {
                string = string.replace('wpmelhorenvio_','');
                return string.replace('_','');
            },

            toogleModal: function(){
                this.show_mask = !this.show_mask;
                this.show_modal = !this.show_modal;
            },

            toogleConfirmer: function(){
                this.show_confirm_mask = !this.show_confirm_mask;
                this.show_confirm_modal = !this.show_confirm_modal;
            },

            toogleFormModal: function(ind){
                this.show_form_modal_mask = !this.show_form_modal_mask;
                this.show_form_modal = !this.show_form_modal;
                this.ind_selected = ind;
                this.modal_info_selected = this.pedidos_page[ind];
                this.order_key_nf_selected = this.pedidos[ind].docs.key_nf;
                this.order_nf_selected = this.pedidos[ind].docs.nf;
                this.order_ie_selected = this.pedidos[ind].docs.ie;
                this.order_cnpj_selected = this.pedidos[ind].docs.cnpj;
            },

            toogleFormModalClose: function(){
                this.show_form_modal_mask = !this.show_form_modal_mask;
                this.show_form_modal = !this.show_form_modal;;
            },

            toogleConfirmerForm: function(ind){
                this.show_confirm_mask = !this.show_confirm_mask;
                this.show_form_modal = !this.show_form_modal;
                this.ind_selected = ind;
                this.modal_info_selected = this.pedidos_page[ind];
                this.order_key_nf_selected = this.pedidos[ind].docs.key_nf;
                this.order_nf_selected = this.pedidos[ind].docs.nf;
                this.order_ie_selected = this.pedidos[ind].docs.ie;
                this.order_cnpj_selected = this.pedidos[ind].docs.cnpj;
            },

            closeModalForm: function() {
                this.show_confirm_mask = false;
                this.show_form_modal = false;
            },

            toogleMessage: function(){
                this.message.show_message = !this.message.show_message;
            },

            goDirectPay: function(){
                this.toogleMessage();
                this.toogleModal();
            },

            removeFromCart: function(id,tracking){
                this.toogleLoader()
                var data = {
                    action: 'wpmelhorenvio_ajax_removeTrackingAPI',
                    tracking:tracking,
                    security:this.security_action
                }
                vm = this;
                jQuery.post(ajaxurl,data,function (response) {
                    resposta = JSON.parse(response);
                    if(resposta.succcess == true){
                        vm.pedidos_page[id].status = undefined;
                        vm.pedidos_page[id].bought_tracking = 0;
                        vm.pedidos_page[id].tracking_code = undefined;
                    }
                    vm.toogleLoader()
                });
            },

            addToCart: function(ind){
                this.payment_tracking_codes = [];
                if(typeof this.selected_shipment[ind] === 'undefined'){
                    this.message.title = 'Envio não foi efetuado';
                    this.message.message = 'Tipo de transporte não selecionado. Selecione o tipo de transporte.';
                    this.message.type = 'error';
                    this.message.show_message = true;
                    return;
                }

                pedido = this.pedidos_page[ind];

                vm = this;
                if(pedido.docs.cnpj != '' && pedido.docs.cnpj != null ){
                    pedido_cnpj = pedido.docs.cnpj;
                }
                
                var pedido_ie = null;
                if(pedido.docs.ie != '' && pedido.docs.ie != null){
                    pedido_ie = pedido.docs.ie;
                }
                
                if(typeof pedido_cnpj === 'undefined' && this.selected_shipment[ind] > 2){
                    this.message.title = 'Dados incompletos';
                    this.message.message = 'Documento CPF/CNPJ não informados, Adicione essas informaçoes no pedido #' + pedido.id;
                    this.message.type = 'error';
                    this.message.show_message = true;
                    return;
                }

                if(pedido.customer_document == ''){
                    this.message.title = 'Dados incompletos';
                    this.message.message = 'Documento do cliente não informado. Adicione junto ao painel de pedidos do WooCommerce';
                    this.message.type = 'error';
                    this.message.show_message = true;
                    return;
                }
                if(this.selected_shipment[ind] > 2 && (typeof pedido.docs.nf === 'undefined' || typeof pedido.docs.cnpj === 'undefined' || typeof pedido.docs.ie === 'undefined') ){
                    vm.message.title = 'Dados Incompletos';
                    vm.message.message = 'Para utilizar essa transportadora, informe a nota fiscal (NF) e os dados da empresa (CNPJ/IE) ';
                    vm.message.type = 'error';
                    vm.message.show_message = true;
                }else{
                    if(this.selected_shipment[ind] < 3){
                        var data = {
                            id: pedido.id,
                            security: this.security_action,
                            action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                            valor_declarado: pedido.price,
                            service_id: this.selected_shipment[ind],
                            from_name: pedido.shop_name,
                            to_name: pedido.shipping.first_name+" "+pedido.shipping.last_name,
                            to_phone: pedido.customer_phone,
                            to_email: pedido.customer_email,
                            to_document: pedido.customer_document,
                            to_company_document: pedido.customer_company_document,
                            to_state_register: pedido.customer_state_register,
                            to_address: pedido.shipping.address_1,
                            to_complement: pedido.shipping.address_2,
                            to_number:  pedido.shipping.number,
                            to_district: pedido.shipping.neighborhood,
                            to_city:    pedido.shipping.city,
                            nf: pedido.docs.nf,
                            key_nf: pedido.docs.key_nf,
                            to_state_abbr: pedido.shipping.state,
                            to_country_id: pedido.shipping.country,
                            to_postal_code: pedido.shipping.postcode,
                            to_note: pedido.customer_note,
                            line_items: pedido.line_items
                        }
                    }else{
                        var data = {
                            id: pedido.id,
                            security: this.security_action,
                            action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                            valor_declarado: pedido.price,
                            service_id: this.selected_shipment[ind],
                            from_name: pedido.shop_name,
                            from_company_document : pedido_cnpj,
                            from_company_state_register: pedido_ie,
                            to_name: pedido.shipping.first_name+" "+pedido.shipping.last_name,
                            to_phone: pedido.customer_phone,
                            to_email: pedido.customer_email,
                            to_document: pedido.customer_document,
                            to_company_document: pedido.customer_company_document,
                            to_state_register: pedido.customer_state_register,
                            to_address: pedido.shipping.address_1,
                            to_complement: pedido.shipping.address_2,
                            to_number:  pedido.shipping.number,
                            to_district: pedido.shipping.neighborhood,
                            to_city:    pedido.shipping.city,
                            to_state_abbr: pedido.shipping.state,
                            to_country_id: pedido.shipping.country,
                            to_postal_code: pedido.shipping.postcode,
                            to_note: pedido.customer_note,
                            line_items: pedido.line_items,
                            nf: pedido.docs.nf,
                            key_nf: pedido.docs.key_nf,
                            company_document: pedido_cnpj,
                            company_state_register: pedido_ie,
                            agency: pedido.agency.agency
                        }
                    }
                    this.toogleLoader();
                    jQuery.post(ajaxurl, data, function(response) {
                        resposta = JSON.parse(response);
                        if(typeof resposta.id != 'undefined'){
                            vm.payment_tracking_codes = [];
                            vm.payment_tracking_codes.push(resposta.id);
                            vm.addTracking(pedido.id,resposta.id,data.service_id);
                            pedido.tracking_code = resposta.id;
                            pedido.bought_tracking = data.service_id;
                            // console.log(pedido.bought_tracking);
                            pedido.status = 'cart';
                            vm.message.title = 'Envio adicionado ao carrinho';
                            vm.message.message = 'Este envio foi adicionado ao seu carrinho, clique em pagar para gerar a sua etiqueta.';
                            vm.message.type = 'success';
                            vm.message.show_message = true;
                        }else{
                            if(resposta.errors && typeof resposta.errors['options.invoice.key'] !== 'undefined') {
                                vm.message.title = 'foi possível adicionar item ao carrinho';
                                vm.message.message = 'Verificar o número da chave da NF'
                                vm.message.type = 'error';
                                vm.message.show_message = true;
                                vm.toogleLoader();
                                return;
                            }
                            if(resposta.errors &&  typeof resposta.errors['options.invoice.number']  !== 'undefined') {
                                vm.message.title = 'foi possível adicionar item ao carrinho';
                                vm.message.message = 'Verificar o número da NF'
                                vm.message.type = 'error';
                                vm.message.show_message = true;
                                vm.toogleLoader();
                                return;
                            }
                            if(typeof resposta.error === 'undefined'){
                                vm.message.title = 'Não foi possível adicionar item ao carrinho';
                                vm.message.message = 'Infelizmente não foi possível adicionar este item ao seu carrinho'
                                vm.message.type = 'error';
                                vm.message.show_message = true;
                            }else{
                                vm.message.title = 'Não foi possível adicionar item ao carrinho';
                                vm.message.message = 'Infelizmente não foi possível adicionar este item ao seu carrinho. '+resposta.error
                                vm.message.type = 'error';
                                vm.message.show_message = true;
                            }

                        }
                        vm.toogleLoader();
                    });
                }
            },

            verifyTracking: function(){
                data = {
                    action:'wpmelhorenvio_ajax_updateStatusTracking',
                    security: this.security_action
                }

                jQuery.post(ajaxurl,data,function(response){
                })
            },

            getAddress: function(){
                data = {
                    action: "wpmelhorenvio_ajax_getAddressAPI",
                    security: this.security_read
                };
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    vm.endereco = JSON.parse(response);
                });
            },

            openSinglePaymentSelector: function(index){
                this.payment_tracking_codes = [];
                this.payment_tracking_codes.push(index);
                this.toogleModal();
            },

            openCancelTicketConfirmer: function(tracking){
                this.payment_tracking_codes = [];
                this.cancel_tracking_codes = [];
                this.cancel_tracking_codes.push(tracking);
                this.toogleConfirmer();
            },

            cancelTicket: function(){
                this.toogleConfirmer();
                this.toogleLoader();
                data = {
                    action: 'wpmelhorenvio_ajax_cancelTicketAPI',
                    tracking: this.cancel_tracking_codes[0],
                    security: this.security_action
                };
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    resposta = JSON.parse(response);

                    arr_index = Object.entries(resposta);
                    if(arr_index[0][1]['canceled']){
                        vm.deleteTracking(data.tracking);
                        vm.message.title = "Etiqueta cancelada com sucesso";
                        vm.message.message = "Após a verificação o valor deverá ser extornado para a carteira";
                        vm.message.type= "success";
                        vm.message.show_message = true;
                        vm.pedidos_page.forEach(function(pedido){
                            if( pedido.tracking_code == data.tracking){
                                pedido.tracking_code = undefined;
                                pedido.status = undefined;
                                pedido.bought_tracking = undefined;
                                // console.log(pedido.bought_tracking);
                            }
                        })
                    }else{
                        vm.message.title = "Não foi possível cancelar esta etiqueta";
                        vm.message.message = "Infelizmente não é possível cancelar esta etiqueta";
                        vm.message.type= "error";
                        vm.message.show_message = true;
                    }
                    vm.toogleLoader();

                });
            },

            printTicket: function(tracking){

                this.toogleLoader();

                data = {
                    action: 'wpmelhorenvio_ajax_ticketPrintingAPI',
                    tracking: [tracking],
                    security: this.security_action
                };
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    resposta = JSON.parse(response);
                    if(typeof resposta.url ){
                        vm.toogleLoader();
                        window.open(resposta.url,'_blank');
                    }else{
                        vm.toogleLoader();
                        vm.message.title = "Não foi possível acessar esta etiqueta";
                        vm.message.message = "Infelizmente não é possível acessar esta etiqueta";
                        vm.message.type= "error";
                        vm.message.show_message = true;
                    }
                });
            },


            getOptionals: function(){
                data = {
                    action: "wpmelhorenvio_ajax_getOptionsAPI",
                    security: this.security_read
                };
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    vm.opcionais = JSON.parse(response);
                });
            },

            getTrackings: function(){
                vm = this;
                this.pedidos.forEach(function (pedido){
                    var data = {
                        action:'wpmelhorenvio_ajax_getTrackingsData',
                        security:this.security_read,
                        order_id: pedido.id,
                        timeout:30
                    };
                    jQuery.post(ajaxurl, data, function(response) {
                        resposta = JSON.parse(response);
                        resposta.forEach(function(tracking){
                            index = tracking.order_id;
                            trk = data.tracking.tracking_id
                            vm.tracking_codes[index] = trk;
                        })
                    });
                });
            },

            getSpecificTracking: function(pedido){

                var data = {
                    security:this.security_read,
                    action:'wpmelhorenvio_ajax_getTrackingsData',
                    order_id: pedido.id,
                    timeout:30
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    resposta.forEach(function(tracking){
                        trk = tracking.tracking_id;
                        pedido.tracking_code = trk;
                        pedido.bought_tracking = tracking.service_id;
                        pedido.status = tracking.status;
                        vm.pedidos_page = vm.perPage();
                    })
                });
            },

            payTicket: function(payment_method){
                var data = {
                    security: this.security_action,
                    action:'wpmelhorenvio_ajax_payTicketAPI',
                    orders: this.payment_tracking_codes,
                    gateway: payment_method

                };

                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    vm.toogleModal();
                    resposta = JSON.parse(response)
                    if(typeof resposta.error !== 'undefined'){
                        vm.payment_tracking_codes = [];
                        vm.message.title="Pagamento não efetuado";
                        vm.message.message=resposta.error
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }else{
                        if(resposta.redirect != null){
                            data.orders.forEach(function(order) {
                                vm.payment_tracking_codes = [];
                                vm.updateTracking(order, 'waiting');
                                vm.message.title = "Esperando confirmação do meio de pagamento";
                                vm.message.message = "Esperando confirmação do meio de pagamento";
                                vm.message.type = 'success';
                                vm.message.show_message = true;
                                vm.pedidos_page.forEach( function (pedido) {
                                    if(order == pedido.tracking_code)
                                        pedido.status = 'waiting';
                                });
                                window.open(resposta.redirect,'_blank');
                            });
                        }else{
                            data.orders.forEach(function(order){
                                vm.updateTracking(order,'paid');
                                vm.payment_tracking_codes = [];
                                vm.pedidos_page.forEach( function (pedido) {
                                    if(order == pedido.tracking_code)
                                        pedido.status = 'paid';
                                });
                            });
                            vm.payment_tracking_codes = [];
                            vm.getBalance();
                            vm.getLimits();
                            vm.message.title="Pagamento feito com sucesso";
                            vm.message.message="Seu pagamento foi efetuado com sucesso";
                            vm.message.type = 'success';
                            vm.message.show_message = true;
                        }
                    }
                });
            },

            updateTracking: function(tracking_code,status){
                var data = {
                    security: this.security_action,
                    action: "wpmelhorenvio_ajax_updateStatusData",
                    tracking_code:tracking_code,
                    status:status
                };
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                });

            },

            getTracking: function(){

                tracking_codes = [];
                this.pedidos.forEach(function (pedido) {
                    tracking_codes.push(pedido.id);
                });
                var data = {
                    action:'wpmelhorenvio_ajax_getTrackingAPI',
                    tracking_codes: tracking_codes,
                    security: this.security_read
                };
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                });
            },

            getTrackingMR: function(pedido){
                
                this.toogleLoader();

                if (pedido.tracking_mr) {
                    vm.toogleLoader();
                    window.open('https://www.melhorrastreio.com.br/rastreio/' + pedido.tracking_mr, '_blank');
                    return null;
                }

                var data = {
                    action:'wpmelhorenvio_ajax_getTrackingApiMR',
                    order: pedido.tracking_code,
                    id: pedido.id,
                    security: this.security_read
                };

                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                
                    if (response != 0) {
                        vm.toogleLoader();
                        window.open('https://www.melhorrastreio.com.br/rastreio/' + response, '_blank');
                        return false;
                    } else {
                        vm.toogleLoader();
                        vm.message.title = 'Ops!';
                        vm.message.message = 'Essa encomenda ainda não foi postada, só é possível ver o rastreio após a impressão da etiqueta.';
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }
                });
            },

            addTracking: function(order_id,tracking,service){
                var data = {
                    action: "wpmelhorenvio_ajax_addTrackingAPI",
                    security:this.security_action,
                    order_id:order_id,
                    tracking:tracking,
                    service: service
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    if(tracking !== null){
                        vm.tracking_codes[order_id]= tracking;
                    }
                });
            },

            getOrders: function(){
               
                var data = {
                    action:'wpmelhorenvio_ajax_getJsonOrders',
                    status: '<?php echo $_GET['status'] ?>',
                    time: '<?php echo $_GET['time'] ?>',
                    timeout:30,
                    security:this.security_read
                };

                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);


                    var array = [];
                    try{
                        resposta.forEach(function (pedido,index) {
                            pedido.cotacoes.forEach(function (cotacao) {
                                if(Array.isArray(pedido.shipping_lines)){
                                    if( pedido.shipping_lines[0].method_id == 'wpmelhorenvio_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)){
                                        array[index] = cotacao.id;
                                    }
                                }
                            });

                            vm.getSpecificTracking(pedido);
                            vm.pedidos = resposta;
                        });
                        vm.selected_shipment = array;
                    }
                    catch (err){
                        vm.message.title = 'Erro ao carregar as cotações';
                        vm.message.message = 'Houve um erro ao carregar as cotações, tente novamente mais tarde.';
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }

                    vm.loadShipments();

                    vm.finished= true;
                });
            },

            openMultiplePaymentSelector: function(){
                this.payment_tracking_codes = [];
                if(this.pedidos_checked.length > 0 && this.pedidos_checked.find(function(data){ return data == true;})){
                    for(var i = 0 ; i < this.pedidos_checked.length ;i++){
                        if(this.pedidos_checked[i]) {
                            if (typeof this.pedidos_page[i].tracking_code != 'undefined')
                                if(this.pedidos_page[i].status == "cart"){
                                    this.payment_tracking_codes.push(this.pedidos_page[i].tracking_code);
                                }
                        }
                    }
                    if(this.payment_tracking_codes.length > 0){
                        this.toogleModal();
                    }else{
                        this.message.title = "Nenhum dos itens adicionados faz parte do seu carrinho";
                        this.message.message= "Para adicionar estes itens ao seu carrinho selecione e clique em adicionar ao carrinho";
                        this.message.type = "error";
                        this.message.show_message = true;;
                    }
                }else{
                    this.message.title = "Selecione seus pedidos"
                    this.message.message= "Selecione os pedidos que você deseja pagar"
                    this.message.type = "error"
                    this.message.show_message = true;
                }
            },

            pagego: function(valor){
                this.payment_tracking_codes = [];
                this.cancel_tracking_codes = [];
                this.pedidos_checked = [];
                pedidos_cnpj = [];
                pedidos_ie = [];
                pedidos_nf = [];
                this.page = valor;
            },

            selectall: function (){
                var vm = this;
                this.pedidos_page.forEach( function (pedido,index) {
                    vm.pedidos_checked[index] = !vm.selectallatt
                });
            },

            addManyToCart: function(){
                this.toogleLoader();
                this.error_desc = [];
                this.success_desc = [];
                this.payment_tracking_codes = [];
                vm = this;
                for(var i = 0; i < this.pedidos_checked.length; i++){
                    if(this.pedidos_checked[i]){
                        if(typeof this.pedidos_page[i].tracking_code == 'undefined' || typeof this.pedidos_page[i].tracking_code.length < 1 ){
                            retorno = vm.addToCartOneFromMany(i);
                            if(this.error_desc.length > 0 ){
                                vm.message.title = "Pedidos não adicionados ao carrinho"
                                vm.message.message = '<table><tr><td><strong>Pedido </strong></td><td width="70%"><strong>Erro</strong></td></tr>';
                                this.error_desc.forEach(function(erro,i){
                                    vm.message.message = vm.message.message+ '<tr><td>'+vm.pedidos_page[i].id+'</td><td>'+erro+"</td></tr>";
                                });
                                vm.message.type = 'error';
                                vm.message.show_message = true;
                                console.log(vm.error_desc);
                            }else{
                                vm.message.title = 'Envios adicionados ao carrinho';
                                vm.message.message = 'Estes envios foram adicionados ao seu carrinho.';
                                vm.message.type = 'success';
                                vm.message.show_message = true;
                            }
                        }
                    }
                }
                this.toogleLoader();
            },

            addToCartOneFromMany: function(ind){

                var pedido = this.pedidos_page[ind];
                
                var retorno;
                vm = this;
                if(typeof this.selected_shipment[ind] === 'undefined'){
                    this.error_desc[ind] =  'Transportadora não selecionada.'
                    return false;
                }
                if(pedido.docs.cnpj != '' && pedido.docs.cnpj != null ){
                    pedido_cnpj = pedido.docs.cnpj;
                }

                if(pedido.docs.ie != '' && pedido.docs.ie != null){
                    pedido_ie = pedido.docs.ie;
                }

                if(this.selected_shipment[ind] > 2 && (typeof pedido.docs.nf === 'undefined' || typeof pedido_cnpj === 'undefined' || typeof pedido_ie === 'undefined') ){
                    this.error_desc[ind] =  'Nota Fiscal não informada para transportadora privada.'
                }else{
                    if(pedido.customer_document == ''){
                        this.error_desc[ind] =  'Documentos do cliente não informados. Verifique junto ao painel de pedidos do WordPress'
                    }else{
                        if(this.selected_shipment[ind] < 3){
                            var data = {
                                security: this.security_action,
                                action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                                // valor_declarado: pedido.price,
                                service_id: this.selected_shipment[ind],
                                from_name: pedido.shop_name,
                                to_name: pedido.shipping.first_name+" "+pedido.shipping.last_name,
                                to_phone: pedido.customer_phone,
                                to_email: pedido.customer_email,
                                to_document: pedido.customer_document,
                                to_company_document: pedido.customer_company_document,
                                to_state_register: pedido.customer_state_register,
                                to_address: pedido.shipping.address_1,
                                to_complement: pedido.shipping.address_2,
                                to_number:  pedido.shipping.number,
                                to_district: pedido.shipping.neighborhood,
                                to_city:    pedido.shipping.city,
                                to_state_abbr: pedido.shipping.state,
                                to_country_id: pedido.shipping.country,
                                to_postal_code: pedido.shipping.postcode,
                                to_note: pedido.customer_note,
                                line_items: pedido.line_items,
                                nf: this.pedidos_nf[ind],
                                key_nf: this.pedidos_chave_nf[ind]
                            }
                        }else{
                            var data = {
                                security: this.security_action,
                                action: "wpmelhorenvio_ajax_ticketAcquirementAPI",
                                // valor_declarado: pedido.price,
                                service_id: this.selected_shipment[ind], from_name: pedido.shop_name,
                                from_company_document : pedido_cnpj,
                                from_company_state_register: pedido_ie,
                                to_name: pedido.shipping.first_name+" "+pedido.shipping.last_name,
                                to_phone: pedido.customer_phone,
                                to_email: pedido.customer_email,
                                to_document: pedido.customer_document,
                                to_company_document: pedido.customer_company_document,
                                to_state_register: pedido.customer_state_register,
                                to_address: pedido.shipping.address_1,
                                to_complement: pedido.shipping.address_2,
                                to_number:  pedido.shipping.number,
                                to_district: pedido.shipping.neighborhood,
                                to_city:    pedido.shipping.city,
                                to_state_abbr: pedido.shipping.state,
                                to_country_id: pedido.shipping.country,
                                to_postal_code: pedido.shipping.postcode,
                                to_note: pedido.customer_note,
                                line_items: pedido.line_items,
                                nf: this.pedidos_nf[ind],
                                key_nf: 'nf-e',
                                company_document: pedido_cnpj,
                                company_state_register: pedido_ie,
                                agency: this.endereco.agency
                            }
                        }
                    }
                    
                    jQuery.post(ajaxurl, data, function(response) {
                        resposta = JSON.parse(response);
                        if(typeof resposta.id != 'undefined'){ 
                            vm.payment_tracking_codes.push(resposta.id);
                            vm.addTracking(pedido.id,resposta.id,data.service_id);
                            pedido.tracking_code = resposta.id;
                            pedido.bought_tracking = data.service_id;
                            pedido.status = 'cart';
                            retorno = true;
                            vm.succes_desc[ind] = 'Adicionado com Sucesso';
                            pedido.line_items.splice(ind);
                            
                        }else{
                            vm.error_desc[ind] = "Erro nos dados de envio, Verifique os dados painel de Pedidos do Woocommerce";
                        }
                        return retorno;
                    });
                }
                return retorno;
            },

            getUser: function(){
                var data = {
                    security:this.security_read,
                    action:'wpmelhorenvio_ajax_getCustomerInfoAPI',
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    vm.user_info.firstname = resposta.firstname;
                    vm.user_info.lastname = resposta.lastname;
                    vm.user_info.thumbnail = resposta.thumbnail;
                });
            },

            getBalance: function(){
                var data = {
                    action:'wpmelhorenvio_ajax_getBalanceAPI',
                    security:this.security_read
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    try{resposta = JSON.parse(response);
                        vm.user_info.balance = resposta.balance;
                    }catch (err){
                        vm.message.title = 'Erro ao carregar seus dados';
                        vm.message.message = 'Erro ao carregar seus dados, tente novamente mais tarde.';
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }
                });
            },

            deleteTracking: function(tracking){
                this.toogleLoader();
                data = {
                    action:'wpmelhorenvio_ajax_cancelTicketData',
                    tracking: tracking,
                    security:this.security_action
                };
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    console.log(response);
                    vm.toogleLoader();
                    vm.getBalance();
                    vm.getLimits();
                });
            },

            updateQuotation: function(id){
                this.toogleLoader();
                data = {
                    action:'wpmelhorenvio_ajax_update_quotation_order',
                    id: id,
                    security:this.security_action
                };
                vm = this;
                
                jQuery.post(ajaxurl,data,function(response){
                    location.reload();
                });
            },

            getLimits: function(){
                var data = {
                    action:'wpmelhorenvio_ajax_getLimitsAPI',
                    security:this.security_read
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    try{resposta = JSON.parse(response);
                        vm.user_info.shipments = resposta.shipments;
                        vm.user_info.available_shipments = resposta.shipments_available;
                    }catch (err){
                        vm.message.title = 'Erro ao carregar seus dados';
                        vm.message.message = 'Erro ao carregar seus dados, tente novamente mais tarde.';
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }
                });
            },

            getCompany: function(){
                var data = {
                    action:'wpmelhorenvio_ajax_getCompanyAPI',
                    security: this.security_read
                };
                vm  = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    vm.company = resposta;

                });
            },

            PrintMultiple: function(){
                var trackings = [];
                if(this.pedidos_checked.length < 1 || ! this.pedidos_checked.find(function(data){ return data == true;}) ){
                    vm.message.title = "Nenhuma etiqueta foi impressa";
                    vm.message.message = "Selecione as etiquetas para impressão";
                    vm.message.type= "error";
                    vm.message.show_message = true;
                }else{
                    for(var i = 0; i < this.pedidos_checked.length; i++){
                        if(this.pedidos_page[i].status == 'paid' && (this.pedidos_checked[i] == true)){
                            trackings.push(this.pedidos_page[i].tracking_code)
                        }
                    }
                    if(trackings.length > 0){
                        data = {
                            action: 'wpmelhorenvio_ajax_ticketPrintingAPI',
                            tracking: trackings,
                            security: this.security_action
                        };
                        vm = this;
                        jQuery.post(ajaxurl,data,function(response){
                            resposta = JSON.parse(response);
                            if(typeof resposta.url ){
                                window.open(resposta.url,'_blank');
                            }else{
                                vm.message.title = "Não foi possível acessar esta etiqueta";
                                vm.message.message = "Infelizmente não é possível acessar esta etiqueta";
                                vm.message.type= "error";
                                vm.message.show_message = true;
                            }
                        });
                    }else{
                        vm.message.title = "Nenhuma etiqueta válida foi selecionada";
                        vm.message.message = "Selecione as etiquetas, já pagas, para impressão";
                        vm.message.type= "error";
                        vm.message.show_message = true;
                    }
                }

            }
        }

    })
</script>

