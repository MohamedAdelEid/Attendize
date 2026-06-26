@extends('ViewEvent.layouts.layout')

@push('styles')
<style>
@media print {
    header, footer, .ticket-actions, .no-print { display: none !important; }
    body { background: #fff !important; }
    .ticket-print-wrapper { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    main { padding-top: 0 !important; }
}
body {
	
	direction: initial;
}
</style>
@endpush

@section('content')
<div class="max-w-4xl px-4 py-12 mx-auto ticket-print-wrapper" >
    <div class="overflow-hidden bg-white shadow-lg rounded-xl">
        <div class="p-6 md:p-8">
               
                <div id="ticket">
                    <div class="overflow-hidden bg-white ">
                        <div class="px-4 py-5 text-center sm:p-6">
							<h1 style="font-size: 22px;"><strong>{{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}}</strong></h1>

                            <div class="mt-6">
                                <span class="block mb-3 text-sm"><strong>{{$user->unique_code}}</strong></span>
                                <img src="{{ asset('storage/' . $user->qr_code_path) }}" alt="QR Code" class="mx-auto" style="width:90px !important;height:90px">
                            </div>
                        </div>
						
                    </div>
                </div>


            
        </div>
    </div>
</div>

<script>
window.onload = function() {
	
	
	var printContents = '<br/><br/><br/><br/><br/><br/>'+document.getElementById("ticket").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    
}
window.onafterprint = function(){
	var url = "{!! $url !!}";

    if (url !== null && url !== "null" && url !== "") {
        	document.location.href = url,true ;
    }
    
    if (window.parent) {
        window.parent.postMessage('print-finished', '*');
    }

		
}




</script>
@endsection
