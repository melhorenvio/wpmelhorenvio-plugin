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


    .wpme_mainform{
        color: white;
        width: 70%;
        margin: auto;
        text-align: center;
        font-size: 1.200rem;
        vertical-align: middle;
    }

    .wpme_mainform form{
        display: inline-block;
        width: 100% !important;
    }

    .wpme_inputtext{
        border: none !important;
        border-bottom: solid 1px #ffffff !important;
        background-color:transparent !important;
        margin:30px 0 0;
        width:100%;
    }

    .wpme_inputtext:focus{
        border: none !important;
        box-shadow: none !important;
        border-bottom: solid 2px #ffffff !important;
        transition: 100ms;
        background-color:transparent !important;
        margin:29px 0 0;
        width:100%;
    }

    .wpme_body_init{
        background-image: url(<?=plugins_url("../img/backhome.png",__FILE__ )?>);
        background-attachment: fixed;
        background-repeat: no-repeat;
        background-size: cover;
        margin: 20px 0;
        height: 80vh;
        display: flex;
        color:#DDDDFF;
        padding: 10px;
    }

    .wpme_body_init h1 {
        color: #ffffff;
        text-align: center;
    }

    .wpme_body_init p {
        padding: 1px;
        line-height: 1;
    }

    .wpme_body_init a{
        color:#e7505a;
        text-decoration: none;
    }

    .wpme_body_init a:hover{
        text-decoration: underline;
    }
</style>

<div class="wpme_body_init">
    <div class="wpme_mainform">
        <div class="wpme_tutorial">
            <h1>Melhor Envio</h1>
        </div>
    <form>

       <label>Cole aqui seu Token de acesso</label>
        <input type="text" class="wpme_inputtext"> <br>
        <p>Para utilizar o Plugin é necessário estar cadastrado no <a href="https://melhorenvio.com.br">Melhor Envio</a>.</p>
        <p>Encontre seu <a href=""> Token de Acesso</a></p>
        <button class="wpme_button" type="submit">Salvar</button>
    </form>
    </div>
</div>