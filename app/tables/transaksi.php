<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/transaksi', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    $query = $db->query('CALL SelectTransaksi()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});


$app->get('/transaksi/{id_Transaksi}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL TransaksiById(:idtransaksi)');
    $query->bindParam(':idtransaksi', $args['id_Transaksi'], PDO::PARAM_INT);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($results)) {
        $response->getBody()->write(json_encode($results[0]));
    } else {
        $response = $response->withStatus(404); // Not Found
        $response->getBody()->write(json_encode(["message" => "Transaksi not found"]));
    }

    return $response->withHeader("Content-Type", "application/json");
});


$app->delete('/transaksi/{id_Transaksi}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);
    $id_Transaksi = $args['id_Transaksi'];

    $query = $db->prepare('CALL HapusTransaksi(:p_Id_Transaksi)');
    $query->bindParam(':p_Id_Transaksi', $id_Transaksi, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Transaksi dengan ID ' . $id_Transaksi . ' berhasil dihapus.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});


$app->put('/transaksi/{id_Transaksi}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();
    $tanggal_Transaksi = $parsedBody["tanggal_Transaksi"];
    $metode_Pembayaran = $parsedBody["metode_Pembayaran"];
    $total_Harga = $parsedBody["total_Harga"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL PerbaruiTransaksi(:p_Id_Transaksi, :p_Tanggal_Transaksi, :p_Metode_Pembayaran, :p_Total_Harga)');

    $query->bindParam(':p_Id_Transaksi', $args['id_Transaksi'], PDO::PARAM_INT);
    $query->bindParam(':p_Tanggal_Transaksi', $tanggal_Transaksi, PDO::PARAM_STR);
    $query->bindParam(':p_Metode_Pembayaran', $metode_Pembayaran, PDO::PARAM_STR);
    $query->bindParam(':p_Total_Harga', $total_Harga, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Transaksi dengan ID ' . $args['id_Transaksi'] . ' berhasil diperbarui.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

$app->post('/transaksi', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $id_Pembeli = $parsedBody["id_Pembeli"];
    $tanggal_Transaksi = $parsedBody["tanggal_Transaksi"];
    $metode_Pembayaran = $parsedBody["metode_Pembayaran"];
    $total_Harga = $parsedBody["total_Harga"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL TambahTransaksi(:p_Id_Pembeli, :p_Tanggal_Transaksi, :p_Metode_Pembayaran, :p_Total_Harga)');
    $query->bindParam(':p_Id_Pembeli', $id_Pembeli, PDO::PARAM_INT);
    $query->bindParam(':p_Tanggal_Transaksi', $tanggal_Transaksi, PDO::PARAM_STR);
    $query->bindParam(':p_Metode_Pembayaran', $metode_Pembayaran, PDO::PARAM_STR);
    $query->bindParam(':p_Total_Harga', $total_Harga, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Transaksi berhasil ditambahkan'
        ]
    ));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201); // Status 201 Created
});


?>