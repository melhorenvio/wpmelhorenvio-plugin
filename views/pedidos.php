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
    });
</script>

<div id="app">
    <div class="loader">
    </div>
    <div class="content">
        <div class="data-client">
            <div class="data-client__item -profile">
                <h5>Usuário</h5>
                <div>
                    <img :src="user_info.thumbnail" v-if="user_info.thumbnail" width="100px">
                    <img src="<?=plugins_url("assets/img/bgpdr.png",__DIR__ )?>" v-if="!user_info.thumbnail" width="100px">
                    <div class="about">
                        <h2>{{user_info.firstname}}</h2>
                        <ul>
                            <li><p>Saldo: R$ <strong>{{user_info.balance}}</strong></p></li>
                            <li><p>Limite: <strong>{{user_info.shipments}}</strong></p></li>
                            <li><p>Liberado:  <strong>{{user_info.available_shipments}}</strong></p></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="data-client__item -address">
                <h5>Endereço</h5>
                <div>
                    <h2>{{endereco.label}}</h2>
                    <div>
                        <ul>
                            <li><p>{{endereco.address}}, {{endereco.number}} -{{endereco.complement}}</p></li>
                            <li><p>{{endereco.district}} - {{ endereco.city.city}} / {{endereco.city.state.state_abbr}}</p></li>
                            <li><p>CEP: {{endereco.postal_code}}</p></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="data-client__item -options">
                <h5>Opcionais</h5>
                <div>
                    <ul>
                        <li><p><span>Aviso de Recebimento:</span> <span class="circle" :class="{'true' : opcionais.AR}"></span></p></li>
                        <li><p><span>Disponível para o cliente:</span> <span class="circle" :class="{'true' : opcionais.CF}"></span></p></li>
                        <li><p><span>Dias extras:</span> <span>{{opcionais.DE}}</span></p></li>
                        <li><p><span>Mão Própria:</span> <span class="circle" :class="{'true' : opcionais.MP}"></span></p></li>
                        <li><p><span>Porcentagem de lucro</span> {{opcionais.PL}}%</p></li>
                        <li><p><span>Valor Declarado:</span> <span class="circle" :class="{'true' : opcionais.VD}"></span></p></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="link-box"><a href="<?= get_admin_url(get_current_blog_id(),"/admin.php?page=wpmelhorenvio_melhor-envio-config")?>">Editar configurações</a></div>

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
            show_confirm_mask:false
        },

        created: function(){
            this.load();
            this.change_updated();
        },

        computed:{
            pedidos_page: function () {
                this.total = this.pedidos.length;
                return this.pedidos.slice(((this.page -1) * this.perpage), this.page*this.perpage);
            }
        },

        methods: {

            change_updated: function () {
                vm = this;
                setInterval(function () {
                    vm.updated = ! vm.updated;
                },100)
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

            toogleMessage: function(){
                this.message.show_message = !this.message.show_message;
            },

            load: function(){
                this.verifyTracking();
                this.getUser();
                this.getCompany();
                this.getLimits();
                this.getAddress();
                this.getBalance();
                this.getOptionals();
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

            getUser: function(){
                var data = {
                    security:this.security_read,
                    action:'wpmelhorenvio_ajax_getCustomerInfoAPI',
                };
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    console.log(response);
                    resposta = JSON.parse(response);
                    console.log(resposta);
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

        }

    })
</script>

