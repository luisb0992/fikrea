{{--
    Visualiza un icono según el tipo Mime del archivo

    @param string $type                  El tipo mime del archivo

    @link https://developer.mozilla.org/es/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
--}}

@switch($type)
    @case('image/bmp')
    @case('image/x-ms-bmp')
    @case('image/gif')
    @case('image/jpg')
    @case('image/jpeg')
    @case('image/png')
    @case('image/tiff')
    @case('image/webp')
    @case('image/svg+xml')
        <i class="far fa-3x fa-file-image text-success"></i>
        @break
    @case('application/x-7z-compressed')
    @case('application/zip')
    @case('application/vnd.rar')
    @case('application/x-7z-compressed')
        <i class="far fa-3x fa-file-archive text-info"></i>
        @break
    @case('video/mp4')
    @case('video/mpeg')
    @case('video/webm')
    @case('video/x-msvideo')
    @case('video/x-matroska')
        <i class="far fa-3x fa-file-video text-danger"></i>
        @break
    @case('audio/mpeg')
    @case('audio/mp3')
    @case('audio/ogg')
    @case('audio/wav')
    @case('audio/wave')
    @case('audio/x-wav')
    @case('audio/webm')
    @case('audio/aac')
        <i class="far fa-3x fa-file-audio text-primary"></i>
        @break
    @case('application/pdf')
        <i class="far fa-3x fa-file-pdf text-danger"></i>
        @break
    @case('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
    @case('application/msword')
    @case('application/vnd.oasis.opendocument.text')
        <i class="far fa-3x fa-file-word text-info"></i>
        @break
    @case('text/plain')
    @case('application/rtf')
        <i class="far fa-3x fa-file-alt text-secondary"></i>
        @break
    @case('text/csv')
        <i class="far fa-3x fa-file-csv text-success"></i>
        @break
    @case('application/xml')
        <i class="far fa-3x fa-file-code text-secondary"></i>
         @break
    @case('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    @case('application/vnd.ms-excel')
    @case('application/vnd.oasis.opendocument.spreadsheet')
    <i class="far fa-3x fa-file-excel text-success"></i>
    @break
    @case('application/vnd.ms-powerpoint')
    @case('application/vnd.openxmlformats-officedocument.presentationml.presentation')
    <i class="far fa-3x fa-file-powerpoint text-danger"></i>
    @break
    @case('application/octet-stream')
    <i class="fas fa-3x fa-file-code text-danger"></i>
    @break
    @case('application/folder')
    <i class="fas fa-3x fa-folder text-warning"></i>
    @break
    @default
    <i class="far fa-3x fa-file text-secondary"></i>
@endswitch
