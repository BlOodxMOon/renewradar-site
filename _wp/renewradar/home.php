<?php
/**
 * Guides list (the posts page).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$rr_page = (int) get_option( 'page_for_posts' );
rr_page_hero(
	array(
		'tag'   => 'Guides',
		'title' => 'Pay only for what you actually use.',
		'lede'  => $rr_page && get_post_meta( $rr_page, 'rr_lede', true ) ? get_post_meta( $rr_page, 'rr_lede', true ) : 'Short, practical guides for keeping subscriptions under control.',
		'cta'   => false,
	)
);
?>
<section class="section"><div class="wrap">
	<div class="grid-2">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<?php $rr_cover = rr_cover_url( get_the_ID() ); ?>
			<a class="card<?php echo $rr_cover ? ' card-cover' : ''; ?>" href="<?php the_permalink(); ?>"><?php if ( $rr_cover ) : ?><img class="card-img" src="<?php echo esc_url( $rr_cover ); ?>" alt="" width="1200" height="630" loading="lazy" decoding="async"><?php else : ?><div class="ic"><?php echo rr_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?><p class="card-meta"><?php echo esc_html( rr_reading_minutes( get_the_ID() ) ); ?> minute read</p><h3><?php the_title(); ?></h3><p><?php echo esc_html( get_the_excerpt() ); ?></p><span class="go">Read &rarr;</span></a>
			<?php
		endwhile;
		?>
		<a class="card card-cover" href="<?php echo esc_url( home_url( '/subscription-cost-calculator/' ) ); ?>"><img class="card-img" src="<?php echo esc_url( rr_asset( 'img/calculator-cover.jpg' ) ); ?>" alt="" width="1200" height="630" loading="lazy" decoding="async"><p class="card-meta">Free tool</p><h3>Subscription cost calculator</h3><p>A free tool that shows what your subscriptions cost per month, per year and over five years.</p><span class="go">Open &rarr;</span></a>
	</div>
	<?php
	the_posts_pagination(
		array(
			'prev_text' => 'Newer',
			'next_text' => 'Older',
		)
	);
	?>
</div></section>
<?php
echo rr_cta_band(); // phpcs:ignore WordPress.Security.EscapeOutput
get_footer();
