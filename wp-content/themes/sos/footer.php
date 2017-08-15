<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package sos-knowledge-base
 */

$the_theme = wp_get_theme();
$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_sidebar( 'footerfull' ); ?>


<section class="pre-footer">

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <div><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/sos-logo-128x78.png"></div>
            </div>
            <div class="col-xs-12 col-md-3">
                <h6>Address</h6>
                <p>
                    1234 Street Name<br/>
                    City, AA 99999
                </p>
            </div>
            <div class="col-xs-12 col-md-3">
                <h6>Contacts</h6>
                <p>
                    Email: support@email.com<br/>
                    Phone: +1 (0) 000 0000 001<br/>
                    Fax: +1 (0) 000 0000 002
                </p>
            </div>
            <div class="col-xs-12 col-md-3">
                <h6>Links</h6>
                <p>
                    <a class="text-primary" href="#">Link 1</a><br/>
                    <a class="text-primary" href="#">Link 2</a><br/>
                    <a class="text-primary" href="#">Link 3</a>
                </p>
            </div>
        </div><!-- .row -->
    </div><!-- .container -->
</section>

<footer class="footer footer-default">
    <div class="container">
        <nav>
            <ul>
                <li>
                    <a href="#" target="_blank">Terms of Service</a>
                </li>
                <li>
                    <a href="#" target="_blank">Privacy Policy</a>
                </li>
            </ul>
        </nav>
        <div class="copyright">
            <p class="text-xs-center">&copy; Copyright <script>document.write(new Date().getFullYear())</script> – Students Offering Support</p>
        </div>
    </div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>

