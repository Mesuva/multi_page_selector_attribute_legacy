<?php  
defined('C5_EXECUTE') or die("Access Denied.");

class MultiPageSelectorAttributeTypeController extends AttributeTypeController  {

 	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atMultiPageSelector where avID = ?", array($this->getAttributeValueID()));
		return trim($value);	
	}
	
	public function getPageArrayValue() {
		$value = $this->getValue();
		$pages = array();
		$page_ids = array();
		
		if ($value) {
			$page_ids = explode(',', $value);
		}
			
		foreach($page_ids as $pID) {
			$page = Page::getByID($pID);
			if (!$page->isInTrash()) {
				$pages[] = $page;
			}
		}	
		
		return $pages;
	}
	
	public function getPageLinkArrayValue() {
		Loader::helper('navigation');
		
		$pages = $this->getPageArrayValue();
		$links = array();
		
		foreach($pages as $p) {
			$links[] = array(
			'cID'=>$p->getCollectionID(),
			'url'=>NavigationHelper::getLinkToCollection($p, $full), 
			'name'=>$p->getCollectionName());
		}
		
		return $links;			
	}
	
	
	
 	public function form() {
        if (is_object($this->attributeValue)) {
			$value = trim($this->getAttributeValue()->getValue());
		}
		
		$ak = $this->getAttributeKey();
		
		$form_selector = Loader::helper('form/page_selector');
        $this->addHeaderItem(Loader::helper('html')->javascript($this->attributeType->getAttributeTypeFileURL('form.js')));

        $page_ids = array();
		
		if ($value) {
			$page_ids = explode(',', $value);
		}
		
		$pages = array();
		
		foreach($page_ids as $pID) {
			$page = Page::getByID($pID);
			if (!$page->isInTrash()) {
				$pages[] = $page;
			}
		}

        $akid = $ak->getAttributeKeyID();

        echo '<div id="multipageselector-' . $akid  . '" class="multipageselector" data-akid="' . $akid . ' ">';
        echo '<input type="hidden" class="selectedPages" id="'. $this->field('value'). '" name="'. $this->field('value'). '" value="'.$value.'" />';
        echo '<table style="width: 100%">';
	 	 
		foreach($pages as $page) {
			echo '<tr class="sortable_row" data-pageid="'. $page->getCollectionID() .'"><td class="sort"><img src="'.ASSETS_URL_IMAGES.'/icons/up_down.png" alt="sort" height="14" width="14" style="cursor:move;"></td><td>' . $page->getCollectionName() . '</td><td class="delete"><img src="'.ASSETS_URL_IMAGES.'/icons/remove.png" alt="delete" height="14" width="14" style="cursor:pointer;"></td></tr>';
		}
		
		echo '</table>';

        echo $form_selector->selectPage('pselector', '', 'ccmMultipageSelectorUpdateCallback');
        $assetsUrlImages = ASSETS_URL_IMAGES;

        echo <<<EOM
<script type="text/javascript">
    var multipageSelectorAttrId,
        ccmMultipageSelectorUpdateCallback;

    (function($) { // hide the namespace
        if ('function' !== typeof ccmMultipageSelectorUpdateCallback) {
            $.ccmmultipageselector.setDefaults({'assetImageUrl': '{$assetsUrlImages}'});


            ccmMultipageSelectorUpdateCallback = function(linkId, linkLabel) {
                $('#multipageselector-' + multipageSelectorAttrId).ccmmultipageselector('addPageLink', {'linkId': linkId, 'linkLabel': linkLabel});
            }
        }
    })(jQuery);

    $(document).ready(function () {
        // included to handle limitation of page properties dialog
        $('.multipageselector').closest('form').off('mouseover.ccmmultipageselector').on('mouseover.ccmmultipageselector', '.multipageselector', function(event){
            multipageSelectorAttrId = \$(this).data('akid');
        })

        $('#multipageselector-{$akid}').ccmmultipageselector();
    });
</script>
EOM;

        echo "</div> <!-- end:#multipageselector-{$akid} -->";
	}

 
	public function saveValue($value) {
		$db = Loader::db();
		$db->Replace('atMultiPageSelector', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atMultiPageSelector where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atMultiPageSelector where avID = ?', array($this->getAttributeValueID()));
	}
	
}
