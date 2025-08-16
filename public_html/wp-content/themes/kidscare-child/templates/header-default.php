<?php
/**
 * Custom desktop header for child theme.
 *
 * @package WordPress
 * @subpackage KIDSCARE Child
 */
?>
<header class="site-header">
  <div class="site-header__inner">
    <div class="site-header__left">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="brand">
        <?php
        if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
            the_custom_logo();
        } else {
            echo '<span>' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
        }
        ?>
      </a>
    </div>

    <div class="site-header__center">
      <nav class="main-nav" role="navigation" aria-label="Navigation principale">
        <ul class="main-nav__list">
          <li class="main-nav__item"><a class="main-nav__link" href="#">Accueil</a></li>
          <li class="main-nav__item has-submenu">
            <a class="main-nav__link" href="#" aria-haspopup="true" aria-expanded="false">Services <span aria-hidden="true">▾</span></a>
            <ul class="submenu">
              <li><a class="submenu__link" href="#">Accès à l’aire de jeux</a></li>
              <li><a class="submenu__link" href="#">Fêtes d’anniversaire</a></li>
            </ul>
          </li>
          <li class="main-nav__item has-submenu">
            <a class="main-nav__link" href="#" aria-haspopup="true" aria-expanded="false">Forfaits Anniversaire <span aria-hidden="true">▾</span></a>
            <ul class="submenu">
              <li><a class="submenu__link" href="#">Mini-Roo Table</a></li>
              <li><a class="submenu__link" href="#">Mini-Roo Salle Privée</a></li>
              <li><a class="submenu__link" href="#">Jump-Roo Salle Privée</a></li>
              <li><a class="submenu__link" href="#">Super-Roo Salle Privée</a></li>
            </ul>
          </li>
          <li class="main-nav__item"><a class="main-nav__link" href="#">Contactez-nous</a></li>
          <li class="main-nav__item"><a class="main-nav__link" href="#">Panier</a></li>
          <li class="main-nav__item"><a class="main-nav__link" href="#">À propos</a></li>
          <li class="main-nav__item"><a class="main-nav__link" href="#">Politique de confidentialité</a></li>
        </ul>
      </nav>
    </div>

    <div class="site-header__right">
      <div class="btn-group">
        <a href="#" class="btn btn--primary">Accès à l’aire de jeux</a>
        <a href="#" class="btn btn--primary">Réserver salle de fête</a>
      </div>
    </div>
  </div>
</header>
<?php
// Keep mobile header from parent theme.
get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-mobile' ) );
?>
