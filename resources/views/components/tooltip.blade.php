@props(['text'])
<div class="absolute">
    <span class="tooltip-toggle"
          aria-label="{{$text}}" tabIndex="0">
                                <i class="fa-solid fa-circle-question"></i>
                            </span>
    <style>
        .tooltip-toggle {
            cursor: pointer;
            position: relative;

            &::before {
                position: absolute;
                top: -80px;
                left: -80px;
                background-color: #2B222A;
                border-radius: 5px;
                color: #fff;
                content: attr(aria-label);
                padding: 1rem;
                text-transform: none;
                transition: all 0.5s ease;
                width: 160px;
            }

            &::after {
                position: absolute;
                top: -12px;
                left: 9px;
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-top: 5px solid #2B222A;
                content: " ";
                font-size: 0;
                line-height: 0;
                margin-left: -5px;
                width: 0;
            }

            &::before,
            &::after {
                color: #efefef;
                font-family: monospace;
                font-size: 16px;
                opacity: 0;
                pointer-events: none;
                text-align: center;
            }

            &:focus::before,
            &:focus::after,
            &:hover::before,
            &:hover::after {
                opacity: 1;
                transition: all 0.75s ease;
            }
        }
    </style>
</div>
