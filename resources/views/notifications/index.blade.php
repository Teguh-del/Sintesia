@extends('layouts.dashboard')

@section('title', 'Notifikasi — SINTESA')
@section('header_title', 'Notifikasi Aktivitas Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Notifikasi Masuk</h3>
            <p class="text-xs text-slate-500">Pemberitahuan riil mengenai transaksi, pesanan, dan pembaruan stok Anda</p>
        </div>

        @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="check-check" class="w-4 h-4 text-emerald-600"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Notification Items -->
    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notif)
                <div class="p-5 rounded-2xl border transition flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-white border-slate-200/80 text-slate-600' : 'bg-emerald-50/40 border-emerald-300 shadow-sm text-slate-900' }}">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ $notif->is_read ? 'bg-slate-100 text-slate-500' : 'bg-emerald-600 text-white shadow-sm' }}">
                            @if($notif->type === 'order_created')
                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            @elseif($notif->type === 'order_confirmed')
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                            @elseif($notif->type === 'order_processed')
                                <i data-lucide="package" class="w-5 h-5"></i>
                            @elseif($notif->type === 'order_completed')
                                <i data-lucide="award" class="w-5 h-5"></i>
                            @elseif($notif->type === 'order_cancelled')
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="bell" class="w-5 h-5"></i>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">{{ $notif->title }}</h4>
                                @if(!$notif->is_read)
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $notif->message }}</p>
                            <span class="text-[10px] text-slate-400 block mt-2">{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->translatedFormat('d M Y, H:i') }})</span>
                        </div>
                    </div>

                    @if(!$notif->is_read)
                        <form action="{{ route('notifications.read', $notif) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Tandai dibaca">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    @else
        <div class="bg-white p-12 rounded-3xl border border-slate-200/80 text-center space-y-3">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                <i data-lucide="bell-off" class="w-7 h-7"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-900">Belum Ada Notifikasi</h4>
            <p class="text-xs text-slate-500">Aktivitas baru seperti pesanan dan perubahan status transaksi akan muncul di sini.</p>
        </div>
    @endif
</div>
@endsection
