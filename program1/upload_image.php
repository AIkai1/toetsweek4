<?php
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'filename' => '', 'filepath' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Only POST requests allowed';
    echo json_encode($response);
    exit;
}

$uploadDir = 'uploadedimages/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        $response['message'] = 'Alleen afbeeldingen zijn toegestaan (JPEG, PNG, GIF, WebP)';
        echo json_encode($response);
        exit;
    }
    
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $response['message'] = 'Bestand is te groot. Maximum 5MB toegestaan.';
        echo json_encode($response);
        exit;
    }
    
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('img_') . '.' . strtolower($fileExtension);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $response['success'] = true;
        $response['message'] = 'Afbeelding succesvol geüpload!';
        $response['filename'] = $fileName;
        $response['filepath'] = $targetPath;
    } else {
        $response['message'] = 'Er is een fout opgetreden bij het uploaden.';
    }
} else {
    $response['message'] = 'Geen afbeelding ontvangen of upload fout.';
}

echo json_encode($response);
?>
