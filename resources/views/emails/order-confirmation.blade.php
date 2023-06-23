<!DOCTYPE html>
<html>
<head>
    <title>Confirmação do Pedido</title>
</head>
<body>
<h1>Confirmação do Pedido</h1>
<p>Cliente {{ $order->client->name }},</p>
<p>Seu pedido foi confirmado com sucesso!</p>
<p><b>Detalhes:</b></p>
<ul>
    <li>Código: {{ $order->id }}</li>
    <li>Data: {{ $order->created_at }}</li>
</ul>
<p><b>Itens:</b></p>
<ul>
    @foreach($order->products as $product)
        <li>{{$product->price}} | {{$product->name}}</li>
    @endforeach
</ul>
<p>Obrigado por seu pedido.</p>
</body>
</html>
