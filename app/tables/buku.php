<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// get all
$app->get('/buku', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    $query = $db->query('CALL selectbuku()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});

 //get by id
 $app->get('/buku/{id_Buku}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL selectbukuById(:id_buku)');

    $query->bindParam(':id_buku', $args['id_Buku'], PDO::PARAM_INT);

    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results[0]));

    return $response->withHeader("Content-Type", "application/json");
});
          
// post data
$app->post('/buku', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $judul = $parsedBody["judul"];
    $penulis = $parsedBody["penulis"];
    $jenis = $parsedBody["jenis"];
    $isbn = $parsedBody["isbn"];
    $tahunTerbit = $parsedBody["tahunTerbit"];
    $harga = $parsedBody["harga"];
    $stok = $parsedBody["stok"];

    $db = $this->get(PDO::class);

    // Membuat panggilan ke stored procedure CreateBuku
    $query = $db->prepare('CALL CreateBuku(:judul, :penulis, :jenis, :isbn, :tahunTerbit, :harga, :stok)');
    $query->bindParam(':judul', $judul, PDO::PARAM_STR);
    $query->bindParam(':penulis', $penulis, PDO::PARAM_STR);
    $query->bindParam(':jenis', $jenis, PDO::PARAM_STR);
    $query->bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $query->bindParam(':tahunTerbit', $tahunTerbit, PDO::PARAM_STR);
    $query->bindParam(':harga', $harga, PDO::PARAM_STR);
    $query->bindParam(':stok', $stok, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Buku berhasil ditambahkan'
        ]
    ));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201); // Status 201 Created
});


// put data
$app->put('/buku/{id_Buku}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();
    $hargaBaru = $parsedBody["HargaBaru"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL UpdateHargaBuku(:id_Buku, :hargaBaru)');

    $query->bindParam(':id_Buku', $args['id_Buku'], PDO::PARAM_INT);
    $query->bindParam(':hargaBaru', $hargaBaru, PDO::PARAM_STR);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Harga buku dengan ID ' . $args['id_Buku'] . ' berhasil diupdate.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

//delete
$app->delete('/buku/{id_Buku}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);
    $idBuku = $args['id_Buku'];

    $query = $db->prepare('CALL DeleteBuku(:idBuku)');
    $query->bindParam(':idBuku', $idBuku, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Buku dengan ID ' . $idBuku . ' berhasil dihapus.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

?>