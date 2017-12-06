var app = new Vue({
    el: '#app',
    data: {
        message: 'Hello Vue!',
        pedidos: [],
        user_token: [],
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
            axios({
                method:'get',
                url:'https://melhorenvio.com.br/api/v2/me',
                headers:{
                    'Authorization': 'Bearer '+this.user_token,
                    'Accept' : 'application/json'
                }
            })
                .then(function(response) {
                    console.log(response.data)
                    this.user_info = response.data
                });
        }


    }
})