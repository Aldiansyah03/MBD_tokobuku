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

    // Ensure the 'jumlah_Beli' and 'subtotal' keys exist before accessing them
    if (isset($parsedBody["jumlah_Beli"], $parsedBody["subtotal"])) {
        $jumlah_Beli = $parsedBody["jumlah_Beli"];
        $subtotal = $parsedBody["subtotal"];

        // Assuming $db is your PDO instance
        $db = $this->get(PDO::class);

        // Begin transaction
        $db->beginTransaction();

        // Use a prepared statement to call the stored procedure
        $query = $db->prepare('CALL UpdateDetailTransaksiJumlahSubtotalById(:p_Id_Detail_Transaksi, :p_Jumlah_Beli, :p_Subtotal)');

        // Bind parameters
        $query->bindParam(':p_Id_Detail_Transaksi', $args['id_Detail_Transaksi'], PDO::PARAM_INT);
        $query->bindParam(':p_Jumlah_Beli', $jumlah_Beli, PDO::PARAM_INT);
        $query->bindParam(':p_Subtotal', $subtotal, PDO::PARAM_STR);

        // Handle errors during query execution
        try {
            // Execute the stored procedure
            $query->execute();

            // Commit the transaction only if the execution is successful
            $db->commit();

            $response->getBody()->write(json_encode(
                [
                    'message' => 'Detail transaksi dengan ID ' . $args['id_Detail_Transaksi'] . ' berhasil diperbarui.'
                ]
            ));
        } catch (PDOException $e) {
            // Roll back the transaction in case of an error
            $db->rollBack();

            // Handle database-related errors
            $response->getBody()->write(json_encode(
                [
                    'error' => 'Gagal memperbarui detail transaksi: ' . $e->getMessage()
                ]
            ));
        }
    } else {
        // Handle if keys are not available in the request
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
    $subtotal = $parsedBody["subtotal"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL InsertDetailTransaksi(:p_Id_Transaksi, :p_Id_Buku, :p_Jumlah_Beli, :p_Subtotal)');
    $query->bindParam(':p_Id_Transaksi', $id_Transaksi, PDO::PARAM_INT);
    $query->bindParam(':p_Id_Buku', $id_Buku, PDO::PARAM_INT);
    $query->bindParam(':p_Jumlah_Beli', $jumlah_Beli, PDO::PARAM_INT);
    $query->bindParam(':p_Subtotal', $subtotal, PDO::PARAM_STR);

    try {
        $query->execute();

        $response->getBody()->write(json_encode(
            [
                'message' => 'Detail transaksi berhasil ditambahkan'
            ]
        ));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201); // Status 201 Created
    } catch (PDOException $e) {
        // Handle exceptions, you might want to log or provide a different response
        $response->getBody()->write(json_encode(
            [
                'error' => 'Error adding detail transaksi: ' . $e->getMessage()
            ]
        ));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500); // Status 500 Internal Server Error
    }
});




?>