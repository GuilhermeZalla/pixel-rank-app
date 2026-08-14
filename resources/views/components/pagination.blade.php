@props(['pagination' => ''])

<div {{ $attributes }}>@if(!empty($pagination->links())){{ $pagination->links() }}@endif</div>
