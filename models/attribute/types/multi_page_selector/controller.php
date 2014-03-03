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
	 	
		print '<input type="hidden" id="'. $this->field('value'). '" name="'. $this->field('value'). '" value="'.$value.'" />';
		print '<img src="'.ASSETS_URL_IMAGES.'/icons/up_down.png" class="sortimage_proto" height="14" width="14" style="cursor:move; display: none;">';
		print '<img src="'.ASSETS_URL_IMAGES.'/icons/remove.png" class="deleteimage_proto" height="14" width="14" style="cursor:pointer; display: none;">';
		print '<table class="pagelist table" style="width: 100%">';
	 	 
		foreach($pages as $page) {
			echo '<tr class="sortable_row" data-pageid="'. $page->getCollectionID() .'"><td class="sort_handle"><img src="'.ASSETS_URL_IMAGES.'/icons/up_down.png"   height="14" width="14" style="cursor:move;"></td><td>' . $page->getCollectionName() . '</td><td class="delete_handle"><img src="'.ASSETS_URL_IMAGES.'/icons/remove.png"  height="14" width="14" style="cursor:pointer;"></td></tr>';
		}
		
		echo '</table>';
		
		print $form_selector->selectPage('pselector', '', 'pla_pageselected');
		print "<script type=\"text/javascript\">
		 
		function pla_pageselected(id, name) {
		 
		 	var sorthandle = $('.ccm-sitemap-select-page').parent().parent().find('.sortimage_proto').clone();
		 	var deletehandle = $('.ccm-sitemap-select-page').parent().parent().find('.deleteimage_proto').clone();
		 	sorthandle.css('display','block').removeClass('sortimage_proto');
		 	deletehandle.css('display','block').removeClass('deleteimage_proto');
		 	  
		 	var pagelist = $('.ccm-sitemap-select-page').parent().parent().find('.pagelist');
		 	var newrow = $('<tr class=\"sortable_row\" data-pageid=\"'+ id +'\"><td class=\"sort_handle\"> </td><td>' + name + '</td><td class=\"delete_handle\"></td></tr>');
		 	
		 	pagelist.append(newrow);
		 	newrow.find('.sort_handle').append(sorthandle); 
		 	newrow.find('.delete_handle').append(deletehandle); 
		 
		 	$('.delete_handle img').click(function(){
		 		$(this).parent().parent().remove(); 
		 		pla_updatefield() 
		 	});
		 	
		 	pla_updatefield();
		 	clearPageSelection();
		}
		 
		function pla_updatefield() {
				 
				var field = $('#akID\\\\[".$ak->getAttributeKeyID()."\\\\]\\\\[value\\\\]');
				var data = new Array();
				 
				$('.pagelist .sortable_row').each(function(){
					data.push($(this).data('pageid'));
				});
				
				field.val(data.join(','));
		}
		
		 
		$(document).ready(function(){
		  $('.pagelist').sortable({ items : '.sortable_row',
		        handle: '.sort_handle',
		        update: pla_updatefield
		    });
		    
		   $('.delete_handle img').click(function(){
		   		$(this).parent().parent().remove(); 
		   		pla_updatefield() 
		   }); 
		    
		});
		 
		
		</script>";
		
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