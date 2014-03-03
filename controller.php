<?php       

defined('C5_EXECUTE') or die(_("Access Denied."));

class MultiPageSelectorAttributePackage extends Package {

	protected $pkgHandle = 'multi_page_selector_attribute';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '0.9';
	
	public function getPackageDescription() {
		return t("Attribute that allows the selection of multiple pages in a sortable list.");
	}
	
	public function getPackageName() {
		return t("Multi Page Selector Attribute");
	}
	
	public function install() {
		$pkg = parent::install();
		$pkgh = Package::getByHandle('multi_page_selector_attribute'); 
		Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');
		$pageselector = AttributeType::add('multi_page_selector', t('Multi Page Selector'), $pkgh);
		$col->associateAttributeKeyType(AttributeType::getByHandle('multi_page_selector'));
	}
}