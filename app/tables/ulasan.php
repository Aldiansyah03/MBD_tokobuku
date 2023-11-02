<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//  tabel ulasan
$app->get('/ulasan', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    $query = $db->query('CALL SelectSemuaUlasan()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});

// get berdasarkan id 
$app->get('/ulasan/{id_Ulasan}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL SelectUlasanById(:id_Ulasan)');

    $query->bindParam(':id_Ulasan', $args['id_Ulasan'], PDO::PARAM_INT);

    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($results)) {
        $response->getBody()->write(json_encode($results[0]));
    } else {
        // Handle the case where no results were found, return an appropriate response.
        $response = $response->withStatus(404); // Not Found
        $response->getBody()->write(json_encode(["message" => "Ulasan not found"]));
    }

    return $response->withHeader("Content-Type", "application/json");
});

//  delete ulasan berdasarkan id
$app->delete('/ulasan/{id_Ulasan}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);
    $id_Ulasan = $args['id_Ulasan'];

    $query = $db->prepare('CALL DeleteUlasan(:id_Ulasan)');
    $query->bindParam(':id_Ulasan', $id_Ulasan, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Ulasan dengan ID ' . $id_Ulasan . ' berhasil dihapus.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

// update isi ulasan berdasarkan id
$app->put('/ulasan/{id_Ulasan}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();
    $isi_Ulasan = $parsedBody["isi_Ulasan"];
    $rating = $parsedBody["rating"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL UpdateUlasan(:id_Ulasan, :isi_Ulasan, :rating)');

    $query->bindParam(':id_Ulasan', $args['id_Ulasan'], PDO::PARAM_INT);
    $query->bindParam(':isi_Ulasan', $isi_Ulasan, PDO::PARAM_STR);
    $query->bindParam(':rating', $rating, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Ulasan dengan ID ' . $args['id_Ulasan'] . ' berhasil diperbarui.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

// creeate ulasan
$app->post('/ulasan', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $id_Pembeli = $parsedBody["id_Pembeli"];
    $id_Buku = $parsedBody["id_Buku"];
    $isi_Ulasan = $parsedBody["isi_Ulasan"];
    $rating = $parsedBody["rating"];

    $db = $this->get(PDO::class);

    // Membuat panggilan ke stored procedure CreateUlasan
    $query = $db->prepare('CALL CreateUlasan(:id_Pembeli, :id_Buku, :isi_Ulasan, :rating)');
    $query->bindParam(':id_Pembeli', $id_Pembeli, PDO::PARAM_INT);
    $query->bindParam(':id_Buku', $id_Buku, PDO::PARAM_INT);
    $query->bindParam(':isi_Ulasan', $isi_Ulasan, PDO::PARAM_STR);
    $query->bindParam(':rating', $rating, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Ulasan berhasil ditambahkan'
        ]
    ));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201); // Status 201 Created
});

?>