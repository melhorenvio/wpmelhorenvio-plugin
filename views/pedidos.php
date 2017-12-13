<style>
    table{
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }

    thead td{
        background-color: rgba(50,50,150,0.08) !important;
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
        border: solid 1px rgba(130,130,190,.6);
        border-radius:25px;
        color: rgba(130,100,190,.9);
        transition: 200ms;
    }

    .btn:hover{
        background-color: rgba(130,130,190,1);
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

    .btn.imprimir{
        border:solid 1px rgba(160,10,50,.6);
        color: rgba(160,10,40,.6);
    }

    .btn.imprimir:hover{
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

</style>
<div id="app">
    <div>
        <table>
            <thead>
            <tr>
                <td><input type="checkbox" @click="selectall" v-model="selectallatt"></td>
                <td>
                    <a href="javascript;" class="btn comprar-hard"> Comprar </a>
                </td>
                <td>
                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>
                </td>
                <td>
                    <a href="javascript;" class="btn imprimir"> Imprimir </a>
                </td>
                <td colspan="2">

                </td>
            </tr>
            <tr><th></th>
                <th>Pedido</th>
                <th>Data</th>
                <th>Destinatário</th>
                <th>Transportadora</th>
                <th>Opções</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(pedido,i) in pedidos_page">

                <td><input type="checkbox" v-model="pedidos_checked[pedido.id]" :value="pedido"></td>
                <td>{{pedido.id}}</td>
                <td>{{pedido.date_created}}23/09/2017 </td>
                <td>
                    <ul>
                        <li><strong>{{pedido.shipping.first_name}} {{pedido.shipping.last_name}} </strong></li>
                        <li>{{pedido.shipping.address_1}} {{pedido.shipping.address_2}} - {{pedido.shipping.postcode}}</li>
                        <li>{{pedido.shipping.neighborhood}} - {{pedido.shipping.city}}/{{pedido.shipping.state}}</li>
                    </ul>
                </td>
                <td>
                    <select class="select" v-model="selected_shipment[i]">
                        <option v-for="cotacao in pedido.cotacoes"
                                v-if="(! cotacao.error )"
                                :class="{'selected': pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)}" :value="cotacao.id"
                        >{{cotacao.company.name}} {{cotacao.name}} | {{cotacao.delivery_time}}  dia<template v-if="cotacao.delivery_time > 1">s</template> | {{cotacao.currency}} {{cotacao.price}}</option>
                    </select>
                </td>
                <td>
                    <a href="javascript;" class="btn comprar" @click.prevent="addToCart(i)"> Comprar </a>
                    <!--                    <a href="javascript;" class="btn comprar"> Comprar </a>-->
                    <!--                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>-->
                    <!--                    <a href="javascript;" class="btn imprimir"> Imprimir </a>-->
                    <!--                    <a href="javascript;" class="btn melhorrastreio"> Rastreio </a>-->
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td>

                </td>
                <td>
                    <a href="javascript;" class="btn comprar-hard"> Comprar </a>
                </td>
                <td>
                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>
                </td>
                <td>
                    <a href="javascript;" class="btn imprimir"> Imprimir </a>
                </td>
                <td colspan="2">

                </td>
            </tr>
            </tfoot>
        </table>
        <div class="wpme_pagination_wrapper">
            <ul class="wpme_pagination" v-for="i in Math.ceil(total/perpage)" v-show="total > perpage">
                <li :class="{'active': i == page}"><a href="javascript:;" @click.prevent="pagego(i)">{{i}}</a></li>
            </ul>
        </div>

    </div>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            message: 'Hello Vue!',
            pedidos: [],
            total:0,
            pedidos_checked:[],
            selected_shipment: [],
            page:1,
            selectallatt:false,
            perpage:10,
            user_info: {
                firstname:'',
                lastname:'',
                thumbnail:'',
                balance:''
            }
        },

        created: function(){
            this.load()
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
                    })


                });
                return array;
            }


        },
        methods: {
            stripcode: function(string) {
                string = string.replace('wpme_','');
                return string.replace('_','');
            },

            addToCart: function(ind){

                pedido = this.pedidos_page[ind];
                var data = {
                    action: "wpme_ajax_ticketAcquirementAPI",
                    valor_declarado: price,
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
                jQuery.post(ajaxurl, data, function(response) {
                        //resposta = JSON.parse(response);
                        console.log(response);
                });
            },

            getQuotation: function(){

            },

            load: function(){
                this.getOrders();
                this.getUser();
                this.getBalance();
            },

            getOrders: function(){
                var data = {
                    action:'wpme_ajax_getJsonOrders',
                }
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    vm.pedidos = resposta;
                    var array = [];
                    resposta.forEach(function (pedido,index) {
                        pedido.cotacoes.forEach(function (cotacao) {
                            if( pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)){
                                array[index] = cotacao.id;
                            }
                        });
                    });
                    vm.selected_shipment = array;
                });
            },

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
                    resposta = JSON.parse(response);
                    vm.user_info.firstname = resposta.firstname;
                    vm.user_info.lastname = resposta.lastname;
                    vm.user_info.thumbnail = resposta.thumbnail;
                    vm.user_info.balance = resposta.balance;
                });
            },

            getBalance: function(){
                var data = {
                    action:'wpme_ajax_getBalanceAPI'
                }
                vm = this;
                jQuery.post(ajaxurl, data, function(response) {
                    resposta = JSON.parse(response);
                    vm.user_info.balance = resposta.balance;
                });
            }





        }
    })
</script>

