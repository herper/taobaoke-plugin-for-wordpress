<?php
class TaobaokePostItemList {
	public function getColumns() {
		return array(
			'item_id' => array(
				'header' => 'Item Id',
				'function' => 'showItemDetail'
				),
		    );
    }

    public function getDatasource() {
		$user = wp_get_current_user();
        $user_id = $user->id;

		global $wpdb;
		$table_name = $wpdb->prefix . TAOBAOKE_CART_TABLE;

		return new DatabaseDataSource("SELECT * FROM {$table_name} WHERE `user_id` = $user_id", array('update_time' => 'desc'), array(0, 2));
    }

    public function showItemDetail($item_id, $row) {
        return <<<HTML
	    <div style="width:100%">
		  <img src="{$row['item_pic']}" alt="淘宝推广商品图片" width=100 height=100 />
		<div>
		<div style="font-size:12px">
		  {$row['item_title']}
		</div>
		<div><input type="radio" value="{$row['item_id']}" name="taobaoke_selected_item" id="taobaoke_selected_item_{$row['item_id']}" /></div>
HTML;
    }
}

function taobaoke_media_buttons() {
    //empty here
}

function taobaoke_media_buttons_context($context) {
	global $post_ID, $temp_ID;

    $uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";

    $title = '添加淘宝客商品';
    $image_btn = taobaoke_img_path() . 'ke.gif';

    $out = ' <a href="' . $media_upload_iframe_src . '&tab=taobaoke_list_fav&TB_iframe=true&height=500&width=640" class="thickbox" title="' . $title .
           '"><img width="13px" height="13px" src="' . $image_btn . '" alt="添加淘宝客商品" /></a>';

	return $context . $out;
}

function taobaoke_list_fav() {

	if (!empty($_POST['taobaoke_post_submit'])) {
		$item_id = empty($_POST['taobaoke_selected_item']) ? null : $_POST['taobaoke_selected_item'];

		$message = '';
		if (null == $item_id) {
			$message = '<span style="font-weight:bold;font-size:14px;color:red">请选择您要插入的商品</span>';
		}
		else {
			$insert_type = $_POST['taobaoke_post_type'];

			global $wpdb;

			$user = wp_get_current_user();
            $user_id = $user->id;

			$table_name = $wpdb->prefix . TAOBAOKE_CART_TABLE;
			$item = $wpdb->get_row("SELECT * FROM {$table_name} WHERE `user_id` = $user_id AND `item_id` = '{$item_id}'", ARRAY_A);
			if (!empty($item)) {
				$html = '';

				include taobaoke_tpl_path() . 'html.tpl.php';

				if (1 == $insert_type) {
					$html = taobaoke_get_post_item_html_full();
					$html = parse_string($html,
                        taobaoke_show_color('bg'), taobaoke_show_width(), taobaoke_show_color('border'),
                        $item['item_id'], $item['item_url'], $item['item_pic'], $item['item_pic'], $item['item_title'],
                        taobaoke_show_color('price'), $item['item_price'], $item['item_id'], $item['item_url']);
				}
				else if (2 == $insert_type) {
					$html = taobaoke_get_post_item_html();
					$html = parse_string($html, $item['item_id'], $item['item_url'], 'alignleft', $item['item_title'], $item['item_pic'], $item['item_title']);
				}
				else {
					$html = taobaoke_get_post_item_html();
					$html = parse_string($html, $item['item_id'], $item['item_url'], 'alignright', $item['item_title'], $item['item_pic'], $item['item_title']);
				}
				media_send_to_editor($html);
			}
		}
	}

    $controller = new TaobaokePostItemList();
    $table = new Table($controller, $controller->getColumns(), $controller->getDatasource());
	$table->setNoRecordLabel('您还没有将任何商品加入推广列表，快去到淘宝客页面去收藏商品吧');
	$table->setGridTableColumn(4);

	include taobaoke_tpl_path() . 'post.tpl.php';
}
