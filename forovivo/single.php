<!-- This is the view when one single idea is displayed. It shows the idea on the upper side and comments on the body side -->
<?php get_header();?>
<div id="headerFondo">
	<div id="header">
		<a href="<?php bloginfo('home'); ?>"><h1 id="CabeceraTitulo">ForoVivo. Patrimonio y Ciudad</h1></a>
		<h2 id="CabeceraDetalles">Martes 22 de Septiembre 20:00h Colegio Oficial de Arquitectos de Córdoba</h2>
	</div>
	<div id="CitaSingle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="Cita" id="Invitados">
			<span class="QuoteMark">“</span><p class="CitaTexto"><?php the_content_rss(); ?></p><p class="CitaAutor"><?php echo c2c_get_custom('autor'); ?></p>	
			<?php if(function_exists('the_ratings')) { the_ratings(); } ?>
		</div>
	</div>
</div>
<div class="CabeceraSombraUp"></div>
<div id="cuerpo">
	<div class="commentarios">
	<p class="notaComentarios">Participa en el debate! Puedes utilizar tu perfil de Facebook para firmar tus comentarios –además si pulsas Share Options>Share on news feed podrás publicar tu comentario en tu muro, pudiendo personalizar el título– Si no tienes un perfil de Facebook puedes participar registrándote en Disqus o en nuestra propia página. Recuerda que los comentarios anónimos son revisados antes de su publicación</p>
	<?php comments_template(); ?>
	<?php endwhile; else: ?>
	<p>Lo sentimos, no lo hemos encontrado</p>
	<?php endif; ?>
	</div>
</div>
<div class="CabeceraSombraDown"></div>
<div id="footerFondo">
<?php get_footer(); ?>