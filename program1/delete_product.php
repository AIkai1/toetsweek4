<?php
require_once 'product_crud.php';
require_once 'ownership_manager.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id']) || !isset($input['user_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID en User ID zijn vereist', 'debug' => ['input' => $input]]);
    exit;
}

$productId = (int)$input['product_id'];
$userId = $input['user_id'];

$crud = new ProductCrud();
$ownershipManager = new OwnershipManager();

$isOwner = $ownershipManager->isOwner($productId, $userId);
if (!$isOwner) {
    $currentOwner = $ownershipManager->getOwner($productId);
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'message' => 'Je bent niet de eigenaar van dit product',
        'debug' => [
            'product_id' => $productId,
            'user_id' => $userId,
            'current_owner' => $currentOwner,
            'is_owner' => $isOwner
        ]
    ]);
    exit;
}

$product = $crud->get_product_by_id($productId);

$result = $crud->delete_product($productId);

if (is_array($result) && isset($result['error'])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $result['error']]);
    exit;
}

if ($result === true) {
    $ownershipManager->removeOwnership($productId);
    
    if ($product && !empty($product['afbeelding']) && file_exists($product['afbeelding'])) {
        @unlink($product['afbeelding']);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product succesvol verwijderd']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Product niet gevonden of al verwijderd']);
}
?>
