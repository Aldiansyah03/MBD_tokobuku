<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
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


// Mendapatkan semua data pembeli
$app->get('/pembeli', function (Request $request, Response $response) {
    $db = $this->get(PDO::class);

    // Memanggil prosedur SQL 'SelectPembeli'
    $query = $db->query('CALL SelectPembeli()');
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});

// Mendapatkan data pembeli berdasarkan ID_Pembeli
$app->get('/pembeli/{id_Pembeli}', function (Request $request, Response $response, $args) {
    $db = $this->get(PDO::class);

    // Membuat query yang memanggil prosedur SQL 'SelectPembeliById' dengan parameter ':id_Pembeli'
    $query = $db->prepare('CALL SelectPembeliById(:id_Pembeli)');

    // Mengikat parameter ':id_Pembeli' dengan nilai dari $args['id_Pembeli']
    $query->bindParam(':id_Pembeli', $args['id_Pembeli'], PDO::PARAM_INT);

    // Mengeksekusi query
    $query->execute();

    // Mengambil hasil query dalam bentuk array asosiatif
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // Mengirim hasil dalam format JSON
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


};