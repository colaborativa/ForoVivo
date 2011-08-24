<!-- You will have to create two categories in your installation citausuario for ideas from users and citainvitado for ideas from speakers -->
<?php get_header(); ?>
<?php
/**/
/**/
/** HEADER BEGINS. h1 h2 & h3 use images background assigned on the style.css file */
/** If category is "citausuario" ideas suggested by users it will show a different header */
/** Else it uses the main one                                                      */
/**/
if (is_category('citausuario')) { ?>
<div id="headerFondo">
	<div id="headerLista">
		<a href="<?php bloginfo('home'); ?>"><h1 id="CabeceraTitulo">ForoVivo. Patrimonio y Ciudad</h1></a>
		<h2 id="CabeceraDetalles">Martes 22 de Septiembre 20:00h Colegio Oficial de Arquitectos de Córdoba</h2>
		<h3 id="CabeceraUsuarios">Estas son las ideas compartidas por los visitantes</h3>
		<a id="botonAnadir2" href="#enviaIdea" class="lightview" title=" :: :: topclose: true, width: 500, height: 600">Añade tu Idea</a>
	</div>
</div>
<div class="CabeceraSombraUp"></div>
<?php } else { ?>
<div id="headerFondo">
	<div id="header">
		<a href="<?php bloginfo('home'); ?>"><h1 id="CabeceraTitulo">ForoVivo. Patrimonio y Ciudad</h1></a>
		<h2 id="CabeceraDetalles">Martes 22 de Septiembre 20:00h Colegio Oficial de Arquitectos de Córdoba</h2>
		<h3 id="CabeceraInvitados"><ul><li>Juan Murillo</li><li>Miguel Gómez Losada</li><li>Manuel Pedregosa</li><ul></h3>
		<p>En Córdoba el Patrimonio, herencia cultural propia del pasado, mantiene con las fuerzas modernizadoras de la Ciudad una díficil relación sobre la que caben muchos puntos de vista. En esta web encontrarás las reflexiones aportadas por nuestros tres invitados al respecto para abrir el debate, pudiendo añadir la tuya. Todas las reflexiones podrán ser objeto de comentario y valoración. El conjunto de ideas recogidas en esta web constituirá el material para el debate en el Encuentro público que tendrá lugar en el Colegio de Arquitectos de Córdoba el 22 de septiembre a las 20:00.</p>
	</div>
</div>
<div class="CabeceraSombraUp"></div>
<?php }
/**/
/* HEADER ENDS */
/**/
/**/
/**/
/**/
/** BODY TEXT. If category is "citausuario" it will show only the ideas suggested by users */
/** Else It shows only the ideas sent by the speakers */ 
/**/
if (is_category('citausuario')) { ?>
<div id="cuerpo">
	<!-- Shows only ideas suggested by users. Change category name according to your installation -->
	<?php query_posts('category_name=citausuario'); ?>
	<?php while (have_posts()) : the_post(); ?>
	<div class="Cita" id="Invitados">
		<span class="QuoteMark">“</span><p class="CitaTexto"><?php the_content_rss(); ?></p><p class="CitaAutor"><?php echo c2c_get_custom('autor'); ?></p>	
		<ul><li><a class="boton comentar invitado" href="<?php the_permalink() ?>">Comentar</a> <?php comments_number('Ningún comentario', 'Un comentario', '% comentarios'); ?></li></ul><?php if(function_exists('the_ratings')) { the_ratings(); } ?>
	</div>
	<?php endwhile; ?>
</div>
<div id="logIn">
	<?php dynamic_sidebar(1); ?> 
</div>
<div id="enviaIdea" style="display:none">
	<?php insert_cform(1); ?>
</div>
<?php } else { ?>
<div id="cuerpo">
	<!-- Shows only ideas by speakers. Change category name according to your installation -->
	<?php query_posts('category_name=citainvitado&showposts=10'); ?>
	<?php while (have_posts()) : the_post(); ?>
	<div class="Cita" id="Invitados">
		<span class="QuoteMark">“</span><p class="CitaTexto"><?php the_content_rss(); ?></p><p class="CitaAutor"><?php echo c2c_get_custom('autor'); ?></p>	
		<ul><li><a class="boton comentar invitado" href="<?php the_permalink() ?>">Comentar</a> <?php comments_number('Ningún comentario', 'Un comentario', '% comentarios'); ?></li></ul><?php if(function_exists('the_ratings')) { the_ratings(); } ?>
	</div>
	<?php endwhile; ?>
</div>
<!-- Sidebar using facebook registration plugin fb connect and login logout plugin. You will have to set the first one as a widget on your wordpress install -->
<div id="logIn">
	<?php dynamic_sidebar(1); ?> 
</div>
<!-- Suggested ideas by users are sent using cform plugin to an email address. Form is displayed using lightview javascript -->
<div id="enviaIdea" style="display:none">
	<?php insert_cform(1); ?>
</div>
<?php }
/**/
/**/
/** FOOTER BEGINS Displays the five most rated ideas*/
/**/
if (is_category('citausuario')) { ?>
<div class="CabeceraSombraDown"></div>
<div id="footerFondo">
<?php } else { ?>
<div class="CabeceraSombraDown"></div>
<div id="footerFondo">
	<div id="footer">
				<p id="Explain">Estas son las 5 ideas de los visitantes más valoradas:</p>
				<a id="botonAnadir" href="#enviaIdea" class="lightview" title=" :: :: topclose: true, width: 500, height: 600">Añade tu Idea</a>
				<!-- Here it is defined the category and the amount of suggested ideas --> 
				<?php $category_id = get_cat_ID( 'citausuario' ); query_posts('r_sortby=highest_rated&cat='.$category_id.'&showposts=5'); ?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="Cita" id="Usuarios">
					<span class="QuoteMark">“</span><p class="CitaTexto"><?php the_content_rss(); ?></p><p class="CitaAutor"><?php echo c2c_get_custom('autor'); ?></p>	
					<ul><li><a class="boton comentar invitado" href="<?php the_permalink() ?>">Comentar</a> <?php comments_number('Ningún comentario', 'Un comentario', '% comentarios'); ?></li></ul><?php if(function_exists('the_ratings')) { the_ratings(); } ?>
				</div>
				<?php endwhile; else: ?>
			</div>				
			<div id="botonHolder">
				<!-- this link shows only the ideas suggested by users category citausuario -->
				<a class="botonMasIdeas" href="<?php $category_id = get_cat_ID( 'citausuario' ); $category_link = get_category_link( $category_id ); echo $category_link; ?>">Ver el resto de ideas sugeridas por los visitantes</a>
			</div>		
				<a id="botonAnadir" href="#enviaIdea" class="lightview" title=" :: :: topclose: true, width: 500, height: 600">Añade tu Idea</a>
	</div>				
	<div id="botonHolder">
	</div>
				<?php endif; ?>
	</div>
	<div id="botonHolder">
		<a class="botonMasIdeas" href="<?php $category_id = get_cat_ID( 'citausuario' ); $category_link = get_category_link( $category_id ); echo $category_link; ?>">Ver el resto de ideas sugeridas por los visitantes</a>
	</div>
	<div id="FondoCredits">
<?php }
/**/
/* FOOTER ENDS */
/**/
/**/
?>
<?php get_footer(); ?>