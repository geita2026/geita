<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($exhibitedProducts as $key=>$product)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $product->productName }}</td>
            </tr>
        @endforeach
    </tbody>
</table>