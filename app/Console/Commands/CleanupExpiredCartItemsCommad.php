<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupExpiredCartItems extends Command
{
    protected $signature = 'cart:cleanup 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompts}';
    
    protected $description = 'Remove expired cart items and clean up old session data';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->info('🛒 Starting cart cleanup...');
        
        // Clean expired cart items
        $this->cleanExpiredItems($dryRun, $force);
        
        // Clean old soft-deleted items
        $this->cleanOldSoftDeletedItems($dryRun, $force);
        
        $this->info('✅ Cart cleanup completed!');
    }

    /**
     * Clean expired cart items
     */
    private function cleanExpiredItems(bool $dryRun, bool $force): void
    {
        $expiredItems = CartItem::expired()->get();
        
        if ($expiredItems->isEmpty()) {
            $this->info('ℹ️  No expired cart items found.');
            return;
        }

        $this->info("📦 Found {$expiredItems->count()} expired cart items:");

        // Group by type and expiration date
        $guestItems = $expiredItems->where('user_id', null);
        $userItems = $expiredItems->whereNotNull('user_id');

        if ($guestItems->isNotEmpty()) {
            $this->line("  - {$guestItems->count()} guest cart items");
        }
        
        if ($userItems->isNotEmpty()) {
            $this->line("  - {$userItems->count()} user cart items");
        }

        // Show breakdown by expiration date
        $groupedByDate = $expiredItems->groupBy(function($item) {
            return $item->expires_at->format('Y-m-d');
        });

        foreach ($groupedByDate as $date => $items) {
            $this->line("  - {$items->count()} items expired on {$date}");
        }

        if ($dryRun) {
            $this->warn('🔍 DRY RUN: No items were actually deleted.');
            return;
        }

        if (!$force && !$this->confirm('❓ Do you want to delete these expired cart items?')) {
            $this->info('❌ Cleanup cancelled.');
            return;
        }

        // Perform soft delete
        $deletedCount = CartItem::expired()->delete();
        
        $this->info("🗑️  Successfully soft-deleted {$deletedCount} expired cart items.");
    }

    /**
     * Clean old soft-deleted items
     */
    private function cleanOldSoftDeletedItems(bool $dryRun, bool $force): void
    {
        $cutoffDate = Carbon::now()->subDays(30);
        $oldSoftDeletedItems = CartItem::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->get();

        if ($oldSoftDeletedItems->isEmpty()) {
            $this->info('ℹ️  No old soft-deleted cart items found.');
            return;
        }

        $this->info("🗂️  Found {$oldSoftDeletedItems->count()} old soft-deleted cart items (deleted > 30 days ago):");

        // Show breakdown
        $groupedByDate = $oldSoftDeletedItems->groupBy(function($item) {
            return $item->deleted_at->format('Y-m-d');
        });

        foreach ($groupedByDate->take(5) as $date => $items) {
            $this->line("  - {$items->count()} items deleted on {$date}");
        }

        if ($groupedByDate->count() > 5) {
            $remaining = $groupedByDate->count() - 5;
            $this->line("  - ... and {$remaining} more dates");
        }

        if ($dryRun) {
            $this->warn('🔍 DRY RUN: No items would be permanently deleted.');
            return;
        }

        if (!$force && !$this->confirm('❓ Permanently delete these old soft-deleted items? This cannot be undone.')) {
            $this->info('❌ Permanent deletion cancelled.');
            return;
        }

        // Perform hard delete
        $permanentlyDeletedCount = CartItem::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->forceDelete();
                
        $this->info("🔥 Permanently deleted {$permanentlyDeletedCount} old cart items.");
    }
}