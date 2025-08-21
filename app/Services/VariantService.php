<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantTemplate;

class VariantService
{
    /**
     * Create variants from template for a product
     */
    public function createVariantsFromTemplate(Product $product, array $combinations = null)
    {
        // Use provided combinations or generate all possible ones
        $combinations = $combinations ?? $product->generateVariantCombinations();
        
        $createdVariants = [];
        
        foreach ($combinations as $combination) {
            // Skip if variant already exists
            if ($this->variantExists($product, $combination)) {
                continue;
            }
            
            $createdVariants[] = $product->createVariantFromCombination($combination);
        }
        
        return collect($createdVariants);
    }

    /**
     * Check if variant combination already exists
     */
    public function variantExists(Product $product, array $combination)
    {
        return $product->variants()
            ->where('variant_combination', json_encode($combination))
            ->exists();
    }

    /**
     * Update product attributes and sync variants
     */
    public function updateProductVariants(Product $product, array $newAttributes)
    {
        $product->update(['variant_attributes' => $newAttributes]);
        
        // Get current variants
        $currentVariants = $product->variants()->whereNotNull('variant_combination')->get();
        
        // Check which variants are still valid
        $validCombinations = $product->generateVariantCombinations();
        
        foreach ($currentVariants as $variant) {
            $isValid = $this->isValidCombination($variant->variant_combination, $validCombinations);
            
            if (!$isValid) {
                // Mark invalid variants as inactive or delete them
                $variant->update(['status' => 'discontinued']);
            }
        }
    }

    /**
     * Check if a combination is still valid
     */
    private function isValidCombination($combination, $validCombinations)
    {
        foreach ($validCombinations as $valid) {
            if ($combination == $valid) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get variant by specific attributes
     */
    public function findVariantByCombination(Product $product, array $attributes)
    {
        $query = $product->variants();
        
        foreach ($attributes as $key => $value) {
            $query->whereJsonContains('variant_combination->' . $key, $value);
        }
        
        return $query->first();
    }

    /**
     * Get variants filtered by attributes
     */
    public function getVariantsByAttributes(Product $product, array $filters)
    {
        $query = $product->variants()->where('status', 'active');
        
        foreach ($filters as $attribute => $value) {
            if ($value !== null) {
                $query->whereJsonContains('variant_combination->' . $attribute, $value);
            }
        }
        
        return $query->get();
    }

    /**
     * Get available values for each attribute from existing variants
     */
    public function getAvailableAttributeValues(Product $product)
    {
        $variants = $product->variants()
            ->whereNotNull('variant_combination')
            ->where('status', 'active')
            ->get();
            
        $availableValues = [];
        
        foreach ($variants as $variant) {
            foreach ($variant->variant_combination as $key => $value) {
                if (!isset($availableValues[$key])) {
                    $availableValues[$key] = [];
                }
                
                if (!in_array($value, $availableValues[$key])) {
                    $availableValues[$key][] = $value;
                }
            }
        }
        
        return $availableValues;
    }
}