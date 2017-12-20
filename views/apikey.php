


<style>
    ::-webkit-scrollbar              { background-color: rgba(50,50,50,0.7);width: 8px; height: 8px; border-radius: 5px;}
    ::-webkit-scrollbar-button       { display: none}
    ::-webkit-scrollbar-track        { /* 3 */ }
    ::-webkit-scrollbar-track-piece  { }
    ::-webkit-scrollbar-thumb        {  background-color: rgba(255,255,255,1);
        border-radius: 5px;
    }
    ::-webkit-scrollbar-corner       { display: none;}
    ::-webkit-resizer                { display: none; }


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

    .wpme_button:focus{
        outline: none;
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
        box-shadow: none !important;
        border: solid 1px #ffffff !important;
        background-color:rgba(255,255,255,.05) !important;
        margin:30px 0 0;
        color: #DDDDDD !important;
        width:100%;
        min-height: 150px;
        max-height: 250px;
    }

    .wpme_inputtext:focus{
        border: none !important;
        box-shadow: none !important;
        border: solid 1px #bbddff !important;
        transition: 100ms;
        background-color:rgba(205,205,255,0.08) !important;
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

    .absolute{
        position: absolute;
        z-index: 100;
        max-width: 350px;
    }
</style>



<div class="wpme_body_init">
<div class="absolute">
    <?php
    if(isset($_POST['wpme_token'])){
        $token = trim($_POST['wpme_token']);
        if(updateUserData($token)){
            ?>
            <div class="notice notice-success is-dismissible">
                <h4>Token Válido</h4>
                <p> Token aceito </p>
            </div>
            <?php
        }else{
            ?>
            <div class="notice notice-error is-dismissible">
                <h2>Token Inválido</h2>
                <p>Favor utilizar um Token Válido, siga o <a href="tutorial">tutorial</a> para descobrir como achar o seu token.</p>
            </div>
            <?php
        }
    }

    ?>
</div>
    <div class="wpme_mainform">
        <div class="wpme_tutorial">
            <h1>Melhor Envio</h1>
        </div>
        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">

            <label>Cole aqui seu Token de acesso</label>
            <textarea type="text" class="wpme_inputtext" name="wpme_token" ><?=get_option('wpme_token')?></textarea> <br>
            <p>Para utilizar o Plugin é necessário estar cadastrado no <a href="https://melhorenvio.com.br">Melhor Envio</a>.</p>
            <p>Encontre seu <a href=""> Token de Acesso</a></p>
            <button class="wpme_button" type="submit">Salvar</button>
        </form>
    </div>
</div>