<style>
    table{
        width: 100%;
        border-collapse: collapse;
    }
    tr{
        width: 100%;
        transition: 300ms;
    }
    th{
        background-color: rgba(90,90,200,.12);
        border: none 1px rgba(90,90,200,.12);
        margin: 0;
        padding: 10px 20px;
    }
    td{
        padding: 5px;
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
        height: 40px !important;
        line-height: 40px !important;
        border-radius: 7px !important;
        padding: 15px 35px;
        font-size: .900em ;
    }

    .selected{
        background-color: rgba(40,230,150,0.3); ;
    }

    tfoot tr td{
        background-color: rgba(50,50,150,0.05) !important;
    }

    table ul li{
        padding: 0px;
        margin: 0px;
    }

</style>
<div id="app">
    <div>
        <table>
            <thead>
            <tr>
                <th><input type="checkbox"></th>
                <th>Pedido</th>
                <th>Data</th>
                <th>Destinatário</th>
                <th>Transportadora</th>
                <th>Opções</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(pedido,i) in pedidos_page">
                <td><input type="checkbox"></td>
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
                        <option v-for="cotacao in pedido.cotacoes" v-if="(! cotacao.error )"  :class="{'selected': pedido.shipping_lines[0].method_id == 'wpme_'.concat(cotacao.company.name).concat('_').concat(cotacao.name)}" >{{cotacao.company.name}} {{cotacao.name}} | {{cotacao.delivery_time}}  dia<template v-if="cotacao.delivery_time > 1">s</template> | {{cotacao.currency}} {{cotacao.price}}</option>
                    </select>
                </td>
                <td>
                    <a href="javascript;" class="btn comprar"> Comprar </a>
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
                        <a href="javascript;" class="btn comprar-hard"> Comprar </a>
                    </td>
                    <td>
                        <a href="javascript;" class="btn melhorenvio"> Pagar </a>
                    </td>
                    <td>
                        <a href="javascript;" class="btn imprimir"> Imprimir </a>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/vue"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            message: 'Hello Vue!',
            pedidos: <?php  echo wpme_getJsonOrders(); ?>,
            total:0,
            selected_shipment:[],
            page:1,
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


        },
        methods: {
            stripcode: function(string) {
                string = string.replace('wpme_','');
                return string.replace('_','');
            },

            getQuotation: function(){

            },

            load: function(){
                this.getOrders();
                this.getUser();
                this.getBalance();
            },

            getOrders: function(){

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
                    console.log(resposta);
                });
            }





        }
    })
</script>

