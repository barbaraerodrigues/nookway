<footer>
    <div class="container footer-grid">
        <div>
            <strong>NOOKWAY</strong>
            <p class="muted">Cantos secretos, experiências reais.
                Conectamos viajantes e guias locais em uma plataforma moderna, simples e segura.
            </p>
        </div>

        <div>
            <strong>Info</strong>
            <ul class="footer-links" style="list-style:none; padding:0; margin:8px 0 0; display:grid; gap:6px;">
                <li><a href="sobre.php">Sobre</a></li>
                <li><a href="privacidade.php">Política de Privacidade</a></li>
                <li><a href="termos.php">Termos de Uso</a></li>
            </ul>
        </div>



        <div>
            <strong>Contato</strong>
            <ul class="footer-links" style="list-style:none; padding:0; margin:8px 0 0; display:grid; gap:6px;">
                <li><a href="contato.php" >Fale Conosco</a></li>
            </ul>
        </div>

        <div>
            <strong>Redes</strong>
            <ul class="footer-links" style="list-style:none; padding:0; margin:8px 0 0; display:grid; gap:6px;">
                <li><a href="https://www.youtube.com" target="_blank">Instagram</a></li>
                <li><a href="https://www.youtube.com" target="_blank">YouTube</a></li>
            </ul>
        </div>
    </div>
    
    <section style="margin-bottom: 1px;">
    <div class="container footer-bottom">
        <small class="muted"> © <span id="ano"></span> NOOKWAY.</small>
        <small class="muted">Desenvolvido por Barbara Rodrigues </small>
    </div>

    <script>
        document.getElementById("ano").textContent = new Date().getFullYear();
    </script>
</footer>
