 <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="./public/images/logo.png" alt="Nathpepper Logo" class="footer-logo">
                    <p>L'excellence du poivre depuis 2025</p>
                </div>
                <div class="footer-section">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="#accueil">Accueil</a></li>
                        <li><a href="#nos-poivres">Nos Poivres</a></li>
                        <li><a href="#notre-marque">Notre Marque</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="mentions-legales.php">Mentions Légales</a></li>
                        <li><a href="cgv.php">Conditions Générales de Vente</a></li>
                        <li><a href="#" id="confidentialite">Confidentialité</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Suivez-nous</h4>
                    <div class="social-links">
                        <a href="https://www.tiktok.com/@nath.pepper" class="social-link">Tiktok</a>
                        <a href="https://www.instagram.com/nath.pepper/" class="social-link">Instagram</a>
                        <a href="#" class="social-link">Youtube</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Nathpepper. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <!-- Modals -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Mon Panier</h2>
            </div>
            <div id="cart-items"></div>
            <div class="cart-total">
                <strong>Total: <span id="cart-total">0,00 €</span></strong>
            </div>
            <div class="cart-actions">
                <button class="btn-primary" id="checkout-btn">Commander</button>
                <button class="btn-secondary" id="clear-cart">Vider le panier</button>
            </div>
        </div>
    </div>

    <div id="account-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2>Mon Compte</h2>
            </div>
            <div class="account-tabs">
                <button class="tab-btn active" data-tab="login">Connexion</button>
                <button class="tab-btn" data-tab="register">Inscription</button>
            </div>
            <div id="login-form" class="tab-content active">
                <form>
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Mot de passe</label>
                        <input type="password" id="login-password" required>
                    </div>
                    <button type="submit" class="btn-primary">Se connecter</button>
                </form>
            </div>
            <div id="register-form" class="tab-content">
                <form>
                    <div class="form-group">
                        <label for="register-name">Nom</label>
                        <input type="text" id="register-name" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Mot de passe</label>
                        <input type="password" id="register-password" required>
                    </div>
                    <button type="submit" class="btn-primary">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
    <!--<script src="js/products.js"></script>-->
    <script src="js/cart.js"></script>
    <script src="js/modals.js"></script>
    <script src="js/main.js"></script>
    </body>
</html>