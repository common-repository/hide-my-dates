<?php
/*
Plugin Name: Hide My Dates
Plugin URI: https://wordpress.org/plugins/hide-my-dates/
Description: The plugin hides post and comment publishing dates from Google. Read more in the <a href="options-general.php?page=hide-my-dates.php">Help</a> section on the plugin page.
Version: 2.00
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: hide-my-dates
*/

//проверка версии плагина (запуск функции установки новых опций) begin
function hmd_check_version() {
    $hmd_options = get_option('hmd_options');
    if (!isset($hmd_options['version'])) {$hmd_options['version']='';update_option('hmd_options',$hmd_options);}
    if ( $hmd_options['version'] != '2.00' ) {
        hmd_set_new_options();
    }
}
add_action( 'plugins_loaded', 'hmd_check_version' );
//проверка версии плагина (запуск функции установки новых опций) end

//функция установки новых опций при обновлении плагина у пользователей begin
function hmd_set_new_options() {
    $hmd_options = get_option('hmd_options');

    //если нет опции при обновлении плагина - записываем ее
    //if (!isset($hmd_options['new_option'])) {$hmd_options['new_option']='value';}

    //если необходимо переписать уже записанную опцию при обновлении плагина
    //$hmd_options['old_option'] = 'new_value';

    //перенос старых настроек в новый формат begin
    $opt_date = get_option('hmd_opt_date');
    $opt_modifieddate = get_option('hmd_opt_modifieddate');
    $opt_comments = get_option('hmd_opt_comments'); 

    if ( ! isset($hmd_options['date']) ) {
        if ( $opt_date == '1' ) {
            $hmd_options['date'] = 'enabled';
        } else {
            $hmd_options['date'] = 'disabled';
        }
    }
    if ( ! isset($hmd_options['modifieddate']) ) {
        if ( $opt_modifieddate == '1' ) {
            $hmd_options['modifieddate'] = 'enabled';
        } else {
            $hmd_options['modifieddate'] = 'disabled';
        }
    }
    if ( ! isset($hmd_options['comments']) ) {
        if ( $opt_comments == '1' ) {
            $hmd_options['comments'] = 'enabled';
        } else {
            $hmd_options['comments'] = 'disabled';
        }
    }

    delete_option('hmd_opt_date');
    delete_option('hmd_opt_modifieddate');
    delete_option('hmd_opt_comments');
    //перенос старых настроек в новый формат end

    $hmd_options['version'] = '2.00';
    update_option('hmd_options', $hmd_options);
}
//функция установки новых опций при обновлении плагина у пользователей end

