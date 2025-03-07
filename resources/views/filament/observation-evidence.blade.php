{{-- DOWNLOADABLES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.docx'))
                <div class="">
                    <a class="group relative inline-block text-sm font-medium text-indigo-600 focus:ring-3 focus:outline-hidden"
                        href="{{ $item }}">
                        <span
                            class="absolute inset-0 translate-x-0 translate-y-0 bg-indigo-600 transition-transform group-hover:translate-x-0.5 group-hover:translate-y-0.5"></span>

                        <span class="relative block border border-current bg-white px-8 py-3"> Download File </span>
                    </a>
                </div>
            @endif
        @endif
    @endforeach
</div>

{{-- WATCHABLES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.mp4'))
                <div class="">
                    <video
                        style="
                    width: 420px !important;
                    height: 350px !important;
                "
                        controls>
                        <source src='{{ $item }}' type=''>
                        Your browser does not support the video tag.
                    </video>
                </div>
            @elseif (Str::contains($item, '.docx'))
            @endif
        @endif
    @endforeach
</div>

{{-- IMAGES --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-4 lg:gap-8 mb-3">
    @foreach ($evidences ?? [] as $item)
        @if ($item)
            @if (Str::contains($item, '.mp4'))
            @elseif (Str::contains($item, '.docx'))
            @else
                <div class="">
                    <img src='{{ $item }}' target='_blank' class="myImg"
                        style="
                    width: 420px !important;
                    height: 350px !important;
                " />
                </div>
            @endif
        @endif
    @endforeach
</div>

<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- The Close Button -->
    <span class="close">&times;</span>

    <!-- Modal Content (The Image) -->
    <img class="modal-content" id="img01">

    <!-- Modal Caption (Image Text) -->
    <div id="caption"></div>
</div>

@push('scripts')
    <style>
        /* Style the Image Used to Trigger the Modal */
        .myImg {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .myImg:hover {
            opacity: 0.7;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 9999;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.9);
            /* Black w/ opacity */
        }

        /* Modal Content (Image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Caption of Modal Image (Image Text) - Same Width as the Image */
        #caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }

        /* Add Animation - Zoom in the Modal */
        .modal-content,
        #caption {
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }
    </style>
    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        // Get the images and set up click handlers for each one
        var images = document.getElementsByClassName("myImg");
        for (var i = 0; i < images.length; i++) {
            images[i].onclick = function() {
                console.log('clicked!');
                var modalImg = document.getElementById("img01");
                var captionText = document.getElementById("caption");

                modal.style.display = "block";
                modalImg.src = this.src;
                captionText.innerHTML = this.alt;
            }
        }
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
    </script>
@endpush
