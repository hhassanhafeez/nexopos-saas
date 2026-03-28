@extends( 'layout.dashboard' )
@section( 'layout.dashboard.body' )
    <div>
        @include( Hook::filter( 'ns-dashboard-header-file', '../common/dashboard-header' ) )
        <div id="dashboard-content" class="px-4">
            <div class="page-inner-header mb-4">
                <h3 class="text-3xl text-fontcolor font-bold">{!! sprintf( __( 'Receipt — %s' ), $order->code ) !!}</h3>
                <p class="text-fontcolor-soft">{{ __( 'Order receipt' ) }}</p>
            </div>
            <div class="my-2 w-full mx-auto">
                <ns-link type="info" href="{{ ns()->url( '/dashboard/orders/receipt/' . $order->id . '?dash-visibility=disabled' ) }}">{{ __( 'Hide Dashboard' ) }}</ns-link>
                <?php
                    $storeName   = ns()->option->get('ns_store_name', 'Store');
                    $receiptUrl  = ns()->url('/dashboard/orders/receipt/' . $order->id . '?dash-visibility=disabled');
                    $productLines = $order->combinedProducts->map(fn($p) => $p->name . ' x' . (int)$p->quantity . ' — ' . ns()->currency->define($p->total_price))->implode("\n");
                    $waMessage   = "*{$storeName}*\n" .
                                   "Order: {$order->code}\n\n" .
                                   "{$productLines}\n\n" .
                                   "*Total: " . ns()->currency->define($order->total) . "*\n\n" .
                                   "Receipt: {$receiptUrl}";
                    $customerPhone = $order->customer?->phone ?? '';
                    $waPhone     = preg_replace('/[^0-9]/', '', $customerPhone);
                    // Convert Pakistani local format 03xx to international 923xx
                    if (str_starts_with($waPhone, '0')) {
                        $waPhone = '92' . substr($waPhone, 1);
                    }
                    $waUrl = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessage);
                ?>
                <a href="{{ $waUrl }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded text-white text-sm font-medium ml-2"
                   style="background-color:#25D366;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.558 4.114 1.535 5.836L.057 23.93l6.243-1.637A11.94 11.94 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 0 1-5.006-1.373l-.36-.214-3.706.972.989-3.613-.235-.372A9.818 9.818 0 0 1 2.182 12c0-5.42 4.398-9.818 9.818-9.818 5.42 0 9.818 4.398 9.818 9.818 0 5.42-4.398 9.818-9.818 9.818z"/></svg>
                    {{ __('Send via WhatsApp') }}
                </a>
                @include( Hook::filter( 'ns-web-receipt-template', 'pages.dashboard.orders.templates._receipt' ) )
            </div>
        </div>
    </div>
@endsection