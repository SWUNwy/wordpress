<?php
/**
 * @Author: Dami
 * @Date:   2017-06-19 14:23:50
 * @Last Modified by:   Dami
 * @Last Modified time: 2017-06-20 22:33:43
 * Plugin Name: Easy-Anti-Spam
 * Plugin URI: https://www.latoooo.com/xia_zhe_teng/310.htm
 * Description: 防止垃圾评论，远离万恶机器评论！
 * Version: 0.0.1
 * Author URI: https://www.latoooo.com/
 */

session_start();

/**
 * 文章页加载JS
 */

function Easy_Anti_Spam_Load_Js(){

	if( is_singular() && comments_open() && !is_user_logged_in() ){

		$script = 'function withjQuery(callback,safe){if(typeof(jQuery)=="undefined"){var script=document.createElement("script");script.type="text/javascript";script.src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js";if(safe){var cb=document.createElement("script");cb.type="text/javascript";cb.textContent="jQuery.noConflict();("+callback.toString()+")(jQuery, window);";script.addEventListener("load",function(){document.head.appendChild(cb)})}else{var dollar=undefined;if(typeof($)!="undefined")dollar=$;script.addEventListener("load",function(){jQuery.noConflict();$=dollar;callback(jQuery,window)})}document.head.appendChild(script)}else{setTimeout(function(){callback(jQuery,typeof unsafeWindow==="undefined"?window:unsafeWindow)},30)}}function D_tag($){$.ajaxSetup({async:false});$.get(D_ajaxurl,{action:"Get_dver"},function(data){if(data.s==200){$("#Dami-ver").attr("name",data.name)}},"json")}withjQuery(function($,window){$(document).on("click","#submit",function(event){D_tag($)})},true);';
		printf( '<script type="text/javascript">var D_ajaxurl = "%s";</script>', admin_url("admin-ajax.php") );
		printf( '<script type="text/javascript">%s</script>', $script );

	}

}

add_action('wp_footer', 'Easy_Anti_Spam_Load_Js', 99);

/**
 * 评论表单加隐藏input
 */
function Comment_Form_Verification() {

	echo '<input type="hidden" id="Dami-ver" name="" value="2333">';

}

add_action( 'comment_form', 'Comment_Form_Verification' );

/**
 * 随机字符串
 */

function generate_str( $length = 8 ) {   
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  
	$str = '';  
	for ( $i = 0; $i < $length; $i++ ){  
		$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
	}  
	return $str;  
} 

/**
 * 获取input name
 */

function Get_dver(){

	$name = generate_str(32);

	$_SESSION['D_hidden_name'] = $name;

	echo json_encode(
		array(
			's' => 200,
			'name' => $name
		)
	);

	die();
}
add_action( 'wp_ajax_nopriv_Get_dver', 'Get_dver' );

/**
 * 评论提交验证
 */
function Easy_Anti_Spam_Verification( $comment ){

	if( is_user_logged_in() ){
		return $comment;
	}else{

		if(!session_id()) session_start();

		$input_name = $_SESSION['D_hidden_name'];

		if( isset( $_POST[$input_name] ) && !empty( $_POST[$input_name] ) ){
			unset($_SESSION[$input_name]);
			return $comment;
		}else{
			if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
				if(function_exists('err')){
					err("蛤？");
				}else{
					wp_die('蛤？');
				}
				
			}else{
				wp_die('蛤？');
			}
			
		}

	}

}
add_action('preprocess_comment', 'Easy_Anti_Spam_Verification');

?>
