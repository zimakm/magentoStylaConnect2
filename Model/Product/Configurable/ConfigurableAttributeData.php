<?php
namespace Styla\Connect2\Model\Product\Configurable;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{
    /**
     * Get product attributes
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesDataExtended(Product $product, array $options = [], array $allowProducts = [])
    {
        $defaultValues = [];
        $attributes = [];
        foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsDataExtended($attribute, $options, $allowProducts);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $attributes[$attributeId] = [
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $productAttribute->getStoreLabel($product->getStoreId()),
                    'options' => $attributeOptionsData,
                    'position' => $attribute->getPosition(),
                ];
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }
        return [
            'attributes' => $attributes,
            'defaultValues' => $defaultValues,
        ];
    }
    
    /**
     * @param Attribute $attribute
     * @param array $config
     * @return array
     */
    protected function getAttributeOptionsDataExtended($attribute, $config, $allowProducts)
    {
        $attributeOptionsData = [];
        foreach ($attribute->getOptions() as $attributeOption) {
            $optionId = $attributeOption['value_index'];
            $productsarr = $this->_getSimpleProducts($attribute, $config, $optionId, $allowProducts);
            if (empty($productsarr)){
                continue;
            }
            if ($productsarr[0]['enabled']){
                $attributeOptionsData[] = [
                    'id' => $optionId,
                    'label' => $attributeOption['label'],
                    'products' => $productsarr
                ];
            }

        }
        return $attributeOptionsData;
    }
    
    /**
     * 
     * @param Attribute $attribute
     * @param array $config
     * @param int $optionId
     * @param array $allowProducts
     * @return array
     */
    protected function _getSimpleProducts($attribute, $config, $optionId, $allowProducts)
    {
        $simpleProducts = isset($config[$attribute->getAttributeId()][$optionId])
            ? $config[$attribute->getAttributeId()][$optionId]
            : [];
        //here we enter all the product data that might be useful later on.
        //for now, the id and saleability status
        $productData = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $stockState = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
        foreach ($simpleProducts as $productId) {
            if (!isset($allowProducts[$productId])) {
                continue;
            }
            $product = $allowProducts[$productId];
            $productData[] = [
                'id'       => $productId,
                'sku'      => $product->getSku(),
                'enabled'  => $product->getIsSalable(),
                'saleable' => ($stockState->getStockQty($productId) < 1 ? false : true)
            ];
        }
        
        return $productData;
    }
}
