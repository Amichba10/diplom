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
    @foreach($res as $item)
    <form id="myForm">
        <div class="form-group">
            <label for="word">Word:</label>
            <input type="text" class="form-control" id="word" value="{{$item}}">
        </div>
        <button class="btn btn-primary ajaxSubmit">Submit</button>
    </form>
    @endforeach
</div>
<script src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
</script>
<script>
    jQuery(document).ready(function(){
        jQuery('.ajaxSubmit').click(function(e){
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ route('addExceptional') }}",
                method: 'post',
                data: {
                    word: jQuery('#word').val(),
                    },
                success: function(result){
                    jQuery('.alert').show();
                    jQuery('.alert').html(result.success);
                }});
        });
    });
</script>
</body>
</html>
