<div class="overlay">
    <span {!! $attributes->merge(["class"=>"loader"]) !!}></span>
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        .loader {
            position: absolute;
            left: 50%;
            bottom: 50%;
            width: 48px;
            height: 48px;
            border: 5px solid #4299e1;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</div>
