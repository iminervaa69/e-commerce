<?php
// File: app/View/Components/ReviewCard.php

namespace App\View\Components;

use Illuminate\View\Component;

class ReviewCard extends Component
{
    public $title;
    public $averageRating;
    public $maxRating;
    public $satisfactionText;
    public $totalReviews;
    public $totalComments;
    public $ratings;

    /**
     * Create a new component instance.
     *
     * @param string $title
     * @param float $averageRating
     * @param int $maxRating
     * @param string $satisfactionText
     * @param int $totalReviews
     * @param int $totalComments
     * @param array $ratings
     */
    public function __construct(
        $title = 'ULASAN PEMBELI',
        $averageRating = 5.0,
        $maxRating = 5,
        $satisfactionText = '100% pembeli merasa puas',
        $totalReviews = 13,
        $totalComments = 11,
        $ratings = []
    ) {
        $this->title = $title;
        $this->averageRating = $averageRating;
        $this->maxRating = $maxRating;
        $this->satisfactionText = $satisfactionText;
        $this->totalReviews = $totalReviews;
        $this->totalComments = $totalComments;
        
        // Default ratings distribution if not provided
        $this->ratings = $ratings ?: [
            5 => 13,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.common.review-card');
    }

    /**
     * Calculate percentage for rating bar
     *
     * @param int $count
     * @return float
     */
    public function getPercentage($count)
    {
        return $this->totalReviews > 0 ? ($count / $this->totalReviews) * 100 : 0;
    }

    /**
     * Get star display for rating
     *
     * @return array
     */
    public function getStars()
    {
        $stars = [];
        $fullStars = floor($this->averageRating);
        $hasHalfStar = ($this->averageRating - $fullStars) >= 0.5;

        for ($i = 1; $i <= $this->maxRating; $i++) {
            if ($i <= $fullStars) {
                $stars[] = 'full';
            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                $stars[] = 'half';
            } else {
                $stars[] = 'empty';
            }
        }

        return $stars;
    }
}