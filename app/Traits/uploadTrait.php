<?php

namespace App\Traits;

trait UploadTrait
{
    function upload($file, $id, $name, $folder = null)
    {
        $slug = str_replace(' ', '_', strtolower(preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
        $filename = "{$id}_{$slug}_" . now()->format('YmdHis') . '.' . $file>getClientOriginalExtension();

        if (!folder) {
            $folder = 'uncategories';;
        }

        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }

}
