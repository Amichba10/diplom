<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body><table class="table table-bordered table-hover">
    <tbody>
    @foreach($res as $item)
        <tr>
            <td><form action={{route('addExceptional')}} method="post" enctype="multipart/form-data">
                @csrf              <!-- с версии Laravel 5.6 -->
                    <!-- поле для загрузки файла -->
                    <input type="text" name="word" value="{{$item}}" readonly>
                    <input type="submit"    value="Добавить в исключения">
                </form></td>
        </tr>
@endforeach
</table>
</body>
</html>
