<div>
    <script src="{{asset('html5barcode.js')}}"></script>

    <div wire:ignore style="width: 70vh;" id="reader"></div>

    <style>
        #reader {
            border: none !important;
        }
        div#reader__dashboard_section_csr {
            display: flex !important;
            flex-direction: column;
        }
    </style>

</div>
