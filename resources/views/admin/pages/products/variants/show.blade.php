<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <title>products</title>
</head>
<div class="container h-100 mt-5">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="col-10 col-md-8 col-lg-6">
      <h3>Update Post</h3>
      <form action="{{ route('products.update', $product->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" class="form-control" id="name" name="name"
            value="{{ $product->name }}" required>
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <input type="text" class="form-control" id="description" name="description"
            value="{{ $product->description }}" required>
        </div>
        <div class="form-group">
          <label for="store_id">store_id</label>
          <textarea class="form-control" id="store_id" name="store_id" rows="3" required>{{ $product->store_id }}</textarea>
        </div>
        <button type="submit" class="btn mt-3 btn-primary">Update Post</button>
      </form>
    </div>
  </div>
</div>
