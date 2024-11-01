<?php
/*
 * Plugin Name: Sumtracker Connector
   Plugin URI: https://sumtracker.com
   Description: Plugin to connect your woo commerce store with sumtracker inventory management software
   License: GPL v2 or later
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
   Author: Sumtracker
   Version: 2022.1
*/

const FRONTEND_URL = 'https://inventory.sumtracker.com';
const API_URL      = 'https://inventory-api.sumtracker.com';

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'smtcr_plugin_settings_link' );

function smtcr_plugin_settings_link( $links )
{
	$url = site_url('/wp-admin/admin.php?page=sumtracker-connector');
    $_link = '<a href="'.$url.'" target="_blank">Settings</a>';
	$links[] = $_link;

	return $links;
}

function smtcr_register_menu_page() {
	add_menu_page(
		'Sumtracker Connector',
		'Sumtracker',
		'manage_options',
		'sumtracker-connector',
		'smtcr_page_callback',
	);
}
function smtcr_page_callback() {

	$login_url = FRONTEND_URL.'/login';
	$sign_up_url = FRONTEND_URL.'/signup/new';
	$help = 'https://help.sumtracker.com/';
    $is_connected = false;

	try {
		$response = wp_remote_get(API_URL.'/ecom/settings/channels/woocommerce/check_store_connection/?store_url='.site_url());
		$body     = wp_remote_retrieve_body( $response );
        $status = json_decode($body,true);
        if ($status['is_connected']) $is_connected = true;
    }catch (Exception $exception){

    }
	?>
		<div class="sumtracker">
			<h1 style="text-align: center;margin-top: 50px;">Sumtracker Connector</h1>
			<div style="text-align: center;">
				<img style="max-width: 130px;" src="<?php echo esc_attr(plugin_dir_url(__FILE__).'/sumtracker_logo.png')?>" alt="Sumtracker-logo"/>
			</div>
				<div style="max-width: 700px;margin: auto;margin-top: 50px;">

                    <?php if (!defined('WC_VERSION')): ?>
                        <div class="notice notice-error" style="margin-bottom: 20px;">
                            <p>Please install woocommerce</p>
                        </div>
                    <?php endif;?>

                     <p style="text-align: center;">
                            Plugin to connect your woocommerce store with sumtracker.
                     </p>

					<?php if (!$is_connected): ?>
                        <h2 style="display: flex;align-items: center;">Create a new account on Sumtracker. <a target="_blank" href="<?php echo esc_attr($sign_up_url);?>" class="button" style="margin-left: 13px;"> Sign Up </a></h2>
                        <h2 style="display: flex;align-items: center;">Login to existing Sumtracker account. <a target="_blank" class="button" href="<?php echo esc_attr($login_url);?>"  style="margin-left: 10px;"> Login </a></h2>


                        <h2 style="margin-top: 50px;">How to connect ?</h2>

                        <p>
                            1. Login/Signup to sumtracker
                        </p>
                        <p>
                            2. Click on Add New Store button on the dashboard
                        </p>
                        <p>
                            3. Select WooCommerce and enter the store URL
                        </p>
					<?php else: ?>
						<div>
                            <div style="display: flex;align-items:center; justify-content: center">
                                <img src="<?php echo esc_attr(plugin_dir_url(__FILE__).'/accept.png'); ?>" alt="tick" style="width: 50px">
							    <p style="margin-left: 15px;">Sumtracker is connected!</p>
                            </div>
                            <p>
                                Login to existing Sumtracker Account <a href="<?php echo esc_attr($login_url);?>" target="_blank" class="button">Login</a>
                            </p>
						</div>
					<?php endif;?>
					<p>
                        Learn more about sumtracker <a href="<?php echo esc_attr($help);?>" class="button" target="_blank">Help Docs</a>
					</p>
				</div>
		    </div>
		<style>
			.sumtracker p{font-size: 16px;font-weight: 500;color: black;}
            .sumtracker .button{font-size: 18px}
		</style>
	<?php
}
add_action('admin_menu', 'smtcr_register_menu_page',99);