<?php
class TaobaoItemCatsApi extends TaobaoApi {
    public function getItemCats(ItemCatsGetV2Request $request) {
        //���صĸ�ʽΪ:array('last_modified' => '', 'item_cats' => array(0=>obj, 1=>obj))
        return $this->sendRequest($request, 'TaobaoItemCatsApi:getItemCats', 'taobao.itemcats.get.v2');
    }
}
?>
