@extends('layouts.main')
@section('title')
Dashboard
@endsection
@section('content')
<div class="p-4 h-100000 border-2 border-gray-200 border-dashed rounded-lg mt-14">
<x-common.tab_header
    :tabs="[
        ['id' => 'profile', 'label' => 'Profile'],
        ['id' => 'dashboard', 'label' => 'Dashboard']
    ]"
/>


</div>
@endsection

@section('insert-scripts')
<script>
</script>
@endsection
