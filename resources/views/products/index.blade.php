<!-- resources/views/products/index.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
</head>

<body>

    <h1>List Produk</h1>

    @if(count($products) > 0)
    <ul>
        @foreach($products as $product)
        <li>Nama Produk : {{ $product->name }}</li>
        <li>Harga Produk : {{ $product->price }}</li>
        <li>Desk Produk : {{ $product->description }}</li>
        <li>Source : {{ $product->source }}</li>
        @endforeach
    </ul>
    @else
    <p>Tidak Ada Produk</p>
    @endif

</body>

</html>