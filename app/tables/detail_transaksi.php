<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/detailTransaksi', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    $query = $db->query('CALL SelectdetailTransaksi()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});

$app->get('/detailTransaksi/{id_Detail_Transaksi}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL detailTransaksiById(:id_Detail_Transaksi)');
    $query->bindParam(':id_Detail_Transaksi', $args['id_Detail_Transaksi'], PDO::PARAM_INT);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($results)) {
        $response->getBody()->write(json_encode($results[0]));
    } else {
        // Handle the case where no results were found, return an appropriate response.
        $response = $response->withStatus(404); // Not Found
        $response->getBody()->write(json_encode(["message" => "Detail transaksi not found"]));
    }

    return $response->withHeader("Content-Type", "application/json");
});

$app->delete('/detailTransaksi/{id_Detail_Transaksi}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);
    $id_Detail_Transaksi = $args['id_Detail_Transaksi'];

    $query = $db->prepare('CALL HapusDetailTransaksi(:p_Id_Detail_Transaksi)');
    $query->bindParam(':p_Id_Detail_Transaksi', $id_Detail_Transaksi, PDO::PARAM_INT);
    
    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Detail transaksi dengan ID ' . $id_Detail_Transaksi . ' berhasil dihapus.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

$app->put('/detailTransaksi/{id_Detail_Transaksi}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();

    // Pastikan kunci 'jumlah_Beli' dan 'subtotal' ada sebelum mengaksesnya
    if (isset($parsedBody["jumlah_Beli"], $parsedBody["subtotal"])) {
        $jumlah_Beli = $parsedBody["jumlah_Beli"];
        $subtotal = $parsedBody["subtotal"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL PerbaruiDetailTransaksi(:p_Id_Detail_Transaksi, :p_Jumlah_Beli, :p_Subtotal)');

        $query->bindParam(':p_Id_Detail_Transaksi', $args['id_Detail_Transaksi'], PDO::PARAM_INT);
        $query->bindParam(':p_Jumlah_Beli', $jumlah_Beli, PDO::PARAM_INT);
        $query->bindParam(':p_Subtotal', $subtotal, PDO::PARAM_STR);

        // Lakukan penanganan kesalahan untuk eksekusi query
        try {
            $query->execute();

            $response->getBody()->write(json_encode(
                [
                    'message' => 'Detail transaksi dengan ID ' . $args['id_Detail_Transaksi'] . ' berhasil diperbarui.'
                ]
            ));
        } catch (PDOException $e) {
            // Tangani kesalahan yang terkait dengan database
            $response->getBody()->write(json_encode(
                [
                    'error' => 'Gagal memperbarui detail transaksi: ' . $e->getMessage()
                ]
            ));
        }
    } else {
        // Tangani jika kunci tidak tersedia dalam permintaan
        $response->getBody()->write(json_encode(
            [
                'error' => 'Kunci "jumlah_Beli" atau "subtotal" tidak tersedia dalam permintaan.'
            ]
        ));
    }

    return $response->withHeader('Content-Type', 'application/json');
});


$app->post('/detailTransaksi', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $id_Transaksi = $parsedBody["id_Transaksi"];
    $id_Buku = $parsedBody["id_Buku"];
    $jumlah_Beli = $parsedBody["jumlah_Beli"];
   

    $db = $this->get(PDO::class);

    // Prepare statement untuk memanggil stored procedure
    $query = $db->prepare('CALL TambahDetailTransaksi(:p_Id_Transaksi, :p_Id_Buku, :p_Jumlah_Beli, :p_Subtotal)');

    // Bind parameter
    $query->bindParam(':p_Id_Transaksi', $id_Transaksi, PDO::PARAM_INT);
    $query->bindParam(':p_Id_Buku', $id_Buku, PDO::PARAM_INT);
    $query->bindParam(':p_Jumlah_Beli', $jumlah_Beli, PDO::PARAM_INT);


    // Eksekusi statement
    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Detail transaksi berhasil ditambahkan'
        ]
    ));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201); // Status 201 Created
});





?>