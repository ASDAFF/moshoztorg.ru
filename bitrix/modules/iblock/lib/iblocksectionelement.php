<?php

use Bitrix\Main\Entity;

class IBlockSectionElementEntity extends Entity\Base
{
	protected function __construct() {}

	public function Initialize()
	{
		$this->className = __CLASS__;
		$this->filePath = __FILE__;

		$this->dbTableName = 'b_iblock_section_element';

		$this->fieldsMap = array(
			'IBLOCK_SECTION_ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'IBLOCK_SECTION' => array(
				'data_type' => 'IBlockSection',
				'reference' => array(
					'=this.IBLOCK_SECTION_ID' => 'ref.ID'
				)
			),
			'IBLOCK_ELEMENT_ID' => array(
				'data_type' => 'integer',
				'primary' => true
			),
			'IBLOCK_ELEMENT' => array(
				'data_type' => 'IBlockElement',
				'reference' => array(
					'=this.IBLOCK_ELEMENT_ID' => 'ref.ID'
				)
			),
			'ADDITIONAL_PROPERTY_ID' => array(
				'data_type' => 'integer'
			)
		);
	}
}
?>
