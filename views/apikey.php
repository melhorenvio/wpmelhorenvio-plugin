<script> var hasContent = true; </script>

<div class="wpme_body_init">
    <div class="absolute">
        <?php
            if ( ! defined( 'ABSPATH' ) ) {
                exit; // Exit if accessed directly
            }

            if(isset($_POST['wpmelhorenvio_token'])){
                if(check_admin_referer('wpmelhorenvio_apikey_nonce')){
                    $token = trim(wp_filter_nohtml_kses(sanitize_text_field($_POST['wpmelhorenvio_token']))); // I used Sanitize_text because Sanitize key blocks dots from Bearer tokens.
                    if (wpmelhorenvio_updateUserData($token)) {
                        ?>
                        <div class="notice notice-success is-dismissible">
                            <h4>Token Válido</h4>
                            <p>Token aceito</p>
                        </div>
                        <?php
                        $url = admin_url('admin.php?page=wpmelhorenvio_melhor-envio-config');
                        wp_redirect($url);
                    } else {
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <h2>Token Inválido</h2>
                            <p>Favor utilizar um token de acesso válido. Crie seu token de acesso através do <a href="https://www.melhorenvio.com.br/painel/gerenciar/tokens">Painel</a> no Melhor Envio.</p>
                        </div>
                        <?php
                    }
                }
            }
        ?>
    </div>
    <div class="wpme_mainform">
        <div class="wpme_tutorial">
            <h1>Melhor Envio</h1>
        </div>
        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post">
            <?php wp_nonce_field('wpmelhorenvio_apikey_nonce'); ?>

            <div class="token-box">
                <?php if (get_option('wpmelhorenvio_token')): ?>
                    <script> hasContent = true; </script>
                <?php endif; ?>
                
                <a href="javascript:;" class="js-changetoken changeToken">Alterar token</a>
                <div class="inputToken">
                    <h2>Cole aqui seu Token de acesso</h2>
                    <textarea type="text" class="wpme_inputtext" name="wpmelhorenvio_token"></textarea>
                </div>
            </div>

            <p>Para utilizar o Plugin é necessário estar cadastrado no <a href="https://melhorenvio.com.br">Melhor Envio</a>.</p>
            <p>Encontre seu <a href="https://www.melhorenvio.com.br/painel/gerenciar/tokens"> Token de Acesso</a></p>
            <button class="wpme_button" type="submit">Salvar</button>
        </form>
    </div>
</div>

<script>
    $(function(){
        if (hasContent) {
            $('.changeToken').show();
            $('.inputToken').hide();
        }
        $('.js-changetoken').click(function(){
            $('.changeToken').hide();
            $('.inputToken').show();
        });
    });
</script>
