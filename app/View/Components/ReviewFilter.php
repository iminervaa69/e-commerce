<?php
// app/View/Components/ReviewFilter.php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ProductReview;
// use App\Models\Topic;

class ReviewFilter extends Component
{
    public $productId;
    public $categoryId;
    protected $baseQuery;

    /**
     * Create a new component instance.
     */
    public function __construct($productId = null, $categoryId = null)
    {
        $this->productId = $productId;
        $this->categoryId = $categoryId;
        $this->baseQuery = $this->buildBaseQuery();
    }

    /**
     * Build the base query for filtering
     */
    private function buildBaseQuery()
    {
        $query = ProductReview::query();

        if ($this->productId) {
            $query->where('product_id', $this->productId);
        }

        if ($this->categoryId) {
            $query->whereHas('product', function($q) {
                $q->where('category_id', $this->categoryId);
            });
        }

        return $query;
    }

    /**
     * Get rating options with counts
     */
    public function getRatingOptions()
    {
        return collect([5, 4, 3, 2, 1])->map(function($rating) {
            return [
                'value' => $rating,
                'label' => $rating,
                'stars' => $rating,
                'count' => $this->getRatingCount($rating)
            ];
        });
    }

    /**
     * Get count of reviews for specific rating
     */
    private function getRatingCount($rating)
    {
        return (clone $this->baseQuery)->where('rating', $rating)->count();
    }

    /**
     * Get topic options with counts
     */
    // public function getTopicOptions()
    // {
    //     $query = Topic::query();

    //     if ($this->productId || $this->categoryId) {
    //         $query->whereHas('reviews', function($q) {
    //             if ($this->productId) {
    //                 $q->where('product_id', $this->productId);
    //             }
    //             if ($this->categoryId) {
    //                 $q->whereHas('product', function($q2) {
    //                     $q2->where('category_id', $this->categoryId);
    //                 });
    //             }
    //         });
    //     }

    //     return $query->withCount('reviews')
    //                 ->orderBy('reviews_count', 'desc')
    //                 ->get()
    //                 ->map(function($topic) {
    //                     return [
    //                         'value' => $topic->slug,
    //                         'label' => $topic->name,
    //                         'count' => $topic->reviews_count
    //                     ];
    //                 });
    // }

    /**
     * Get media filter options
     */
    // public function getMediaOptions()
    // {
    //     return [
    //         [
    //             'value' => 'photo_video',
    //             'label' => 'Dengan Foto & Video',
    //             'count' => (clone $this->baseQuery)->whereHas('media')->count()
    //         ]
    //     ];
    // }

    /**
     * Get currently selected filters
     */
    public function getSelectedFilters()
    {
        return [
            'rating' => request('rating', []),
            'topics' => request('topics', []),
            'media' => request('media', []),
            'sort' => request('sort', 'newest')
        ];
    }

    /**
     * Check if any filters are active
     */
    public function hasActiveFilters()
    {
        $selected = $this->getSelectedFilters();
        return !empty($selected['rating']) || 
               !empty($selected['topics']) || 
               !empty($selected['media']);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.common.review-filter', [
            'ratingOptions' => $this->getRatingOptions(),
            // 'topicOptions' => $this->getTopicOptions(),
            // 'mediaOptions' => $this->getMediaOptions(),
            'selectedFilters' => $this->getSelectedFilters(),
            'hasActiveFilters' => $this->hasActiveFilters()
        ]);
    }
}