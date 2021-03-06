<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="referrer" content="origin">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header/site', 'header' ); ?>

<main>