//функция установки значений по умолчанию при активации плагина begin
function hmd_init() {

    $hmd_options = array();

    $hmd_options['version'] = '2.00';
    $hmd_options['date'] = 'enabled';
    $hmd_options['modifieddate'] = 'enabled';
    $hmd_options['comments'] = 'enabled';

    add_option('hmd_options', $hmd_options);
}
add_action( 'activate_hide-my-dates/hide-my-dates.php', 'hmd_init' );
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function hmd_on_deactivation() {
    if ( ! current_user_can('activate_plugins') ) return;
}
register_deactivation_hook( __FILE__, 'hmd_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function hmd_on_uninstall() {
    if ( ! current_user_can('activate_plugins') ) return;
    delete_option('hmd_options');
}
register_uninstall_hook( __FILE__, 'hmd_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function hmd_setup() {
    load_plugin_textdomain('hide-my-dates');
}
add_action( 'init', 'hmd_setup' );
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function hmd_actions( $links ) {
    return array_merge(array('settings' => '<a href="options-general.php?page=hide-my-dates.php">' . __('Settings', 'hide-my-dates') . '</a>'), $links);
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'hmd_actions' );
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function hmd_files_admin( $hook_suffix ) {
    $purl = plugins_url('', __FILE__);
    $hmd_options = get_option('hmd_options');
    if ( $hook_suffix == 'settings_page_hide-my-dates' ) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('hmd-script', $purl . '/inc/hmd-script.js', array(), $hmd_options['version']);
        wp_enqueue_style('hmd-css', $purl . '/inc/hmd-css.css', array(), $hmd_options['version']);
    }
}
add_action( 'admin_enqueue_scripts', 'hmd_files_admin' );
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция вывода страницы настроек плагина begin
function hmd_options_page() {
$purl = plugins_url('', __FILE__);

if ( isset($_POST['submit']) ) {

//проверка безопасности при сохранении настроек плагина begin
if ( ! wp_verify_nonce( $_POST['hmd_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
    wp_die(__( 'Cheatin&#8217; uh?', 'hide-my-dates' ));
}
//проверка безопасности при сохранении настроек плагина end

    //проверяем и сохраняем введенные пользователем данные begin
    $hmd_options = get_option('hmd_options');

    if(isset($_POST['date'])){$hmd_options['date'] = sanitize_text_field($_POST['date']);}else{$hmd_options['date'] = 'disabled';}
    if(isset($_POST['modifieddate'])){$hmd_options['modifieddate'] = sanitize_text_field($_POST['modifieddate']);}else{$hmd_options['modifieddate'] = 'disabled';}
    if(isset($_POST['comments'])){$hmd_options['comments'] = sanitize_text_field($_POST['comments']);}else{$hmd_options['comments'] = 'disabled';}

    update_option('hmd_options', $hmd_options);
    //проверяем и сохраняем введенные пользователем данные end
}
$hmd_options = get_option('hmd_options');

if ( ! empty($_POST) ) :

    if ( ! wp_verify_nonce( $_POST['hmd_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
        wp_die(__( 'Cheatin&#8217; uh?', 'hide-my-dates' ));
    }
?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'hide-my-dates'); ?></strong></p></div>
<?php endif; ?>

<div class="wrap foptions">
<h2><?php _e('&#8220;Hide My Dates&#8221; Settings', 'hide-my-dates'); ?><span id="restore-hide-blocks" class="dashicons dashicons-admin-generic hide" title="<?php _e('Show hidden blocks', 'hide-my-dates'); ?>"></span></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<?php $lang = get_locale(); ?>
<?php if ($lang == 'ru_RU') { ?>
<div class="postbox" id="donat">
<script>
var closedonat = localStorage.getItem('hmd-close-donat');
if (closedonat == 'yes') {
    document.getElementById('donat').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;">Вам нравится этот плагин ?
    <span id="close-donat" class="dashicons dashicons-no-alt" title="Скрыть блок"></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="Купить мне чашку кофе :)" style=" margin: 5px; float:left;" />
        <p>Привет, меня зовут <strong>Flector</strong>.</p>
        <p>Я потратил много времени на разработку этого плагина.<br />
        Поэтому не откажусь от небольшого пожертвования :)</p>
        <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a> 
        <p>Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.<br />
        Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target="_blank" href="https://www.wpuslugi.ru/?from=hmd-plugin">https://www.wpuslugi.ru/</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } else { ?>
<div class="postbox" id="donat">
<script>
var closedonat = localStorage.getItem('hmd-close-donat');
if (closedonat == 'yes') {
    document.getElementById('donat').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><?php _e('Do you like this plugin ?', 'hide-my-dates'); ?>
    <span id="close-donat" class="dashicons dashicons-no-alt" title="<?php _e('Hide block', 'hide-my-dates'); ?>"></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="<?php _e('buy me a coffee', 'hide-my-dates'); ?>" style=" margin: 5px; float:left;" />
        <p><?php _e('Hi! I\'m <strong>Flector</strong>, developer of this plugin.', 'hide-my-dates'); ?></p>
        <p><?php _e('I\'ve spent many hours developing this plugin.', 'hide-my-dates'); ?> <br />
        <?php _e('If you like and use this plugin, you can <strong>buy me a cup of coffee</strong>.', 'hide-my-dates'); ?></p>
        <a target="_blank" href="https://www.paypal.me/flector"><img alt="" src="<?php echo $purl . '/img/donate.gif'; ?>" title="<?php _e('Donate with PayPal', 'hide-my-dates'); ?>" /></a>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } ?>

<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><?php _e('Options', 'hide-my-dates'); ?></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">

            <tr>
                <th class="tdcheckbox"><?php _e('Date:', 'hide-my-dates') ?></th>
                <td>
                    <label for="date"><input type="checkbox" value="enabled" name="date" id="date" <?php if ($hmd_options['date'] == 'enabled') echo 'checked="checked"'; ?> /><?php _e('Hide date', 'hide-my-dates'); ?></label>
                    <br /><small><?php _e('Post creation date (<tt>the_date</tt> and <tt>the_time</tt>) will be hidden from Google.', 'hide-my-dates'); ?></small>
                </td>
            </tr>
            <tr>
                <th class="tdcheckbox"><?php _e('Modified Date:', 'hide-my-dates') ?></th>
                <td>
                    <label for="modifieddate"><input type="checkbox" value="enabled" name="modifieddate" id="modifieddate" <?php if ($hmd_options['modifieddate'] == 'enabled') echo 'checked="checked"'; ?> /><?php _e('Hide modified date', 'hide-my-dates'); ?></label>
                    <br /><small><?php _e('The last modification date (<tt>the_modified_date</tt> and <tt>the_modified_time</tt>) will be hidden from Google.', 'hide-my-dates'); ?></small>
                </td>
            </tr>
            <tr>
                <th class="tdcheckbox"><?php _e('Dates of Comments:', 'hide-my-dates') ?></th>
                <td>
                    <label for="comments"><input type="checkbox" value="enabled" name="comments" id="comments" <?php if ($hmd_options['comments'] == 'enabled') echo 'checked="checked"'; ?> /><?php _e('Hide dates of comments', 'hide-my-dates'); ?></label>
                    <br /><small><?php _e('Comment creation dates will be hidden from Google.', 'hide-my-dates'); ?></small>
                </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Update options &raquo;', 'hide-my-dates'); ?>" />
                </td>
            </tr> 
        </table>
    </div>
</div>

<div class="postbox" id="help">
<script>
var closehelp = localStorage.getItem('hmd-close-help');
if (closehelp == 'yes') {
    document.getElementById('help').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>

    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><?php _e('Help', 'hide-my-dates'); ?>
    <span id="close-help" class="dashicons dashicons-no-alt" title="<?php _e('Hide block', 'hide-my-dates'); ?>"></span></h3>
      <div class="inside" style="display: block;">

      <p>
      <?php _e('How it works:', 'hide-my-dates'); ?> <br /><br />
      <?php _e('The plugin uses a CSS hack to show the date – your visitors see it, but Google takes it <br />for the <tt2>title</tt2> parameter of a <tt2>span</tt2> element and does not consider it to be a part of <br />the page content. Therefore, the publishing date is not shown in the search snippet.', 'hide-my-dates'); ?>
      </p>
      <p><img src="<?php echo $purl . '/img/1.png'; ?>" style="border: 1px solid #CCC;" /></p>

      <p><?php _e('Some themes only show the date of the last post modification. Hide it from Google <br />if you don’t want it to appear in the search snippet.', 'hide-my-dates'); ?></p>

      <p><?php _e('If Google doesn’t find the date in a post, it will take it from the first comment on the <br />page, so hiding comment creation dates is also a good idea.', 'hide-my-dates'); ?></p>

      <p><?php _e('I hope you will find this plugin useful.', 'hide-my-dates'); ?></p>

      </div>
</div>

<div id="about" class="postbox" style="margin-bottom:0;">
<script>
var closeabout = localStorage.getItem('hmd-close-about');
if (closeabout == 'yes') {
    document.getElementById('about').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><?php _e('About', 'hide-my-dates'); ?>
    <span id="close-about" class="dashicons dashicons-no-alt" title="<?php _e('Hide block', 'hide-my-dates'); ?>"></span></h3>
      <div class="inside" style="padding-bottom:15px;display: block;">

      <p><?php _e('If you liked my plugin, please <a target="_blank" href="https://wordpress.org/support/plugin/hide-my-dates/reviews/#new-post"><strong>rate</strong></a> it.', 'hide-my-dates'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('You may also like my other plugins:', 'hide-my-dates'); ?></p>

      <div class="about">
        <ul>
            <?php if ($lang == 'ru_RU') : ?>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - создание RSS-ленты для сервиса Яндекс.Дзен.</li>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-turbo/">RSS for Yandex Turbo</a> - создание RSS-ленты для сервиса Яндекс.Турбо.</li>
            <?php endif; ?>
            <li><a target="_blank" href="https://wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('this plugin allows you to hide text using the tags [spoiler]your text[/spoiler].', 'hide-my-dates'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('very beautiful text animations (shortcodes in posts and widgets or PHP code in theme files).', 'hide-my-dates'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('this plugin adds social sharing icons to each image in your posts.', 'hide-my-dates'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('this plugin changes the creation dates of posts to relative dates.', 'hide-my-dates'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/truncate-comments/">Truncate Comments</a> - <?php _e('this plugin uses Javascript to hide long comments (Amazon-style comments).', 'hide-my-dates'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-yandex-share/">Easy Yandex Share</a> - <?php _e('share buttons for WordPress from Yandex.', 'hide-my-dates'); ?></li>
            <li style="margin: 3px 0px 3px 35px;"><a target="_blank" href="https://wordpress.org/plugins/html5-cumulus/">HTML5 Cumulus</a> <span class="new">new</span> - <?php _e('a modern (HTML5) version of the classic &#8220;WP-Cumulus&#8221; plugin.', 'hide-my-dates'); ?></li>
        </ul>
      </div>

    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'hmd_nonce' ); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function hmd_menu() {
    add_options_page( 'Hide My Dates', 'Hide My Dates', 'manage_options', 'hide-my-dates.php', 'hmd_options_page' );
}
add_action( 'admin_menu', 'hmd_menu' );
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end

//скрываем даты создания записей begin
function hmd_hide_date( $tdate = '' ) {
    if ( ! is_admin() && ! is_feed() ) {
        $hmd_options = get_option('hmd_options');
        if ( $hmd_options['date'] == 'enabled' ) {
            $tdate = '<span class="sdata" title="' .  $tdate . '"></span>';
        }
    }
    return $tdate;
}
add_filter( 'get_the_time', 'hmd_hide_date' );
add_filter( 'get_the_date', 'hmd_hide_date' );
//скрываем даты создания записей end

//скрываем даты изменения записей begin
function hmd_hide_modifieddate( $tdate = '' ) {
    if ( ! is_admin() && ! is_feed() ) {
        $hmd_options = get_option('hmd_options');
        if ( $hmd_options['modifieddate'] == 'enabled' ) {
            $tdate = '<span class="sdata" title="' .  $tdate . '"></span>';
        }
    }
    return $tdate;
}
add_filter( 'get_the_modified_time', 'hmd_hide_modifieddate' );
add_filter( 'get_the_modified_date', 'hmd_hide_modifieddate' );
//скрываем даты изменения записей end

//скрываем даты создания комментариев begin
function hmd_hide_comments( $tdate = '' ) {
    if ( ! is_admin() && ! is_feed() ) {
        $hmd_options = get_option('hmd_options');
        if ( $hmd_options['comments'] == 'enabled' ) {
            $tdate = '<span class="sdata" title="' .  $tdate . '"></span>';
        }
    }
    return $tdate;
}
add_filter( 'get_comment_date', 'hmd_hide_comments' );
add_filter( 'get_comment_time', 'hmd_hide_comments' );
//скрываем даты создания комментариев end

//стили плагина для скрытия даты begin
function hmd_print_style() {
?>
<style>
.sdata:before{content:attr(title);}
</style>
<?php }
add_action( 'wp_head', 'hmd_print_style' );
//стили плагина для скрытия даты end

//удаляем то, что попало внутрь тегов begin
function hmd_output_buffer_start() {
    ob_start('hmd_output_callback');
}
add_action( 'wp_head', 'hmd_output_buffer_start' );
function hmd_output_buffer_end() {
    if ( ob_get_length() ) {
        ob_end_flush();
    }
}
add_action( 'wp_footer', 'hmd_output_buffer_end' );
function hmd_output_callback( $buffer ) {

    $pattern = '/title="&lt;span class=&quot;sdata&quot; title=&quot;(.*?)&quot;&gt;&lt;\/span&gt;"/i';
    $replacement = 'title="$1"';
    $buffer = preg_replace($pattern, $replacement, $buffer);

    $pattern = '/ datetime="(.*?)"/i';
    $replacement = '';
    $buffer = preg_replace($pattern, $replacement, $buffer);

    $pattern = '/&lt;span class=&quot;sdata&quot; title=&quot;(.*?)&quot;&gt;&lt;\/span&gt;/i';
    $replacement = '<span class="sdata" title="$1"></span>';
    $buffer = preg_replace($pattern, $replacement, $buffer);

    $pattern = '/<time(.*?)"><\/span>"(.*?)>/i';
    $replacement = '<time>';
    $buffer = preg_replace($pattern, $replacement, $buffer);

    $pattern = '/<meta(.*?)content="<span class="sdata" title="(.*?)"><\/span>">/i';
    $replacement = '';
    $buffer = preg_replace($pattern, $replacement, $buffer);

    return $buffer;
}
//удаляем то, что попало внутрь тегов end