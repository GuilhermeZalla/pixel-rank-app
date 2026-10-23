@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<x-layout>
    <x-slot:title>{{ __('dashboard.notifications') }}</x-slot:title>
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-bold text-2xl sm:text-[1.7rem]">{{ __('dashboard.notifications') }}</h1>
                <p class="text-sm text-base-content/70 mt-1">{{ __('dashboard.notifications_description') }}</p>
            </div>
            @if($unreadCount > 0)
                <a href="{{ route('notifications.read-all') }}"
                   class="btn btn-sm btn-outline border-white/20 hover:border-accent hover:text-accent font-bold min-h-[44px] gap-2 px-4">
                    <x-heroicon-o-bell class="size-4" />
                    <span>{{ __('dashboard.mark_all_read') }}</span>
                </a>
            @endif
        </div>
        <x-divisor class="my-6"></x-divisor>

        @if($notifications->isEmpty())
            <div class="py-16 flex flex-col items-center justify-center text-center gap-4">
                <x-heroicon-o-bell class="text-accent size-24 opacity-80" />
                <h3 class="font-bold text-xl sm:text-2xl">{{ __('dashboard.no_notifications') }}</h3>
                <p class="text-sm text-base-content/70 max-w-md">{{ __('dashboard.no_notifications_description') }}</p>
            </div>
        @else
            <ul class="flex flex-col gap-3">
                @foreach($notifications as $notification)
                    <li class="flex items-start gap-4 p-4 rounded-xl border border-white/10 bg-base-200
                        {{ $notification->read_at ? 'opacity-70' : 'bg-base-300 border-accent/30' }}">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-bell class="size-5 text-accent {{ $notification->read_at ? 'text-base-content/40' : '' }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-base-content/90 leading-relaxed">
                                @php
                                    $data = $notification->data;
                                @endphp
                                @if($data['type'] === 'comment_on_review')
                                    <strong>{{ $data['from_user_nickname'] }}</strong>
                                    {{ __('dashboard.commented_on_your_review', ['title' => $data['review_title']]) }}
                                @elseif($data['type'] === 'reply_to_comment')
                                    <strong>{{ $data['from_user_nickname'] }}</strong>
                                    {{ __('dashboard.replied_to_your_comment') }}
                                @else
                                    {{ $data['type'] }}
                                @endif
                            </p>
                            <a href="{{ $data['url'] }}"
                               class="text-xs text-accent font-medium mt-1 inline-block hover:underline">
                                {{ __('dashboard.view_review') }} →
                            </a>
                        </div>
                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="flex-shrink-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-xs btn-circle btn-outline border-white/20 hover:border-accent hover:text-accent p-2"
                                        title="{{ __('dashboard.mark_as_read') }}">
                                    <x-heroicon-o-check class="size-3.5" />
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
            <x-pagination :pagination="$notifications" class="mt-8"></x-pagination>
        @endif
    </div>
</x-layout>
