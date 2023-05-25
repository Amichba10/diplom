<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="_token" content="{{csrf_token()}}" />
    <title>Grocery Store</title>
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container">
    <div class="alert alert-success" style="display:none"></div>
    @foreach($verbs as $item)
        <form id="myForm">

            <input type="text"  id="word" value="{{$item->word}}" readonly="readonly">

            <button class="ajaxSubmit">Добавить в исключения</button>
        </form>
    @endforeach
</div>
<div>
    {{$verbs ->links()}}
</div>
<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
</script>
<script>
    jQuery(document).ready(function(){
        jQuery('.ajaxSubmit').click(function(e){
            e.preventDefault();
             console.log(e.target.parentNode.querySelector('#word').value);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ route('addExceptional') }}",
                method: 'post',
                data: {
                    word: e.target.parentNode.querySelector('#word').value
                },
                success: function(result){
                    jQuery('.alert').show();
                    jQuery('.alert').html(result.success);
                }});
        });
    });
</script>
<tr>
    <form action={{route('showDocs')}}  enctype="multipart/form-data">
        @csrf              <!-- с версии Laravel 5.6 -->
        <!-- поле для загрузки файла -->
        <input type="submit"    value="Назад">
    </form>
</tr>

</body>
</html>
