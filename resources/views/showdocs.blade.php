<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Path</th>
        </tr>
        </thead>
        <tbody>
        @foreach($documents as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->path }}</td>
            <td><form action={{route('parseToFile', [$item->id])}} method="post" enctype="multipart/form-data">
            @csrf              <!-- с версии Laravel 5.6 -->
                <!-- поле для загрузки файла -->
                <input type="submit"    value="Распарсить">
                </form></td>
            <td><form action={{route('parseToDisplay', [$item->id])}} method="post" enctype="multipart/form-data">
                    @csrf              <!-- с версии Laravel 5.6 -->
                    <!-- поле для загрузки файла -->
                    <input type="submit"    value="Вывести на экран">
                </form></td>

        </tr>
        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>


