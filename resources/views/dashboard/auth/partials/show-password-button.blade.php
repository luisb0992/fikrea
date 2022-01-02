{{-- Botón para ver la contraseña --}}
<div @click="tooglePassword" class="input-group-append password-eye"
:class="passwordHidden ? 'show' : 'hide'">
    <span class="input-group-text">
        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation">
            <g fill="currentColor" fill-rule="evenodd">
                <path d="M12 18c-4.536 0-7.999-4.26-7.999-6 0-2.001 3.459-6 8-6 4.376 0 7.998 3.973 7.998 6 0 1.74-3.462 6-7.998 6m0-14C6.48 4 2 8.841 2 12c0 3.086 4.576 8 10 8 5.423 0 10-4.914 10-8 0-3.159-4.48-8-10-8"></path><path d="M11.977 13.984c-1.103 0-2-.897-2-2s.897-2 2-2c1.104 0 2 .897 2 2s-.896 2-2 2m0-6c-2.206 0-4 1.794-4 4s1.794 4 4 4c2.207 0 4-1.794 4-4s-1.793-4-4-4">
                </path>
            </g>
        </svg>
    </span>
</div>

<div @click="tooglePassword" class="input-group-append password-eye"
    :class="passwordHidden ? 'hide' : 'show'">
    <span class="input-group-text">
        <svg width="24" height="24" viewBox="0 0 24 24" focusable="false" role="presentation">
            <g fill="currentColor" fill-rule="evenodd"><path d="M11.983 15.984a4.005 4.005 0 0 1-4.002-4c0-2.206 1.795-4 4.002-4a4.005 4.005 0 0 1 4.002 4c0 2.206-1.795 4-4.002 4M12 4C6.48 4 2 8.84 2 12c0 3.086 4.577 8 10 8s10-4.914 10-8c0-3.16-4.481-8-10-8"></path>
                <circle cx="12" cy="12" r="2"></circle>
            </g>
        </svg>
    </span>
</div>
{{--/Botón para ver la contraseña --}}