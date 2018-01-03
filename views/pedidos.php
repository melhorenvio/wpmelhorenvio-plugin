
<script>
    jQuery(document).ready(function($) {

// site preloader -- also uncomment the div in the header and the css style for #preloader
        $(window).load(function(){
            $('.loader').fadeOut('slow',function(){$(this).remove();})
            $('.content').show();
        });

    });
</script>

<style>


    .content{
        display: none;
    }
    .loader{
        position: absolute;
        right: 44%;
        top: 40%;
        z-index: 1;
        width: 150px;
        height: 150px;
        display: block;
        border: solid 30px rgba(100,100,100,.1);
        border-top-color:rgba(100,100,200,1) ;
        border-bottom-color:rgba(100,100,200,1) ;
        border-radius: 50%;
        animation:load 2s linear infinite ;
    }

    @keyframes load {
        0%{
            transform: rotate(0deg);

        }

        100%{
            transform: rotate(359deg);
        }
    }



    .mask{
        width: 100vw;
        height: 100vh;
        background-color: rgba(20,20,20,.6);
        position: fixed;
        top:0;
        left: 0;
        display: flex;
    }

    .modal h1{
        padding: 15px;
    }

    .modal{
        background-color:rgba(250,250,255,.90);
        max-width: 80%;
        display: block;
        width: 800px;
        margin:auto;
        left: 25%;
        top: 20%;
        height: 290px;
        padding: 20px;
        text-align: center;
        position: absolute;
        border-radius:10px;
        overflow: auto;
    }

    .modal a.btn.cancelar{
        border-radius: 30px;
        height: 50px;
        padding: 15px;
        color:rgba(159,80,100,1);
        font-size: 1.2rem;
        border:solid 2px rgba(159,80,100,1);
    }

    .modal a.btn.cancelar:hover{
        background-color: rgba(159,80,100,1);
        color: rgba(250,250,250,.9);
        border:solid 2px rgba(100,100,100,0.1);
    }

    .modal a.btn{
        margin: 30px 15px;
        border-radius: 30px;
        height: 50px;
        padding: 15px;
        font-size: 1.2rem;
        border:solid 2px rgba(100,100,100,0.8);
        color: rgba(100,100,100,0.8);
    }

    .modal a.btn:hover{
        background-color: rgba(100,100,100,0.8);
        color: rgba(255,255,255,.9);
        border:solid 2px rgba(100,100,100,0.1);
    }

    .modal .select{
        display: flex;
        flex-wrap: wrap;
        padding: 15px;
        position: relative;
        align-items: center;
        horiz-align: center;
        vert-align: middle;
        width: 750px;
    }

    .modal a{
        display: inline-block;
        padding: 15px;
        border-radius: 8px;
        box-sizing: border-box;
        border: solid 2px rgba(50,180,250,0);
        transition: 300ms;
        margin: 0px 20px;
        width: 200px;
        height: 100px;
    }

    .modal a img{
        width: 120px;
        margin: auto;
        vertical-align: middle;
    }
    .modal a:hover{
        border: solid 2px rgba(50,180,250,1);
        transition: 300ms;

    }

    .modal a .pgsaldo{
        height: 100px;
        display: inline-block;
        text-decoration: none;
    }
    .modal a .pgsaldo h4{
        padding: 0px;
        font-size: 1.1rem;
        margin: 0px;
    }

    .modal a .pgsaldo p{
        color:rgba(50,205,50,1);
    }

    .modal .close-modal ,.modal .close-modal:focus {
        color:rgba(100,100,100,.8);
        position: absolute;
        font-size: 3.100rem;
        text-decoration: none;
        border: none !important;
        width: 20px;
        height: 10px;
        top: 0px;
        right: 10px;
        box-shadow: none;
    }

    table{
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }

    thead td{
        background-color: rgba(50,50,150,0.08) !important;
        text-align: left;
    }
    tr{
        width: 100%;
        padding: 0px;
        transition: 300ms;
    }
    th{
        background-color: rgba(90,90,200,.12);
        border: none 1px rgba(90,90,200,.12);
        margin: 0;
        padding: 10px 20px;
    }
    td{
        padding: 0 5px;
        text-align: center;
        margin: 0;
    }

    td input{
        border-radius: 3px;
        border: solid 1px rgba(210,210,210,.9);
        line-height: 20px;
        height: 35px;
    }

    tr:hover{
        background-color: rgba(0,0,200,0.06);
        transition: 300ms;
    }

    tr:nth-child(even):hover{
        background-color: rgba(0,0,200,0.06);
        transition:300ms;
    }

    tr:nth-child(even){
        background-color: rgba(0,0,200,0.02);
    }

    .btn{
        display: inline-block;
        padding: 8px 18px;
        margin: 13px 4px;
        text-decoration: none;
        border: solid 1px rgba(130,130,220,.9);
        border-radius:25px;
        color: rgba(130,120,220,.9);
        transition: 200ms
        float:left;
    }

    .btn:hover{
        background-color: rgba(130,130,220,.9);
        color: rgba(255,255,255,.99);
        transition: 200ms;
    }

    .btn.comprar{
        border:solid 1px rgba(80,190,130,1);
        color: rgba(110,190,130,1);
    }

    .btn.comprar:hover{
        background-color: rgba(80,200,130,1);
        color: rgba(255,255,255,.9);
    }

    .btn.comprar-hard{
        border:solid 1px rgba(50,180,100,1);
        color: rgba(110,190,130,1);
    }

    .btn.comprar-hard:hover{
        background-color: rgba(50,180,100,1);
        color: rgba(255,255,255,.9);
    }

    .btn.cancelar{
        border:solid 1px rgba(160,10,50,.6);
        color: rgba(160,10,40,.6);
    }

    .btn.cancelar:hover{
        background-color: rgba(150,10,50,.6);
        border:solid 1px rgba(160,10,50,.1);
        color: rgba(255,255,255,.9);
    }

    .btn.melhorenvio{
        border:solid 1px rgba(30,140,160,1);
        color: rgba(30,140,160,.9);
    }

    .btn.melhorenvio:hover{

        background-color: rgba(30,140,160,1);
        color: rgba(255,255,255,.9);
    }

    .btn.melhorrastreio{
        border:solid 1px rgba(234,108,01,.8);
        color: rgba(234,108,01,.8);
    }

    .btn.melhorrastreio:hover{
        background-color: rgba(234,108,01,.8);
        color: rgba(255,255,255,.9);
    }

    select{
        box-shadow: none;
        height: 35px !important;
        line-height: 40px !important;
        border-radius: 7px !important;
        padding: 15px 35px;
        font-size: .940em ;
    }

    .selected{
        background-color: rgba(40,230,150,0.3); ;
    }

    tfoot tr td{
        background-color: rgba(50,50,150,0.05) !important;
        text-align: left;
    }

    table ul{
        padding: 0;
        margin: 5px;
    }

    table ul li{
        padding: 0px;
        margin: 0px;
        line-height: 15px;
    }

    .wpme_pagination{
        display: inline;
        margin: auto;
        width: inherit;
        text-align: center;
    }

    .wpme_pagination li{
        display: inline-block;
        text-align: center;
        padding: 0px 0px 0px -1px;
    }

    .wpme_pagination_wrapper{
        display: block;
        margin: 20px auto;
        text-align: center;
        width: 100%;
    }

    .wpme_pagination li  a{
        border: solid 1px rgba(190,190,190,.8);
        box-sizing: border-box;
        padding: 7px 12px;
        background-color: #fff;
        text-decoration: none;
        color: rgba(30,120,200,1);
        transition: 200ms;
    }

    .wpme_pagination li a:hover{
        transition: 200ms;
        background-color: rgba(230,230,255,1);
    }

    .wpme_pagination li.active a{
        background-color: rgba(30,160,230,.8);
        color: rgba(255,255,255,.95);

    }

    .wpme_pagination li a:active{
        outline: none;
        box-shadow: none;
    }

    .wpme_pagination li a:focus{
        outline: none;
        box-shadow: none;
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
        width: 300px;
        box-sizing: border-box;
        min-width: 300px;
        margin:15px 7px;
        height:160px;
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

    .circle{
        display: inline-block;
        vertical-align: middle;
        width: 10px;
        height: 10px;
        margin: 0 5px;
        border: solid 1px rgba(240,50,50,.95);
        border-radius: 50%;
    }

    .circle.true{
        background-color: rgba(50,180,40,1);
        border: solid 1px rgba(40,170,40,.95);
    }


    .wpme_optionals{
        text-align: left;
        border-radius: 5px;
        border: solid 1px #cccccc;
        padding: 10px;
        width: 300px;
        box-sizing: border-box;
        min-width: 300px;
        margin:15px 7px;
        background: #fefeff;
        height:160px;
    }
    .wpme_optionals ul{
        margin: 0;
    }
    .data_client{
        display: flex;
    }

    .profile{
        box-sizing: border-box;
        display: inline-block;
        text-align: left;
        border-radius: 5px;
        border: solid 1px #cccccc;
        padding: 10px;
        max-width: 400px;
        min-width: 300px;
        margin:0 7px;
        height:160px;
        background: #fefeff;
    }

    .profile img{
        border-radius:50%;
        float: left;
        display: inline-block;
    }

    .profile .about{
        float: left;
        padding: 0 10px;
        display: inline-block;
    }

    .wpme_message{
        position:absolute;
        text-align: center;
        top:30%;
        right: 40%;
        border-radius:5px ;
        display: inline-block;
        height: 200px;
        width: 500px;
        max-width: 80%;
        background-color: rgba(244,255,255,.95);
    }

    .wpme_message_header{
        font-size:1.65rem;
        padding: 23px;
    }

    .wpme_success{
        color:rgba(80,199,100,1);
    }

    .wpme_error{
        color:rgba(199,80,100,1);
    }

    .wpme_message_body{
        vertical-align: middle;
        padding: 10px 15px 10px;

    }

    .wpme_message_comprar{
        float: left;
        margin:10px;

    }

    .wpme_message_action{
        float:left;
        margin:10px;
    }

    .wpme_message_comprar a{
        text-decoration: none;
        border-radius: 20px;
        font-size: 1.05rem;
        display: inline-block;
        padding: 10px 24px;
        border: solid 1px rgba(100,150,250,1);
        color: rgba(100,150,250,1);;
        bottom: 10px;
    }

    .wpme_message_comprar a:hover{
        text-decoration: none;
        border-radius: 20px;
        font-size: 1.05rem;
        display: inline-block;
        padding: 10px 24px;
        border: solid 1px rgba(100,150,250,1);
        background-color: rgba(100,150,250,1);
        color: rgba(250,250,250,1);;
        bottom: 10px;
    }
    .wpme_message_action a{
        text-decoration: none;
        border-radius: 20px;
        font-size: 1.05rem;
        display: inline-block;
        padding: 10px 24px;
        border: solid 1px rgba(100,110,150,1);
        color: rgba(100,110,150,1);;
        bottom: 10px;
    }

    .wpme_message_action a:hover{
        text-decoration: none;
        border-radius: 20px;
        font-size: 1.05rem;
        display: inline-block;
        padding: 10px 24px;
        border: solid 1px rgba(100,110,150,1);
        background-color: rgba(100,110,150,1);
        color: rgba(250,250,250,1);;
        bottom: 10px;
    }

    .wpme_wrapper_center{
        display: inline-block;
        margin: auto;
        width: fit-content;
        text-align: center;
    }

</style>
<div id="app">
    <div class="loader">

    </div>
    <div class="content">
        <div class="data_client">
            <div>
                <h5>Usuário</h5>
                <div class="profile">
                    <img :src="user_info.thumbnail" v-if="user_info.thumbnail" width="100px">
                    <img src="https://www.melhorrastreio.com.br/img/bgpdr.png" v-if="!user_info.thumbnail" width="100px">
                    <div class="about">
                        <h2>{{user_info.firstname}}</h2>
                        <ul>
                            <li>Saldo: R$  <strong>{{user_info.balance}}</strong></li>
                            <li>Limite: <strong>{{user_info.shipments}}</strong></li>
                            <li>Liberado:  <strong>{{user_info.available_shipments}}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h5>Endereço</h5>
                <div class="wpme_address">
                    <div class="wpme_address-top"><h2>{{endereco.label}}</h2>
                    </div>
                    <div class="wpme_address-body">
                        <ul>
                            <li>{{endereco.address}}, {{endereco.number}} -{{endereco.complement}}</li>
                            <li>{{endereco.district}} - {{ endereco.city.city}} / {{endereco.city.state.state_abbr}}</li>
                            <li>CEP: {{endereco.postal_code}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <h5>Opcionais</h5>
                <div class="wpme_optionals">
                    <ul>
                        <li>  Aviso de Recebimento :<span class="circle" :class="{'true' : opcionais.AR}"> </span></li>
                        <li>  Disponível para o cliente:<span class="circle" :class="{'true' : opcionais.CF}"> </span></li>
                        <li>  Dias extras: {{opcionais.DE}}</li>
                        <li>  Mão Própria :<span class="circle" :class="{'true' : opcionais.MP}"> </span></li>
                        <li>  Porcentagem de lucro {{opcionais.PL}}%</li>
                        <li>  Valor Decladado:<span class="circle" :class="{'true' : opcionais.VD}"> </span></li>
                    </ul>
                </div>
            </div>
            <div><a href="<?= get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-config")?>">Editar configurações</a></div>

        </div>
        <table>
            <thead>
            <tr>
                <td width="10px"><input type="checkbox" @click="selectall" v-model="selectallatt"></td>
                <td colspan="6">
                    <a href="javascript;" class="btn comprar-hard"> Comprar </a>

                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>

                    <a href="javascript;" class="btn imprimir"> Imprimir </a>
                </td>
            </tr>
            <tr><th></th>
                <th width="50px">Pedido</th>
                <th width="50px">Data</th>
                <th width="400px">Destinatário</th>
                <th width="200px">Transportadora</th>
                <th>Dados adicionais</th>
                <th>Opções</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(pedido,i) in pedidos_page">

                <td><input type="checkbox" v-model="pedidos_checked[pedido.id]" :value="pedido"></td>
                <td>{{pedido.id}}
                </td>
                <td>{{pedido.date_created}}23/09/2017 </td>
                <td>
                    <ul>
                        <li><strong>{{pedido.shipping.first_name}} {{pedido.shipping.last_name}} </strong></li>
                        <li>{{pedido.shipping.address_1}} {{pedido.shipping.address_2}} - {{pedido.shipping.postcode}}</li>
                        <li>{{pedido.shipping.neighborhood}} - {{pedido.shipping.city}}/{{pedido.shipping.state}}</li>
                    </ul>
                </td>
                <td>
                    <span v-if="pedido.bought_tracking"  v-for="cotacao in pedido.cotacoes"><template v-if="(cotacao.id == pedido.bought_tracking)">{{cotacao.company.name}} {{cotacao.name}} |  {{cotacao.delivery_time}}  dia<template v-if="cotacao.delivery_time > 1">s</template> | {{cotacao.currency}} {{cotacao.price}}</template></span>

                    <select class="select" v-model="selected_shipment[i]" v-if="!pedido.bought_tracking">
                        <option v-for="cotacao in pedido.cotacoes"
                                v-if="(! cotacao.error )"
                                :class="{'selected': pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)}" :value="cotacao.id"
                        >{{cotacao.company.name}} {{cotacao.name}} | {{cotacao.delivery_time}}  dia<template v-if="cotacao.delivery_time > 1">s</template> | {{cotacao.currency}} {{cotacao.price}}</option>
                    </select>
                </td>
                <td>
                    <template v-if="!pedido.bought_tracking">
                        <strong> NF :</strong> <input v-model="pedidos_nf[i]"><br>
                        <template v-if="company.cnpj == ''"><strong>CNPJ:</strong> <input v-model="pedidos_cnpj[i]"><br></template>
                        <template v-if="company.ie   == ''"><strong> IE :</strong> <input v-model="pedidos_ie[i]"><br></template>
                    </template>
                    <template v-if="pedido.bought_tracking" ><p class="wpme_success">Ok!</p></template>
                </td>
                <td>
                    <template  v-if="pedido.status != 'cart' && pedido.status != 'paid' && pedido.status != 'printed'">
                        <a href="javascript;" class="btn comprar" @click.prevent="addToCart(i)" > Comprar </a>
                    </template>
                    <template v-if="pedido.status == 'cart'">
                        <a href="javascript;" class="btn melhorenvio" @click.prevent="openSinglePaymentSelector(pedido.tracking_code)"> Pagar </a>
                        <a href="javascript;" class="btn cancelar" @click.prevent="showCon" > Remover </a>
                    </template>
                    <!--                    <a href="javascript;" class="btn comprar"> Comprar </a>-->
                    <!--                    <a href="javascript;" class="btn melhorenvio" @click.prevent="payTicket(tracking_codes[pedido.id])"> Pagar </a>-->
                    <template v-if="pedido.status == 'paid'">
                        <a href="javascript;" class="btn imprimir" @click.prevent="printTicket(pedido.tracking_code)"> Imprimir </a>
                        <a href="javascript;" class="btn cancelar" @click.prevent="openCancelTicketConfirmer(pedido.tracking_code)" > Cancelar </a>
                    </template>
                    <!--                    <a href="javascript;" class="btn melhorrastreio"> Rastreio </a>-->
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td colspan="6">
                    <a href="javascript;" class="btn comprar-hard"> Comprar </a>

                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>

                    <!--                    <a href="javascript;" class="btn cancelar"> Imprimir </a>-->
                </td>
            </tr>
            </tfoot>
        </table>
        <div class="wpme_pagination_wrapper">
            <ul class="wpme_pagination" v-for="i in Math.ceil(total/perpage)" v-show="total > perpage">
                <li :class="{'active': i == page}"><a href="javascript:;" @click.prevent="pagego(i)">{{i}}</a></li>
            </ul>
        </div>
        <div class="mask" v-show="show_mask" @click.prevent="toogleModal">
        </div>
        <div class="modal" v-show="show_modal">
            <a href="javascript;" @click.prevent="toogleModal()" class="close-modal"> &times </a>
            <h1>Escolha seu método de pagamento</h1>
            <div class="select">
                <a href="" @click.prevent="payTicket(1)">
                    <img src="https://melhorenvio.com.br/images/payment/moip.png">
                </a>
                <a href="" @click.prevent="payTicket(2)">
                    <img src="https://melhorenvio.com.br/images/payment/mpago.png">
                </a>
                <a href="" @click.prevent="payTicket(99)">
                    <div class="pgsaldo">
                        <h4>Pagar com Saldo</h4>
                        <p>Saldo <strong>{{user_info.balance}}</strong></p>
                    </div>
                </a>
            </div>
            <p><strong>Escolha o método de pagamento para finalizar a sua compra</strong></p>
        </div>
        <div class="mask" v-show="show_confirm_mask" @click.prevent="toogleConfirmer">
        </div>
        <div class="modal" v-show="show_confirm_modal">
            <a href="javascript;" @click.prevent="toogleConfirmer" class="close-modal"> &times </a>
            <h1 class="wpme_error">Você tem certeza que deseja cancelar?</h1>
            <p>Ao clicar em "Quero Cancelar" a etiqueta se torna inutilizavel.</p>
            <a href="javascript;" class="btn cancelar" @click.prevent="cancelTicket()">Quero cancelar</a>  <a href="javascript;" @click.prevent="toogleConfirmer" class="btn">Fechar</a>

        </div>


        <div class="mask" v-if="message.show_message" @click.prevent="toogleMessage"></div>
        <div class="wpme_message" v-if="message.show_message" >
            <div class="wpme_message_header" :class="{'wpme_success': message.type == 'success', 'wpme_error': message.type == 'error'}">{{message.title}}</div>
            <div class="wpme_message_body">{{message.message}}</div>
            <div class="wpme_wrapper_center">
                <template v-if="payment_tracking_codes.length > 0">
                    <div class="wpme_message_comprar"><a href="javascript;" @click.prevent="goDirectPay">Pagar</a></div>
                </template>
                <div class="wpme_message_action"><a href="javascript;" @click.prevent="toogleMessage">Fechar</a></div>
            </div>
        </div>


    </div>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            payment_tracking_codes:[],
            pedidos: [],
            total:0,
            company:{
                cnpj:'',
                ie:''
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
                    state:"",
                },
            },
            pedidos_checked:[],
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
            show_confirm_mask:false,

        },

        created: function(){
            this.load();
        },

        computed:{

            pedidos_page: function () {
                this.total = this.pedidos.length;
                return this.pedidos.slice(((this.page -1) * this.perpage), this.page*this.perpage);
            },

            selected_shipmeent: function () {
                var array = [];
                this.pedidos_page.forEach(function (pedido,index) {
                    pedido.cotacoes.forEach(function (cotacao) {
                        if( pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name))
                            array[index] = cotacao.id;
                    });
                });
                return array;
            }


        },

        methods: {
            utf8_from_str: function(s) {
                for(var i=0, enc = encodeURIComponent(s), a = []; i < enc.length;) {
                    if(enc[i] === '%') {
                        a.push(parseInt(enc.substr(i+1, 2), 16))
                        i += 3
                    } else {
                        a.push(enc.charCodeAt(i++))
                    }
                }
                return a
            },

            stripcode: function(string) {
                string = string.replace('wpme_','');
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

            goDirectPay: function(){
                this.toogleMessage();
                this.toogleModal();
            },

            addToCart: function(ind){
                pedido = this.pedidos_page[ind];
                vm = this;
                console.log(ind);
                if(this.company.cnpj != ''){
                    pedido_cnpj = this.company.cnpj;
                }else {
                    pedido_cnpj = this.pedidos_cnpj[ind];
                }
                if(this.company.ie != ''){
                    pedido_ie = this.company.ie;
                }else{
                    pedido_ie = this.pedidos_ie[ind];
                }

                if(this.selected_shipment[ind] > 2 && (typeof this.pedidos_nf[ind] === 'undefined' || typeof pedido_cnpj === 'undefined' || typeof pedido_ie === 'undefined') ){
                    vm.message.title = 'Dados Incompletos';
                    vm.message.message = 'Para utilizar essa transportadora, informe a nota fiscal (NF) e os dados da empresa (CNPJ/IE) ';
                    vm.message.type = 'error';
                    vm.message.show_message = true;
                }else{
                    if(this.selected_shipment < 3){
                        var data = {
                            action: "wpme_ajax_ticketAcquirementAPI",
                            valor_declarado: pedido.price,
                            service_id: this.selected_shipment[ind],
                            from_name: this.user_info.firstname+" "+ this.user_info.lastname,
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
                            line_items: pedido.line_items
                        }
                    }else{
                        var data = {
                            action: "wpme_ajax_ticketAcquirementAPI",
                            valor_declarado: pedido.price,
                            service_id: this.selected_shipment[ind],
                            from_name: this.user_info.firstname+" "+ this.user_info.lastname,
                            from_company_document : this.company.cnpj,
                            from_company_state_register: this.company.ie,
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

                    jQuery.post(ajaxurl, data, function(response) {
                        console.log(response);
                        resposta = JSON.parse(response);
                        if(typeof resposta.id != 'undefined'){
                            vm.payment_tracking_codes = [];
                            vm.payment_tracking_codes.push(resposta.id);
                            vm.addTracking(pedido.id,resposta.id,data.service_id);
                            pedido.tracking_code = resposta.id;
                            pedido.bought_tracking = data.service_id;
                            pedido.status = 'cart';

                            vm.message.title = 'Envio adicionado ao carrinho';
                            vm.message.message = 'Este envio foi adicionado ao seu carrinho, clique em pagar para gerar a sua etiqueta.';
                            vm.message.type = 'success';
                            vm.message.show_message = true;
                        }else{
                            resposta = JSON.parse(response);
                            vm.message.title = 'Não foi possível adicionar item ao carrinho';
                            vm.message.message = 'Infelizmente não foi possível adicionar este item ao seu carrinho --'+resposta.errors;
                            vm.message.type = 'error';
                            vm.message.show_message = true;
                        }
                    });
                }
            },

            getQuotation: function(){

            },

            load: function(){
                this.getUser();
                this.getLimits();
                this.getAddress();
                this.getBalance();
                this.getOptionals();
                this.getOrders();
            },

            getAddress: function(){
                data = {
                    action: "wpme_ajax_getAddressAPI"
                }
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
                this.cancel_tracking_codes = []
                this.cancel_tracking_codes.push(tracking);
                this.toogleConfirmer();
            },

            cancelTicket: function(){
                this.toogleConfirmer();
                data = {
                    action: 'wpme_ajax_cancelTicketAPI',
                    tracking: this.cancel_tracking_codes
                }
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    console.log(response);
                    resposta = JSON.parse(response);
                    arr_index = Object.entries(resposta);
                    console.log(arr_index[0][1]['canceled']);
                    if(arr_index[0][1]['canceled']){
                        vm.deleteTracking(data.tracking);
                        vm.message.title = "Etiqueta cancelada com sucesso";
                        vm.message.message = "Após a verificação o valor deverá ser extornado para a carteira";
                        vm.message.type= "success";
                        vm.message.show_message = true;
                    }else{
                        vm.message.title = "Não foi possível cancelar esta etiqueta";
                        vm.message.message = "Infelizmente não é possível cancelar esta etiqueta";
                        vm.message.type= "error";
                        vm.message.show_message = true;
                    }

                });
            },

            printTicket: function(tracking){
                data = {
                    action: 'wpme_ajax_ticketPrintingAPI',
                    tracking: [tracking]
                }
                vm = this;
                console.log(tracking);
                jQuery.post(ajaxurl,data,function(response){
                    resposta = JSON.parse(response);
                    console.log(resposta);
                    if(typeof resposta.url ){
                        console.log(resposta.url)
                        window.open(resposta.url,'_blank');
                    }else{
                        vm.message.title = "Não foi possível acessar esta etiqueta";
                        vm.message.message = "Infelizmente não é possível acessar esta etiqueta";
                        vm.message.type= "error";
                        vm.message.show_message = true;
                    }
                });
            },


            getOptionals: function(){
                data = {
                    action: "wpme_ajax_getOptionsAPI"
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
                        action:'wpme_ajax_getTrackingsData',
                        order_id: pedido.id,
                        timeout:30
                    }
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
                    action:'wpme_ajax_getTrackingsData',
                    order_id: pedido.id,
                    timeout:30
                }
                console.log(pedido);
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    console.log(pedido);
                    resposta.forEach(function(tracking){
                        trk = tracking.tracking_id;
                        pedido.tracking_code = trk;
                        pedido.bought_tracking = tracking.service_id;
                        pedido.status = tracking.status;
                    })
                });
            },


            payTicket: function(payment_method){
                var data = {
                    action:'wpme_ajax_payTicketAPI',
                    orders: this.payment_tracking_codes,
                    gateway: payment_method

                }

                console.log(data);
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    vm.toogleModal();
                    console.log(response);
                    resposta = JSON.parse(response)
                    if(typeof resposta.error !== 'undefined'){
                        vm.payment_tracking_codes = [];
                        vm.message.title="Pagamento não efeituado";
                        vm.message.message=resposta.error;
                        vm.message.type = 'error';
                        vm.message.show_message = true;
                    }else{
                        data.orders.forEach(function(order){
                            vm.updateTracking(order,'paid');
                            vm.payment_tracking_codes = [];
                            vm.pedidos_page.forEach( function (pedido) {
                                if(pedido.tracking_code == order)
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
                    //TODO:Escrever a mensagem de sucesso e ou de erro
                });
            },

            updateTracking: function(tracking_code,status){
                var data = {
                    action: "wpme_ajax_updateStatusData",
                    tracking_code:tracking_code,
                    status:status
                }
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    console.log(resposta);
                });

            },

            getTracking: function(){
                tracking_codes = [];
                this.pedidos.forEach(function (pedido) {
                    tracking_codes.push(pedido.id);
                });
                var data = {
                    action:'wpme_ajax_getTrackingAPI',
                    tracking_codes: tracking_codes
                }
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                });
            },

            addTracking: function(order_id,tracking,service){
                var data = {
                    action: "wpme_ajax_addTrackingAPI",
                    order_id:order_id,
                    tracking:tracking,
                    service: service
                }
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    if(tracking != null){
                        vm.tracking_codes[order_id]= tracking;
                    }
                });
            },

            getOrders: function(){
                var data = {
                    action:'wpme_ajax_getJsonOrders',
                    timeout:30
                };

                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    var array = [];
                    try{
                        resposta.forEach(function (pedido,index) {
                            pedido.cotacoes.forEach(function (cotacao) {
                                if( pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)){
                                    array[index] = cotacao.id;
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
                });
            },
            //TODO: fAZER O VARIOS, ARRUMAR PRA FUNCIONAR COM A JADLOG, REMOVER CARRINHO,
            pagego: function(valor){
                this.page = valor;
            },

            selectall: function (){
                var vm = this;
                this.pedidos_page.forEach( function (pedido) {
                    vm.pedidos_checked[pedido.id] = !vm.selectallatt
                });
            },

            getUser: function(){
                var data = {
                    action:'wpme_ajax_getCustomerInfoAPI',
                }
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
                    action:'wpme_ajax_getBalanceAPI'
                }
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    console.log(response);
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
                data = {
                    action:'wpme_ajax_cancelTicketData',
                    tracking: tracking
                }
                vm = this;
                jQuery.post(ajaxurl,data,function(response){
                    console.log(response);
                    vm.getBalance();
                    vm.getLimits();
                    vm.getOrders();
                });
            },

            getLimits: function(){
                var data = {
                    action:'wpme_ajax_getLimitsAPI'
                }
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    console.log(response);
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
            }
        }

    })
</script>

