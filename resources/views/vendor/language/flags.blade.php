<ul class="{{config('language.flags.ul_class')}}">
@foreach (language()->allowed() as $code => $name)
    <li class="{{config('language.flags.li_class')}}">
        <a href="{{language()->back($code)}}">
            <img src="{{asset('assets/images/akaunting/flags/'. language()->country($code) .'.png')}}" alt="{{$name}}" />
             {{$name}}
        </a>
    </li>
@endforeach
</ul>