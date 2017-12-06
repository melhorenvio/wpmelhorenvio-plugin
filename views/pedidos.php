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
        padding: 10px;
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
        border:solid 1px rgba(254,78,41,.4);
        color: rgba(254,78,41,.6);
    }

    .btn.melhorrastreio:hover{
        background-color: rgba(254,78,41,.65);
        color: rgba(255,255,255,.9);
    }

    .btn-melhor


</style>
<div id="app">
    <div>
        <table>
            <thead>
            <tr>
                <th><input type="checkbox"></th>
                <th>Pedido</th>
                <th>Data</th>
                <th>Prazo</th>
                <th>Transportadora</th>
                <th>Valor da Compra</th>
                <th>Destinatário</th>
                <th>Opções</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="pedido in pedidos">
                <td></td>
                <td>{{pedido.id}}</td>
                <td>{{pedido.date_created}}</td>
                <td>{{pedido.total}}</td>
                <td><span></span>
                    <span></span>
                </td>
                <td>{{stripcode(pedido.shipping_lines[0].method_id)}}</td>
                <td>
                    <ul>
                        <li>{{pedido.shipping.address_1}} {{pedido.shipping.address_2}} - {{pedido.shipping.postcode}}</li>
                        <li>{{pedido.shipping.neighborhood}} - {{pedido.shipping.city}} / {{pedido.shipping.state}}</li>
                        <li>{{pedido.shipping.first_name}} {{pedido.shipping.last_name}}</li>
                    </ul>
                </td>
                <td>
                    <a href="javascript;" class="btn"> Calcular </a>
                    <a href="javascript;" class="btn comprar"> Comprar </a>
                    <a href="javascript;" class="btn melhorenvio"> Pagar </a>
                    <a href="javascript;" class="btn imprimir"> Imprimir </a>
                    <a href="javascript;" class="btn melhorrastreio"> Rastreio </a>
                </td>
            </tr>
            </tbody>
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
            user_token: "<?php echo get_option('wpme_token')?>",
            user_info: {
                firstname:'',
                lastname:'',
                thumbnail:'',
                document:'',
                balance:'',


            }
        },

        created: function(){
            this.load()
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
            },

            getOrders: function(){

            },

            getUser: function(){
                form_data = new FormData;
                form_data.append('action', 'wpme_getCustomerTrackingAPI');
                axios({
                    method:'post',
                    url:'/jadlogwp/wordpress/wp-admin/admin-ajax.php',
                    data: form_data
                })
                        .then(function(response) {
                            console.log(response.data)
                       this.user_info = response.data
                        console.log(response.data);
                    });
            }


        }
    })
</script>

