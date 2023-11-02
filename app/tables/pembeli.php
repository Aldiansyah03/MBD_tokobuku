<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Mendapatkan semua data pembeli
$app->get('/pembeli', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    // Memanggil prosedur SQL 'SelectPembeli'
    $query = $db->query('CALL SelectPembeli()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});


$app->get('/pembeli/{id_Pembeli}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    
    $query = $db->prepare('CALL SelectPembeliById(:id_Pembeli)');

    
    $query->bindParam(':id_Pembeli', $args['id_Pembeli'], PDO::PARAM_INT);

   
    $query->execute();

  
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

  
    $response->getBody()->write(json_encode($results[0]));

    return $response->withHeader("Content-Type", "application/json");
});

$app->post('/pembeli', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $nama_Pembeli = $parsedBody["nama_Pembeli"];
    $kata_Sandi = $parsedBody["kata_Sandi"];
    $Alamat_Email = $parsedBody["Alamat_Email"];

    $db = $this->get(PDO::class);

    // Membuat panggilan ke stored procedure CreatePembeli
    $query = $db->prepare('CALL CreatePembeli(:nama_Pembeli, :kata_Sandi, :Alamat_Email)');
    $query->bindParam(':nama_Pembeli', $nama_Pembeli, PDO::PARAM_STR);
    $query->bindParam(':kata_Sandi', $kata_Sandi, PDO::PARAM_STR);
    $query->bindParam(':Alamat_Email', $Alamat_Email, PDO::PARAM_STR);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Pembeli berhasil ditambahkan'
        ]
    ));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201); // Status 201 Created
});

$app->put('/pembeli/{id_Pembeli}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();
    $nama_Pembeli = $parsedBody["nama_Pembeli"];
    $kata_Sandi = $parsedBody["kata_Sandi"];
    $Alamat_Email = $parsedBody["Alamat_Email"];

    $db = $this->get(PDO::class);

    $query = $db->prepare('CALL UpdatePembeli(:id_Pembeli, :nama_Pembeli, :kata_Sandi, :Alamat_Email)');

    $query->bindParam(':id_Pembeli', $args['id_Pembeli'], PDO::PARAM_INT);
    $query->bindParam(':nama_Pembeli', $nama_Pembeli, PDO::PARAM_STR);
    $query->bindParam(':kata_Sandi', $kata_Sandi, PDO::PARAM_STR);
    $query->bindParam(':Alamat_Email', $Alamat_Email, PDO::PARAM_STR);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Data pembeli dengan ID ' . $args['id_Pembeli'] . ' berhasil diupdate.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

$app->delete('/pembeli/{id_Pembeli}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);
    $id_Pembeli = $args['id_Pembeli'];

    $query = $db->prepare('CALL DeletePembeli(:id_Pembeli)');
    $query->bindParam(':id_Pembeli', $id_Pembeli, PDO::PARAM_INT);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Pembeli dengan ID ' . $id_Pembeli . ' berhasil dihapus.'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

?>
