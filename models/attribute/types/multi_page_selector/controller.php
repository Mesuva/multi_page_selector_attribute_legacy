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
		
		$akid = $ak->getAttributeKeyID();
	 	
	 	echo '<div id="multipageselector_' . $akid  . '">';
		echo '<input type="hidden" id="'. $this->field('value'). '" name="'. $this->field('value'). '" value="'.$value.'" />';
		echo '<table id="pagelist_'. $akid . '" class="pagelist table" style="width: 100%">';
	 	 
		foreach($pages as $page) {
			echo '<tr class="sortable_row" data-pageid="'. $page->getCollectionID() .'"><td class="sort_handle"><img src="'.ASSETS_URL_IMAGES.'/icons/up_down.png"   height="14" width="14" style="cursor:move;"></td><td>' . $page->getCollectionName() . '</td><td class="delete_handle"><img src="'.ASSETS_URL_IMAGES.'/icons/remove.png"  height="14" width="14" style="cursor:pointer;"></td></tr>';
		}
		
		echo '</table>';
		
	 
		echo $form_selector->selectPage('pselector', '', 'pla_pageselected_' .  $akid);
 
		
		echo "<script type=\"text/javascript\">
		var lastselected = 0;
	 	 
	 	 
		function pla_pageselected_".  $akid ."(id, name) {
		     
		 	var pagelist = $('#pagelist_'+ lastselected);
		 	var newrow = $('<tr class=\"sortable_row\" data-pageid=\"'+ id +'\"><td class=\"sort_handle\"> </td><td>' + name + '</td><td class=\"delete_handle\"></td></tr>');
		 	
		 	pagelist.append(newrow);
		 	newrow.find('.sort_handle').append('<img src=\"".ASSETS_URL_IMAGES."/icons/up_down.png\" height=\"14\" width=\"14\" style=\"cursor:move;\">'); 
		 	newrow.find('.delete_handle').append('<img src=\"".ASSETS_URL_IMAGES."/icons/remove.png\" height=\"14\" width=\"14\" style=\"cursor:pointer;\">'); 
		 
		 	$('#pagelist_'+ lastselected + ' .delete_handle img').click(function(){
		 		$(this).parent().parent().remove(); 
		 		pla_updatefield_".  $akid . "() 
		 	});
		 	
		 	pla_updatefield_".  $akid . "();
		 	clearPageSelection();
		}
		 
		function pla_updatefield_".  $akid . "() {
				 
				var field = $('#akID\\\\['+lastselected+'\\\\]\\\\[value\\\\]');
				var data = new Array();
				 
				$('#pagelist_'+ lastselected + ' .sortable_row').each(function(){
					data.push($(this).data('pageid'));
				});
				
				field.val(data.join(','));
		}
		
		
		function pla_updatefieldsort_".  $akid . "() {
				 
				var field = $('#akID\\\\[".$akid ."\\\\]\\\\[value\\\\]');
				var data = new Array();
				 
				$('#pagelist_".$akid." .sortable_row').each(function(){
					data.push($(this).data('pageid'));
				});
				
				field.val(data.join(','));
		}
		 
		$(document).ready(function(){
		  $('#pagelist_". $akid ."').sortable({ items : '.sortable_row',
		        handle: '.sort_handle',
		        update: pla_updatefieldsort_" .  $akid . "
		    });
		    
		   $('#pagelist_". $akid ." .delete_handle img').click(function(){
		   		$(this).parent().parent().remove(); 
		   		pla_updatefieldsort_".  $akid . "();
		   }); 
		   
		   // included to handle limitation of page properties dialog
		   $(document).on('hover','#multipageselector_" . $akid .  " .ccm-sitemap-select-page' , function(e){
		         lastselected = ".$akid.";
		   })
		   
		});
		
		
		// included to handed composer
		$(window).load(function() { 
			$('#multipageselector_" . $akid .  " .ccm-sitemap-select-page').click(function(){
				    lastselected = ".$akid.";
			});
			
		});
	   	
	 		  		  
	 	</script>";
		
		echo '</div>';
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