<?php

//Add all custom functions, hooks, filters, ajax etc here

include('functions/start.php');

include('functions/clean.php');

//Custon wp-admin logo
function my_custom_login_logo() {
  echo '<style type="text/css">
		        h1 a {
		          background-size: 227px 85px !important;
		          margin-bottom: 20px !important;
		          background-image:url('.get_bloginfo('template_directory').'/images/logo.png) !important; }
		    </style>';
}

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Global Site Settings',
		'menu_title'	=> 'Global Site Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

function the_breadcrumb() {
    if (!is_home()) {
        echo '<a href="';
        echo get_option('home');
        echo '">';
        bloginfo('name');
        echo "</a>  ";
        if (is_category() || is_single()) {
          if(is_category()){
            single_term_title();
           }elseif (is_single()) {
                echo " > ";
                //the_title();
            //echo " > ";
                echo '<a href='.get_page_link(get_option('page_for_posts')).'>'.get_the_title(get_option('page_for_posts')).'</a>';
                // $cats = get_the_category( get_the_ID() );
                // $cat = array_shift($cats);
                // echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $cat->name ) ) . '">'. $cat->name .'</a>';
                 echo " > ";
                the_title();
            }
        } elseif (is_page()) {
            echo the_title();
        }
    }
}

/* Display post thumbnail meta box including description */
//if(is_single()){
add_filter( 'admin_post_thumbnail_html', 'post_thumbnail_add_description', 10, 2 );
//}

function post_thumbnail_add_description( $content, $post_id ){
$post = get_post( $post_id );
$post_type = $post->post_type;
//$extra = '';
  //$extra = '<br>Recommended image size 800px x 800px';
if($post_type == 'post'){
    $content .= '<p><label for=\"html\">This image will be used for Facebook shares.  Absolute minimum for Facebook share image is 200px wide x 200px tall for a small image.  To provide a large image for Facebook sharing, add an image that is 600x315 pixels at a minimum, but 1200x630 or larger is preferred (up to 5MB).</label></p>';
    return $content;
    return $post_id;
  }
}
//if(is_single()){
add_filter( 'gettext', 'wpse22764_gettext', 10, 3 );
//}
function wpse22764_gettext( $translation, $original, $post_id)
{
  //var_dump($post_id);
  //$post_type = $post->post_type;
  //if($post_type == 'post'){
    if ( 'Excerpt' == $original ) {
        return 'Curated excerpt';
    }else{
        $pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
        if ($pos !== false) {
            return  'Add a brief custom description that can be used for social shares';
        }
    }
    return $translation;
  //}
}


/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function wpdocs_custom_excerpt_length( $length ) {
    return 50;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

/**
 * Filter the "read more" excerpt string link to the post.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function wpdocs_excerpt_more( $more ) {
    return sprintf( '... <a class="read-more" href="%1$s">%2$s</a>',
        get_permalink( get_the_ID() ),
        __( 'Read More >', 'textdomain' )
    );
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );

add_action( 'admin_head', 'hide_text_editor' );

function hide_text_editor() {
  // Get the Post ID.
  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
  if( !isset( $post_id ) ) return;

  // Hide the editor on certian pages
   $pgname = get_the_title($post_id);
   $pages = array('Content Tester');
  if(in_array($pgname, $pages)){ 
    remove_post_type_support('page', 'editor');
  }

  // Hide the editor on a page with a specific page template
  // Get the name of the Page Template file.
  $template_file = get_post_meta($post_id, '_wp_page_template', true);
  $templates = array('templates/template-landing.php', 'templates/template-home.php', 'page.php', 'index.php');


  if(in_array($template_file, $templates)){ // the filename of the page template
    remove_post_type_support('page', 'editor');
  }
}

?>
